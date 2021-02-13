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
  <i
    v-if="event.elector_id"
    class="fa fa-commenting-o bg-blue"
  />
  <i
    v-else
    class="fa fa-recycle bg-orange"
  />

  <div
    v-if="event.elector_id"
    class="timeline-item"
  >
    <span class="time"><i class="fa fa-clock-o" /> {{ formatDate(event.date) }}</span>

    <h3 class="timeline-header">
      <a
        href="#"
        @click.prevent
      >Değerlendirici Grup Kodu: {{ detectHeader(event) }}</a>
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
    class="timeline-item"
  >
    <span class="time"><i class="fa fa-clock-o" /> {{ formatDate(event.date) }}</span>

    <h3 class="timeline-header">
      <a href="javascript:">Revizyon</a>
    </h3>

    <div class="timeline-body">
      {{ event.comment }}
    </div>
    <div class="timeline-footer">
      Revizyon Tarihi <span class="label label-warning">{{ event.date | trDate }}</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TimeLineItem'
}
</script>

<style scoped>

</style>
