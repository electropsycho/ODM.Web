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

import jwtDecode from 'jwt-decode'

export default class JwtUser {
  id = 0;
  instId = 0;
  fullName = '';
  role=null;
  email = '';
  userName = '';
  expirationDate = null;
  issuedDate = null;

  constructor (accessToken) {
    const decodedToken = jwtDecode(accessToken)
    this.id = decodedToken.sub
    this.instId = decodedToken.instId
    this.fullName = decodedToken.name
    this.role = decodedToken.role
    this.email = decodedToken.email
    this.userName = decodedToken.code
    this.expirationDate = new Date(decodedToken.exp)
    this.issuedDate = new Date(decodedToken.iat)
  }
}
