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
    <template slot="header">
      <h4>Otomatik Sınav Oluşturma</h4>
    </template>
    <template slot="content">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row justify-content-md-center">
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label>Sınav Adı/Başlığı *</label>
                    <input
                      v-model="title"
                      v-validate="'required|min:5'"
                      name="examTitle"
                      class="form-control"
                      :class="{'is-invalid': errors.has('examTitle')}"
                      type="text"
                      placeholder="Sınav adı/Başlığı"
                    >
                    <span
                      v-if="errors.has('examTitle')"
                      class="error invalid-feedback"
                    >{{ errors.first('examTitle') }}</span>
                  </div>
                  <div class="form-group mb-3">
                    <label>
                      Sınavın Genel Amacı *
                    </label>
                    <v-select
                      v-model="selectedPurpose"
                      v-validate="'required'"
                      :options="purposes"
                      :reduce="b => b.id"
                      label="purpose"
                      :class="{'is-invalid': errors.has('examPurpose')}"
                      name="examPurpose"
                      placeholder="Sınav amacını seçiniz"
                    >
                      <div slot="no-options">
                        Burada bişey bulamadık :-(
                      </div>
                    </v-select>
                    <span
                      v-if="errors.has('examPurpose')"
                      class="error invalid-feedback"
                    >{{ errors.first('examPurpose') }}</span>
                  </div>
                  <div class="form-group mb-3">
                    <label>Planlanan Tarih *</label>
                    <date-picker
                      v-model="plannedDate"
                      v-validate="'required'"
                      name="plannedDate"
                      style="width:100%"
                      :lang="lang"
                      type="datetime"
                      :time-picker-options="{
                        start: '08:30',
                        step: '00:15',
                        end: '17:00',
                      }"
                      input-class="form-control"
                      :popup-style="{ top: '100%', left: 0}"
                      :append-to-body="false"
                      placeholder="Tarih seçiniz"
                      format="DD.MM.YYYY - HH:mm"
                      :class="{'is-invalid': errors.has('plannedDate')}"
                    />
                    <span
                      v-if="errors.has('plannedDate')"
                      class="error invalid-feedback"
                    >{{ errors.first('plannedDate') }}</span>
                  </div>
                  <div class="form-group mb-3">
                    <label>Sınıf Seviyesi Seçimi *</label>
                    <v-select
                      ref="classLevelDD"
                      v-model="selectedClassLevel"
                      v-validate="'required'"
                      :options="classLevels"
                      name="selectedClassLevel"
                      :class="{'is-invalid': errors.has('selectedClassLevel')}"
                      placeholder="Sınıf seviyesini seçiniz"
                      @input="onClassLevelChanged"
                    />
                    <span
                      v-if="errors.has('selectedClassLevel')"
                      class="error invalid-feedback"
                    >{{ errors.first('selectedClassLevel') }}</span>
                  </div>
                  <div
                    v-if="this.selectedClassLevel"
                    class="row mb-3"
                  >
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-md-12">
                          <label>Ders ve soru sayısı seçimi *</label>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 justify-content-center">
                          <div class="form-group">
                            <v-select
                              ref="branchesRef"
                              v-model="selectedBranch"
                              :options="branches"
                              label="name"
                              name="branch"
                              placeholder="Ders seçimi yapınız"
                            >
                              <div slot="no-options">
                                Burada bişey bulamadık :-(
                              </div>
                            </v-select>
                          </div>
                        </div>
                        <div class="col-md-4 justify-content-center">
                          <div class="form-group">
                            <v-select
                              v-model="selectedQuestionCount"
                              :options="questionCounts"
                              name="questionCount"
                              placeholder="Soru sayısı giriniz/seçiniz"
                            >
                              <div slot="no-options">
                                Burada bişey bulamadık :-(
                              </div>
                            </v-select>
                          </div>
                        </div>
                        <div class="col-md-2 justify-content-center">
                          <button
                            type="button"
                            class="btn btn-primary btn-block btn-sm"
                            :class="{'disabled': isSelectedClassAndQuestionCount}"
                            @click="addLesson"
                          >
                            Ekle
                          </button>
                        </div>
                      </div>
                      <div
                        v-if="hasLessons"
                        class="row mb-3"
                      >
                        <div class="col-md-12">
                          <div class="card">
                            <div class="card-body">
                              <ul class="nav flex-column">
                                <li
                                  v-for="lesson in lessons"
                                  :key="lesson.id"
                                  class="nav-item"
                                >
                                  {{ lesson.name }} - <b>{{ lesson.question_count }}</b> adet soru
                                  <button
                                    class="btn btn-danger btn-xs float-right"
                                    @click="removeLesson(lesson.id)"
                                  >
                                    Sil
                                  </button>
                                </li>
                              </ul>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label>Açıklamalar</label>
                    <textarea
                      v-model="description"
                      name="description"
                      class="form-control"
                      rows="2"
                      style="max-width: 100%; min-width: 100%; min-height: 50px"
                      placeholder="Açıklama girebilirsiniz"
                    />
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <button
                        class="btn btn-danger btn-block"
                        @click="cancel"
                      >
                        İptal
                      </button>
                    </div>
                    <div class="col-md-6">
                      <button
                        class="btn btn-primary btn-block"
                        :class="{ disabled: isEnabledCreate }"
                        @click="create"
                      >
                        Oluştur
                      </button>
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
import Page from '../../components/Page'
import vSelect from 'vue-select'
import BranchService from '../../services/BranchService'
import ExamService, { ExamPurposeService } from '../../services/ExamService'
import Messenger from '../../helpers/messenger'
import DatePicker from 'vue2-datepicker'
import tr from 'vue2-datepicker/locale/tr'
import FileSaver from '../../helpers/FileSaver'
import { ResponseCodes } from '../../helpers/constants'

export default {
  name: 'NewAutoExam',
  components: { Page, vSelect, DatePicker },
  data: () => ({
    title: '',
    description: '',
    selectedPurpose: '',
    purposes: [],
    selectedClassLevel: '',
    classLevels: [...Array(9).keys()].map(i => i + 4), // 4-12 arası sınıf array oluşturma
    selectedQuestionCount: 0,
    questionCounts: [...Array(50).keys()].map(i => i + 1), // 1-50 arası soru sayısı array oluşturma
    lessons: [],
    selectedBranch: '',
    branches: [],
    tempBranches: [],
    tempBranchesClassLevels: [],
    plannedDate: '',
    lang: tr
  }),
  computed: {
    hasLessons () {
      return this.lessons !== null && this.lessons.length > 0
    },
    isSelectedClassAndQuestionCount () {
      return !(this.selectedQuestionCount && this.selectedBranch)
    },
    isEnabledCreate () {
      return !(this.lessons &&
          this.lessons.length > 0 &&
          this.title &&
          this.selectedClassLevel
      )
    }
  },
  beforeRouteEnter (to, from, next) {
    Promise.all([
      BranchService.getBranches(),
      ExamPurposeService.getPurposes()])
      .then(([branches, purposes]) => {
        next(vm => {
          vm.branches = branches.sort((a, b) => a.name.localeCompare(b.name))
          vm.purposes = purposes
          vm.tempBranchesClassLevels = vm.branches
        })
      })
      .catch(reason => console.log(reason))
  },
  methods: {
    addLesson () {
      if (this.selectedBranch && this.selectedQuestionCount) {
        this.lessons.push({
          id: this.selectedBranch.id,
          name: this.selectedBranch.name,
          question_count: this.selectedQuestionCount
        })
        // Sınava eklenen dersi ana listeden çıkarıp temp'e atıyoruz silinirse geri alıcaz
        this.tempBranches.push(this.branches.filter((value) => value.id === this.selectedBranch.id)[0])
        this.branches = this.branches.filter((value) => value.id !== this.selectedBranch.id)
        if (this.$refs.branchesRef) {
          this.$refs.branchesRef.clearSelection()
        }
      }
    },
    removeLesson (id) {
      // Listeden ders silinirse geri ana listeye dersi ekleyelim
      this.branches.push(...this.tempBranches.filter((value) => value.id === id))
      // Sınav için seçilen dersler içinden silelim
      this.lessons.splice(this.lessons.findIndex(value => value.id === id), 1)
      this.branches.sort((a, b) => a.name.localeCompare(b.name))
    },
    onClassLevelChanged () {
      this.branches = this.tempBranchesClassLevels
        .filter(value => {
          return value.class_levels.split(',')
            .map(Number)
            .includes(this.selectedClassLevel)
        })
      if (this.$refs.branchesRef) {
        this.$refs.branchesRef.clearSelection()
      }
    },
    cancel () {
      Messenger.showPrompt('Soru olurmayı iptal etmek istiyor musunuz?')
        .then(value => {
          if (value.isConfirmed) {
            this.$router.go(-1)
          }
        })
    },
    async create () {
      let loader = null
      try {
        const valRes = await this.$validator.validateAll()
        if (valRes) {
          const promptRes = await Messenger.showPrompt('Otomatik sınav oluşturmak istediğinizden emin misiniz?')
          if (promptRes.isConfirmed) {
            loader = this.$loading.show()
            const data = {
              title: this.title,
              purpose_id: this.selectedPurpose,
              class_level: this.selectedClassLevel,
              planned_date: this.plannedDate,
              description: this.description,
              lessons: this.lessons.map(l => {
                return {
                  id: l.id, question_count: l.question_count
                }
              })
            }
            const exam = await ExamService.createAutoExam(data)
            if (exam?.code === ResponseCodes.CODE_WARNING) {
              // eslint-disable-next-line no-unused-expressions
              loader?.hide()
              await Messenger.showWarning(exam.message)
              return
            }
            loader.hide()
            await Messenger.showInfo('Sınavınız başarıyla oluşturuldu şimdi dosyalar hazırlanıp indirilecek lütfen sabırlı olun bu işlem biraz uzun sürebilir.')
            loader = this.$loading.show()
            const fileInfo = await ExamService.getExamFile(exam?.id)
            FileSaver.save(fileInfo.file, fileInfo.fileName)
            loader.hide()
            await this.$router.push({ name: 'examList' })
          }
        }
      } catch (e) {
        console.log(e)
        // eslint-disable-next-line no-unused-expressions
        loader?.hide()
      }
    }
  }
}
</script>

<style scoped>

</style>
