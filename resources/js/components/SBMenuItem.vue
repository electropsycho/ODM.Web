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
  <router-link
    v-if="router && router.name"
    tag="li"
    :to="router"
  >
    <a href="#">
      <i :class="icon" /> <span>{{ name }}</span>
      <span
        v-show="badge"
        class="pull-right-container"
      >
        <small
          class="label pull-right"
          :class="[badge.type==='String'?'bg-green':'label-primary']"
        >{{ badge.data }}
        </small>
      </span>
    </a>
  </router-link>
  <li
    v-else
    :class="getType"
  >
    {{ isHeader ? name : '' }}
    <a
      v-if="!isHeader"
      href="#"
    >
      <i :class="icon" /> <span>{{ name }}</span>
      <span class="pull-right-container">
        <small
          v-if="badge && badge.data"
          class="label pull-right"
          :class="[badge.type==='String'?'bg-green':'label-primary']"
        >{{ badge.data }}</small>
        <i
          v-else
          class="fa fa-angle-left pull-right"
        />
      </span>
    </a>
    <ul
      v-if="items.length > 0"
      class="treeview-menu"
    >
      <router-link
        v-for="(item,index) in items"
        v-if="item.router && item.router.name"
        :key="index"
        tag="li"
        :data="item"
        :to="item.router"
      >
        <a>
          <i :class="item.icon" /> {{ item.name }}
        </a>
      </router-link>
      <li v-else>
        <a>
          <i :class="item.icon" /> {{ item.name }}
        </a>
      </li>
    </ul>
  </li>
</template>

<script>
export default {
  name: 'SbMenuItem',
  props: {
    type: {
      type: String,
      default: 'item'
    },
    isHeader: {
      type: Boolean,
      default: false
    },
    icon: {
      type: String,
      default: ''
    },
    name: {
      type: String
    },
    badge: {
      type: Object,
      default () {
        return {}
      }
    },
    items: {
      type: Array,
      default () {
        return []
      }
    },
    router: {
      type: Object,
      default () {
        return {
          name: ''
        }
      }
    },
    link: {
      type: String,
      default: ''
    }
  },
  computed: {
    getType () {
      if (this.isHeader) {
        return 'header'
      }
      return this.type === 'item' ? '' : 'treeview'
    }
  },
  created () {
  }
}
</script>
