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
      <h4><span class="mdi mdi-delete-restore" /> Soru Silme İstekleri</h4>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div
                class="dataTables_wrapper dt-bootstrap4"
              >
                <table
                  id="qdrList"
                  style="width:100%"
                  class="table table-bordered table-hover dataTable dtr-inline"
                  role="grid"
                >
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>QuestionId</th>
                      <th>Oluşturan</th>
                      <th>Açıklama</th>
                      <th>Kazanım</th>
                      <th
                        data-type="date"
                        data-format="DD.MM.YYYY"
                      >
                        Kayıt Tarihi
                      </th>
                      <th>Onaylayan</th>
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
import { QuestionDeleteService } from '../../services/QuestionDeleteRequestService'
import tr from '../../helpers/dataTablesTurkish'
import Page from '../../components/Page'

export default {
  name: 'QuestionDeleteRequests',
  components: { Page },
  mounted () {
    const vm = this
    const table = $('#qdrList')
      .DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        stateSave: true,
        stateDuration: -1,
        ajax: {
          url: `${vm.$getBasePath()}/api/questions/delete_requests`,
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
            name: 'users.id',
            visible: false
          },
          {
            data: 'question_id',
            name: 'qdr.question_id',
            visible: false
          },
          {
            data: 'full_name',
            name: 'users.full_name',
            searchable: true
          },
          {
            data: 'comment',
            searchable: true
          },
          {
            data: 'learning_outcome',
            searchable: false
          },
          {
            data: 'created_at',
            name: 'qdr.created_at',
            searchable: true
          },
          {
            data: 'acceptor_name',
            name: 'um.full_name',
            searchable: false
          },
          {
            data: '',
            className: 'text-center',
            width: '15%',
            render (data, type, row, meta) {
              if (vm.$isInRole('admin')) {
                if (row.acceptor_name === null) {
                  return '<div class="btn-group">' +
                                    '<button class="btn btn-xs btn-info">Gör</button>' +
                                    '<button class="btn btn-xs btn-warning">İsteği Sil</button>' +
                                    '<button class="btn btn-xs btn-danger">Onayla</button>' +
                                 '</div>'
                }
                return 'Silinmiş'
              }
              return 'Yetkiniz yok'
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
      console.log(data)
      vm.$router.push({ name: 'showQuestion', params: { questionId: data.question_id } })
    })

    table.on('click', '.btn-warning', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      console.log(data)
      Messenger.showPrompt('Bu soru silme talebinin kendisini silerek talebi iptal etmek istediğinize emin misiniz?')
        .then(value => {
          if (value.isConfirmed) {
            QuestionDeleteService.delete(data.id, data.question_id)
              .then(resp => {
                Messenger.showSuccess(resp.message)
              })
              .catch(err => {
                Messenger.showError(err)
              })
              .finally(() => table.ajax.reload())
          }
        })
    })

    table.on('click', '.btn-danger', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      Messenger.showPrompt('Bu soruya ait tüm veriler silinecek! Bu isteğe onay vermek istediğinize emin misiniz?')
        .then(value => {
          if (value.isConfirmed) {
            QuestionDeleteService.approve(data.id)
              .then(res => {
                Messenger.showSuccess(res.message)
              })
              .catch(err => {
                Messenger.showError(err)
              })
              .finally(() => table.ajax.reload())
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
