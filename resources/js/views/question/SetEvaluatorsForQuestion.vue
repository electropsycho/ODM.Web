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
      <header-delete-request
        :question="question"
      >
        <h4>Soru Değerlendirme</h4>
      </header-delete-request>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body">
                  <div class="col-md-12">
                    <question
                      :question="question"
                      :question-file="questionFile"
                    />
                  </div>
                </div>
              </div>
              <div
                v-if="checkForEvaluationReq"
                class="card"
              >
                <div class="card-header with-border">
                  <h4>
                    Değerlendirici Ataması
                    <span v-if="selectedEvaluators.length>0">- Değerlendirici sayısı:
                      <b>{{ selectedEvaluators.length }}</b>
                    </span>
                  </h4>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="row justify-content-md-center">
                        <div class="col-md-6 col-xs-12">
                          <div
                            class="form-group has-feedback"
                          >
                            <label>Manuel hesaplama için gereken asgari değerlendirme sayısı</label>
                            <v-select
                              ref="evaluatorsRef"
                              v-model="minRequiredElection"
                              v-validate="'required'"
                              :options="electionRanges"
                              append-to-body
                              :calculate-position="withPopper"
                              name="mre"
                              :class="{'is-invalid': errors.has('mre')}"
                              placeholder="Seçim yapınız"
                            >
                              <div slot="no-options">
                                Maalesef bişey bulamadık
                              </div>
                            </v-select>
                            <span
                              v-if="errors.has('mre')"
                              class="error invalid-feedback"
                            >{{ errors.first('mre') }}</span>
                          </div>
                        </div>
                      </div>
                      <div class="row justify-content-md-center">
                        <div class="col-md-6 col-xs-12">
                          <div
                            class="form-group has-feedback"
                          >
                            <label>Değerlendirici Seçiniz</label>
                            <v-select
                              ref="evaluatorsRef"
                              v-model="selectedEvaluators"
                              v-validate="'required'"
                              multiple
                              append-to-body
                              :calculate-position="withPopper"
                              name="evaluators"
                              :class="{'is-invalid': errors.has('evaluators')}"
                              label="full_name"
                              :options="evaluators"
                              placeholder="Değerlendirici/leri seçebilirsiniz"
                            >
                              <div slot="no-options">
                                Maalesef hiç değerlendiricimiz yok
                              </div>
                            </v-select>
                            <span
                              v-if="errors.has('evaluators')"
                              class="error invalid-feedback"
                            >{{ errors.first('evaluators') }}</span>
                          </div>
                          <div class="col-md-12 col-xs-12">
                            <button
                              class="btn btn-primary btn-block"
                              style="margin-bottom: 5em"
                              :class="{'disabled': errors.any()}"
                              @click="setElectors"
                            >
                              Değerlendiricileri Kaydet
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="hasSavedEvaluators"
                class="card"
              >
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="dataTables_wrapper dt-bootstrap4">
                        <table
                          class="table"
                          style="width: 100%"
                        >
                          <thead>
                            <tr>
                              <th>Sıra</th>
                              <th>Ad Soyad</th>
                              <th>Kod</th>
                              <th>Yorum</th>
                              <th>Puan</th>
                              <th>Tarih</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="(elector, index) in savedEvaluators"
                              :key="index"
                            >
                              <td>{{ index + 1 }}</td>
                              <td>{{ elector.full_name }}</td>
                              <td>{{ elector.code }}</td>
                              <td>{{ elector.comment }}</td>
                              <td>{{ elector.point }}</td>
                              <td>{{ elector.updated_at | trDate }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <timeline :question-id="Number($route.params.questionId)" />
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import vSelect from 'vue-select'
import QuestionService from '../../services/QuestionService'
import Messenger from '../../helpers/messenger'
import QuestionEvaluationService from '../../services/QuestionEvaluationService'
import Question from '../../components/questions/Question'
import { QuestionStatuses } from '../../helpers/QuestionStatuses'
import UserService from '../../services/UserService'
import HeaderDeleteRequest from '../../components/HeaderDeleteRequest'
import Timeline from '../../components/questions/Timeline'
import Page from '../../components/Page'
import OverlayHelper from '../../helpers/OverlayHelper'
import { calculateWithPopper, range } from '../../helpers/utils'
import { SettingService } from '../../services/SettingService'

export default {
  name: 'SetEvaluatorsForQuestion',
  components: { Page, Timeline, HeaderDeleteRequest, vSelect, Question },
  data: () => ({
    question: null,
    questionFile: null,
    evaluators: [],
    selectedEvaluator: '',
    selectedEvaluators: [],
    savedEvaluators: [],
    minRequiredElection: '',
    electionRanges: [],
    minElectorCount: '',
    maxElectorCount: ''
  }),
  async beforeRouteEnter (to, from, next) {
    const questionId = to.params.questionId
    try {
      const [question, savedEvaluations, setting] = await Promise.all([
        QuestionService.findById(questionId),
        QuestionEvaluationService.findByQuestionId(questionId),
        SettingService.getSettings()
      ])
      next(vm => {
        vm.question = question
        vm.savedEvaluators = savedEvaluations
        vm.electionRanges = range(setting.min_elector_count, setting.max_elector_count)
        vm.minRequiredElection = setting.min_elector_count
        vm.minElectorCount = setting.min_elector_count
        vm.maxElectorCount = setting.max_elector_count
      })
    } catch (reason) {}
  },
  computed: {
    checkForEvaluationReq () {
      if (this.question) {
        const res = (this.$isInRole('admin') && this.question.status === QuestionStatuses.WAITING_FOR_ACTION) ||
            (this.$isInRole('admin') && this.question.status === QuestionStatuses.REVISION_COMPLETED) // &&
        // (this.savedEvaluators !== null &&
        // this.savedEvaluators.filter(se => se.point !== null || se.point > 0).length > 0))
        return res
      }
      return false
    },
    hasSavedEvaluators () {
      return this.savedEvaluators && this.savedEvaluators.length > 0
    }
  },
  created () {
    setTimeout(() => this.getFile(), 1500)
    setTimeout(() => {
      this.findElectorsByBranchId()
    }, 500)
  },
  methods: {
    async getFile () {
      const loader = this.$loading.show()
      try {
        this.questionFile = await QuestionService.getFile(this.$route.params.questionId)
        loader.hide()
      } catch (reason) {
        loader.hide()
        await Messenger.showError(reason.message)
      }
    },
    async getQuestion () {
      const questionId = this.$route.params.questionId
      try {
        this.question = await QuestionService.findById(questionId)
      } catch (reason) {
        await Messenger.showError(reason)
      }
    },
    async findElectorsByBranchId () {
      try {
        const electors = await UserService.findElectorsByBranchId(this.question.branch_id)
        this.evaluators = electors.filter(elector => elector.id !== this.question.creator_id)
      } catch (reason) {
        await Messenger.showError(reason?.message)
      }
    },
    calculateRule () {
      const res = this.selectedEvaluators
        .filter(elector => elector.branch_id === this.question.branch_id)
        .length >= Math.round(this.minRequiredElection / 2)
      return res
    },
    async setElectors () {
      const validationResult = await this.$validator.validateAll()
      if (validationResult) {
        let loader = {}
        if (this.selectedEvaluators.length >= this.minRequiredElection && this.calculateRule()) {
          const electors = this.selectedEvaluators.map(value => value.full_name).join(', ')
          const promptRes = await Messenger.showPrompt(`Bu soruya değerlendirici olarak <b>${electors}</b> adlı kişileri seçtiniz. Onaylıyor musunuz?`)
          try {
            if (promptRes.isConfirmed) {
              loader = this.$loading.show()
              OverlayHelper.setLoader(loader)
              const data = { electors: this.selectedEvaluators, minRequiredElection: this.minRequiredElection }
              const response = await QuestionEvaluationService.saveElectors(this.question.id, data)
              await loader.hide()
              await Messenger.showSuccess(response.message)
              await this.refreshQuestion()
            }
          } catch (reason) {
            // eslint-disable-next-line no-unused-expressions
            await loader?.hide()
            this.$refs.evaluatorsRef.clearSelection()
            if (reason?.message) {
              await Messenger.showError(reason.message)
            }
          }
        } else {
          await Messenger.showWarning(`Aynı alandan olmak üzere lütfen en az ${Math.round(this.minRequiredElection / 2)} tane ve toplamda ${this.minRequiredElection} değerlendirici olacak şekilde seçim yapınız!`)
        }
      }
    },
    async refreshQuestion () {
      try {
        const [question, savedEvaluators] = await Promise.all([
          QuestionService.findById(this.question.id),
          QuestionEvaluationService.findByQuestionId(this.question.id)
        ])
        this.question = question
        this.savedEvaluators = savedEvaluators
      } catch (reason) {
      }
    },
    withPopper: calculateWithPopper
  }
}

</script>

<style>
.v-select.drop-up.vs--open .vs__dropdown-toggle {
  border-radius: 0 0 4px 4px;
  border-top-color: transparent;
  border-bottom-color: rgba(60, 60, 60, 0.26);
}

[data-popper-placement='top'] {
  border-radius: 4px 4px 0 0;
  border-top-style: solid;
  border-bottom-style: none;
  box-shadow: 0 -3px 6px rgba(0, 0, 0, 0.15)
}

</style>
