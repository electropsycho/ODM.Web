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

import UserList from '../views/user/UserList'
import EditUser from '../views/user/EditUser'
import PassiveUserList from '../views/user/PassiveUserList'
import User from '../views/user/User'

const userRoutes = [
  {
    path: '/users/list',
    name: 'users',
    component: UserList
  },
  {
    path: '/users/edit_my_info',
    name: 'editMyInfo',
    component: EditUser
  },
  {
    path: '/users/passives/list',
    name: 'passiveUsers',
    component: PassiveUserList
  },
  {
    path: '/users/:id',
    name: 'user',
    component: User
  }
]

export default userRoutes
