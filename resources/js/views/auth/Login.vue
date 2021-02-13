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
  <div class="login-box mt-4">
    <div class="login-logo">
      <!--        <div class="row">-->
      <!--          <div class="text-center">-->
      <!--            <img src="images/Logo.PNG" class="img-circle" alt="...">-->
      <!--          </div>-->
      <!--        </div>-->
      <a :href="sanitizeUrl"><b>{{ settings.city }}</b>ÖDM</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">
          Kullanmaya başlamak için lütfen oturum açınız.
        </p>

        <form
          method="post"
          @submit.prevent
        >
          <div class="input-group mb-3">
            <input
              v-model="email"
              v-validate="'required|email'"
              name="email"
              class="form-control"
              :class="{'is-invalid': errors.has('email')}"
              autocomplete="username"
              placeholder="Kullancı Adı/E-Posta giriniz"
            >
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="mdi mdi-email form-control-feedback" />
              </div>
            </div>
            <span
              v-if="errors.has('email')"
              class="error invalid-feedback"
            >{{ errors.first('email') }}</span>
          </div>
          <div class="input-group mb-3">
            <input
              v-model="password"
              v-validate="'required|min:8|max:16|verify_password'"
              name="password"
              type="password"
              class="form-control"
              :class="{'is-invalid': errors.has('password')}"
              autocomplete="current-password"
              placeholder="Şifrenizi giriniz"
            >
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="mdi mdi-lock form-control-feedback" />
              </div>
            </div>
            <span
              v-if="errors.has('password')"
              class="error invalid-feedback"
            >{{ errors.first('password') }}</span>
          </div>
          <div class="mx-auto mb-3">
            <div
              v-if="siteKey && captchaEnabled"
              id="captcha"
              style="text-align: center;"
            >
              <vueRecaptcha
                style="display: inline-block;"
                :sitekey="siteKey"
                @verify="markRecaptchaAsVerified"
              />
              <span
                v-if="!recaptchaVerified"
                class="error invalid-feedback"
              >Lütfen robot olmadığınızı doğrulayın!</span>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <button
                :class="{ disabled : validateLogin }"
                type="submit"
                class="btn btn-primary btn-block mb-3"
                @click="loginUser"
              >
                Oturum Aç
              </button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <router-link
          :to="{ name: 'forgotMyPassword' }"
          class="mb-2"
        >
          Şifremi unuttum
          <span class="mdi mdi-passport-biometric" />
        </router-link>
        <br>
        <router-link
          :to="{ name: 'register' }"
        >
          Yeni kayıt talebi oluştur
          <span class="mdi mdi-account-plus" />
        </router-link>
      </div>
    </div>
    <licence-info />
  </div>
</template>

<script>
import Messenger from '../../helpers/messenger'
import vueRecaptcha from 'vue-recaptcha'
import { sanitizeUrl } from '@braintree/sanitize-url'
import { ResponseCodes } from '../../helpers/constants'
import LicenceInfo from '../../components/LicenceInfo'
import SkinHelper from '../../helpers/SkinHelper'
import Auth from '../../services/AuthService'
import { mapActions } from 'vuex'
import store from '../../store'

export default {
  name: 'Login',
  components: { LicenceInfo, vueRecaptcha },
  data: () => ({
    recaptchaVerified: false,
    captchaToken: '',
    email: '',
    password: '',
    isSigningIn: false,
    settings: {},
    web_address: '',
    siteKey: '',
    captchaEnabled: true
  }),
  beforeRouteEnter (to, from, next) {
    store.dispatch('app/getGeneralInfo')
      .then((value) => {
        next(vm => {
          vm.settings = value
          vm.web_address = value.web_address
          vm.siteKey = value.captcha_public_key
          vm.captchaEnabled = value.captcha_enabled
        })
      })
  },
  computed: {
    sanitizeUrl () {
      return sanitizeUrl(this.web_address)
    },
    validateLogin () {
      if (this.captchaEnabled) {
        return this.$validator.errors.any() || !this.recaptchaVerified || this.isSigningIn
      }
      return this.$validator.errors.any() || this.isSigningIn
    }
  },
  beforeCreate () {
    SkinHelper.LoginSkin()
  },
  methods: {
    ...mapActions('app', {
      login: 'login',
      getGeneralInfo: 'getGeneralInfo'
    }),
    loginUser () {
      this.$validator.validateAll()
        .then(valRes => {
          if (valRes) {
            const credentials = {
              email: this.email,
              password: this.password,
              recaptcha: this.captchaToken
            }
            this.isSigningIn = true
            const loader = this.$loading.show()
            this.login(credentials)
              .then(value => {
                if (!value.code && value.code === ResponseCodes.CODE_UNAUTHORIZED) {
                  Messenger.showWarning(value.message)
                  this.isSigningIn = false
                } else {
                  this.$router.push({ name: 'stats' })
                    .then(() => { this.isSigningIn = true })
                }
              })
              .catch(() => {
                this.isSigningIn = false
                Messenger.showWarning('Oturumunuz açılamadı, e-posta, şifre ve robot doğrulamasını kontrol ediniz.\n' +
                'Hatasız giriş yatığınızı düşünüyosanız sistem yöneticinize başvurunuz.')
              })
              .finally(() => loader.hide())
          }
        })
    },
    markRecaptchaAsVerified (response) {
      // console.log(response)
      this.captchaToken = response
      this.recaptchaVerified = true
    },
    getUser () {
      Auth.getUser()
        .then(user => { this.user = user })
        .catch(err => Messenger.showError(err.message))
    }
  }
}
</script>

<style lang="scss">

</style>
