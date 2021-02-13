<?php
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

namespace App\Http\Controllers\Api\Auth;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Silber\Bouncer\Database\Ability;


class PermissionController extends Controller
{

  public function getAllPermissions() {
    $abilities = Ability::all("id","name", "title");
    return response()->json($abilities, 200);
  }

  public function getCurrentUserPermissions() {
    return auth()->user()->getAbilities();
//    return $permissions;
  }

  public function getRolePermissions($roleId) {

  }

  public function addPermissionToRole(Request $request, $roleId) {

  }

  public function addPermissionToUser(Request $request, $userId) {

  }

  public function removePermissionFromRole(Request $request) {

  }

  public function removePermissionFromUser(Request $request) {

  }
}
