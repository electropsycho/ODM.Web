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

const Constants = {
  CURRENT_USER: 'current_user',
  ACCESS_TOKEN: 'access_token',
  PERMISSIONS: 'permissions',
  ROLES: 'roles',
  EXPIRES_IN: 'expires_in',
  GENERAL_INFO: 'general_info'
}

const ResponseCodes = {
  CODE_SUCCESS: 1000,
  CODE_UNAUTHORIZED: 1001,
  CODE_NOT_FOUND: 1002,
  CODE_INFO: 1003,
  CODE_WARNING: 1004,
  CODE_ERROR: 1005
}

const MessengerConstants = {
  errorMessage: 'Üzgünüz, bir hata ile karşılaştık lütfen sistem yöneticinize başvurunuz!'
}

const MessageKeys = {
  MESSAGE: 'message',
  CODE: 'code'
}

const Mutations = {
  LOGIN_SUCCESSFUL: 'loginSuccessful',
  LOGOUT: 'logout',
  LOGIN_ERROR: 'loginError',
  IS_SIGNING_IN: 'isSigningIn',
  SEARCH_QUESTIONS: 'searchQuestions',
  SET_SEARCH_QUESTIONS_RESULT: 'setSearchQuestionsResult',
  SET_USER: 'setUser',
  SAVE_TOKEN: 'saveToken',
  SET_ROLES: 'setRoles',
  SET_GENERAL_INFO: 'setGeneralInfo'
}

const MutationsLO = {
  CURRENT_PAGE: 'currentPage',
  TOTAL_PAGES: 'totalPages',
  PER_PAGE: 'perPage',
  FROM: 'from',
  TO: 'to',
  LAST_PAGE: 'lastPage',
  CURRENT_BRANCH: 'currentBranch',
  CURRENT_CLASS_LEVEL: 'currentClassLevel',
  SEARCH_TERM: 'searchTerm'
}

const MutationsQT = {
  QUESTION_STATE: 'questionState',
  BRANCH: 'branch',
  CLASS_LEVEL: 'classLevel',
  SEARCH_TERM: 'searchTerm',
  IS_DESIGN_REQUIRED: 'isDesignRequired',
  CURRENT_PAGE: 'currentPage'
}

const MutationsEL = {
  EXAM_PURPOSE: 'examPurpose',
  CLASS_LEVEL: 'classLevel'
}

export {
  MessengerConstants,
  ResponseCodes,
  Mutations,
  MutationsLO,
  MessageKeys,
  MutationsQT,
  MutationsEL
}

export default Constants
