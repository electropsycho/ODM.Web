
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

import ExamList from '../views/exam/ExamList'
import NewExam from '../views/exam/NewExam'
import NewAutoExam from '../views/exam/NewAutoExam'
import UnderConstruction from '../views/utils/UnderConstruction'
import ShowExam from '../views/exam/ShowExam'

const examRoutes = [
  {
    path: '/exams/list',
    name: 'examList',
    component: ExamList
  },
  {
    path: '/exams/wizard',
    name: 'newExam',
    component: NewExam
  },
  {
    path: '/exams/create/auto',
    name: 'newAutoExam',
    component: NewAutoExam
  },
  {
    path: '/exams/create/manual',
    name: 'newManualExam',
    component: UnderConstruction
  },
  {
    path: '/exams/:examId',
    name: 'showExam',
    component: ShowExam
  }
]

export default examRoutes
