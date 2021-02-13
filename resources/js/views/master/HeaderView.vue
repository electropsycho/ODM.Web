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
  <nav class="main-header navbar navbar-expand navbar-white navbar-light elevation-1">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a
          class="nav-link"
          data-widget="pushmenu"
          href="#"
          role="button"
        ><i class="fas fa-bars" />
        </a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a
          class="nav-link"
          role="button"
          @click="logout"
        >
          <i class="mdi mdi-logout" /></a>
      </li>
    </ul>
  </nav>
</template>

<script>
import img from '../../../images/Logo.png'
import { mapActions } from 'vuex'

export default {
  name: 'NHeader',
  data: () => ({
    image: img,
    user: null,
    name: ''
  }),
  mounted () {
    $('[data-widget="pushmenu"]').PushMenu()
  },
  methods: {
    ...mapActions('app', ['logout'])
  }
}
</script>

<style lang="sass">

</style>
