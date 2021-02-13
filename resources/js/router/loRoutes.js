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

import NewLearningOutcome from '../views/learningOutcome/NewLearningOutcome'
import LearningOutcomeList from '../views/learningOutcome/LearningOutcomeList'
import EditLearningOutcome from '../views/learningOutcome/EditLearningOutcome'

const loRoutes = [
  {
    path: '/learning_outcomes/new',
    name: 'newLO',
    component: NewLearningOutcome
  },
  {
    path: '/learning_outcomes/list',
    name: 'loList',
    component: LearningOutcomeList
  },
  {
    path: '/learning_outcomes/:id/edit',
    name: 'editLO',
    component: EditLearningOutcome
  }
]

export default loRoutes
