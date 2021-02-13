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
      <h4>Soru Listesi</h4>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div
                  v-if="checkPermission"
                  class="col-md-3 col-xs-12"
                >
                  <div
                    class="form-group has-feedback"
                  >
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
                  <div
                    class="form-group has-feedback"
                  >
                    <label>Sınıf Seviyesi Seçimi</label>
                    <v-select
                      ref="classLevelDD"
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
                    <label>Soru Durumu</label>
                    <v-select
                      ref="qStatusDD"
                      v-model="selectedStatus"
                      :options="statuses"
                      :reduce="b => b.statusCode"
                      label="title"
                      placeholder="Sınıf seviyesini seçebilirsiniz"
                      @input="onSelectionChanged"
                    />
                  </div>
                </div>
                <div class="col-md-3 col-xs-12">
                  <div
                    class="form-group has-feedback"
                  >
                    <label>Tasarım İhtiyacı</label>
                    <v-select
                      ref="qIsDesignReq"
                      v-model="selectedDesignOption"
                      :options="designOptions"
                      :reduce="b => b.key"
                      label="value"
                      placeholder="Tasarım durumu seçebilirsiniz"
                      @input="onSelectionChanged"
                    />
                  </div>
                </div>
              </div>
              <div class="dataTables_wrapper dt-bootstrap4">
                <table
                  id="questionList"
                  style="width:100%"
                  class="table row-border table-hover dataTable"
                  role="grid"
                >
                  <thead>
                    <tr>
                      <th>Id</th>
                      <th>Oluşturucu Id</th>
                      <th>Kazanım Id</th>
                      <th>Ad Soyad</th>
                      <th>Branş/Ders</th>
                      <th>Kazanım Kodu</th>
                      <th>Durum</th>
                      <th>Kayıt Tarihi</th>
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
import tr from '../../helpers/dataTablesTurkish'
import Messenger from '../../helpers/messenger'
import LearningOutcomesService from '../../services/LearningOutcomesService'
import vSelect from 'vue-select'
import BranchService from '../../services/BranchService'
import UserService from '../../services/UserService'
import { QuestionStatuses } from '../../helpers/QuestionStatuses'
import Page from '../../components/Page'
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'QuestionTableList',
  components: { Page, vSelect },
  data: () => ({
    statuses: [
      { statusCode: '', title: 'Hepsi' },
      { statusCode: QuestionStatuses.WAITING_FOR_ACTION, title: 'İşleme alınmamışlar' },
      { statusCode: QuestionStatuses.IN_ELECTION, title: 'Değerlendirme aşamasında' },
      { statusCode: QuestionStatuses.NEED_REVISION, title: 'Revizyon alması gerekenler' },
      { statusCode: QuestionStatuses.NOT_MUST_ASKED, title: 'Sorulamayacak sorular' },
      { statusCode: QuestionStatuses.REVISION_COMPLETED, title: 'Revizyonu tamamlananlar' },
      { statusCode: QuestionStatuses.APPROVED, title: 'Havuza girenler' }
    ],
    selectedStatus: '',

    branches: [],
    selectedBranch: '',

    classLevels: [],
    selectedClassLevel: '',

    designOptions: [
      { key: '', value: 'Belirsiz' },
      { key: true, value: 'İhtiyaç Var' },
      { key: false, value: 'İhtiyaç Yok' }
    ],
    selectedDesignOption: '',

    user: '',
    table: null
  }),
  async beforeRouteEnter (to, from, next) {
    try {
      const [branches, user] = await Promise.all([
        BranchService.getBranches(),
        UserService.findById(Auth.getUserId())
      ])
      next(vm => {
        vm.user = user
        // Burada kullanıcı sosyal bilgiler öğretmeni ise
        // hem sosyal bilgilere hem de inklap tarihi dersine soru yazabilmeli
        if (!vm.$isInRole('admin') && (user.branch.code === 'SB')) {
          vm.branches = branches.filter(b => b.code === 'SB' || b.code === 'İTA')
        } else if (!vm.$isInRole('admin') && (user.branch.code === 'TAR')) {
          vm.branches = branches.filter(b => b.code === 'TAR' || b.code === 'İTA')
        } else {
          vm.branches = branches
        }
      })
    } catch (error) {
      await Messenger.showError(error.message)
      next(from.path)
    }
  },
  computed: {
    ...mapGetters('questionList', [
      'branch',
      'classLevel',
      'questionState',
      'isDesignRequired'
    ]),
    checkPermission () {
      if (this.user) {
        return this.$isInRole('admin') ||
            this.user.branch.code === 'SB' ||
            this.user.branch.code === 'TAR'
      }
      return false
    }
  },
  created () {
    // Kullancı eğer yönetici değil ise üzücü olmamak adına
    // Çöp soruları kullanıcı ya da değerlendiriciye göstermiyoruz
    if (!this.$isInRole('admin')) {
      this.statuses = this.statuses.filter(value => value.statusCode !== QuestionStatuses.NOT_MUST_ASKED)
    }
    this.classLevels.push('Hepsi')
    this.classLevels = this.classLevels.concat([...Array(9).keys()].map(i => i + 4))

    this.selectedClassLevel = this.$store.getters['questionList/classLevel']
    this.selectedBranch = this.$store.getters['questionList/branch']
    this.selectedStatus = this.$store.getters['questionList/questionState']
    this.selectedDesignOption = this.$store.getters['questionList/isDesignRequired']
  },
  mounted () {
    const vm = this
    const table = $('#questionList')
      .on('preXhr.dt', (e, settings, data) => {
        // Bu event sunucuya datatable üzerinden veri gitmeden önce
        // yeni parametre eklemek için ateşleniyor
        data.question_status = vm.selectedStatus
        data.branch_id = vm.selectedBranch
        data.is_design_required = vm.selectedDesignOption
        if (vm.selectedClassLevel !== 'Hepsi') { data.class_level = vm.selectedClassLevel }
      })
      .DataTable({
        stateSave: true,
        stateDuration: -1,
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
          url: `${vm.$getBasePath()}/api/questions/list`,
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
            name: 'q.id',
            visible: false
          },
          {
            data: 'creator_id',
            name: 'q.creator_id',
            visible: false
          },
          {
            data: 'lo_id',
            name: 'l.id',
            visible: false
          },
          {
            data: 'full_name',
            name: 'u.full_name',
            searchable: true,
            render (data, type, row, meta) {
              return data || 'Silinmiş Kullanıcı'
            }
          },
          {
            data: 'branch_name',
            name: 'b.name',
            searchable: true
          },
          {
            data: 'code',
            name: 'l.code',
            className: 'text-center',
            render (data, type, row, meta) {
              return `<button class="btn btn-xs btn-default">${data}</button>`
            },
            searchable: true
          },
          {
            data: 'status',
            name: 'q.status',
            className: 'text-center',
            searchable: false,
            render (data, type, row, meta) {
              switch (Number(data)) {
                case QuestionStatuses.WAITING_FOR_ACTION: return '<span class="badge bg-gradient-secondary">İşleme alınmamış</span>'
                case QuestionStatuses.NOT_MUST_ASKED: return '<span class="badge bg-danger">Sorulamaz</span>'
                case QuestionStatuses.NEED_REVISION: return '<span class="badge bg-warning">Revizyon Gerekiyor</span>'
                case QuestionStatuses.IN_ELECTION: return '<span class="badge bg-info">Değerlendirme Aşamasında</span>'
                case QuestionStatuses.REVISION_COMPLETED: return '<span class="badge bg-primary">Revizyon Tamamlanmış</span>'
                case QuestionStatuses.APPROVED: return '<span class="badge bg-success">Havuza Girmiş</span>'
              }
            }
          },
          {
            data: 'created_at',
            name: 'q.created_at',
            className: 'text-center',
            searchable: true
          },
          {
            data: '',
            className: 'text-center',
            width: '15%',
            render (data, type, row, meta) {
              const number = Number(row.status)
              if (number === QuestionStatuses.NEED_REVISION) {
                return '<button class="btn btn-xs btn-warning">Revize Et</button>'
              } else if (vm.$isInRole('admin') && (number === QuestionStatuses.REVISION_COMPLETED || number === QuestionStatuses.WAITING_FOR_ACTION)) {
                return '<button class="btn btn-xs btn-primary">Değerlendirici Ata</button>'
              } else {
                return '<button class="btn btn-xs btn-info">Göster</button>'
              }
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
    table.on('click', '.btn-info', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      vm.$router.push({ name: 'showQuestion', params: { questionId: data.id } })
    })

    table.on('click', '.btn-warning', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      vm.$router.push({ name: 'reviseQuestion', params: { questionId: data.id } })
    })

    table.on('click', '.btn-primary', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      vm.$router.push({ name: 'setEvaluators', params: { questionId: data.id } })
    })

    table.on('click', '.btn-default', (e) => {
      const data = table.row($(e.toElement).parents('tr')[0]).data()
      // console.log(data);
      vm.showLearningOutcome(data.lo_id)
    })
    // this.$nextTick(() => {
    //   this.table?.page(vm.$store.getters['questionList/currentPage']).draw(false)
    // })
    //
    // this.table?.page(vm.$store.getters['questionList/currentPage'])
  },
  methods: {
    ...mapActions('questionList', [
      'setSelectedBranch',
      'setSelectedClassLevel',
      'setSelectedQuestionState',
      'setIsDesignRequired'
    ]),
    onSelectionChanged () {
      this.setSelectedQuestionState(this.selectedStatus)
      this.setSelectedClassLevel(this.selectedClassLevel)
      this.setSelectedBranch(this.selectedBranch)
      this.setIsDesignRequired(this.selectedDesignOption)
      if (this.table) {
        this.table.ajax.reload()
      }
    },
    async showLearningOutcome (loId) {
      try {
        const result = await LearningOutcomesService.findById(loId)
        await Messenger.showInfo(`<b>${result.branch_name}</b>: ${result.code} ${result.content}`)
      } catch (err) { await Messenger.showError(err.message) }
    }
  }
}
</script>

<style lang="sass">
  /*@import '~datatables.net-bs4/css/dataTables.bootstrap4.min.css'*/
  /*@import '~datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css'*/
</style>
