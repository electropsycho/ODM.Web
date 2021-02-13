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
    <div class="col-md-10 col-sm-12">
      <slot />
    </div>
    <div class="col-md-2 col-sm-12">
      <h4
        v-if="hasDeleteRequest"
        class=" center-block float-right"
      >
        <span class="badge badge-danger">Silme Talep Edilmiş</span>
      </h4>
      <button
        v-if="isOwner"
        class="btn btn-danger btn-block float-right"
        style="margin-right: 10px"
        @click="deleteRequest"
      >
        Silme Talep Et
      </button>
    </div>
  </div>
</template>

<script>
import Messenger from '../helpers/messenger'
import QuestionService from '../services/QuestionService'
import AuthService from '../services/AuthService'

export default {
  name: 'HeaderDeleteRequest',
  props: {
    question: {
      type: Object,
      default: Object
    }
  },
  computed: {
    isOwner () {
      if (this.question) {
        return ((this.question.creator_id === AuthService.getUserId() ||
          this.$isInRole('admin')) &&
          !this.question.has_delete_request)
      }
      return false
    },
    hasDeleteRequest () {
      if (this.question) {
        return this.question.has_delete_request
      }
      return false
    }
  },
  methods: {
    deleteRequest () {
      Messenger.showInput('Soruyu neden silmek istiyorsunuz? Kısaca yazınız')
        .then(result => {
          if (result.isConfirmed) {
            QuestionService.sendDeleteRequest(this.question.id, { reason: result.value })
              .then(resp => {
                Messenger.showInfo(resp.message)
                  .then(() => this.$router.go(-1))
              })
              .catch(err => Messenger.showError(err.message))
          }
        })
    }
  }
}
</script>

<style scoped>

</style>
