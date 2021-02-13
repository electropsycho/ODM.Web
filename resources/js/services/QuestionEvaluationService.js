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

const QuestionEvaluationService = {
  saveElectors (id, data) {
    return new Promise((resolve, reject) => {
      http.post(`questions/${id}/evaluations`, data)
        .then(value => { resolve(value.data) })
        .catch(reason => reject(reason.data))
    })
  },
  save (id, data) {
    return new Promise((resolve, reject) => {
      http.put(`/questions/${id}/evaluations`, data)
        .then(response => { resolve(response.data) })
        .catch(error => reject(error))
    })
  },
  deleteById (id) {
    return new Promise((resolve, reject) => {
      http.delete(`/evaluations/${id}/`)
        .then(response => resolve(response.data))
        .catch(error => reject(error))
    })
  },
  deleteByCode (questionId, code) {
    return new Promise((resolve, reject) => {
      http.delete(`/questions/${questionId}/evaluations/${code}`)
        .then(value => resolve(value.data))
        .catch(reason => reject(reason.data))
    })
  },
  findByQuestionId (id, isOpen = '') {
    return new Promise((resolve, reject) => {
      http.get(`/questions/${id}/evaluations`, { params: { is_open: isOpen } })
        .then(response => { resolve(response.data) })
        .catch(error => reject(error))
    })
  },
  addElectors (questionId, code, electors) {
    return new Promise((resolve, reject) => {
      http.put(`/questions/${questionId}/evaluations/${code}/add_electors`, { electors })
        .then(response => { resolve(response.data) })
        .catch(error => reject(error))
    })
  },
  manuallyCalculate (questionId, code) {
    return new Promise((resolve, reject) => {
      http.put(`/questions/${questionId}/evaluations/${code}`)
        .then(response => { resolve(response.data) })
        .catch(error => reject(error))
    })
  }
}

export default QuestionEvaluationService
