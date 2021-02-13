
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

const StatService = {
  getQuestionCounts () {
    return new Promise((resolve, reject) => {
      http.get('/stats/question_counts/total')
        .then(resp => resolve(resp.data))
        .catch(err => reject(err))
    })
  },
  getClasses () {
    return new Promise((resolve, reject) => {
      http.get('/stats/classes')
        .then(resp => resolve(resp.data))
        .catch(err => reject(err))
    })
  },
  getLearningOutcomes (classLevel, isAnyQuestionLO) {
    return new Promise((resolve, reject) => {
      http.get('/stats/lo_count_by', { params: { class_level: classLevel, is_any_question_lo: isAnyQuestionLO } })
        .then(resp => resolve(resp.data))
        .catch(err => reject(err))
    })
  }
}

export default StatService
