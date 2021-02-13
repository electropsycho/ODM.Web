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

import { MutationsEL } from '../../helpers/constants'

const state = {
  examPurpose: '',
  classLevel: ''
}

const getters = {
  examPurpose: (state) => {
    if (!state.examPurpose) {
      state.examPurpose = JSON.parse(sessionStorage.getItem(MutationsEL.EXAM_PURPOSE))
    }
    return state.examPurpose
  },
  classLevel: (state) => {
    if (!state.classLevel) {
      state.classLevel = JSON.parse(sessionStorage.getItem(MutationsEL.CLASS_LEVEL))
    }
    return state.classLevel
  }
}

const mutations = {
  [MutationsEL.EXAM_PURPOSE] (state, examPurpose) {
    state.examPurpose = examPurpose
    sessionStorage.setItem(MutationsEL.EXAM_PURPOSE, JSON.stringify(examPurpose))
  },
  [MutationsEL.CLASS_LEVEL] (state, classLevel) {
    state.classLevel = classLevel
    sessionStorage.setItem(MutationsEL.CLASS_LEVEL, JSON.stringify(classLevel))
  }
}

const actions = {
  setExamPurpose ({ commit }, examPurpose) {
    commit(MutationsEL.EXAM_PURPOSE, examPurpose)
  },
  setClassLevel ({ commit }, classLevel) {
    commit(MutationsEL.CLASS_LEVEL, classLevel)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
