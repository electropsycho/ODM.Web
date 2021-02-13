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
import Messenger from '../../helpers/messenger'
import UserService from '../../services/UserService'
import Page from '../../components/Page'

export default {
  name: 'UserList',
  components: { Page },
  data () {
    return {
      isApproving: false,
      userList: []
    }
  },
  mounted () {
    const vm = this
    const table = $('#userList')
      .DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
          url: `${vm.$getBasePath()}/api/users/passives`,
          dataType: 'json',
          type: 'POST',
          beforeSend (xhr) {
            Auth.check()
            const token = localStorage.getItem(Constants.ACCESS_TOKEN)
            xhr.setRequestHeader('Authorization',
              `Bearer ${token}`)
          }
          // error: function (jqXHR, textStatus, errorThrown) {
          //   console.log(errorThrown)
          // }
        },
        language: {
          sDecimal: ',',
          sEmptyTable: 'Tabloda herhangi bir veri mevcut değil',
          sInfo: '_TOTAL_ kayıttan _START_ - _END_ arasındaki kayıtlar gösteriliyor',
          sInfoEmpty: 'Kayıt yok',
          sInfoFiltered: '(_MAX_ kayıt içerisinden bulunan)',
          sInfoPostFix: '',
          sInfoThousands: '.',
          sLengthMenu: 'Sayfada _MENU_ kayıt göster',
          sLoadingRecords: 'Yükleniyor...',
          sProcessing: 'İşleniyor...',
          sSearch: 'Ara:',
          sZeroRecords: 'Eşleşen kayıt bulunamadı',
          oPaginate: {
            sFirst: 'İlk',
            sLast: 'Son',
            sNext: 'Sonraki',
            sPrevious: 'Önceki'
          },
          oAria: {
            sSortAscending: ': artan sütun sıralamasını aktifleştir',
            sSortDescending: ': azalan sütun sıralamasını aktifleştir'
          },
          select: {
            rows: {
              _: '%d kayıt seçildi',
              0: '',
              1: '1 kayıt seçildi'
            }
          }
        },
        columns: [
          {
            data: 'id',
            name: 'users.id',
            visible: false
          },
          {
            data: 'full_name',
            name: 'users.full_name',
            searchable: true
          },
          {
            data: 'phone',
            searchable: true
          },
          {
            data: 'branch_name',
            name: 'branches.name',
            searchable: true
          },
          {
            data: 'inst_name',
            name: 'institutions.name',
            searchable: true
          },
          {
            data: 'activator_name',
            name: 'um.full_name',
            searchable: false
          },
          {
            data: 'created_at',
            name: 'users.created_at',
            searchable: true
          },
          {
            data: '',
            className: 'text-center',
            width: '15%',
            render (data, type, row, meta) {
              return '<button class="btn btn-xs btn-info">Aktifleştir</button>'
            },
            searchable: false,
            orderable: false
          }
        ],
        retrieve: true,
        searching: true,
        paging: true
      })

    table.on('click', '.btn-info', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      Messenger.showPrompt('Bu kullanıcı tekrar aktive etmek istediğinizden emin misiniz?')
        .then(value => {
          if (value.isConfirmed) {
            UserService.reactivate(data.id)
              .then(resp => {
                Messenger.showInfo(resp.message)
                  .then(() => table.ajax.reload())
              })
              .catch(err => Messenger.showError(err.message))
          }
        })
    })
  }
}
</script>

<style lang="sass">
  /*@import '~datatables.net-bs4/css/dataTables.bootstrap4.min.css'*/
  /*@import '~datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'*/
</style>
