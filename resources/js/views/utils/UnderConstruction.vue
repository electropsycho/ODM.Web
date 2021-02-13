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
      <h4 class="text-danger">
        Yapım aşamasında 🛠️
      </h4>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-12">
          <div class="card card-danger">
            <div class="card-title" />
            <div class="card-body">
              <div class="col-md-6">
                <div class="row justify-content-md-center ">
                  <p><b>Üzgünüz 😔</b> istediğiniz sayfa şu an için yapım aşamasındadır. Sabrınız için teşekkür ederiz.</p>
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
  name: 'UnderConstruction',
  components: { Page }
}
</script>

<style scoped>

</style>
