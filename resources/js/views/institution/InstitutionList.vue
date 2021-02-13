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
      <h4><span class="mdi mdi-bank" /> Okul/Kurum Listesi</h4>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="dataTables_wrapper dt-bootstrap4">
                <table
                  id="instList"
                  style="width:100%"
                  class="table table-bordered table-hover dataTable dtr-inline"
                  role="grid"
                >
                  <thead>
                    <tr>
                      <th>Kurum Kodu</th>
                      <th>Genel Müdürlük</th>
                      <th>Okul/Kurum Adı</th>
                      <th>Telefon</th>
                      <th>Öğretmen Sayısı</th>
                      <th>Soru Sayısı</th>
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
import Auth from '../../services/AuthService'
import Constants from '../../helpers/constants'
import tr from '../../helpers/dataTablesTurkish'
import Page from '../../components/Page'

export default {
  name: 'InstitutionList',
  components: { Page },
  data: () => ({
    instList: []
  }),
  mounted () {
    const vm = this
    const table = $('#instList')
      .DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
          url: `${vm.$getBasePath()}/api/institutions/list`,
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
        language: tr,
        columns: [
          {
            data: 'id',
            name: 'i.id',
            width: '10%'
          },
          {
            data: 'unit_name',
            name: 'u.name',
            searchable: true
          },
          {
            data: 'name',
            name: 'i.name',
            searchable: true
          },
          {
            data: 'phone',
            name: 'i.phone',
            searchable: true,
            orderable: false
          },
          {
            data: 'teacher_count',
            name: 'teacher_count',
            searchable: false
          },
          {
            data: 'question_count',
            name: 'question_count',
            searchable: false
          },
          {
            data: '',
            className: 'text-center',
            width: '15%',
            render (data, type, row, meta) {
              return '<div class="btn-group">' +
                '<button class="btn btn-xs btn-info">Göster</button>' +
                '<button class="btn btn-xs btn-warning">Düzenle</button>' +
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

    table.on('click', '.btn-info', (e) => {
      // const data = table.row($(e.toElement).parents('tr')[0]).data()
      vm.$router.push({ name: 'underConstruction' })
    })

    table.on('click', '.btn-warning', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      vm.$router.push({ name: 'editInst', params: { instId: data.id } })
    })
  }
}
</script>

<style lang="sass">
  /*@import '~datatables.net-bs4/css/dataTables.bootstrap4.min.css'*/
  /*@import '~datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'*/
</style>
