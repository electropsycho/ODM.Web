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
      <div class="row">
        <div class="col-md-9">
          <h4>Bilgi Güncelleme</h4>
        </div>
        <div class="col-md-3">
          <button
            type="button"
            class="btn btn-warning btn-block"
            @click="resendForgetPassword"
          >
            Şifremi Yenile
          </button>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
              <div class="row justify-content-md-center">
                <div class="col-md-6">
                  <form @submit.prevent>
                    <div class="form-group has-feedback">
                      <label>Ad Soyad</label>
                      <input
                        v-model="user.full_name"
                        type="text"
                        class="form-control"
                        placeholder="Ad Soyad Giriniz"
                      >
                    </div>
                    <div class="form-group has-feedback">
                      <label>E-Posta</label>
                      <input
                        v-model="user.email"
                        class="form-control"
                        type="email"
                        placeholder="E-Posta adresi"
                      >
                    </div>
                    <div class="form-group has-feedback">
                      <label>Telefon</label>
                      <input
                        v-model="user.phone"
                        class="form-control"
                        placeholder="Kullanıcı Cep telefonu No"
                      >
                    </div>
                    <div class="form-group has-feedback">
                      <label>Kurum Seçimi</label>
                      <v-select
                        v-model="selectedInst"
                        :value="selectedInst.id"
                        :options="institutions"
                        append-to-body
                        :calculate-position="withPopper"
                        label="name"
                        placeholder="Kurum adını en az 3 harf girin"
                        @search="searchInstitutions"
                      >
                        <div slot="no-options">
                          Burada bişey bulamadık :-(
                        </div>
                      </v-select>
                    </div>
                  </form>
                </div>
              </div>
              <div class="row justify-content-md-center">
                <div class="col-md-4">
                  <button
                    class="btn btn-primary btn-large btn-block"
                    @click="save"
                  >
                    Kaydet
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import Page from '../../components/Page'
import BranchService from '../../services/BranchService'
import UserService from '../../services/UserService'
import debounce from 'lodash/debounce'
import InstitutionService from '../../services/InstitutionService'
import Messenger from '../../helpers/messenger'
import AuthService from '../../services/AuthService'
import { MessengerConstants } from '../../helpers/constants'
import vSelect from 'vue-select/dist/vue-select'
import { calculateWithPopper } from '../../helpers/utils'

export default {
  name: 'EditUser',
  components: { Page, vSelect },
  data: () => ({
    user: {},
    institutions: [],
    selectedInst: { },
    instId: null
  }),
  async beforeRouteEnter  (to, from, next) {
    const [branches, user] = await Promise.all([
      BranchService.getBranches(),
      UserService.findById(AuthService.getUserId())
    ])
    next(vm => {
      vm.user = user
      vm.branches = branches
      vm.selectedBranch = user.branch
      vm.selectedInst = user.institution
      vm.selectedRole = user.role
    })
  },
  methods: {
    searchInstitutions: debounce(async function (search, loading) {
      if (search) {
        loading(true)
        try {
          this.institutions = await InstitutionService.findByName(search)
          loading(false)
        } catch (err) {
          await Messenger.showError(err)
          loading(false)
        }
      }
    }, 1000),
    async resendForgetPassword () {
      const data = { email: this.user.email }
      const promptRes = await Messenger.showPrompt('Şifre yenileme e-postası ile şifrenizi güncellemek istediğinizden emin misiniz?')
      if (promptRes.isConfirmed) {
        const loader = this.$loading.show()
        try {
          const response = await AuthService.forgetPassword(data)
          loader.hide()
          await Messenger.showInfo(response.message)
        } catch (error) {
          loader.hide()
          await Messenger.showError(MessengerConstants.errorMessage)
        }
      }
    },
    async save () {
      const promptRes = await Messenger.showPrompt('Bilgilerinizi güncellemek istediğinize emin misiniz?')
      if (promptRes.isConfirmed) {
        const data = {
          inst_id: this.selectedInst.id,
          full_name: this.user.full_name,
          phone: this.user.phone,
          email: this.user.email
        }
        try {
          const response = await UserService.updateMyInfo(this.user.id, data)
          await Messenger.showSuccess(response.message)
        } catch (error) { await Messenger.showError('Güncelleme işlemi başarısız!') }
      }
    },
    withPopper: calculateWithPopper
  }
}
</script>

<style>
.v-select.drop-up.vs--open .vs__dropdown-toggle {
  border-radius: 0 0 4px 4px;
  border-top-color: transparent;
  border-bottom-color: rgba(60, 60, 60, 0.26);
}

[data-popper-placement='top'] {
  border-radius: 4px 4px 0 0;
  border-top-style: solid;
  border-bottom-style: none;
  box-shadow: 0 -3px 6px rgba(0, 0, 0, 0.15)
}
</style>
