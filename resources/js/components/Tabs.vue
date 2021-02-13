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
  <div class="card card-primary card-outline card-tabs">
    <div class="card-header p-0 pt-1 border-bottom-0">
      <ul class="nav nav-tabs">
        <li
          v-for="(tab, index) in tabs"
          :key="tab.title"
          class="nav-item"
          @click="selectTab(index)"
        >
          <a
            href="#"
            class="nav-link"
            :class="{active: tab.isActive}"
            @click.prevent
          >{{ tab.title }}</a>
        </li>
      </ul>
      <div class="card-body">
        <div class="tab-content">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Tabs',
  data: () => ({
    selectedIndex: 0,
    tabs: []
  }),
  created () {
    this.tabs = this.$children
  },
  mounted () {
    this.selectTab(0)
  },
  methods: {
    selectTab (index) {
      this.selectedIndex = index
      this.tabs.forEach((tab, i) => {
        tab.isActive = (i === index)
      })
    }
  }

}
</script>

<style scoped>

</style>
