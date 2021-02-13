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
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">
          <div class="row">
            <div class="col-md-10 col-sm-12">
              Değerlendirici Düzenleme ve Elle Puan Hesaplama
            </div>
            <div class="col-md-2 col-sm-12">
              <button
                class="btn btn-danger btn-block float-right"
                @click="deleteAllEvaluations"
              >
                Tüm grubu sil
              </button>
            </div>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
              <h6 v-if="question">
                Manuel hesaplama için gereken puanlama sayısı en az <span class="badge bg-gradient-warning"><b style="font-size:1.3em">{{ question.min_required_election }}</b></span> olmalıdır
              </h6>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-body table-responsive p-0">
                  <table class="table table-hover text-nowrap table-sm">
                    <thead>
                      <tr>
                        <th style="width: 10px">
                          #
                        </th>
                        <th>Değerlendirici</th>
                        <th>Kod</th>
                        <th>Tarih</th>
                        <th style="width: 40px">
                          Puan
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(evaluation, index) in questionEvaluations"
                        :key="evaluation.elector_id"
                      >
                        <td>{{ index + 1 }}.</td>
                        <td>{{ evaluation.full_name }}</td>
                        <td>{{ evaluation.code }}</td>
                        <td>{{ evaluation.date | trDate }}</td>
                        <td>
                          <div class="row justify-content-md-center">
                            <a
                              v-if="!evaluation.point"
                              class="btn btn-xs btn-danger"
                              :class="{disabled: !evaluation.canRemove}"
                              @click="removeEvalRequest(evaluation.id)"
                            >
                              <strong style="color: white">X</strong>
                            </a>
                            <a
                              v-else
                              class="btn btn-xs btn-warning"
                            ><strong>{{ evaluation.point }}</strong></a>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div
            v-if="hasRequiredMinPointAndElector"
            class="row justify-content-md-center"
          >
            <div class="col-md-6">
              <button
                class="btn btn-primary btn-block"
                @click="manuallyCalculate"
              >
                Manuel Hesapla
              </button>
            </div>
          </div>
          <hr>
          <div
            class="row justify-content-md-center mt-3"
          >
            <div class="col-md-8">
              <v-select
                ref="selectedElectorRef"
                v-model="selectedEvaluators"
                multiple
                :class="{'is-invalid': selectionMessage}"
                :options="allElectors"
                label="full_name"
                append-to-body
                :calculate-position="withPopper"
                placeholder="Değerlendirici seçiniz"
                @input="onSelectionChanged"
              >
                <div slot="no-options">
                  Burada birşey bulamadık
                </div>
              </v-select>
              <span
                v-if="selectionMessage"
                class="error invalid-feedback"
              >{{ selectionMessage }}</span>
            </div>
          </div>
          <div class="row justify-content-md-center mt-3">
            <div class="col-md-6">
              <button
                :class="{disabled : selectedEvaluators.length === 0}"
                class="btn btn-success btn-block"
                @click="addElectors"
              >
                Kaydet
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from 'vue-select'
import UserService from '../../services/UserService'
import Messenger from '../../helpers/messenger'
import QuestionEvaluationService from '../../services/QuestionEvaluationService'
import { ResponseCodes } from '../../helpers/constants'
import { SettingService } from '../../services/SettingService'
import { calculateWithPopper } from '../../helpers/utils'

export default {
  name: 'EvaluationCalculation',
  components: { vSelect },
  props: {
    question: {
      type: Object,
      default: Object,
      required: true
    }
  },
  data: () => ({
    selectedEvaluators: [],
    allElectors: [],
    questionEvaluations: [],
    code: '',
    constraints: '',
    selectionMessage: '',
    placement: 'top',
    isEnableManualCalculation: true,
    sameBranchEvals: []
  }),
  computed: {
    hasPoint (currentElection) {
      return !!currentElection.point
    },
    hasMaxNumberOfElectors () {
      const res = this.questionEvaluations.length + this.selectedEvaluators.length < this.constraints.max_elector_count
      // eslint-disable-next-line no-unused-expressions
      return res
    },
    hasRequiredMinPointAndElector () {
      // Minimum alandan geçerli puana ve toplam gerekli puana bakılıyor
      const rule1 = this.sameBranchEvals.filter(e => e.point).length >= Math.round(this.question.min_required_election / 2)
      const rule2 = this.questionEvaluations.filter(e => e.point).length >= this.question.min_required_election
      return rule1 && rule2
    }
  },
  watch: {
    questionEvaluations: {
      deep: true,
      handler () {
        this.calculate()
      }
    }
  },
  async created () {
    await this.loadData()
    this.constraints = await SettingService.getQuestionConstraints()
  },
  methods: {
    hasMinSameBranchElector () {
      this.sameBranchEvals = this.questionEvaluations.filter(elector => elector.branch_id === this.question.branch_id)
      const rule1 = this.sameBranchEvals.length <= Math.round(this.question.min_required_election / 2)
      return rule1
    },
    hasRequiredMinEvals () {
      const rule2 = this.questionEvaluations.length <= this.question.min_required_election
      return rule2
    },
    calculate () {
      if (this.hasMinSameBranchElector()) {
        this.questionEvaluations.forEach((q) => {
          if (this.sameBranchEvals.find(a => a.id === q.id)) { q.canRemove = false }
        })
      }
      if (this.hasRequiredMinEvals()) {
        this.questionEvaluations.forEach((q) => { q.canRemove = false })
      }
    },
    async removeEvalRequest (id) {
      const result = await Messenger.showPrompt('Bu değerlendiriciye atanmış isteği silmek istiyor munuz?')
      if (result.isConfirmed) {
        try {
          const response = await QuestionEvaluationService.deleteById(id)
          if (response.code === ResponseCodes.CODE_SUCCESS) {
            await Messenger.showSuccess('Seçili istek başarıyla silindi.')
            this.allElectors.push(this.questionEvaluations.find(e => e.id === id))
            this.questionEvaluations.splice(this.questionEvaluations.findIndex(e => e.id === id), 1)
            if (this.questionEvaluations.length <= 0) {
              await Messenger.showWarning('Mevcut gruptan tüm değerlendirciler silinmiş yeni değerlendirme grubu ataması yapmanız gerekiyor! Sizi bir önceki menüye yönlendiriyoruz')
              await this.$router.push({ name: 'questionTableList' })
            }
          }
        } catch (err) {
          await Messenger.showError(err?.message)
        }
      }
    },
    async addElectors () {
      if (!this.selectedEvaluators && this.selectedEvaluators.length === 0) return
      const electors = this.selectedEvaluators.map(value => value.full_name).join(', ')
      const promptRes = await Messenger.showPrompt(`Bu soruya <b>${electors}</b> ad ve branşlı değerlendirici(yi/leri) eklemek isdeğinizden emin misiniz?`)
      if (promptRes.isConfirmed) {
        try {
          const response = await QuestionEvaluationService
            .addElectors(this.question.id, this.code, this.selectedEvaluators.map(e => e.id))
          switch (response.code) {
            case ResponseCodes.CODE_SUCCESS: {
              await Messenger.showSuccess(response.message)
              break
            }
            case ResponseCodes.CODE_ERROR: {
              await Messenger.showError(response.message)
              break
            }
          }
        } catch (error) {
          await Messenger.showError(error?.message)
        } finally {
          await this.loadData()
          // eslint-disable-next-line no-unused-expressions
          this.$refs?.selectedElectorRef?.clearSelection()
        }
      }
    },
    async manuallyCalculate () {
      const promptRes = await Messenger.showPrompt('Bu soruya ait değerlendirme puanını elle hesaplamak istediğinizden emin misiniz?')
      if (promptRes.isConfirmed) {
        try {
          const result = await QuestionEvaluationService.manuallyCalculate(this.question.id, this.questionEvaluations[0]?.code)
          switch (result.code) {
            case ResponseCodes.CODE_SUCCESS: {
              await Messenger.showSuccess(`${result?.message}.Şimdi sizi sorular sayfasına yönlendiriyoruz.`)
              await this.$router.push({ name: 'questionTableList' })
              break
            }
            case ResponseCodes.CODE_WARNING: {
              await Messenger.showWarning(result.message)
              break
            }
          }
        } catch (e) {
          await Messenger.showError('Hesaplama sırasında bir hata meydana geldi!')
        }
      }
    },
    async loadData () {
      try {
        const [questionEvaluations, electors] = await Promise.all([
          QuestionEvaluationService.findByQuestionId(this.question.id, 1),
          UserService.findElectorsByBranchId(this.question.branch_id)
        ])
        this.questionEvaluations = questionEvaluations.map(question => ({ ...question, canRemove: true }))
        this.allElectors = electors
          .filter(elector => !this.questionEvaluations.find(e => e.elector_id === elector.id))
          .filter(elector => elector.id !== this.question.creator_id)
        this.code = questionEvaluations[0]?.code
      } catch (reason) {
        await Messenger.showError('Üzgünüz bu soruyla ilgili gerekli yüklememleri yapamadık!')
      }
    },
    onSelectionChanged (value) {
      if (this.questionEvaluations.length + this.selectedEvaluators.length > this.constraints.max_elector_count) {
        this.selectionMessage = `Seçilebilecek azami değerlendirici/puanlayıcı sayısı ${this.constraints.max_elector_count} olmaldır`
        this.selectedEvaluators.splice(this.selectedEvaluators.findIndex(e => e.id === value.id), 1)

        setTimeout(() => { this.selectionMessage = '' }, 2500)
      }
    },
    async deleteAllEvaluations () {
      const promptRes = await Messenger.showPrompt('Bu soruya atanmış tüm değerlendiriciler puan bile vermiş olasalar silincektir ve bu soru tekrar <b>İşleme Alınmamış</b> olarak işraretlenecektir. Kabul ediyor musunuz?')
      if (promptRes.isConfirmed) {
        try {
          const response = await QuestionEvaluationService.deleteByCode(this.question.id, this.code)
          await Messenger.showSuccess(response.message)
          await Messenger.showWarning('Mevcut gruptan tüm değerlendirciler silinmiş yeni değerlendirme grubu ataması yapmanız gerekiyor! Sizi bir önceki menüye yönlendiriyoruz')
          await this.$router.push({ name: 'questionTableList' })
        } catch (error) {
          await Messenger.showError(error.message)
        }
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
