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

const ExamPurposeService = {
  async getPurposes () {
    try {
      const response = await http.get('/exams/purposes')
      return response.data
    } catch (error) {}
  }
}

const ExamService = {
  async createAutoExam (data) {
    try {
      const response = await http.post('/exams/auto', data)
      return response.data
    } catch (error) {}
  },
  async getExamFile (examId) {
    try {
      const response = await http.get(`exams/${examId}/file`, { responseType: 'blob', errorHandler: true })
      return {
        file: response.data,
        fileName: response.headers['content-disposition']
          .split(';')
          .find(n => n.includes('filename='))
          .replace('filename=', '')
          .trim()
      }
    } catch (error) {}
  },
  async getExam (examId = null, code = null) {
    try {
      const response = await http.get('exams', { params: { id: examId, code: code } })
      return response.data
    } catch (error) {}
  }
}

export {
  ExamPurposeService
}
export default ExamService
