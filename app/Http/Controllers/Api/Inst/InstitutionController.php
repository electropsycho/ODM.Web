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

namespace App\Http\Controllers\Api\Inst;


use App\Http\Controllers\ApiController;

use App\Http\Controllers\Utils\ResponseCodes;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\Institution;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class InstitutionController extends ApiController
{
    public function create(Request $request)
    {
        $validationResult = $this->apiValidator($request, [
            'id' => 'required|unique:institutions,id|digits_between:6,8',
            'unit_id' => 'required|exists:units,id',
            'name' => 'required',
            'phone' => 'required|size:10'
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        $institutionReq = $request->only('id', 'unit_id', 'name', 'phone');
        $institution = new Institution($institutionReq);
        try {
            DB::beginTransaction();
            $institution->save();
            DB::commit();
            return response()->json([ResponseKeys::MESSAGE => "Okul/Kurum kayıt işlemi başarılı"]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    public function update($id, Request $request) {
        $validationResult = $this->apiValidator($request, [
            'unit_id' => 'required',
            'name' => 'required',
            'phone' => 'required'
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        try {
            DB::beginTransaction();
            Institution::where('id', $id)
                ->update([
                    'unit_id' => $request->get('unit_id'),
                    'name' => $request->get('name'),
                    'phone' => $request->get('phone')
                ]);
            DB::commit();
            return response()->json([
               ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => 'Kurum gücenlleme işlemi başarılı.'
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_ERROR,
                ResponseKeys::MESSAGE => 'Kurum gücenlleme işlemi başarısız oldu!',
                ResponseKeys::EXCEPTION => $exception->getMessage()
            ]);
        }
    }

    public function get($id) {
        $inst = Institution::where('id', $id)->first();
        return response()->json($inst);
    }

    public function findByNameInstitutions(Request $request)
    {
        if (!$request->exists("searchTerm")) {
            return response()->json([
                ResponseKeys::MESSAGE => "Gönderilen veri sunucuyla uyumsuz!"
            ], 406);
        }
        $searchTerm = $request->query('searchTerm');
        $insts = Institution::where('name', 'like', '%' . $searchTerm . '%')->get();
        return response()->json($insts);
    }

    public function getInstitutions()
    {
        $table = DB::table('institutions as i')
            ->join('units as u', 'u.id', '=', 'i.unit_id')
            ->leftJoin('users as us', 'i.id', '=', 'us.inst_id')
            ->leftJoin('questions as q', 'q.creator_id', '=', 'us.id')
            ->select('i.id',
                DB::raw('u.name as unit_name'),
                'i.name',
                'i.phone',
                DB::raw('(SELECT COUNT(us.id) FROM users as us WHERE us.inst_id = i.id) as teacher_count'),
                DB::raw('(SELECT COUNT(q.id) FROM questions as q WHERE q.creator_id = us.id) as question_count'))
            ->groupBy(['i.id']);

        return Datatables::of($table)
            ->make(true);
    }
}
