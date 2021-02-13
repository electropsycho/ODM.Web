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

import Constants from '../helpers/constants'

export default {
  install (Vue, options) {
    // Burada İzinler localStorage içine almak sıkıntı yaratabilir
    // TODO: Refactoring gerekli
    Vue.prototype.$can = function (permission) {
      const permissions = JSON.parse(localStorage.getItem(Constants.PERMISSIONS))
      return permissions.filter(p => p.name === permission).length > 0
    }
    // Burada Rolleri localStorage içine almak sıkıntı yaratabilir
    // TODO: Refactoring gerekli
    Vue.prototype.$isInRole = function (role) {
      const roles = JSON.parse(localStorage.getItem(Constants.ROLES))
      if (roles) { return roles.filter(r => r === role).length > 0 }
      return false
    }
  }
}
