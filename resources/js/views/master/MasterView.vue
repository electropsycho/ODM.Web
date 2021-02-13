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
  <div class="wrapper">
    <n-header />
    <n-sidebar />
    <div
      class="content-wrapper"
      style="min-height: 0 !important;"
    >
      <router-view />
    </div>
    <n-footer />
    <div
      id="sidebar-overlay"
      @click="toggleSidebar"
    />
  </div>
</template>

<script>
import NHeader from './HeaderView'
import NSidebar from './MainSidebar'
import NFooter from './FooterView'
import SkinHelper from '../../helpers/SkinHelper'

export default {
  name: 'DashBoard',
  components: { NFooter, NSidebar, NHeader },
  beforeCreate () {
    SkinHelper.MainSkin()
  },
  methods: {
    toggleSidebar () {
      $('[data-widget="pushmenu"]').PushMenu('toggle')
    }
  }
}
</script>
<style lang="sass">
  // @import "../../../../../node_modules/animate.css/animate.min.css"
</style>
