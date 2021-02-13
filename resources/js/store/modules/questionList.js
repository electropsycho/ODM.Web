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
import { MutationsQT } from '../../helpers/constants'

const state = () => ({
  questionState: '',
  branch: '',
  classLevel: '',
  searchTerm: '',
  isDesignRequired: '',
  currentPage: 0
})

const getters = {
  questionState: (state) => {
    if (!state.questionState) {
      state.questionState = JSON.parse(sessionStorage.getItem(MutationsQT.QUESTION_STATE))
    }
    return state.questionState
  },
  branch: (state) => {
    if (!state.branch) {
      state.branch = JSON.parse(sessionStorage.getItem(MutationsQT.BRANCH))
    }
    return state.branch
  },
  classLevel: (state) => {
    if (!state.classLevel) {
      state.classLevel = JSON.parse(sessionStorage.getItem(MutationsQT.CLASS_LEVEL))
    }
    return state.classLevel
  },
  isDesignRequired: (state) => {
    if (!state.isDesignRequired) {
      state.isDesignRequired = JSON.parse(sessionStorage.getItem(MutationsQT.IS_DESIGN_REQUIRED))
    }
    return state.isDesignRequired
  }
}

const mutations = {
  [MutationsQT.QUESTION_STATE] (state, questionState) {
    state.questionState = questionState
    sessionStorage.setItem(MutationsQT.QUESTION_STATE, JSON.stringify(questionState))
  },
  [MutationsQT.BRANCH] (state, branch) {
    state.branch = branch
    sessionStorage.setItem(MutationsQT.BRANCH, JSON.stringify(branch))
  },
  [MutationsQT.CLASS_LEVEL] (state, classLevel) {
    state.classLevel = classLevel
    sessionStorage.setItem(MutationsQT.CLASS_LEVEL, JSON.stringify(classLevel))
  },
  [MutationsQT.IS_DESIGN_REQUIRED] (state, isDesignRequired) {
    state.isDesignRequired = isDesignRequired
    sessionStorage.setItem(MutationsQT.IS_DESIGN_REQUIRED, JSON.stringify(isDesignRequired))
  }
}

const actions = {
  setSelectedQuestionState ({ commit }, questionState) {
    commit(MutationsQT.QUESTION_STATE, questionState)
  },
  setSelectedClassLevel ({ commit }, classLevel) {
    commit(MutationsQT.CLASS_LEVEL, classLevel)
  },
  setSelectedBranch ({ commit }, branch) {
    commit(MutationsQT.BRANCH, branch)
  },
  setIsDesignRequired ({ commit }, isDesignRequired) {
    commit(MutationsQT.IS_DESIGN_REQUIRED, isDesignRequired)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
