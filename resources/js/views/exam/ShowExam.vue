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
      <h4>{{ exam.code }} Kodlu Sınav Bilgileri</h4>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <tabs>
            <tab title="Sınav Genel Bilgileri">
              <div class="row">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Kod</b></h6>
                        <p>{{ exam.code }}</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Başlık</b></h6>
                        <p>{{ exam.title }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Amaç</b></h6>
                        <p>{{ exam.purpose }}</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Durum</b></h6>
                        <p>{{ exam.status }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Sınıf Seviyesi</b></h6>
                        <p>{{ exam.class_level }}. Sınıf</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Oluşturan</b></h6>
                        <p>{{ exam.creator }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Planlanan Tarih / Saat</b></h6>
                        <p>{{ exam.planned_date | dateTime }}</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Uygulama Tarihi</b></h6>
                        <p>{{ exam.date_of_occurrence | dateTime }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="callout callout-info">
                        <h6><b>Oluşturma Tarihi</b></h6>
                        <p>{{ exam.created_at | trDate }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="callout callout-info">
                        <h6><b>Açıklamalar</b></h6>
                        <p>{{ exam.description }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </tab>
            <tab title="Sınav İçerik Bilgileri">
              <under-construction />
            </tab>
          </tabs>
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import Page from '../../components/Page'
import ExamService from '../../services/ExamService'
import Tabs from '../../components/Tabs'
import Tab from '../../components/Tab'
import UnderConstruction from '../utils/UnderConstruction'

export default {
  name: 'ShowExam',
  components: { UnderConstruction, Page, Tabs, Tab },
  data: () => ({
    exam: ''
  }),
  async beforeRouteEnter (to, from, next) {
    const exam = await ExamService.getExam(to.params.examId)
    next(vm => {
      vm.exam = exam
    })
  },
  methods: {}
}
</script>

<style scoped>

</style>
