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
  <div
    v-if="isShow"
    class="row"
  >
    <spinner
      v-if="isLoading"
      spin-style="double_bounce"
    />
    <div
      class="col-md-12"
    >
      <div class="card">
        <div class="card-header">
          <h5>Olaylar</h5>
        </div>
        <div class="card-body">
          <div class="timeline">
            <div
              v-for="event in questionEvents"
              :key="event.id"
            >
              <i
                v-if="event.elector_id"
                class="fa fa-commenting-o bg-blue"
              />
              <i
                v-else
                class="fa fa-recycle bg-warning"
              />

              <div
                v-if="event.elector_id"
                class="timeline-item elevation-1"
              >
                <span class="time"><i class="fa fa-clock-o" /> {{ formatDate(event.date) }}</span>

                <h3 class="timeline-header">
                  <a
                    href="#"
                    @click.prevent
                  ><b>{{ event.code }}</b> grubundan değerlendirme</a>
                </h3>

                <div class="timeline-body">
                  {{ event.comment }}
                </div>
                <div class="timeline-footer">
                  Değerlendirme Tarihi <span class="label label-primary">{{ event.date | trDate }}</span>
                </div>
              </div>
              <div
                v-else
                class="timeline-item elevation-1"
              >
                <span class="time"><i class="fa fa-clock-o" /> {{ formatDate(event.date) }}</span>

                <h3 class="timeline-header">
                  <a
                    href="#"
                    @click.prevent
                  >Revizyon</a>
                </h3>

                <div class="timeline-body">
                  {{ event.comment }}
                </div>
                <div class="timeline-footer">
                  Revizyon Tarihi <span class="label label-warning">{{ event.date | trDate }}</span>
                </div>
              </div>
            </div>
            <div v-if="questionEvents.length > 0">
              <i class="fa fa-clock-o bg-gray" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dayjs from '../../helpers/dayjs'
import QuestionEvaluationService from '../../services/QuestionEvaluationService'
import Messenger from '../../helpers/messenger'
import RevisionService from '../../services/CommentService'
import Spinner from '../Spinner'

export default {
  name: 'Timeline',
  components: { Spinner },
  props: {
    questionId: {
      type: Number,
      required: true,
      default: 0
    }
  },
  data: () => ({
    questionEvents: [],
    isLoading: false
  }),
  computed: {
    isShow () {
      return this.questionId > 0 && this.questionEvents.length > 0
    }
  },
  watch: {
    questionId () {
      this.getAllQuestionEvents()
    }
  },
  methods: {
    getAllQuestionEvents () {
      this.isLoading = true
      Promise.all([
        QuestionEvaluationService.findByQuestionId(this.questionId),
        RevisionService.getRevisions(this.questionId)
      ])
        .then(([evaluationList, revisions]) => {
          // const eventList = []
          this.questionEvents = revisions.concat(evaluationList.filter(q => q.point !== null && q.point > 0))
          this.sortByDate()
          // this.filteredEvalList = evaluationList.filter(q => q.point !== null && q.point > 0)
          // this.revisions = revisions
          this.isLoading = false
        })
        .catch((e) => {
          Messenger.showError('Revizyonlar/Değerlendirme listesi yüklenemedi!')
          this.isLoading = false
          console.log(e)
        })
    },
    sortByDate () {
      this.questionEvents = this.questionEvents
        .sort(function (a, b) {
          return new Date(b.date) - new Date(a.date)
        })
    },
    formatDate (date) {
      return dayjs(date).fromNow()
    }
  }
}
</script>

<style scoped>

</style>
