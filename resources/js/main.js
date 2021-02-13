/*
 * ODM.Web  https://github.com/electropsycho/ODM.Web
 * Copyright 2019-2020 Hakan GÜLEN
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

import './bootstrap'
import Vue from 'vue'
import router from './router'
import App from './App'
import store from './store'

import PrettyCheckbox from 'pretty-checkbox-vue/dist/pretty-checkbox-vue.min'
import axios from './helpers/axios'
import VueMask from 'v-mask/dist/v-mask.min'
import ACL from './plugins/ACL'
import VueAxios from 'vue-axios/dist/vue-axios.min'
import PathManPlugin from './plugins/PathManPlugin'
import Loading from 'vue-loading-overlay/dist/vue-loading.min'

Window.Vue = Vue

Vue.use(VueAxios, axios)
Vue.use(ACL)
Vue.use(PathManPlugin)
Vue.use(PrettyCheckbox)
Vue.use(VueMask)
// Vue.use(require('vue-moment'))
Vue.use(Loading, {
  // container: this.$refs.overlayContainer,
  color: '#007bff',
  height: 128,
  width: 128,
  opacity: 0.8,
  blur: '2px'
})

// eslint-disable-next-line no-unused-vars
const main = new Vue({
  router,
  store,
  components: { App },
  template: '<App/>'
}).$mount('#app')
