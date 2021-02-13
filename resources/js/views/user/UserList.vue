<!--
  - ODM.Web  https://github.com/electropsycho/ODM.Web
  - Copyright 2019-2020 Hakan GÜLEN
  -
  - Licensed under the Apache License, Version 2.0 (the "License");
  - you may not use this file except in compliance with the License.
  - You may obtain a copy of the License at
  -
  - http://www.apache.org/licenses/LICENSE-2.0
  -
  - Unless required by applicable law or agreed to in writing, software
  - distributed under the License is distributed on an "AS IS" BASIS,
  - WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  - See the License for the specific language governing permissions and
  - limitations under the License.
  -->

<template>
  <page>
    <template v-slot:header>
      <h4>Kullanıcı Listesi</h4>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 col-xs-12">
                  <div class="form-group has-feedback">
                    <label>Ders/Alan Seçimi</label>
                    <v-select
                      v-model="selectedBranch"
                      :options="branches"
                      :reduce="b => b.id"
                      label="name"
                      placeholder="Alan/Ders seçebilirsiniz"
                      @input="onSelectionChanged"
                    />
                  </div>
                </div>
                <div class="col-md-3 col-xs-12">
                  <div class="form-group has-feedback">
                    <label>Rol Seçimi</label>
                    <v-select
                      ref="refRole"
                      v-model="selectedRole"
                      :options="roles"
                      :reduce="r => r.id"
                      label="title"
                      placeholder="Rol seçebilirsiniz"
                      @input="onSelectionChanged"
                    />
                  </div>
                </div>
              </div>
              <div
                class="dataTables_wrapper dt-bootstrap4"
                :class="{ disabled : isApproving }"
              >
                <table
                  id="userList"
                  style="width:100%"
                  class="table table-bordered table-hover dataTable"
                  role="grid"
                >
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Ad Soyad</th>
                      <th>Telefon</th>
                      <th>Branş/Ders</th>
                      <th>Kurum</th>
                      <th>Onaylayan</th>
                      <th
                        data-type="date"
                        data-format="DD/MM/YYYY"
                      >
                        Kayıt Tarihi
                      </th>
                      <th>Aksiyon</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import Constants from '../../helpers/constants'
import Auth from '../../services/AuthService'
import UserService from '../../services/UserService'
import Messenger from '../../helpers/messenger'
import tr from '../../helpers/dataTablesTurkish'
import Page from '../../components/Page'
import vSelect from 'vue-select'
import RoleService from '../../services/RoleService'
import BranchService from '../../services/BranchService'

export default {
  name: 'UserList',
  components: { Page, vSelect },
  data: () => ({
    isApproving: false,
    userList: [],
    selectedBranch: '',
    branches: [],
    selectedRole: '',
    roles: [],
    userTable: null
  }),
  async beforeRouteEnter (to, from, next) {
    const [branches, roles] = await Promise.all([
      BranchService.getBranches(),
      RoleService.getRoles()
    ])
    next(vm => {
      vm.branches = branches
      vm.roles = roles
    })
  },
  mounted () {
    const vm = this
    const table = $('#userList')
      .on('preXhr.dt', (e, settings, data) => {
        // Bu event sunucuya datatable üzerinden veri gitmeden önce
        // yeni parametre eklemek için ateşleniyor
        data.branch_id = vm.selectedBranch
        data.role_id = vm.selectedRole
      })
      .DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
          url: `${vm.$getBasePath()}/api/users`,
          dataType: 'json',
          type: 'POST',
          beforeSend (xhr) {
            Auth.check()
            const token = localStorage.getItem(Constants.ACCESS_TOKEN)
            xhr.setRequestHeader('Authorization',
              `Bearer ${token}`)
          }
        },
        language: tr,
        columns: [
          {
            data: 'id',
            name: 'u.id',
            visible: false
          },
          {
            data: 'full_name',
            name: 'u.full_name',
            searchable: true
          },
          {
            data: 'phone',
            name: 'u.phone',
            searchable: true
          },
          {
            data: 'branch_name',
            name: 'b.name',
            searchable: true
          },
          {
            data: 'inst_name',
            name: 'i.name',
            searchable: true
          },
          {
            data: 'activator_name',
            name: 'um.full_name',
            searchable: false
          },
          {
            data: 'created_at',
            name: 'u.created_at',
            searchable: true
          },
          {
            data: '',
            className: 'text-center',
            width: '15%',
            render (data, type, row, meta) {
              if (row.activator_name !== null) {
                return '<div class="btn-group  btn-block">' +
                    '<button class="btn btn-xs btn-default">Göster</button>' +
                    '<button class="btn btn-xs btn-danger">Pasif.</button>' +
                    '</div>'
              }
              return '<div class="btn-group btn-block">' +
                  '<button class="btn btn-xs btn-default">Göster</button>' +
                  '<button class="btn btn-xs btn-warning">Onayla</button>' +
                  '<button class="btn btn-xs btn-danger">Sil</button>' +
                  '</div>'
            },
            searchable: false,
            orderable: false
          }
        ],
        retrieve: true,
        searching: true,
        paging: true
      })

    table.on('click', '.btn-default', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      vm.$router.push({ name: 'user', params: { id: data.id } })
    })

    table.on('click', '.btn-danger', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      Messenger.showPrompt('Kullanıcıyı pasifleştirmek istediğinize emin misiniz?')
        .then(value => {
          if (value.isConfirmed) {
            UserService.delete(data.id)
              .then(res => { Messenger.showSuccess(res) })
              .catch(err => { Messenger.showError(err) })
              .finally(() => {
                table.ajax.reload()
              })
          }
        })
    })

    table.on('click', '.btn-warning', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      vm.isApproving = true
      UserService.approveUser(data.id)
        .then(resp => {
          Messenger.showSuccess(resp.message)
          table.ajax.reload()
        })
        .finally(() => { vm.isApproving = false })
    })

    this.userTable = table
  },
  methods: {
    onSelectionChanged () {
      // eslint-disable-next-line no-unused-expressions
      this.userTable?.ajax.reload()
    }
  }
}
</script>

<style lang="sass">
</style>
