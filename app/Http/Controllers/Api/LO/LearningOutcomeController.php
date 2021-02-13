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

namespace App\Http\Controllers\Api\LO;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\Utils\ResponseCodes;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\LearningOutcome;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Kazanım kayıt ve listeleme kontrolcüsü
 * Class LearningOutcomeController
 * @package App\Http\Controllers\Api\LO
 */
class LearningOutcomeController extends ApiController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $validationResult = $this->apiValidator($request, [
            'branch_id' => 'required',
            "class_level" => "required",
            "code" => "required",
            "content" => "required"
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        $data = $request->only("branch_id", "code", "class_level", "content", "description");

        try {
            DB::beginTransaction();
            $branch = new LearningOutcome($data);
            $branch->save();
            DB::commit();
            return response()->json([ResponseKeys::MESSAGE => "Kazanım kayıt işlemi başarılı."], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validationResult = $this->apiValidator($request, [
            'code' => 'required',
            'class_level' => 'required',
            "content" => "required"
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        try {
            DB::beginTransaction();
            $lo = LearningOutcome::findOrFail($id);
            $lo->code = $request->input("code");
            $lo->class_level = $request->input("class_level");
            $lo->content = $request->input("content");
            $lo->description = $request->input("description");
            $lo->save();
            DB::commit();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => "Kazanım güncelleme işlemi başarılı."
            ], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        $res = LearningOutcome::destroy($id);
        if ($res)
            return response()->json([ResponseKeys::MESSAGE => "Kazanım silme işlemi başarılı."], 200);
        return response()->json([ResponseKeys::MESSAGE => "Kazanım silme işlemi başarısız."], 450);
    }


    public function findById($id)
    {
        $lo = LearningOutcome::where('learning_outcomes.id', $id)
            ->join("branches as b", "learning_outcomes.branch_id", "=", "b.id")
            ->select("learning_outcomes.id",
                DB::raw("b.name as branch_name"),
                "class_level",
                "learning_outcomes.code",
                "content",
                "learning_outcomes.description")
            ->first();


        return response()->json($lo);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findByContentAndLessonIdAndClassLevel(Request $request)
    {

        $validationResult = $this->apiValidator($request, [
            'lesson_id' => 'required',
            "content" => "required",
            "class_level" => "required",
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        $content = $request->input("content");
        $lesson_id = $request->input("lesson_id");
        $class_level = $request->input("class_level");

        $lo = $this->findLO($lesson_id, $class_level, $content);
        return response()->json($lo, 200);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function searchWithPaging(Request $request): JsonResponse
    {
        $content = $request->query("searched_content");
        $branchId = $request->query("branch_id");
        $classLevel = $request->query("class_level");
        $perPage = $request->query("per_page");
        if (!$perPage) { $perPage = 18; }

        $query = DB::table("learning_outcomes as lo")
            ->join("branches as b", "lo.branch_id", "=", "b.id")
            ->orderBy('b.name')
            ->orderBy('lo.class_level')
            ->orderBy('lo.code')
            ->select("lo.id",
                DB::raw("b.name as branch_name"),
                "lo.class_level",
                "lo.code",
                "lo.content",
                "lo.description");
        if ($content) {
            $query->where(static function ($wq) use ($content) {
                $wq->where("lo.code", "like", "%" . $content . "%")
                    ->orWhere("lo.content", "like", "%" . $content . "%");
            });
        }
        if ($branchId) {
            $query->where("b.id", $branchId);
        }
        if ($classLevel) {
            $query->where("lo.class_level", $classLevel);
        }
        $result = $query->paginate($perPage);

        return response()->json($result);
    }

    /**
     * @param $size
     * @return JsonResponse
     */
    public function getLastSavedRecords($size) {
        !isset($size) ? $size = 18 : null;
        $result = DB::table("learning_outcomes as lo")
            ->join("branches as b", "lo.branch_id", "=", "b.id")
            ->where("lo.created_at", "!=", null)
            ->orderBy("lo.created_at", "desc")
            ->take($size)
            ->select("lo.id",
                DB::raw("b.name as branch_name"),
                "lo.class_level",
                "lo.code",
                "lo.content",
                "lo.description")
            ->get();
        return response()->json($result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function findBy(Request $request)
    {
        $validationResult = $this->apiValidator($request, [
            "content" => "required",
            "class_level" => "required",
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        $content = $request->query("content");
        $lesson_id = $request->query("lesson_id");
        $class_level = $request->query("class_level");

        if (!isset($lesson_id)) {
            $lesson_id = Auth::user()->branch_id;
        }

        $lo = $this->findLO($lesson_id, $class_level, $content);
        return response()->json($lo, 200);
    }

    /**
     * @param $lesson_id
     * @param $class_level
     * @param $content
     * @return mixed
     */
    protected function findLO($lesson_id, $class_level, $content)
    {
        $lo = LearningOutcome::where([
            ["branch_id", "=", $lesson_id],
            ["class_level", "=", $class_level]
        ])
            ->where(function ($query) use ($content) {
                $query->where("content", "like", "%" . $content . "%")
                    ->orWhere("code", "like", "%" . $content . "%");
            })->select("id", "code", "content")
            ->get();
        return $lo;
    }
}
