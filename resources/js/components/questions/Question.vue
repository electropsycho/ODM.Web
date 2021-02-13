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
    <div class="col-md 12">
      <div class="row">
        <div class="col-md-12">
          <object
            type="application/pdf"
            :data="questionFile"
            height="600pt"
            style="width: 100%"
          />
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <h6 v-if="question != null">
            <span style="font-weight: bold">Madde Kökü/Anahtar Kelimeler: </span>{{ question.keywords == null ?
              'Girilmemiş' : question.keywords }}
          </h6>
          <h6 v-if="question !== null && $isInRole('admin')">
            <span style="font-weight: bold">Soru yazarı: </span>{{ question.creator }}
          </h6>
          <h6 v-if="question != null">
            <span style="font-weight: bold">Kazanım: </span>{{ question.learning_outcome }}
          </h6>
          <h6 v-if="question != null">
            <span style="font-weight: bold">Sınıf seviyesi: </span>{{ question.class_level }} .Sınıf
          </h6>
          <h6 v-if="question != null">
            <span style="font-weight: bold">Doğru Cevap: </span><span class="text-red text-bold">{{ question.correct_answer }}</span> Seçeneği
          </h6>
          <h6 v-if="question != null">
            <span style="font-weight: bold">Zorluk Seviyesi: </span>{{ getDifficulty() }}
          </h6>
          <h6 v-if="question != null">
            <span style="font-weight: bold">Tasarım ihtiyacı: </span>{{ question.is_design_required ? 'Var' : 'Yok' }}
          </h6>
          <h6 v-if="question != null">
            <span style="font-weight: bold">Durumu: </span>
            <span
              class="badge"
              :class="getStatusClass"
            >{{ question.status_title }} </span>
          </h6>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import { QuestionStatuses } from '../../helpers/QuestionStatuses'

export default {
  name: 'Question',
  props: ['question', 'questionFile'],
  data () {
    return {
      difficultyLevels: [
        { degree: 1, content: 'Çok Kolay' },
        { degree: 2, content: 'Kolay' },
        { degree: 3, content: 'Normal' },
        { degree: 4, content: 'Zor' },
        { degree: 5, content: 'Çok Zor' }
      ]
    }
  },
  computed: {
    getStatusClass () {
      return {
        'bg-danger': this.question.status === QuestionStatuses.NOT_MUST_ASKED,
        'bg-info': this.question.status === QuestionStatuses.IN_ELECTION,
        'bg-primary': this.question.status === QuestionStatuses.REVISION_COMPLETED,
        'bg-warning': this.question.status === QuestionStatuses.NEED_REVISION,
        'bg-success': this.question.status === QuestionStatuses.APPROVED,
        'bg-gradient-secondary': this.question.status === QuestionStatuses.WAITING_FOR_ACTION
      }
    }
  },
  methods: {
    getDifficulty () {
      return this.difficultyLevels.filter(diff => diff.degree === this.question.difficulty)[0].content
    }
  }
}
</script>

<style scoped>

</style>
