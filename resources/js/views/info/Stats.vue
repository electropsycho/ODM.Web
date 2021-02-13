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
    <template v-slot:content>
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h4 v-if="$isInRole('admin')">
                Genel Soru Sayıları
              </h4>
              <h4 v-else>
                Branş/Alan Geneli Soru Sayıları
              </h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-gray">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">İşleme Alınmamış Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.waiting_for_action }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-aqua">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Değerlendirmedeki Soru sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.in_election }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-yellow">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Revizyon Alması Gereken Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.need_revision }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-blue">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Revizyonu Tamamlanmış Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.revision_completed }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-red">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Sorulamayacak Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.not_must_asked }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-green">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Onaylanmış Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.approved }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 text-center">
                  <div class="info-box bg-navy">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Toplam Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.total }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h4>
                Bana Ait Soru Sayıları
              </h4>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-gray">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">İşleme Alınmamış Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.own_waiting_for_action }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-aqua">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Değerlendirmedeki Soru sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.own_in_election }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-yellow">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Revizyon Alması Gereken Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.own_need_revision }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-blue">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Revizyonu Tamamlanmış Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.own_revision_completed }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-red">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Sorulamayacak Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.own_not_must_asked }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box bg-green">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Onaylanmış Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.own_approved }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 text-center">
                  <div class="info-box bg-navy">
                    <div class="info-box-content">
                      <span class="info-box-text text-center">Toplam Soru Sayısı</span>
                      <span class="info-box-number text-center">{{ questionCounts.own_total }}</span>
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

import StatService from '../../services/StatService'
import Messenger from '../../helpers/messenger'
import { MessengerConstants } from '../../helpers/constants'
import Page from '../../components/Page'

export default {
  name: 'Stats',
  components: { Page },
  data () {
    return {
      questionCounts: {},
      total: 0,
      nonValidCount: 0,
      validCount: 0,
      classes: [],
      isAnyQuestionLO: false,
      learningOutComes: []
    }
  },
  beforeRouteEnter (to, from, next) {
    Promise.all([StatService.getClasses(), StatService.getQuestionCounts()])
      .then(([classes, questionCounts]) => {
        next(vm => {
          vm.questionCounts = questionCounts
          // vm.validCount = questionCounts.valid_count
          // vm.nonValidCount = questionCounts.non_valid_count
          // vm.total = questionCounts.total_count
          vm.classes = classes
        })
      })
      .catch(reason => {})

    // StatService.getQuestionCounts()
    //            .then((counts) => {
    //              next(vm => {
    //                vm.validCount = counts.valid_count
    //                vm.nonValidCount = counts.non_valid_count
    //                vm.total = counts.total_count
    //              })
    //            })
  },
  methods: {
    getLearningOutComesByClass (cls) {
      StatService.getLearningOutcomes(cls, this.isAnyQuestionLO ? 0 : 1)
        .then(data => { this.learningOutComes = data })
        .catch(() => { Messenger.showError(MessengerConstants.errorMessage) })
    }
  }
}
</script>

<style scoped>
.info-box {
  min-height: 50px;
}
.info-box .info-box-content {
  margin-left: auto;
}
</style>
