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
      <h4>Sınav Listesi</h4>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3 col-xs-12">
                  <div
                    class="form-group has-feedback"
                  >
                    <label>Sınıf Seviyesi Seçimi</label>
                    <v-select
                      v-model="selectedClassLevel"
                      :options="classLevels"
                      placeholder="Sınıf seviyesini seçebilirsiniz"
                      @input="onSelectionChanged"
                    />
                  </div>
                </div>
                <div class="col-md-3 col-xs-12">
                  <div
                    class="form-group has-feedback"
                  >
                    <label>Sınav amacı</label>
                    <v-select
                      v-model="selectedExamPurpose"
                      :options="examPurposes"
                      :reduce="b => b.id"
                      label="purpose"
                      placeholder="Sınav amacını seçebilirsiniz"
                      @input="onSelectionChanged"
                    />
                  </div>
                </div>
              </div>
              <div class="dataTables_wrapper dt-bootstrap4">
                <table
                  id="examList"
                  style="width:100%"
                  class="table row-border table-hover dataTable"
                  role="grid"
                >
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Oluşturucu Id</th>
                      <th>Kod</th>
                      <th>Başlık</th>
                      <th>Oluşturan</th>
                      <th>Amaç</th>
                      <th>Durum</th>
                      <th>Sınıf Seviyesi</th>
                      <th>Planlanan Tarih</th>
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
import Constants, { MessengerConstants } from '../../helpers/constants'
import Messenger from '../../helpers/messenger'
import tr from '../../helpers/dataTablesTurkish'
import Page from '../../components/Page'
import ExamService, { ExamPurposeService } from '../../services/ExamService'
import FileSaver from '../../helpers/FileSaver'
import vSelect from 'vue-select'
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'ExamList',
  components: { Page, vSelect },
  data: () => ({
    examPurposes: [],
    selectedExamPurpose: '',
    classLevels: [...Array(9).keys()].map(i => i + 4),
    selectedClassLevel: '',
    table: null
  }),
  async beforeRouteEnter (to, from, next) {
    const purposes = await ExamPurposeService.getPurposes()
    next(vm => {
      vm.examPurposes = purposes
    })
  },
  computed: {
    ...mapGetters('examList', [
      'classLevel',
      'examPurpose'
    ])
  },
  created () {
    this.selectedClassLevel = this.classLevel
    this.selectedExamPurpose = this.examPurpose
  },
  mounted () {
    const vm = this
    const table = $('#examList')
      .on('preXhr.dt', (e, settings, data) => {
        // Bu event sunucuya datatable üzerinden veri gitmeden önce
        // yeni parametre eklemek için ateşleniyor
        data.class_level = vm.selectedClassLevel
        data.ep_id = vm.selectedExamPurpose
      })
      .DataTable({
        stateSave: true,
        stateDuration: -1, // session storage kullanıulıyor böyle yapınca
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
          url: `${vm.$getBasePath()}/api/exams/list`,
          dataType: 'json',
          type: 'POST',
          beforeSend (xhr) {
            Auth.check()
            const token = localStorage.getItem(Constants.ACCESS_TOKEN)
            xhr.setRequestHeader('Authorization',
                  `Bearer ${token}`)
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown)
            Messenger.showError(MessengerConstants.errorMessage)
          }
        },
        language: tr,
        columns: [
          {
            data: 'id',
            name: 'e.id',
            visible: false,
            searchable: false
          },
          {
            data: 'creator_id',
            name: 'e.creator_id',
            visible: false,
            searchable: false

          },
          {
            data: 'code',
            name: 'e.code'
          },
          {
            data: 'title',
            name: 'e.title'
          },
          {
            data: 'full_name',
            name: 'u.full_name'
          },
          {
            data: 'purpose',
            name: 'p.purpose',
            searchable: false
          },
          {
            data: 'status',
            name: 'status',
            searchable: false
          },
          {
            data: 'class_level',
            name: 'e.class_level',
            className: 'text-center',
            searchable: true
          },
          {
            data: 'planned_date',
            name: 'e.planned_date',
            className: 'text-center',
            searchable: true
          },
          {
            data: '',
            className: 'text-center',
            width: '15%',
            render (data, type, row, meta) {
              // const number = Number(row.status)
              // if (number === QuestionStatuses.NEED_REVISION) {
              //   return '<button class="btn btn-xs btn-warning">Revize Et</button>'
              // } else if (vm.$isInRole('admin') && (number === QuestionStatuses.REVISION_COMPLETED || number === QuestionStatuses.WAITING_FOR_ACTION)) {
              //   return '<button class="btn btn-xs btn-primary">Değerlendirici Ata</button>'
              // } else {
              return '<button class="btn btn-xs btn-info">Göster</button>' +
                  '<button class="btn btn-xs btn-warning">İndir</button>'
              // }
            },
            searchable: false,
            orderable: false
          }
        ],
        retrieve: true,
        searching: true,
        paging: true
      })

    vm.table = table

    // Tablo içindeki belli bir css sınıfına sahip bir butona basınca çalışacak event
    table.on('click', '.btn-warning', async (e) => {
      const promptRes = await Messenger.showPrompt('Sınav dosyasını indirmek istiyor musunuz?')
      if (!promptRes.isConfirmed) return
      const loader = vm.$loading.show()
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      try {
        const fileInfo = await ExamService.getExamFile(data?.id)
        FileSaver.save(fileInfo.file, fileInfo.fileName)
      } catch (e) {
        console.log(e)
      } finally {
        loader.hide()
      }
    })

    // Tablo içindeki belli bir css sınıfına sahip bir butona basınca çalışacak event
    table.on('click', '.btn-info', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      vm.$router.push({ name: 'showExam', params: { examId: data.id } })
      // vm.$router.push({ name: 'showQuestion', params: { questionId: data.id } })
    })
  },
  methods: {
    ...mapActions('examList', [
      'setClassLevel',
      'setExamPurpose'
    ]),
    onSelectionChanged () {
      this.setExamPurpose(this.selectedExamPurpose)
      this.setClassLevel(this.selectedClassLevel)
      // eslint-disable-next-line no-unused-expressions
      this.table?.ajax.reload()
    }
  }
}
</script>

<style scoped>

</style>
