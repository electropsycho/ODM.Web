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

import Swal from 'admin-lte/plugins/sweetalert2/sweetalert2.all.min'
import router from '../router'
import Constants from './constants'
import pace from 'pace-progressbar'
import OverlayHelper from './OverlayHelper'

const axios = require('axios').default

const isHandlerEnabled = (config = {}) => {
  return !(!config?.errorHandler)
}

const handleError = (error) => {
  if (isHandlerEnabled(error.config)) {
    // VanillaToasts.create({
    //   title: `Request failed: ${error.response.status}`,
    //   text: `Unfortunately error happened during request: ${error.config.url}`,
    //   type: 'warning',
    //   timeout: TIMEOUT
    // })
  }
  return Promise.reject({ ...error })
}

const domain = document.querySelector('meta[name="base-url"]').getAttribute('content')
const http = axios.create({
  baseURL: `${domain}/api`
})
// istek interceptor u ekleniyor
http.interceptors.request.use((config) => {
  // Her istekte gönderilercek http başlıkları ayarlanıyor
  // eslint-disable-next-line no-undef
  config.headers['X-CSRF-TOKEN'] = Laravel.csrfToken
  const token = localStorage.getItem(Constants.ACCESS_TOKEN)
  if (token !== null) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, (error) => {
  // pace.stop()
  return Promise.reject(error)
})

http.interceptors.response.use((response) => {
  // pace.stop()
  return response
}, (error) => {
  if (error.config?.errorHandle === false) {
    return Promise.reject(error)
  }
  if (error.response.status === 401) {
    Swal.fire({
      title: 'Oturum süreniz dolmuştur',
      text: 'Kullanıcı giriş sayfasına yönlendirileceksiniz',
      icon: 'warning',
      confirmButtonText: 'Tamam'
    }).then((value) => {
      pace.stop()
      router.push({ name: 'login' })
    })
  } else {
    pace.stop()
    return Promise.reject(error)
  }
})

http.interceptors.response.use((response) => {
  // pace.stop()
  return response
}, async (error) => {
  if (error.response.status === 422) {
    const validationMessages = Object.entries(error.response.data)
      .map(entry => entry[1])
      .join('<br>')
    // eslint-disable-next-line no-unused-expressions
    OverlayHelper.close()
    const msg = `Aşağıdaki veri doğrulama hataları giderilmelidir.<br><b>${validationMessages}</b>`
    await Swal.fire({
      title: 'Veri doğrulama hatası!',
      html: msg,
      icon: 'warning',
      confirmButtonText: 'Tamam'
      // timer: 3000
    })
    pace.stop()
  } else if (error.response.status === 500) {
    const msg = 'Maalesef sunucu taraflı bir hata meydana geldi. Lütfen sistem yöneticinize başvurunuz.'
    await Swal.fire({
      title: 'Sistem hatası!',
      html: msg,
      icon: 'error',
      confirmButtonText: 'Tamam'
    })
    pace.stop()
  }
  return Promise.reject(error)
})

// window.axios = http

export default http
