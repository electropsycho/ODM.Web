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
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box">
          <div class="box-header with-border">
            <h4>
              E-Posta Senkronizasyonu(Deneysel Özellik)
            </h4>
          </div>
          <div class="box-body">
            <div class="row">
              <div class="col-md-offset-4 col-md-4">
                <button
                  class="btn btn-primary btn-block"
                  @click="sync"
                >
                  E-Postaları Dağıt
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { SettingService } from '../../services/SettingService'

export default {
  name: 'MailSync',
  methods: {
    sync () {
      SettingService.mailSync()
    }
  }
}
</script>

<style scoped>

</style>
