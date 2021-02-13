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


/**
 *  Bu yazılım Elektrik Elektronik Teknolojileri Alanı/Elektrik Öğretmeni Hakan GÜLEN tarafından geliştirilmiş olup
 *  geliştirilen bütün kaynak kodlar
 *  Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0) ile lisanslanmıştır.
 *   Ayrıntılı lisans bilgisi için https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode.tr sayfasını ziyaret edebilirsiniz.2019
 */

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\ApiController;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class UserQueryController extends ApiController
{

    /**
     * Bütün kulanıcıları Jquery.Datatables() formatında gösteren api fonk.
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getUsers(Request $request)
    {
        $roleId = $request->input('role_id');
        $branchId = $request->input('branch_id');

        $query = DB::table('users as u')
            ->leftJoin('branches as b', 'u.branch_id', '=', 'b.id')
            ->leftJoin('institutions as i', 'i.id', '=', 'u.inst_id')
            ->leftJoin(DB::raw('users as um'), 'u.activator_id', '=', 'um.id')
            ->where("u.deleted_at", "=", null)
            ->select(
                'u.id',
                DB::raw('i.name as inst_name'),
                DB::raw('b.name as branch_name'),
                'u.full_name',
                DB::raw('um.full_name as activator_name'),
                'u.created_at',
                'u.phone');
        if ($branchId) {
            $query->where('u.branch_id', $branchId);
        }
        if($roleId) {
            $query->join('assigned_roles as as', 'as.entity_id', '=', 'u.id')
                ->where('as.role_id', $roleId);
        }

        return Datatables::of($query)
            ->orderColumn(
                "full_name",
                "branch_name",
                "inst_name",
                "created_at")
            ->editColumn('created_at', function ($a) {
                Carbon::setLocale("tr-TR");
                $d = strtotime($a->created_at) > 0 ? with(new Carbon($a->created_at))->formatLocalized("%d.%m.%Y") : '';
                return $d;
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(users.created_at,'%d.%m.%Y') like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    /**
     * Bütün kulanıcıları Jquery.Datatables() formatında gösteren api fonk.
     * @return mixed
     * @throws \Exception
     */
    public function getPassiveUsers()
    {
        $res = DB::table('users')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoin('institutions', 'institutions.id', '=', 'users.inst_id')
            ->leftJoin(DB::raw('users as um'), 'users.activator_id', '=', 'um.id')
            ->where("users.deleted_at", "!=", null)
            ->select(
                'users.id',
                DB::raw('institutions.name as inst_name'),
                DB::raw('branches.name as branch_name'),
                'users.full_name',
                DB::raw('um.full_name as activator_name'),
                'users.created_at',
                'users.phone');

        return Datatables::of($res)
            ->orderColumn(
                "full_name",
                "branch_name",
                "inst_name",
                "created_at")
            ->editColumn('created_at', function ($a) {
                Carbon::setLocale("tr-TR");
                $d = strtotime($a->created_at) > 0 ? with(new Carbon($a->created_at))->formatLocalized("%d.%m.%Y") : '';
                return $d;
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(users.created_at,'%d.%m.%Y') like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    /**
     * Kullanıcının rolünü getiren api fonk
     * @param $id
     * @return JsonResponse
     */
    public function getUser($id)
    {
        $user = User::with('branch', 'institution')->find($id);
        if (isset($user))
            $user->role = $user->getRoles()->first();
//            $user->role = DB::table("roles")->where('name', $user->getRoles()[0])->select("id", "name", "title")->first();
        return response()->json($user);
    }

    /**
     * Bu fonksiyon branşa göre değerlendiricileri listeler
     * @param $branchId
     * @return JsonResponse
     */
    public function findElectorByBranchId($branchId): JsonResponse
    {
        $electors = User::whereIs('elector')
            ->whereIn('branch_id', [$branchId, 1, 13])
            ->join('branches as b', 'b.id', '=', 'branch_id')
            ->select('users.id',
                DB::raw('CONCAT(full_name, " - ", b.name) as full_name'),
                'users.branch_id')
            ->get();
        return response()->json($electors);
    }

    public function findByInstitutionIdWithStats($id) {
        $users = User::where('inst_id', $id)
            ->leftJoin('questions as q', 'q.creator_id', '=', 'users.id')
            ->leftJoin('institutions as i', 'i.id', '=', 'users.inst_id')
            ->groupBy(['users.id'])
            ->select('i.name', 'users.full_name', DB::raw('count(q.id) as question_count'))
        ->get();
        return response()->json($users);
    }
}