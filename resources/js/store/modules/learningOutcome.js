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

import { MutationsLO } from '../../helpers/constants'

const state = () => ({
  currentPage: 1,
  perPage: 18,
  lastPage: 0,
  from: 0,
  to: 0,
  currentBranch: '',
  currentClassLevel: '',
  searchTerm: ''
})

const getters = {
  currentPage: (state) => state.currentPage,
  perPage: (state) => state.perPage,
  lastPage: (state) => state.lastPage,
  from: (state) => state.from,
  to: (state) => state.to,
  currentBranch: (state) => state.currentBranch,
  currentClassLevel: (state) => state.currentClassLevel,
  searchTerm: (state) => state.searchTerm
}

const mutations = {
  [MutationsLO.CURRENT_PAGE] (state, currentPage) {
    state.currentPage = currentPage
  },
  [MutationsLO.PER_PAGE] (state, perPage) {
    state.perPage = perPage
  },
  [MutationsLO.LAST_PAGE] (state, lastPage) {
    state.lastPage = lastPage
  },
  [MutationsLO.FROM] (state, from) {
    state.from = from
  },
  [MutationsLO.TO] (state, to) {
    state.to = to
  },
  [MutationsLO.CURRENT_BRANCH] (state, branch) {
    state.currentBranch = branch
  },
  [MutationsLO.CURRENT_CLASS_LEVEL] (state, level) {
    state.currentClassLevel = level
  },
  [MutationsLO.SEARCH_TERM] (state, term) {
    state.searchTerm = term
  }
}

const actions = {
  setCurrentPage ({ commit }, currentPage) {
    commit(MutationsLO.CURRENT_PAGE, currentPage)
  },
  setPerPage ({ commit }, perPage) {
    commit(MutationsLO.PER_PAGE, perPage)
  },
  setLastPage ({ commit }, lastPage) {
    commit(MutationsLO.LAST_PAGE, lastPage)
  },
  setFrom ({ commit }, from) {
    commit(MutationsLO.FROM, from)
  },
  setTo ({ commit }, to) {
    commit(MutationsLO.TO, to)
  },
  setCurrentBranch ({ commit }, branch) {
    commit(MutationsLO.CURRENT_BRANCH, branch)
  },
  setCurrentClassLevel ({ commit }, level) {
    commit(MutationsLO.CURRENT_CLASS_LEVEL, level)
  },
  setSearchTerm ({ commit }, searchTerm) {
    commit(MutationsLO.SEARCH_TERM, searchTerm)
  }
}

export default {
  namespaced: true,
  state,
  getters,
  mutations,
  actions
}
