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
      <h2>Kazanımlara göre istatistikler</h2>
    </template>
    <template content="content">
      <div class="row">
        <div class="col-md-6">
          <div class="card card-success">
            <div class="box-header with-border">
              <h4 class="pull-left">
                Soru Hazırlanan Sınıflar
              </h4>
            </div>
            <div class="box-body">
              <ul class="nav nav-stacked">
                <li>
                  <a
                    href=""
                    @click.prevent.stop=""
                  >
                    <div class="row">
                      <div class="col-md-6 col-xs-6">
                        Sınıflar
                      </div>
                      <div class="col-md-2 col-xs-2">
                        <span class="badge bg-green">H</span>
                      </div>
                      <div class="col-md-2 col-xs-2">
                        <span class="badge bg-orange">D</span>
                      </div>
                      <div class="col-md-2 col-xs-2">
                        <span class="badge bg-blue">T</span>
                      </div>
                    </div>
                  </a>
                </li>
                <li
                  v-for="c in classes"
                  :key="c.class_level"
                >
                  <a
                    href=""
                    @click.prevent.stop="getLearningOutComesByClass(c.class_level)"
                  >
                    <div class="row">
                      <div class="col-md-6 col-xs-6">{{ c.class_level }} .Sınıf</div>
                      <div class="col-md-2 col-xs-2"><span class="badge bg-green">{{ c.valid_count }}</span></div>
                      <div class="col-md-2 col-xs-2"><span class="badge bg-orange">{{ c.non_valid_count }}</span></div>
                      <div class="col-md-2 col-xs-2"><span class="badge bg-aqua">{{ c.total_count }}</span></div>
                    </div>
                  </a>
                </li>
              </ul>
              <div class="box-footer">
                <ul class="nav flex-column">
                  <li>
                    <br>
                    <p-check
                      v-model="isAnyQuestionLO"
                      class="p-switch p-fill"
                      name="check"
                      color="success"
                    >
                      Hiç Soru Sorulmayan Kaz.
                    </p-check>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <ul class="nav flex-column">
                <li
                  v-for="l in learningOutComes"
                  :key="l.learning_outcome_id"
                  class="nav-item"
                >
                  <a
                    href=""
                    @click.prevent.stop=""
                  >
                    <div class="row">
                      <div class="col-md-6 col-xs-9">{{ l.learning_outcome }}</div>
                      <div class="col-md-2 col-xs-1"><span class="badge bg-green">{{ l.valid_count }}</span></div>
                      <div class="col-md-2 col-xs-1"><span class="badge bg-orange">{{ l.non_valid_count }}</span></div>
                      <div class="col-md-2 col-xs-1"><span class="badge bg-aqua">{{ l.total_count }}</span></div>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import Page from '../../components/Page'
export default {
  name: 'StatsByLO',
  components: { Page }
}
</script>

<style scoped>

</style>
