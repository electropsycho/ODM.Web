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
        <h3>Soru İnceleme</h3>
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
            </div>
          </div>
          <evaluation
            v-if="canShowEvalMenus"
            :question="question"
          />
          <timeline :question-id="Number(questionId)" />
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import QuestionService from '../../services/QuestionService'
import Messenger from '../../helpers/messenger'
import Question from '../../components/questions/Question'
import Timeline from '../../components/questions/Timeline'
import HeaderDeleteRequest from '../../components/HeaderDeleteRequest'
import Page from '../../components/Page'
import Evaluation from '../../components/questions/Evaluation'
import { QuestionStatuses } from '../../helpers/QuestionStatuses'

export default {
  name: 'ShowQuestion',
  components: { Evaluation, Page, Timeline, Question, HeaderDeleteRequest },
  data: () => ({
    question: null,
    questionFile: null,
    oldQuestionFile: null,
    revisions: [],
    evaluations: [],
    questionId: null
  }),
  computed: {
    canShowEvalMenus () {
      return this.$isInRole('admin') &&
        this.question?.status === QuestionStatuses.IN_ELECTION
    }
  },
  created () {
    setTimeout(() => { this.getFile() }, 1500)
  },
  beforeRouteEnter (to, from, next) {
    const questionId = to.params.questionId
    QuestionService.findById(questionId)
      .then((question) => {
        next(vm => {
          vm.question = question
          vm.questionId = questionId
        })
      })
  },
  methods: {
    getFile () {
      const loader = this.$loading.show()
      QuestionService.getFile(this.$route.params.questionId)
        .then(value => {
          this.questionFile = value
        })
        .catch(reason => {
          Messenger.showError(reason.message)
        })
        .finally(() => loader.hide())
    }
  }
}
</script>

<style scoped>

</style>
