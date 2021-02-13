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
      <div class="row">
        <div class="col-md-6">
          <h4>
            Yeni Hızlı Soru Oluşturma
          </h4>
        </div>
        <div class="col-md-6">
          <div class="row justify-content-md-end">
            <div class="col-md-4">
              <label class="btn btn-success btn-block">
                <input
                  ref="qFile"
                  v-validate="'required|size:1024'"
                  name="questionFile"
                  type="file"
                  style="display: none !important;"
                  accept="application/pdf"
                  @change="selectQuestionGraphic($event)"
                >Dosya seçiniz
              </label>
            </div>
            <div class="col-md-2">
              <button
                class="btn btn-warning btn-block"
                style="margin-right: 10px"
                @click="clearSelection"
              >
                Temizle
              </button>
            </div>
          </div>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-3">
                  <div
                    v-if="checkBranches"
                    class="form-group has-feedback"
                  >
                    <label>Ders/Alan Seçimi</label>
                    <v-select
                      v-model="selectedBranch"
                      v-validate="'required'"
                      :options="branches"
                      :reduce="b => b.id"
                      label="name"
                      :clearable="false"
                      :class="{'is-invalid': errors.has('branch')}"
                      name="branch"
                      placeholder="Ders seçimi yapınız"
                      @input="clearClasses"
                    >
                      <div slot="no-options">
                        Burada bişey bulamadık :-(
                      </div>
                    </v-select>
                    <span
                      v-if="errors.has('branch')"
                      class="error invalid-feedback"
                    >{{ errors.first('branch') }}</span>
                  </div>
                  <div
                    class="form-group has-feedback"
                  >
                    <label>Sınıf Seviyesi Seçimi</label>
                    <v-select
                      ref="classLevelDD"
                      v-model="selectedClassLevel"
                      v-validate="'required'"
                      :options="classLevels"
                      name="selectedClassLevel"
                      :class="{'is-invalid': errors.has('selectedClassLevel')}"
                      placeholder="Sınıf seviyesini seçiniz"
                      @input="clearLearningOutCome"
                    >
                      <div slot="no-options">
                        Burada bişey bulamadık :-(
                      </div>
                    </v-select>
                    <span
                      v-if="errors.has('selectedClassLevel')"
                      class="error invalid-feedback"
                    >{{ errors.first('selectedClassLevel') }}</span>
                  </div>
                  <div
                    v-if="selectedClassLevel !== null"
                    class="form-group has-feedback"
                  >
                    <label>Kazanım Seçimi</label>
                    <v-select
                      ref="loDD"
                      v-model="selectedLearningOutCome"
                      v-validate="'required'"
                      :options="learningOutComes"
                      :reduce="lo => lo.id"
                      label="content"
                      name="learningOutCome"
                      :class="{'is-invalid': errors.has('learningOutCome')}"
                      placeholder="Ara"
                      @search="searchLearningOutcomes"
                    >
                      <div slot="no-options">
                        Burada bişey bulamadık :-(
                      </div>
                      <template
                        slot="option"
                        slot-scope="option"
                      >
                        {{ option.code }} - {{ option.content }}
                      </template>
                      <template
                        slot="selected-option"
                        slot-scope="option"
                      >
                        {{ option.code }} - {{ option.content }}
                      </template>
                    </v-select>
                    <span
                      v-if="errors.has('learningOutCome')"
                      class="error invalid-feedback"
                    >{{ errors.first('learningOutCome') }}</span>
                  </div>
                  <div
                    v-if="selectedLearningOutCome !== null"
                    class="form-group has-feedback"
                  >
                    <label>Zorluk Seviyesi Seçimi</label>
                    <v-select
                      v-model="selectedDifficulty"
                      v-validate="'required'"
                      :options="difficultyLevels"
                      :reduce="d => d.degree"
                      label="content"
                      name="difficulty"
                      :class="{'is-invalid': errors.has('difficulty')}"
                      placeholder="Zorluk seviyesini seçiniz"
                    />
                    <span
                      v-if="errors.has('difficulty')"
                      class="error invalid-feedback"
                    >{{ errors.first('difficulty') }}</span>
                  </div>
                  <div
                    v-if="selectedDifficulty !== null"
                    class="form-group has-feedback"
                  >
                    <label>Doğru Cevap</label>
                    <v-select
                      v-model="selectedCorrectAnswer"
                      v-validate="'required'"
                      :options="answers"
                      name="correctAnswer"
                      :class="{'is-invalid': errors.has('correctAnswer')}"
                      placeholder="Doğru cevabı seçiniz"
                    />
                    <span
                      v-if="errors.has('correctAnswer')"
                      class="error invalid-feedback"
                    >{{ errors.first('correctAnswer') }}</span>
                    <!--          <input v-model="branch_id" type="text" class="form-control" placeholder="Branş Seçimi">-->
                    <!--          <span class="glyphicon glyphicon-barcode form-control-feedback"></span>-->
                  </div>
                  <div
                    v-if="selectedCorrectAnswer !== null"
                    class="form-group has-feedback"
                  >
                    <label>Görsel tasarım ihtiyacı</label>
                    <br>
                    <p-check
                      v-model="isDesignRequired"
                      class="p-icon p-smooth"
                      color="success"
                    >
                      <i
                        slot="extra"
                        class="icon fa fa-check"
                      />
                      Tasarıma ihtiyaç {{ isDesignRequired ? 'vardır' : 'yoktur' }}
                    </p-check>
                  </div>
                  <div
                    v-if="selectedCorrectAnswer !== null"
                    class="form-group"
                  >
                    <label>Soru Kökü/Anahtar Kelimeler</label>
                    <textarea
                      class="form-control"
                      rows="3"
                      placeholder="Boşluk bırakarak anahtar kelimeler girebilirsiniz."
                      @model="keywords"
                    />
                  </div>
                  <div
                    v-if="selectedDifficulty !== null"
                    class="form-group has-feedback"
                  >
                    <button
                      :class="{ disabled : errors.any() || isSending }"
                      class="btn btn-primary btn-block"
                      @click="save"
                    >
                      Kaydet
                    </button>
                  </div>
                </div>
                <div class="col-md-9">
                  <div
                    v-if="questionFile !== null && !errors.has('questionFile')"
                    class="row"
                  >
                    <object
                      id="pdf"
                      height="600pt"
                      width="100%"
                      type="application/pdf"
                      :data="questionFileURL"
                    >
                      <span>PDF eklentisi bulunamadı.</span>
                    </object>
                  </div>
                  <div
                    v-if="errors.has('questionFile')"
                    class="row justify-content-md-center"
                  >
                    <div class="col-md-12">
                      <div class="row justify-content-md-center mt-4">
                        <h4><span class="badge badge-danger">{{ errors.first('questionFile') }}</span></h4>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </page>
</template>

<script>

import debounce from 'lodash/debounce'
import vSelect from 'vue-select'
import QuestionService from '../../services/QuestionService'
import BranchService from '../../services/BranchService'
import Messenger from '../../helpers/messenger'
import { MessengerConstants } from '../../helpers/constants'
import LearningOutcomesService from '../../services/LearningOutcomesService'
import UserService from '../../services/UserService'
import AuthService from '../../services/AuthService'
import Page from '../../components/Page'
import PCheck from 'pretty-checkbox-vue/check'

export default {
  name: 'SimpleNewQuestion',
  components: { Page, vSelect, PCheck },
  data () {
    return {
      selectedDifficulty: null,
      difficultyLevels: [
        { degree: 1, content: 'Çok Kolay' },
        { degree: 2, content: 'Kolay' },
        { degree: 3, content: 'Normal' },
        { degree: 4, content: 'Zor' },
        { degree: 5, content: 'Çok Zor' }
      ],

      selectedBranch: '',
      branches: [],

      questionFile: null,
      keywords: '',
      answers: ['A', 'B', 'C', 'D', 'E'],
      selectedCorrectAnswer: null,

      questionFileURL: null,
      selectedClassLevel: null,

      classLevels: [],
      selectedLearningOutCome: null,
      learningOutComes: [],
      isSending: false,
      user: '',
      isDesignRequired: false
    }
  },
  computed: {
    checkBranches () {
      if (this.user) {
        return this.$isInRole('admin') || this.user.branch.code === 'SB' || this.user.branch.code === 'TAR'
      }
      return false
    }
  },
  beforeRouteEnter (to, from, next) {
    Promise.all([BranchService.getBranches(), UserService.findById(AuthService.getUserId())])
      .then(([branches, user]) => {
        next(vm => {
          vm.user = user
          // Burada kullanıcı sosyal bilgiler öğretmeni ise
          // hem sosyal bilgilere hem de inklap tarihi dersine soru yazabilmeli
          if (!vm.$isInRole('admin') && user.branch.code === 'SB') {
            vm.branches = branches.filter(b => b.code === 'SB' || b.code === 'İTA')
          } else if (!vm.$isInRole('admin') && user.branch.code === 'TAR') {
            vm.branches = branches.filter(b => b.code === 'İTA' || b.code === 'TAR')
          } else if (vm.$isInRole('admin')) {
            vm.branches = branches
          } else {
            vm.classLevels = branches.filter(b => b.id === user.branch_id)[0].class_levels.split(',').map(Number)
          }
        })
      })
      .catch(() => {
        Messenger.showError(MessengerConstants.errorMessage)
      })
  },
  methods: {
    clearClasses () {
      // Kullanıcının durumuna göre sınıf listesini düzenleyelim
      if (this.$isInRole('admin') ||
          (!this.$isInRole('admin') &&
          (this.user.branch.code === 'SB' || this.user.branch.code === 'TAR'))) {
        this.classLevels = this.branches
          .filter(branch => branch.id === this.selectedBranch)[0]
          .class_levels
          .split(',')
          .map(Number)
      } else {
        this.classLevels = this.branches
          .filter(branch => branch.id === this.user.branch_id)[0]
          .class_levels
          .split(',')
          .map(Number)
      }
      if (this.selectedClassLevel !== null) {
        this.selectedClassLevel = null
        this.$refs.classLevelDD.clearSelection()
      }
    },
    clearLearningOutCome () {
      if (this.selectedLearningOutCome !== null) {
        this.learningOutComes = []
        this.selectedLearningOutCome = null
        this.$refs.loDD.clearSelection()
      }
    },
    searchLearningOutcomes: debounce(function (search, loading) {
      if (search.length < 3) return
      loading(true)
      const data = {
        class_level: this.selectedClassLevel,
        content: search,
        lesson_id: this.selectedBranch
      }
      LearningOutcomesService.findByCodeOrContent(data)
        .then(value => {
          this.learningOutComes = value
          loading(false)
        })
        .catch(reason => {
          Messenger.showError(MessengerConstants.errorMessage)
          loading(false)
        })
        .finally(() => loading(false))
    }, 800),
    selectQuestionGraphic (event) {
      URL.revokeObjectURL(this.questionFileURL)
      this.questionFile = event.target.files[0]
      this.questionFileURL = URL.createObjectURL(event.target.files[0])
    },
    clearSelection () {
      this.questionFile = null
      URL.revokeObjectURL(this.questionFileURL)
      this.$validator.errors.remove('questionFile')
      this.$refs.qFile.value = null
    },
    async save () {
      const validationResult = await this.$validator.validateAll()
      if (validationResult) {
        const promptRes = await Messenger.showPrompt('Bakanlığımız Ölçme, Değerlendirme ve Sınav Hizmetleri Genel Müdürlüğü ve ilimiz Ölçme Değerlendirme merkezi tarafından kullanılmak amacıyla hazırladığım soruya/sorulara ait metin, görsel, tablo, grafik, vb. inde herhangi bir telif hakkı ihlali olmadığını, Seçenekler dâhil, sorunun tamamıyla özgün, hiçbir yerde kullanılmamış ve kullanılmayacak olduğunu beyan ve kabul ederim.')
        if (promptRes.isConfirmed) {
          this.isSending = true
          const fd = new FormData()
          fd.append('lesson_id', this.selectedBranch)
          fd.append('learning_outcome_id', this.selectedLearningOutCome)
          fd.append('difficulty', this.selectedDifficulty)
          fd.append('keywords', this.keywords)
          fd.append('correct_answer', this.selectedCorrectAnswer)
          fd.append('is_design_required', this.isDesignRequired)
          fd.append('question_file', this.questionFile, this.questionFile.name)
          try {
            await QuestionService.save(fd, progress => {})
            this.isSending = false
            await Messenger.showInfo('Soru kaydı başarılı')
            await this.$router.push({ name: 'questionTableList' })
          } catch (reason) {
            this.isSending = false
            await Messenger.showError()
          }
        }
      }
    }
  }
}
</script>

<style>

</style>
