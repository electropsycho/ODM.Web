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

const Swal = require('admin-lte/plugins/sweetalert2/sweetalert2.all.min')

const Messenger = {
  showError (message, callback = null) {
    return new Promise((resolve, reject) => {
      Swal.fire({
        title: 'Hata mesajı!',
        text: message,
        icon: 'error',
        confirmButtonText: 'Tamam'
      })
        .then(value => resolve(value))
        .catch(error => reject(error))
    })
  },
  showInfo (message) {
    return new Promise((resolve, reject) => {
      Swal.fire({
        title: 'Bilgi mesajı!',
        html: message,
        icon: 'info',
        confirmButtonText: 'Tamam'
      })
        .then(value => resolve(value))
        .catch(error => reject(error))
    })
  },
  showSuccess (message) {
    return new Promise((resolve, reject) => {
      Swal.fire({
        title: 'Başarı mesajı!',
        text: message,
        icon: 'success',
        confirmButtonText: 'Tamam'
      }).then(value => resolve(value))
        .catch(reason => reject(reason))
    })
  },
  showWarning (message) {
    return new Promise((resolve, reject) => {
      Swal.fire({
        title: 'Uyarı mesajı!',
        text: message,
        icon: 'warning',
        confirmButtonText: 'Tamam'
      })
        .then(value => resolve(value))
        .catch(error => reject(error))
    })
  },
  showPrompt (message, buttons = null) {
    if (!buttons) {
      buttons = {
        confirmText: 'Evet',
        cancelText: 'Hayır'
      }
    }
    return new Promise((resolve, reject) => {
      Swal.fire({
        title: 'Dikkat!',
        html: message,
        icon: 'warning',
        showCancelButton: true,
        focusCancel: true,
        cancelButtonColor: '#d33',
        confirmButtonText: buttons.confirmText,
        cancelButtonText: buttons.cancelText
      }).then(value => resolve(value))
        .catch(reason => reject(reason))
    })
  },
  showInput (message) {
    return new Promise((resolve, reject) => {
      Swal.fire({
        title: 'Dikkat!',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        focusConfirm: false,
        input: 'text',
        confirmButtonText: 'Tamam',
        cancelButtonColor: '#d33',
        cancelButtonText: 'İptal'
      })
        .then((value) => {
          resolve(value)
        })
        .catch(reason => {
          reject(reason)
        })
    })
  }
}

export default Messenger
