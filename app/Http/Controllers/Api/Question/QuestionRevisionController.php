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

namespace App\Http\Controllers\Api\Question;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\Question;
use App\Models\QuestionRevisions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionRevisionController extends ApiController {

    public function create($id, Request $request) {
        $validationResult = $this->apiValidator($request, [
            'comment' => "required",
            'question_file' => 'required|mimes:pdf|max:1024'
        ]);
        if ($validationResult) {
            return response()->json($validationResult,422);
        }

        $question_rev = $request->all();
        $comment = $question_rev["comment"];
//        $title = $question_rev["title"];
        $question_file = $question_rev["question_file"];
        $question = Question::findOrFail($id);
        $path = $question->content_url;

        $rev_count = $question->revisions()
            ->count();

        try {
            DB::beginTransaction();
            $rev = new QuestionRevisions([
                "question_id" => $id,
                "title" => "Gözden geçirme " . ($rev_count + 1),
                "comment" => $comment
            ]);
            $rev->save();
            Question::where('id', $id)
                ->where('status', Question::NEED_REVISION)
                ->update(['status' => Question::REVISION_COMPLETED]);
            // TODO adminlere veya komisyona mail atmak gerekebilir
            Storage::copy($path, "temp/".$path);
            Storage::delete($path);
            Storage::put($path, file_get_contents($question_file->getPathName()));
            DB::commit();
            return response()->json($question->revisions(), 201);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            Storage::move("temp/".$path, $path);
            return response()->json([ResponseKeys::MESSAGE=> "Revizyon kaydı başarısız!",
                ResponseKeys::EXCEPTION => $this->apiException($exception)], 500);
//            return response()->json([ResponseKeys::EXCEPTION => "Revizyon kaydı başarısız!"],500);
        }
    }

    public function findByQuestionId($id) {
        $revisions = QuestionRevisions::where("question_id", "=", $id)
            ->select("id", "title", "comment", "created_at as date")
            ->get();
        return response()->json($revisions);
    }

}
