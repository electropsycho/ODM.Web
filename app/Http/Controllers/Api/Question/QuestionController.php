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
use App\Http\Controllers\Utils\ResponseCodes;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\Branch;
use App\Models\LearningOutcome;
use App\Models\Question;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionController extends ApiController
{
    /**
     * @OA\Post(
     *  path="/api/questions",
     *  tags={"Questions"},
     *  summary="Soru oluşturma api tanımlaması",
     *  security={{ "apiAuth": {} }},
     *  @OA\Response(
     *      response=405,
     *      description="Invalid input"
     *     ),
     *  )
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {

        $validationResult = $this->apiValidator($request, [
            'learning_outcome_id' => 'required',
            'difficulty' => 'required',
            'question_file' => 'required|mimes:pdf|max:1024'
        ]);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        // İstek içindeki tüm gelenleri alalım bi değikene
        $qReq = $request->all();
        $question_file = $qReq["question_file"];
        $path = '';

        try {
            DB::beginTransaction();
            $question = new Question();
            if (!isset($qReq["lesson_id"])) {
                $question->lesson_id = Auth::user()->branch_id;
            } else {
                $question->lesson_id = $qReq["lesson_id"];
            }
            $question->learning_outcome_id = $qReq["learning_outcome_id"];
            $question->difficulty = $qReq["difficulty"];
            $question->correct_answer = $qReq["correct_answer"];
            $question->keywords = $qReq["keywords"];
            $question->is_design_required = json_decode($qReq["is_design_required"], true);
            $question->creator_id = Auth::id();
            $isSaved = $question->save();
            $lo = LearningOutcome::find($qReq["learning_outcome_id"])->code;
            $loCode = str_replace(".", "-", $lo);
            if ($isSaved) {
                $code = Branch::find($question->lesson_id)->code;
                if ($question_file !== null) {
                    // İlginç bir şekile aşağıdaki kod parçası çift satırda yazılmaz ise çalışmıyor
                    $expl = explode(".", $question_file->getClientOriginalName());
                    $ext = end($expl);

                    //Tüm dosyalar ana klasör altındaki storage->app->public altına ekleniyot
                    //Path formatı: public/Ders Kodu/Kazanım kodu-kullanıcı id-soru id-dosya uzantısı
                    //örn: public/DERS_KODU(FEN, İMAT vs.)/T-7-4-2-31-73.pdf
                    $path = 'public/' . $code . '/' . $loCode . $question->creator_id . '-' . $question->id . '.' . $ext;
                    Storage::put($path, file_get_contents($question_file->getPathName()));
                    $question->content_url = $path;
                    $question->save();
                }
            }
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Storage::delete($path);
            return response()->json($this->apiException($exception), 500);
        }
        return response()->json([
            ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
            ResponseKeys::MESSAGE => "Soru ekleme işlemi başarılı."
        ], 201);
    }

    public function findById($id)
    {
//      $question = Question::findOrFail($id);
        $question = DB::table("questions as q")
            ->leftJoin("users as u", "u.id", "=", "q.creator_id")
            ->join("branches as b", "b.id", "=", "q.lesson_id")
            ->join("learning_outcomes as lo", "lo.id", "=", "q.learning_outcome_id")
            ->where("q.id", $id)
            ->select(
                "q.id",
                "q.creator_id",
                "q.lesson_id as branch_id",
                "q.keywords",
                "q.difficulty",
                "q.correct_answer",
                "q.is_design_required",
                "q.status",
                "q.min_required_election",
                DB::raw("CASE
                                WHEN status = 0 THEN 'İşleme alınmamış'
                                WHEN status = 1 THEN 'Değerlendirme aşamasında'
                                WHEN status = 2 THEN 'Sorulamayacak soru'
                                WHEN status = 3 THEN 'Revizyon gerekli'
                                WHEN status = 4 THEN 'Revizyon tamamlanmış'
                                WHEN status = 5 THEN 'Havuza girmiş'
                               END AS status_title"),
                DB::raw("DATE_FORMAT(q.created_at, '%d.%m.%Y') as created_at"),
                DB::raw("CONCAT(lo.code, ' ', lo.content) as learning_outcome"),
                "lo.class_level",
                "u.full_name as creator",
                "b.name as branch",
                DB::raw("(SELECT IF(COUNT(id) >= 1, true, false) FROM question_delete_requests as qdr WHERE qdr.question_id = q.id) as has_delete_request")
            )
            ->first();
        if (isset($question)) {
            return response()->json($question, 200);
        }
        return response()->json([
            ResponseKeys::CODE => ResponseCodes::CODE_NOT_FOUND,
            ResponseKeys::MESSAGE => "Böyle bir soru yok!"
        ], 404);
    }

    public function findByContentAndClassLevelAndBranch(Request $request)
    {
        $validationResult = $this->apiValidator($request, [
            'class_level' => 'required'
        ]);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }
        $user = Auth::user();
        $branch_id = $request->query('branch_id');
        if (!isset($branch_id))
            $branch_id = $user->branch_id;
        $class_level = $request->query('class_level');
        $searched_content = $request->query('searched_content');
        //TODO sosyalciler için düzenleme yapılacak
        if ($user->isAn('admin') || $user->isAn('elector')) {
            $res = DB::select("SELECT q.id, q.creator_id, q.keywords, lo.code, lo.content FROM questions as q
                                INNER JOIN learning_outcomes lo on q.learning_outcome_id = lo.id
                                WHERE lo.class_level = :class_level AND
                                      q.lesson_id = :lesson_id AND
                                      (q.keywords like CONCAT('%', :sc1, '%') ||
                                       lo.content like CONCAT('%', :sc2, '%') ||
                                       lo.code like CONCAT(:sc3, '%'))",
                [
                    "class_level" => $class_level,
                    "lesson_id" => $branch_id,
                    "sc1" => $searched_content,
                    "sc2" => $searched_content,
                    "sc3" => $searched_content,
                ]);
            return response()->json($res, 200);
        }
        //TODO Sosyalciler için bir düzenleme yapılacak
        if ($user->isAn('teacher')) {
            $id = $user->id;
            // PArametre sayısı aynı olmazsa ve eşsiz parametre adı olmazsa hata basıypor
            $res = DB::select("SELECT q.id, q.creator_id, q.keywords, lo.code, lo.content FROM questions as q
                                INNER JOIN learning_outcomes lo on q.learning_outcome_id = lo.id
                                WHERE q.creator_id = :user_id AND
                                      lo.class_level = :class_level AND
                                      q.lesson_id = :lesson_id AND
                                      (q.keywords like CONCAT('%', :sc1, '%') ||
                                       lo.content like CONCAT('%', :sc2, '%') ||
                                       lo.code like CONCAT(:sc3, '%'))",
                [
                    "user_id" => $id,
                    "class_level" => $class_level,
                    "lesson_id" => $branch_id,
                    "sc1" => $searched_content,
                    "sc2" => $searched_content,
                    "sc3" => $searched_content,
                ]);
            return response()->json($res, 200);
        }
        return response()->json([ResponseKeys::MESSAGE => "Hiçbir şey bulamadık!"], 404);

    }

    public function getLastQuestions($size)
    {
        $user = Auth::user();
        if ($user->isAn('admin')) {
            $res = DB::table("questions as q")
                ->join("learning_outcomes as l", "l.id", "=", "q.learning_outcome_id")
                ->orderBy("q.created_at", "desc")
                ->take($size)
                ->select("q.id", "q.creator_id", "q.keywords", "l.code", "l.content")
                ->get();
            return response()->json($res, 200);
        }
        if ($user->isAn('elector')) {
            //TODO sadece benim sorularım diyerek sorgulama yapabilmeli değerlendirici
            $branch_id = $user->branch_id;
            $res = DB::table("questions as q")
                ->join("learning_outcomes as l", "l.id", "=", "q.learning_outcome_id")
                ->orderBy("q.created_at", "desc")
                ->take($size)
                ->select("q.id", "q.creator_id", "q.keywords", "l.code", "l.content");
            if ($user->branch->code === 'SB') {
                $brancIds = Branch::where('code', 'SB')
                    ->orWhere('code', 'İTA')
                    ->get('id');
                $res->whereIn("q.lesson_id", $brancIds);
            }
            return response()->json($res->get(), 200);
        }
        if ($user->isAn('teacher')) {
            $id = $user->id;
            // PArametre sayısı aynı olmazsa ve eşsiz parametre adı olmazsa hata basıypor
            $res = DB::table("questions as q")
                ->join("learning_outcomes as l", "l.id", "=", "q.learning_outcome_id")
                ->where("q.creator_id", $id)
                ->orderBy("q.created_at", "desc")
                ->take($size)
                ->select("q.id", "q.creator_id", "q.keywords", "l.code", "l.content")
                ->get();
            return response()->json($res, 200);
        }
        return response()->json([ResponseKeys::MESSAGE => "Hiçbir şey bulamadık!"], 200);

    }

    public function getFile($id)
    {
        $question = Question::findOrFail($id);
        $filePath = $question->content_url;
        if (isset($filePath)) {
            if (!Storage::exists($filePath)) {
                return response()->json([ResponseKeys::MESSAGE => "Belirttiğiniz soruya ait bir dosya yok!"], 404);
            }
            $pdfContent = Storage::get($filePath);
            $encodedPDF = "data:application/pdf;base64," . base64_encode($pdfContent);
            return response($encodedPDF, 200);
        }
        return response()->json([ResponseKeys::MESSAGE => "Veritabanına dosya yolu kaydı girilmemiş!"], 404);
    }
}
