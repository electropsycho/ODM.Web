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
  <div class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <a :href="sanitizeUrl"><b>{{ city }}</b>ÖDM</a>
      </div>
      <div class="card">
        <div class="card-body login-card-body">
          <div
            :class="{disabled : isSending}"
            class="login-box-body"
          >
            <p class="login-box-msg">
              Lütfen e posta adresinizi giriniz.
            </p>

            <form
              method="post"
              @submit.prevent
            >
              <div class="input-group mb-3">
                <input
                  v-model="email"
                  v-validate="'required|email'"
                  type="text"
                  name="email"
                  class="form-control"
                  :class="{'is-invalid': errors.has('email') || isSending}"
                  placeholder="Email adresinizi giriniz"
                >
                <div class="input-group-append">
                  <div class="input-group-text">
                    <span class="mdi mdi-email  form-control-feedback" />
                  </div>
                </div>
                <span
                  v-if="errors.has('email')"
                  class="error invalid-feedback"
                >{{ errors.first('email') }}</span>
              </div>
              <div
                v-if="captchaToken"
                :class="{'is-invalid': !recaptchaVerified}"
                class="mx-auto"
              >
                <div style="text-align: center;">
                  <vueRecaptcha
                    style="display: inline-block;"
                    :sitekey="captchaToken"
                    @verify="markRecaptchaAsVerified"
                  />
                  <span
                    v-if="!recaptchaVerified"
                    class="error invalid-feedback"
                  >Lütfen robot olmadığınızı doğrulayın!</span>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 mt-2">
                  <button
                    :class="{ disabled : errors.any() ||!recaptchaVerified || isSending }"
                    type="submit"
                    class="btn btn-primary btn-block"
                    @click="sendEmail"
                  >
                    Gönder
                  </button>
                </div>
                <!-- /.col -->
              </div>
            </form>
            <div class="row mt-2">
              <div class="col-sm-12">
                <router-link
                  :to="{ name: 'login' }"
                >
                  Şifremi hatırladım
                  <span class="mdi mdi-head-lightbulb" />
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
      <licence-info />
      <spinner
        v-if="isSending"
        spin-style="wave"
      />
    </div>
  </div>
</template>

<script>
import Spinner from '../../components/Spinner'
import Messenger from '../../helpers/messenger'
import vueRecaptcha from 'vue-recaptcha/dist/vue-recaptcha.min'
import AuthService from '../../services/AuthService'
import Constants, { MessengerConstants } from '../../helpers/constants'
import { sanitizeUrl } from '@braintree/sanitize-url'
import LicenceInfo from '../../components/LicenceInfo'
import SkinHelper from '../../helpers/SkinHelper'

export default {
  name: 'ResetPassword',
  components: { LicenceInfo, Spinner, vueRecaptcha },
  data () {
    return {
      email: '',
      isSending: false,
      recaptchaVerified: false,
      captchaToken: '',
      city: ''
    }
  },
  computed: {
    sanitizeUrl () {
      return sanitizeUrl(this.web_address)
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => {
      const settings = JSON.parse(localStorage.getItem(Constants.GENERAL_INFO))
      vm.captchaToken = settings.captcha_public_key
      vm.city = settings.city
    })
  },
  beforeCreate () {
    SkinHelper.LoginSkin()
  },
  methods: {
    sendEmail () {
      this.$validator.validateAll()
        .then(res => {
          if (res) {
            this.isSending = true
            const data = { email: this.email, recaptcha: this.captchaToken }
            AuthService.forgetPassword(data)
              .then(value => {
                Messenger.showInfo(value.message)
                  .then(value => { this.$router.push({ name: 'login' }) })
                  .catch(() => { Messenger.showError(MessengerConstants.errorMessage) })
              })
              .catch(() => {
                Messenger.showError(MessengerConstants.errorMessage)
              })
              .finally(() => { this.isSending = false })
          }
        })
    },
    markRecaptchaAsVerified (response) {
      // console.log(response)
      this.captchaToken = response
      this.recaptchaVerified = true
    }
  }
}
</script>

<style lang="sass">
</style>
