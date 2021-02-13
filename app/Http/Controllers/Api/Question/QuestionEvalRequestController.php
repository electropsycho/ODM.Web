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

use App\Events\NewEvalReqEvent;
use App\Events\QuestionEvalCalculateRequired;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Utils\ResponseCodes;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Logic\Question\EngineConstants;
use App\Logic\Question\QuestionEvalEngine;
use App\Models\Branch;
use App\Models\Question;
use App\Models\QuestionEvalRequest;
use App\Models\Setting;
use App\Rules\CheckElectorCount;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\JsonResponse;
use Yajra\DataTables\DataTables;

class QuestionEvalRequestController extends ApiController
{
    /**
     * Soru değerlendirme isteğini ilgili değerlendiricilere ekleyen api
     * @param $questionId
     * @param Request $request
     * @return JsonResponse
     */
    public function create($questionId, Request $request): ?JsonResponse
    {
        $setting = Setting::select('max_elector_count', 'min_elector_count')->first();

        $validationResult = $this->apiValidator($request, [
            'electors' => "required|array|min:$setting->min_elector_count|max:$setting->max_elector_count",
            'minRequiredElection' => "required|numeric|min:$setting->min_elector_count|max:$setting->max_elector_count"
        ]);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        $electors = $request->input('electors');
        $minRequiredElection = $request->input('minRequiredElection');
        // Değerlendirmeler bir kod vererek gruplamaya çalışıyoruz
        // Yani bir değerlendirme isteğinde bir grup değerlendiriciye değerlendirme yaptırıyoruz
        // Bu değerlendirmeleri de grup kodu vasıtasıyla ayrıştırıyoruz.
        // Aşırı bir örnek olsa da bir soruyu iki farklı değerlendirici grubuna değerlentirtmek
        // isteyebiliriz bu durumda grup değerlendirmelerinin karışmasının önüne geçmiş olacağız.
        $code = strtoupper(Str::random(6));
        try {
            DB::beginTransaction();
            $isSendEmailToElectors = Setting::first()->will_the_electors_be_emailed;
            $lessonId = Question::find($questionId)->lesson_id;
            foreach ($electors as $elector) {
                $qevalReq = new QuestionEvalRequest([
                    'elector_id' => $elector['id'],
                    'creator_id' => Auth::id(),
                    'question_id' => $questionId,
                    'code' => $code
                ]);
                $qevalReq->save();
            }
            Question::where('id', $questionId)
//                ->where('status', Question::WAITING_FOR_ACTION)
//                ->orWhere('status', Question::REVISION_COMPLETED)
                ->update([
                    'status' => Question::IN_ELECTION,
                    'min_required_election' => $minRequiredElection
                ]);
            DB::commit();
            //Değerlendiricilere mail atılsın mı?
            if ($isSendEmailToElectors) {
                //Değerlendiriciye mail atılıyor
                foreach ($electors as $elector) {
                    event(new NewEvalReqEvent($lessonId, $elector['id']));
                }
            }
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => 'Değerlendirme isteği ilgili değerlendircilere iletildi.'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    /**
     * Olası bir değerlendirme isteği silinmesinde aynı gruba yeni
     * değerlendirici ekleyebilen api
     * @param $questionId
     * @param $code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addElectors($questionId, $code, Request $request): ?JsonResponse
    {
        $validationResult = $this->apiValidator($request, [
            'electors' => [
                'required',
                'array',
                'min:1',
                new CheckElectorCount($questionId, $code)],
        ]);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }
        //$code = $request->input('code');
        $electors = $request->input('electors');
//        $questionId = $request->input('question_id');
        try {
            DB::beginTransaction();
            $isSendEmailToElectors = Setting::first()->will_the_electors_be_emailed;
            foreach ($electors as $elector) {
                $qevalReq = new QuestionEvalRequest([
                    'elector_id' => $elector,
                    'creator_id' => Auth::id(),
                    'question_id' => $questionId,
                    'code' => $code
                ]);
                $qevalReq->save();
            }
            DB::commit();
            // Değerlendiricilere mail atılsın mı?
            if ($isSendEmailToElectors) {
                $lessonId = Question::find($questionId)->lesson_id;
                foreach ($electors as $elector) {
                    event(new NewEvalReqEvent($lessonId, $elector));
                }
            }
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => 'Gruba yeni değerlendirici(lerin) kaydı başarıyla yapıldı.'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    public function delete($id)
    {
        try {
            $qer = QuestionEvalRequest::findOrFail($id);
            $questionId = $qer->question_id;
            if (isset($qer->point)|| $qer->point <= 0) {
                DB::beginTransaction();
                $qer->delete();
                $qerCount = QuestionEvalRequest::where('code', $qer->code)->count();
                if ($qerCount === 0) {
                    Question::where('id', $questionId)
                        ->update(['status' => Question::WAITING_FOR_ACTION]);
                }
                DB::commit();
                // Olası değerlendirme silinmesi durumunda yeniden hesaplama yapılması gerekir
                // event(new QuestionEvalCalculateRequired($qer));
                return response()->json([
                    ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                    ResponseKeys::MESSAGE => 'Değerlendirme isteği başarıyla silindi.'
                ], 200);
            }
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_WARNING,
                ResponseKeys::MESSAGE => 'Değerlendirme isteği silinemedi çünkü puanlama yapılmış.'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    public function deleteByCode($questionId, $code)
    {
        try {
            DB::beginTransaction();
            QuestionEvalRequest::where('code', $code)
                ->where('question_id', $questionId)
                ->delete();
            Question::where('id', $questionId)
                ->update(['status' => Question::WAITING_FOR_ACTION]);
            DB::commit();
            // Olası değerlendirme silinmesi durumunda yeniden hesaplama yapılması gerekir
            // QuestionEvalRequest::where('code', $code);
            // event(new QuestionEvalCalculateRequired($qer));
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => 'Bu soruya ait tüm aktif değerlendirme istekleri başarıyla silindi.'
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    /**
     * Değerlendiricinin isteğe yanıt vermesini sağlayan api
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateQuestionEval(Request $request): ?JsonResponse
    {
        $validationResult = $this->apiValidator($request, [
            'qer_id' => 'required', // QuestionEvaluationRequest Id
            'point' => 'required|min:1|max:5',
            'comment' => 'required|max:1000'
        ]);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        $qerId = $request->input('qer_id');
        $point = $request->input('point');
        $comment = $request->input('comment');

        try {
            $qer = QuestionEvalRequest::findOrFail($qerId);
            DB::beginTransaction();
            $qer->point = $point;
            $qer->comment = $comment;
            $qer->save();
            DB::commit();
            // Event fırlatarak soru puanlama ve hesaplamaları yaptırılıyor
            event(new QuestionEvalCalculateRequired($qer));
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => 'Değerlendirmeniz kaydedilmiştir. Teşekkür ederiz.'], 201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    /**
     * Bu fonksiyon manuel hesaplama için kullanılacaktır
     * @param $questionId
     * @param $code
     * @return JsonResponse
     */
    public function manualCalculate($questionId, $code): ?JsonResponse
    {
        try {
            $questionId = (int)$questionId;
            $res = QuestionEvalEngine::getInstance()
                ->setQuestionId($questionId)
                ->setCode($code)
                ->setCalculationType(EngineConstants::MANUAL)
                ->calculate();
            if ($res) {
                return response()->json([
                    ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                    ResponseKeys::MESSAGE => 'Bu soruya ait değerlendirme puanı elle hesaplanarak değerlendirme kapatılmıştır.'
                ], 200);
            }
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_WARNING,
                ResponseKeys::MESSAGE => 'Maalesef yeterli puanlama yapılmamıştır.'
            ], 200);
        } catch (\Exception $exception) {
            return response()->json($this->apiException($exception), 500);
        }
    }

    public function findByQuestionId($questionId, Request $request)
    {
        $isOpen = $request->query('is_open');
        $query = DB::table('question_eval_requests as qer')
            ->join('users as u', 'u.id', '=', 'qer.elector_id')
            ->join('branches as b', 'b.id', '=', 'u.branch_id')
            ->where('qer.question_id', $questionId)
            ->select('qer.id', 'qer.elector_id',
                DB::raw('CONCAT(u.full_name, " - ", b.name) as full_name'),
                'u.branch_id',
                'qer.code',
                'qer.comment',
                'qer.point',
                'qer.is_open',
                'qer.updated_at as date');
        if($isOpen) {
            $query->where('qer.is_open', '=', $isOpen);
        }

        return response()->json($query->get());
    }

    public function getQuestionRequestsList(Request $request)
    {
        // TODO Branch id sosyalciler ve Adminler için parametrik olacak ve istemciden gelecek
        // TODO Durum bilgisi kullanıcıdan alınacak

        $branchId = $request->input('branch_id');

        //TODO başlangıç ve bitiş tarihlerine göre de sınırlama yapılabilir
//        $startData = $endDate = null;

        $user = Auth::user();
        $table = DB::table('question_eval_requests as qer')
            ->join('questions as q', 'q.id', '=', 'qer.question_id')
            ->join('branches as b', 'b.id', '=', 'q.lesson_id')
            ->join('users as elector', 'elector.id', '=', 'qer.elector_id')
            ->join('users as creator', 'creator.id', '=', 'qer.creator_id')
            ->select('qer.id',
                'qer.question_id',
                'qer.creator_id',
                'qer.elector_id',
                DB::raw('creator.full_name as creator_name'),
                DB::raw('elector.full_name as elector_name'),
                'qer.code',
                'qer.point',
                'qer.comment',
                DB::raw('b.name as branch_name'),
                'qer.created_at');

        // Kullanıcı admin değilse çöp sorular saklansın ve sadece kendi soruları listelensin ;-)
        if (!$user->isAn('admin')) {
            $table->where('qer.elector_id', Auth::id());
            $table->where('qer.is_open', '=', 1);

            if ($user->branch->code === 'SB') {
                $branchIds = Branch::where('code', 'SB')
                    ->orWhere('code', 'İTA')
                    ->get('id');
                // TODO dersler ve braşlar aslında ayrılmalı idi ya da çoklu branş seçimi mümkün olmalı idi
                // Tedbir amaçlı sosyalcilerin gönderdiği branş kodu bilgisi burada kontrol ediliyor ki başka branşlardan listeleme yapamasınlar
                if ($branchIds->contains($branchId)) {
                    $table->where('q.lesson_id', $branchId);
                }
            }
        }
        // Aslında buna çok gerek yok çünkü kullanıcıyı ve soru foreign key ile
        // birleştirildi hem de üstteki kod parçasında kullanıcı id sine göre süzdürme yapıldı
        // Alttaki kod parçası branş bazlı parametrik süzdürme için eklendi
        if ($user->isAn('admin')) {
            if (isset($branchId)) {
                $table->where('q.lesson_id', $branchId);
            }
        }

        return Datatables::of($table)
            ->orderColumn('elector.full_name', 'qer.created_at')
            ->editColumn('created_at', static function ($a) {
                Carbon::setLocale("tr-TR");
                $d = strtotime($a->created_at) > 0 ? with(new Carbon($a->created_at))->formatLocalized("%d.%m.%Y") : '';
                return $d;
            })
            ->filterColumn('created_at', static function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(qer.created_at,'%d.%m.%Y') like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }
}