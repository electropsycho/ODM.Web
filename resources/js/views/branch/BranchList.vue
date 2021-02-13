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
  <page>
    <template v-slot:header>
      <h1>Branş Listesi</h1>
    </template>
    <template v-slot:content>
      <div class="row">
        <div class="col-md-12">
          <div
            v-for="branches in branchGroup"
            class="row"
          >
            <div
              v-for="b in branches"
              :key="b.id"
              class="col-md-4"
            >
              <!-- Widget: user widget style 1 -->
              <div class="card">
                <div class="card-header">
                  <h4>
                    {{ b.name }} - {{ b.code }}
                  </h4>
                </div>
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="card-body">
                  <!-- /.widget-user-image -->
                  <ul class="nav flex-column">
                    <li class="nav-item">
                      <a
                        href="#"
                        @click.prevent
                      >Soru Sayısı <span class="float-right badge bg-primary mt-1">{{ b.questionCount }}</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a
                        href="#"
                        @click.prevent
                      >Öğretmen Sayısı <span class="float-right badge bg-primary mt-1">{{ b.userCount }}</span>
                      </a>
                    </li>
                    <!--                      <li><a href="#">Completed Projects <span class="pull-right badge bg-green">12</span></a></li>-->
                    <!--                      <li><a href="#">Followers <span class="pull-right badge bg-red">842</span></a></li>-->
                  </ul>
                </div>
              </div>
              <!-- /.widget-user -->
            </div>
          </div>
        </div>
      </div>
    </template>
  </page>
</template>

<script>
import BranchService from '../../services/BranchService'
import Messenger from '../../helpers/messenger'
import { MessengerConstants } from '../../helpers/constants'
import chunk from 'lodash/chunk'
import Page from '../../components/Page'

export default {
  name: 'BranchList',
  components: { Page },
  data () {
    return {
      branchGroup: null
    }
  },
  created () {
    this.getBranches()
  },
  methods: {
    getBranches () {
      BranchService.getBranchesWithStats()
        .then(data => {
          this.branchGroup = chunk(data, 3)
        })
        .catch(reason => {
          Messenger.showError(MessengerConstants.errorMessage)
        })
    }
  }

}
</script>

<style>

</style>
