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

import Vue from 'vue'
import VeeValidate, { Validator } from 'vee-validate'
// eslint-disable-next-line camelcase
// import { required, min, max, alpha_spaces, email, confirmed, size } from 'vee-validate/dist/rules.esm'
import tr from 'vee-validate/dist/locale/tr'

const dictionary = {
  tr: {
    attributes: {
      email: 'E-posta adresi',
      password: 'Şifre',
      retypePassword: 'Şifre tekrarı',
      full_name: 'Ad Soyad',
      phone: 'Telefon numarası',
      branchName: 'Branş/Ders',
      selectedBranch: 'Branş/Ders',
      inst: 'Kurum',
      branch: 'Alan/Ders',
      code: 'Kod',
      content: 'İçerik',
      questionFile: 'Soru dosyası',
      selectedClassLevel: 'Sınıf seviyesi',
      learningOutCome: 'Kazanım',
      itemRoot: 'Soru kökü',
      searchedContent: 'Aranacak metin',
      difficulty: 'Zorluk seviyesi',
      correctAnswer: 'Doğru cevap',
      comment: 'Gözden geçirme yorumu',
      instName: 'Okul/Kurum adı',
      instId: 'Okul/Kurum kodu',
      unit: 'Birim/Şube',
      evalPoint: 'Değerlendirme puanı',
      evalComment: 'Değerlendirme yorumu',
      examTitle: 'Sınav adı/başlığı',
      examPurpose: 'Sınav genel amacı',
      plannedDate: 'Planlan tarih',
      minElectorCount: 'Min. puanlayıcı',
      maxElectorCount: 'Maks. puanlayıcı',
      mre: 'Manuel hesaplama için gereken geçerli değerlendirme sayısı',
      evaluators: 'Değerlendirici/Puanlayıcı'
    }
  }
}

// Add the rules you need.
// Validator.extend('required', required)
// Validator.extend('min', min)
// Validator.extend('max', max)
// Validator.extend('alpha_spaces', alpha_spaces)
// Validator.extend('email', email)
// Validator.extend('confirmed', confirmed)
// Validator.extend('size', size)
// Merge the messages.

Vue.use(VeeValidate)

Validator.localize('tr', tr)
Validator.localize(dictionary)

Validator.extend('verify_password', {
  getMessage: field => 'Şifre en az: 1 büyük harf, 1 küçük harf, 1 rakam, ve bir özel karakter içermelidir(. , + @ # % vs.)',
  validate: value => {
    const strongRegex = new RegExp('^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[#$^+=!*()@%&.]).{8,16}')
    return strongRegex.test(value)
  }
})

// Validator.extend('is_bigger', {
//   validate: (value, [otherValue]) => {
//     return Number(value) > Number(otherValue)
//   },
//   getMessage: field => field + ' değeri asgari/min. değerden büyük ya da eşit olmalıdır'
// }, {
//   hasTarget: true
// })
//
// Validator.extend('is_small', {
//   validate: (value, [otherValue]) => {
//     return Number(value) < Number(otherValue)
//   },
//   getMessage: field => field + ' değeri azami/maks. değerden küçük ya da eşit olmalıdır'
// }, {
//   hasTarget: true
// })
