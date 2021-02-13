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

import Vue from 'vue'
import dayjs from '../helpers/dayjs'

Vue.filter('year', (value) => {
  if (!value) return ''
  // console.log('Moment Filter: ' + value)
  return dayjs(value).format('YYYY')
})

Vue.filter('dateTime', (value) => {
  if (!value) return ''
  if (!dayjs(value).isValid()) return value
  return dayjs(value).format('DD.MM.YYYY HH:mm')
})

Vue.filter('trDate', (value) => {
  if (!value) return ''
  // console.log('Moment Filter: ' + value)
  return dayjs(value).format('L')
})
