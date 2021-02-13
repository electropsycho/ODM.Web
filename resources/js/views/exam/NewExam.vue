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
    <template slot="header">
      <h4>Sınav Oluşturma Sihirbazı <a href="https://www.google.com/search?q=morpheus">(MORPHEUS)</a></h4>
    </template>
    <template slot="content">
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <button
                    class="btn btn-danger btn-block"
                    style="height:8em"
                    @click="$router.push({name: 'newManualExam'})"
                  >
                    Soruları Seçerek Sınavı Elle Oluşturayım
                  </button>
                </div>
                <div class="col-md-6">
                  <button
                    style="height:8em"
                    class="btn btn-primary btn-block"
                    @click="$router.push({name: 'newAutoExam'})"
                  >
                    Parametreleri Vereyim Otomatik Oluşturuver
                  </button>
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
import Page from '../../components/Page'

export default {
  name: 'NewExam',
  components: { Page }
}
</script>

<style scoped>

</style>
