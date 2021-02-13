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
import Messenger from '../helpers/messenger'
import { MessengerConstants } from '../helpers/constants'

const BranchService = {
  getAllForRegisterReq (name) {
    return new Promise((resolve, reject) => {
      http.get('/auth/branches')
        .then(response => { resolve(response.data) })
        .catch(error => { reject(error) })
    })
  },
  getBranchesWithStats () {
    return new Promise((resolve, reject) => {
      http.get('/branches/with_stats').then(response => {
        if (response === undefined) reject(new Error('Tanımsız cevap'))
        resolve(response.data)
      })
        .catch(error => {
          reject(error)
        })
    })
  },
  getBranches () {
    return new Promise((resolve, reject) => {
      http.get('/branches').then(response => {
        if (response === undefined) reject(new Error('Tanımsız cevap'))
        resolve(response.data)
      })
        .catch(error => {
          reject(error)
        })
    })
  },
  save: function (branch, callback) {
    http.post('/branches', branch)
      .then(response => {
        if (response === undefined) return
        callback(response.data)
      })
      .catch(error => {
        Messenger.showError(MessengerConstants.errorMessage)
        console.log(error)
      })
  },
  update (id, branch, callback) {
    http.put(`/branches/${id}`, branch)
      .then(response => {
        if (response === undefined) return
        callback(response.data)
      })
      .catch(error => {
        Messenger.showError(MessengerConstants.errorMessage)
        console.log(error)
      })
  },
  delete (id, callback) {
    http.delete(`/branches/${id}`)
      .then(response => {
        if (response === undefined) return
        callback(response.data)
      })
      .catch(error => {
        Messenger.showError(MessengerConstants.errorMessage)
        console.log(error)
      })
  }
}

export default BranchService
