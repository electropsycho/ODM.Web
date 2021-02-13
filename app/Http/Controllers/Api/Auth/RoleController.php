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
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Silber\Bouncer\Database\Role;

class RoleController extends Controller
{
  public function getAllRoles() {
    $roles = Role::all("id", "name", "title");
    return response()->json($roles);
  }

  public function getUserRoles($userId) {
//    $roles = User::findOrFail($userId)->roles()->select(["roles.id","name", "title"])->get();
    $roles = User::findOrFail($userId)->getRoles();
    return response()->json($roles);
  }

    public function getCurrentUserRoles() {
//      $userId = Auth::user()->id;
//      $roles = User::findOrFail($userId)->roles()->select(["roles.id","name", "title"])->get();
      $roles = Auth::user()->getRoles();
      return response()->json($roles);
    }



    public function allowRoleToUser(Request $request) {

  }

  public function disallowRoleFromUser(Request $request) {

  }
}
