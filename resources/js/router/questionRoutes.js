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

import SimpleNewQuestion from '../views/question/SimpleNewQuestion'
import QuestionTableList from '../views/question/QuestionTableList'
import QuestionDeleteRequests from '../views/question/QuestionDeleteRequests'
import QuestionEvaluationRequestList from '../views/question/QuestionEvaluationRequestList'
import ShowQuestion from '../views/question/ShowQuestion'
import ReviseQuestion from '../views/question/ReviseQuestion'
import SetEvaluatorsForQuestion from '../views/question/SetEvaluatorsForQuestion'
import EvaluateQuestion from '../views/question/EvaluateQuestion'

const questionRoutes = [
  {
    path: '/questions/new',
    name: 'newQuestion',
    component: SimpleNewQuestion
  },
  {
    path: '/questions/list',
    name: 'questionTableList',
    component: QuestionTableList
  },
  {
    path: '/questions/delete_requests',
    name: 'questionDeleteRequests',
    component: QuestionDeleteRequests
  },
  {
    path: '/questions/eval_requests',
    name: 'questionEvaluationRequests',
    component: QuestionEvaluationRequestList
  },
  {
    path: '/questions/:questionId',
    name: 'showQuestion',
    component: ShowQuestion
  },
  {
    path: '/questions/:questionId/revise',
    name: 'reviseQuestion',
    component: ReviseQuestion
  },
  {
    path: '/questions/:questionId/set_evaluators',
    name: 'setEvaluators',
    component: SetEvaluatorsForQuestion
  },
  {
    path: '/questions/:questionId/evaluate/:qerId',
    name: 'questionEvaluation',
    component: EvaluateQuestion
  }
]

export default questionRoutes
