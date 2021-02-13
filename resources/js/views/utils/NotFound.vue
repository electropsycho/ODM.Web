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
  <div class="error-page">
    <h2 class="headline text-yellow">
      404
    </h2>

    <div class="error-content">
      <h3><i class="fa fa-warning text-yellow" /> Üzgünüz aradığınız sayfa bulunamadı!</h3>

      <p>
        Aradığınız sayfayı bulamadık :-(
        Belki <router-link :to="{name:'master'}">
          anasayfaya
        </router-link> dönmek isteyebilirsiniz.
      </p>

      <!--      <form class="search-form">-->
      <!--        <div class="input-group">-->
      <!--          <input type="text" name="search" class="form-control" placeholder="Search">-->

      <!--          <div class="input-group-btn">-->
      <!--            <button type="submit" name="submit" class="btn btn-warning btn-flat"><i class="fa fa-search"></i>-->
      <!--            </button>-->
      <!--          </div>-->
      <!--        </div>-->
      <!--        &lt;!&ndash; /.input-group &ndash;&gt;-->
      <!--      </form>-->
    </div>
    <!-- /.error-content -->
  </div>
</template>

<script>
export default {
  name: 'NotFound'
}
</script>

<style>

</style>
