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
import Router from 'vue-router'
import Login from '../views/auth/Login'
import MasterView from '../views/master/MasterView'
import RegisterRequest from '../views/auth/RegisterRequest'
import NotFound from '../views/utils/NotFound'
// import NewQuestion from '../views/question/NewQuestion'
import UserList from '../views/user/UserList'
import ForgotMyPassword from '../views/auth/ForgotMyPassword'
import Constants from '../helpers/constants'
import User from '../views/user/User'
import NewBranch from '../views/branch/NewBranch'
import BranchList from '../views/branch/BranchList'
import NewLearningOutcome from '../views/learningOutcome/NewLearningOutcome'
import Stats from '../views/info/Stats'
import LearningOutcomeList from '../views/learningOutcome/LearningOutcomeList'
import PassiveUserList from '../views/user/PassiveUserList'
import UnderConstruction from '../views/utils/UnderConstruction'
import AppSettings from '../views/management/AppSettings'
import examRoutes from './examRoutes'
import questionRoutes from './questionRoutes'
import institutionRoutes from './institutionRoutes'
import loRoutes from './loRoutes'
import EditUser from '../views/user/EditUser'
import userRoutes from './userRoute'
// import Stats from '../views/info/Stats'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: 'otomasyon',
  routes: [
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterRequest
    },
    {
      path: '/forgot_my_password',
      name: 'forgotMyPassword',
      component: ForgotMyPassword
    },
    {
      path: '/',
      component: MasterView,
      children: [
        ...examRoutes,
        ...questionRoutes,
        ...institutionRoutes,
        ...loRoutes,
        ...userRoutes,
        {
          path: '',
          name: 'stats',
          component: Stats
        },
        {
          path: '/branches/new',
          name: 'newBranch',
          component: NewBranch
        },
        {
          path: '/branches/list',
          name: 'branchList',
          component: BranchList
        },
        {
          path: 'under_construction',
          name: 'underConstruction',
          component: UnderConstruction
        },
        {
          path: 'app_settings',
          name: 'appSettings',
          component: AppSettings
        }
      ]
    },
    { path: '*', component: NotFound }
  ]
})

// TODO Burada role denetimi yapılacak
router.beforeEach((to, from, next) => {
  if (to.name === 'login' ||
        to.name === 'register' ||
        to.name === 'forgotMyPassword'
  ) {
    next()
  } else {
    const token = localStorage.getItem(Constants.ACCESS_TOKEN)
    if (token == null) {
      next('/login')
    } else {
      next()
    }
  }
})

export default router
