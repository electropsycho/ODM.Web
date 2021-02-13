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

namespace App\Http\Controllers\Api\Branch;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\Utils\ResponseCodes;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends ApiController
{

    public function create(Request $request)
    {

        $validationResult = $this->apiValidator($request, ['name' => 'required|max:50']);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        try {
            $name = $request->input("name");
            $code = $request->input("code");
            $class_levels = $request->input("class_levels");
            $branch = new Branch();
            $checkBranch = Branch::where("name", $name)->first();
            if (isset($checkBranch)) {
                $branch->fill(["name" => $name, "code" => $code, "class_levels" => $class_levels]);
                $branch->save();
            }
        } catch (Exception $exception) {
            return response()->json($this->apiException($exception), 500);
        }
        return response()->json([
            ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
            ResponseKeys::MESSAGE => 'Branş/Ders kayıt işlemi başarılı.'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validationResult = $this->apiValidator($request, ['name' => 'required|max:50']);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        try {
            $name = $request->input("name");
            $code = $request->input("code");
            $class_levels = $request->input("class_levels");
            $branch = Branch::findOrFail($id);
            $branch->name = $name;
            $branch->code = $code;
            $branch->class_levels = $class_levels;
            $branch->save();
        } catch (Exception $exception) {
            return response()->json($this->apiException($exception), 500);
        }
        return response()->json([
            ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
            ResponseKeys::MESSAGE => "Branş/Ders güncelleme işlemi başarılı."
        ], 201);
    }

    public function delete(Request $request, $id)
    {

    }

    public function getBranchesWithStats()
    {
        $branches = DB::table("branches as b")
            ->select("b.name", "b.code",
                DB::raw('(select count(u.id) from users as u where u.branch_id=b.id) as userCount'),
                DB::raw('(select count(q.id) from questions as q where q.lesson_id=b.id) as questionCount'))
            ->get();
        return response()->json($branches);
    }

    public function getBranches()
    {
        $res = Branch::all("id", "name", "code", "class_levels");
        return response()->json($res);
    }
}
