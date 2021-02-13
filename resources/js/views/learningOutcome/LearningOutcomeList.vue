<!--
  - ODM.Web  https://github.com/electropsycho/ODM.Web
  - Copyright 2019-2020 Hakan GÜLEN
  -
  - Licensed under the Apache License, Version 2.0 (the "License");
  - you may not use this file except in compliance with the License.
  - You may obtain a copy of the License at
  -
  - http://www.apache.org/licenses/LICENSE-2.0
  -
  - Unless required by applicable law or agreed to in writing, software
  - distributed under the License is distributed on an "AS IS" BASIS,
  - WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  - See the License for the specific language governing permissions and
  - limitations under the License.
  -->

<template>
  <page>
    <template v-slot:header>
      <h4 class="mb-0">
        Kazanım Listesi
      </h4>
    </template>
    <template v-slot:content>
      <div class="col-12">
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group has-feedback">
                      <label class="mb-1">Ders</label>
                      <v-select
                        v-model="selectedBranch"
                        :options="branches"
                        label="name"
                        placeholder="Alan/Ders seçebilirsiniz"
                        @input="setClasses"
                      >
                        <div slot="no-options">
                          Burada bişey bulamadık :-(
                        </div>
                      </v-select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group has-feedback">
                      <label class="mb-1">Sınıf Seviyesi</label>
                      <v-select
                        v-model="selectedClassLevel"
                        :options="classLevels"
                        placeholder="Seçebilirsiniz"
                      >
                        <div slot="no-options">
                          Burada bişey bulamadık :-(
                        </div>
                      </v-select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group has-feedback">
                      <label class="mb-1">Sayfadaki kayıt sayısı</label>
                      <v-select
                        v-model="selectedPerPage"
                        :options="[12, 18, 24, 30, 45]"
                        label="name"
                        placeholder="Kayıt sayısı"
                      >
                        <div slot="no-options">
                          Burası boş
                        </div>
                      </v-select>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group has-feedback">
                      <label class="mb-1">Arama metni</label>
                      <input
                        v-model="searchTerm"
                        name="searchedContent"
                        class="form-control"
                        type="text"
                        placeholder="Aranacak içerik"
                      >
                    </div>
                  </div>
                  <div class="col-md-2">
                    <label />
                    <button
                      class="btn btn-block btn-primary"
                      @click="searchLOs"
                    >
                      Ara
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          v-if="!paginationIsEnabled"
          class="row"
        >
          <div class="col-md-12">
            <div class="card">
              <div class="card-body">
                <h4 class="text-center">
                  Üzgünüz ardağınız kriterlere göre bir kazanım bulmadık.😥
                </h4>
              </div>
            </div>
          </div>
        </div>
        <paginator
          v-if="paginationIsEnabled"
          :last-page="lastPage"
          :current-page="currentPage"
          @page-changed="pageChanged"
        />
        <div class="row">
          <div class="col-12">
            <div
              v-for="los in loGroup"
              class="row"
            >
              <div
                v-for="l in los"
                :key="l.id"
                class="col-md-4"
              >
                <div class="card card-warning">
                  <div class="card-header with-border">
                    <h4>{{ l.branch_name }}</h4>
                  </div>
                  <div class="card-body">
                    <ul class="nav flex-column">
                      <li class="nav-item">
                        <span class="text-bold">Sınıf Seviyesi:</span> {{ l.class_level }}. Sınıf Düzeyi
                      </li>
                      <li class="nav-item">
                        <span class="text-bold">Kodu:</span> {{ l.code }}
                      </li>
                      <li class="nav-item">
                        <span class="text-bold">
                          İçerik:
                        </span> {{ l.content }}
                      </li>
                      <li />
                    </ul>
                  </div>
                  <div
                    v-if="isPermitted"
                    class="card-footer"
                  >
                    <button
                      class="btn btn-outline-primary float-right"
                      @click="editLO(l.id)"
                    >
                      Düzenle
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <paginator
          v-if="paginationIsEnabled"
          :last-page="lastPage"
          :current-page="currentPage"
          @page-changed="pageChanged"
        />
      </div>
    </template>
  </page>
</template>

<script>
import LearningOutcomesService from '../../services/LearningOutcomesService'
import Messenger from '../../helpers/messenger'
import vSelect from 'vue-select'
import BranchService from '../../services/BranchService'
import chunk from 'lodash/chunk'
import Page from '../../components/Page'
import Paginator from '../../components/Paginator'
import { mapActions, mapGetters } from 'vuex'
import store from '../../store'

export default {
  name: 'LearningOutcomeList',
  components: { Page, vSelect, Paginator },
  data: () => ({
    branches: [],
    loGroup: [],
    classLevels: []
  }),
  computed: {
    ...mapGetters('learningOutcome', {
      lastPage: 'lastPage',
      currentPage: 'currentPage'
    }),
    selectedPerPage: {
      get () {
        return this.$store.getters['learningOutcome/perPage']
      },
      set (value) {
        this.setPerPage(Number(value))
      }
    },
    selectedClassLevel: {
      get () {
        return this.$store.getters['learningOutcome/currentClassLevel']
      },
      set (value) {
        this.setCurrentClassLevel(Number(value))
      }
    },
    selectedBranch: {
      get () {
        return this.$store.getters['learningOutcome/currentBranch']
      },
      set (value) {
        this.setCurrentBranch(value)
      }
    },
    searchTerm: {
      get () {
        return this.$store.getters['learningOutcome/searchTerm']
      },
      set (value) {
        this.setSearchTerm(value)
      }
    },
    paginationIsEnabled () {
      return this.loGroup.length > 0
    },
    isPermitted () {
      return this.$isInRole('admin')
    }
  },
  async beforeRouteEnter (to, from, next) {
    const [branches, los] = await Promise.all([
      BranchService.getBranches(),
      LearningOutcomesService.findByCodeOrContentWithPaging({
        per_page: store.getters['learningOutcome/perPage'],
        page: store.getters['learningOutcome/currentPage'],
        branch_id: store.getters['learningOutcome/currentBranch']?.id,
        searched_content: store.getters['learningOutcome/searchTerm']
      })
    ])
    next(vm => {
      vm.branches = branches
      vm.loGroup = chunk(los.data, 3)
      vm.setLastPage(los.last_page)
      vm.classLevels = store.getters['learningOutcome/currentBranch']
          ?.class_levels
          ?.split(',')
          ?.map(Number)
    })
  },
  methods: {
    ...mapActions('learningOutcome', {
      setCurrentPage: 'setCurrentPage',
      setLastPage: 'setLastPage',
      setPerPage: 'setPerPage',
      setFrom: 'setFrom',
      setTo: 'setTo',
      setCurrentBranch: 'setCurrentBranch',
      setCurrentClassLevel: 'setCurrentClassLevel',
      setSearchTerm: 'setSearchTerm'
    }),
    pageChanged (value) {
      this.setCurrentPage(value)
      this.searchLOs()
    },
    setClasses () {
      this.classLevels = this.selectedBranch
        ?.class_levels
        ?.split(',')
        ?.map(Number)
    },
    async searchLOs () {
      // const loader = this.$loading.show()
      try {
        const response = await LearningOutcomesService.findByCodeOrContentWithPaging({
          per_page: this.selectedPerPage,
          page: this.currentPage,
          branch_id: this.selectedBranch?.id,
          searched_content: this.searchTerm
        })
        this.loGroup = chunk(response.data, 3)
        this.setLastPage(response.last_page)
      } catch (reason) {
        await Messenger.showError(reason.message)
      }
    },
    editLO (loId) {
      this.$router.push({ name: 'editLO', params: { id: loId } })
    }
  }
}
</script>

<style>
  /*@import '~vue-select/dist/vue-select.css';*/
</style>
