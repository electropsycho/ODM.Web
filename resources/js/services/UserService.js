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

const UserService = {
  findById (id) {
    return new Promise((resolve, reject) => {
      http.get(`/users/${id}`)
        .then(response => resolve(response.data))
        .catch(error => reject(error))
    })
  },
  findElectorsByBranchId (branchId) {
    return new Promise((resolve, reject) => {
      http.get(`/branches/${branchId}/electors`)
        .then(value => resolve(value.data))
        .catch(reason => reject(reason.data))
    })
  },
  approveUser (id) {
    return new Promise((resolve, reject) => {
      http.put(`/users/${id}/confirm_req`)
        .then(response => resolve(response.data))
        .catch(reason => reject(reason))
    })
  },
  reactivate (id) {
    return new Promise((resolve, reject) => {
      http.put(`/users/${id}/reactivate`)
        .then(response => resolve(response.data))
        .catch(err => reject(err.data))
    })
  },
  update (id, user) {
    return new Promise((resolve, reject) => {
      http.put(`/users/${id}`, user)
        .then(resp => resolve(resp.data))
        .catch(error => reject(error))
    })
  },
  updateMyInfo (id, user) {
    return new Promise((resolve, reject) => {
      http.put(`/users/${id}/my_info`, user)
        .then(resp => resolve(resp.data))
        .catch(error => reject(error))
    })
  },
  delete (id) {
    return new Promise((resolve, reject) => {
      http.delete(`/users/${id}`)
        .then(resp => resolve(resp.data))
        .catch(error => reject(error))
    })
  }
}

export default UserService
