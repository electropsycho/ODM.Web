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
          Değerlendirme İstatistikleri
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4">
              <div class="card bg-primary">
                <div class="card-header">
                  <h6>Değerlendirme Sayısı</h6>
                </div>
                <div class="card-body">
                  <h4>45</h4>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card bg-warning">
                <div class="card-header">
                  <h6>Toplam Revizyon Sayısı</h6>
                </div>
                <div class="card-body">
                  <h4>45</h4>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card bg-success">
                <div class="card-header">
                  <h6>Son Değ. Puan Ortalaması</h6>
                </div>
                <div class="card-body">
                  <h4>45</h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EvaluationStat',
  props: ['question']
}
</script>

<style scoped>

</style>
