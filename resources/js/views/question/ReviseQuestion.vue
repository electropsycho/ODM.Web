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
        <h4>Soru Revizyonu</h4>
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
                      :question-file="questionFileURL"
                    />
                    <div class="mb-2">
                      <hr>
                    </div>
                    <div
                      v-if="checkRevisionRequest"
                      class="row"
                    >
                      <div class="col-md-12">
                        <div class="row justify-content-md-center">
                          <div class="col-md-8">
                            <div
                              class="form-group"
                            >
                              <label>Gözden geçirme metni</label>
                              <textarea
                                v-model="comment"
                                v-validate="'required'"
                                name="comment"
                                class="form-control"
                                :class="{'is-invalid': errors.has('comment')}"
                                style="max-width: 100%; min-width: 100%; min-height: 60px"
                                placeholder="Gözden geçirmenizi kısaca yazınız"
                              />
                              <span
                                v-if="errors.has('comment')"
                                class="error invalid-feedback"
                              >{{ errors.first('comment') }}</span>
                            </div>
                          </div>
                        </div>
                        <div
                          v-if="errors.has('questionFile')"
                          class="row justify-content-md-center mb-3"
                        >
                          <div class="col-md-8">
                            <div class="row justify-content-md-center">
                              <span class="badge badge-danger">{{ errors.first('questionFile') }}</span>
                            </div>
                          </div>
                          <br>
                        </div>
                        <div class="row justify-content-md-center">
                          <div class="col-md-8">
                            <div class="row justify-content-md-center">
                              <div class="col-md-4">
                                <label class="btn btn-success btn-block">
                                  <input
                                    ref="qFile"
                                    v-validate="'required|size:1024'"
                                    name="questionFile"
                                    type="file"
                                    style="display: none !important;"
                                    accept="application/pdf"
                                    @change="selectFile($event)"
                                  >Dosya seçiniz
                                </label>
                              </div>
                              <div class="col-md-4 mb-2">
                                <button
                                  class="btn btn-primary btn-block"
                                  @click="save"
                                >
                                  Kaydet
                                </button>
                              </div>
                              <div class="col-md-4">
                                <button
                                  class="btn btn-danger btn-block"
                                  @click="cancel"
                                >
                                  İptal Et
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <timeline :question-id="questionId" />
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import QuestionService from '../../services/QuestionService'
import Messenger from '../../helpers/messenger'
import { MessengerConstants } from '../../helpers/constants'
import RevisionService from '../../services/CommentService'
import { QuestionStatuses } from '../../helpers/QuestionStatuses'
import Question from '../../components/questions/Question'
import HeaderDeleteRequest from '../../components/HeaderDeleteRequest'
import Timeline from '../../components/questions/Timeline'
import Page from '../../components/Page'

export default {
  name: 'ShowQuestion',
  components: { Page, Question, HeaderDeleteRequest, Timeline },
  data: () => ({
    question: null,
    questionFile: null,
    oldQuestionFile: null,
    questionFileURL: null,
    comment: null,
    changeCount: 0,
    questionId: null
  }),
  computed: {
    checkRevisionRequest () {
      // Sorunun revizyon isteği var mı yok mu kontrol ediliyor
      if (this.question) {
        return this.question.status === QuestionStatuses.NEED_REVISION
      }
      return false
    },
    isFileSelected () {
      return this.changeCount !== 0
    }
  },
  watch: {
    questionFile: function (val) {
      this.changeCount++
      this.questionFileURL = URL.createObjectURL(val)
    }
  },
  created () {
    setTimeout(() => { this.getFile() }, 1000)
  },
  beforeRouteEnter (to, from, next) {
    const questionId = to.params.questionId
    QuestionService.findById(questionId)
      .then((question) => {
        next(vm => {
          vm.questionId = questionId
          vm.question = question
        })
      })
  },
  methods: {
    getFile () {
      const loader = this.$loading.show()
      QuestionService.getFile(this.$route.params.questionId)
        .then(value => {
          this.questionFileURL = value
        })
        .catch(reason => {
          Messenger.showError(reason.message)
        })
        .finally(() => loader.hide())
    },
    refreshQuestion () {
      this.questionId = this.$route.params.questionId
      QuestionService.findById(this.question.id)
        .then((question) => {
          this.question = question
        })
        .catch(reason => Messenger.showError(reason.message))
    },
    save () {
      this.$validator.validateAll()
        .then(value => {
          if (value) {
            const msg = 'Gözden geçrime metniniz ve yeni soru dosyanız kayıt edilecek.\n' +
                'Eski soru dosyanıza ulaşamayacaksınız\n' +
                'Kayıt işlemini onaylıyor musunuz?'
            Messenger.showPrompt(msg)
              .then(promptRes => {
                if (promptRes.isConfirmed) {
                  const fd = new FormData()
                  fd.append('comment', this.comment)
                  fd.append('question_file', this.questionFile, this.questionFile.name)
                  RevisionService.save(this.$route.params.questionId, fd)
                    .then(res => {
                      Messenger.showSuccess('Gözden geçirme kaydı başarılı')
                      this.getRevisions()
                      this.refreshQuestion()
                    })
                    .catch(e => {
                      Messenger.showError(MessengerConstants.errorMessage)
                    })
                }
              })
          }
        })
    },
    selectFile ($event) {
      if (this.changeCount === 0) {
        this.oldQuestionFile = this.questionFileURL
      }
      URL.revokeObjectURL(this.questionFile)
      this.questionFile = $event.target.files[0]
    },
    cancel () {
      this.comment = null
      this.$validator.reset()
      if (this.changeCount !== 0) {
        URL.revokeObjectURL(this.questionFile)
        this.questionFileURL = this.oldQuestionFile
      }
    },
    getRevisions () {
      RevisionService.getRevisions(this.$route.params.questionId)
        .then((revisions) => {
          this.revisions = revisions
        })
    }
  }
}
</script>

<style scoped>

</style>
