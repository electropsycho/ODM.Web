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

import http from '../helpers/axios'

export const SettingService = {
  getGeneralInfo () {
    return new Promise((resolve, reject) => {
      http.get('auth/general_info')
        .then(value => {
          // localStorage.setItem(Constants.generalInfo, JSON.stringify(value.data))
          resolve(value.data)
        })
        .catch(reason => reject(reason))
    })
  },
  getSettings () {
    return new Promise((resolve, reject) => {
      http.get('settings')
        .then(response => resolve(response.data))
        .catch(reason => reject(reason))
    })
  },
  update (settings) {
    return new Promise((resolve, reject) => {
      http.put('settings', settings)
        .then((response) => {
          resolve(response.data)
        })
        .catch((reason) => reject(reason))
    })
  },
  mailSync () {
    return new Promise((resolve, reject) => {
      http.get('mails/sync')
        .then((response) => resolve(response.data))
        .catch((reason) => reject(reason))
    })
  },
  async getQuestionConstraints () {
    try {
      const response = await http.get('questions/constraints')
      return response.data
    } catch (error) {}
  }
}
