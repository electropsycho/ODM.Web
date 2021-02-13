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

import router from '../router'
import http from '../helpers/axios'
import Constants from '../helpers/constants'
import jwt from 'jwt-decode'

const AuthService = {
  login: (credentials) => {
    return new Promise((resolve, reject) => {
      http.post('/auth/login', credentials)
        .then(response => {
          resolve(response.data)
        })
        .catch((error) => {
          reject(error)
        })
    })
  },
  async logout () {
    await router.push({ name: 'login' })
  },
  check: () => {

  },
  setRoleAndPermissions () {
    return new Promise((resolve, reject) => {
      Promise.all([
        http.get('/users/current/permissions'),
        http.get('/users/current/roles')
      ]).then(([permission, roles]) => {
        resolve({ roles: roles.data, permissions: permission.data })
      })
        .catch(error => {
          // console.log(error)
          reject(error)
        })
    })
  },
  getUser () {
    return new Promise((resolve, reject) => {
      const id = this.getUserId()
      http.get(`/users/${id}`)
        .then(response => resolve(response.data))
        .catch(e => { reject(e) })
    })
  },
  getUserId () {
    const token = localStorage.getItem(Constants.ACCESS_TOKEN)
    if (token) {
      return jwt(localStorage.getItem(Constants.ACCESS_TOKEN)).sub
    }
    return null
  },
  createRegisterRequest (data) {
    return new Promise((resolve, reject) => {
      http.post('/auth/register', data)
        .then(response => {
          resolve(response.data)
        })
        .catch(reason => {
          reject(reason)
        })
    })
  },
  forgetPassword (data) {
    return new Promise((resolve, reject) => {
      http.post('/auth/password/forget', data)
        .then(response => {
          resolve(response.data)
        })
        .catch(reason => {
          reject(reason)
        })
    })
  }
}

export default AuthService
