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

export const QuestionDeleteService = {
  approve (questionId) {
    return new Promise((resolve, reject) => {
      http.put(`/questions/${questionId}/delete_request`)
        .then(value => resolve(value.data))
        .catch(err => reject(err))
    })
  },
  delete (id, questionId) {
    return new Promise((resolve, reject) => {
      http.delete(`/questions/${questionId}/delete_requests/${id}`)
        .then(response => resolve(response.data))
        .catch(error => reject(error))
    })
  }
}
