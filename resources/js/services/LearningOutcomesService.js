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

const LearningOutcomesService = {
  findById (loId) {
    return new Promise((resolve, reject) => {
      http.get(`/learning_outcomes/${loId}`)
        .then(value => resolve(value.data))
        .catch(reason => reject(reason.message))
    })
  },
  findByContent (content, callback) {
    if (content.length >= 3) {
      http.get('/learning_outcomes', {
        params: {
          searchTerm: content
        }
      }).then(response => {
        callback(response.data)
      })
        .catch(error => {
          Messenger.showError(MessengerConstants.errorMessage)
          console.log(error)
        })
    }
  },
  findByCode (code, callback) {
    if (code.length >= 3) {
      http.get('/learning_outcomes', {
        params: {
          searchTerm: code
        }
      }).then(response => {
        callback(response.data)
      })
        .catch(error => {
          Messenger.showError(MessengerConstants.errorMessage)
          console.log(error)
        })
    }
  },
  findByCodeOrContent (search) {
    return new Promise((resolve, reject) => {
      http.get('learning_outcomes/find_by', { params: search })
        .then(response => resolve(response.data))
        .catch(error => reject(error))
    })
  },
  async findByCodeOrContentWithPaging (search) {
    try {
      const response = await http.get('learning_outcomes/search/with_paging', { params: search })
      return response.data
    } catch (error) {}
  },
  getLastSavedLOs (size) {
    return new Promise((resolve, reject) => {
      http.get(`/learning_outcomes/last_saved/${size}`)
        .then(res => resolve(res.data))
        .catch(error => reject(error))
    })
  },
  save (lo) {
    return new Promise((resolve, reject) => {
      http.post('/learning_outcomes', lo)
        .then(response => resolve(response.data))
        .catch(error => reject(error))
    })
  },
  async update (id, lo) {
    try {
      const response = await http.put(`/learning_outcomes/${id}`, lo)
      return response.data
    } catch (error) {}
  },
  delete (id, callback) {
    http.delete(`/learning_outcomes/${id}`)
      .then(response => { callback(response.data) })
      .catch(error => {
        Messenger.showError(MessengerConstants.errorMessage)
        console.log(error)
      })
  }
}

export default LearningOutcomesService
