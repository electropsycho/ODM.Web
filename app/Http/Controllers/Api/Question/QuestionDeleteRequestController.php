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
use App\Models\Question;
use App\Models\QuestionDeleteRequest;
use App\Models\QuestionEvalRequest;
use App\Models\QuestionRevisions;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class QuestionDeleteRequestController extends ApiController
{
    public function create($id, Request $request) {
        //TODO GAte tanımlaması yapılacak
        $validationResult = $this->apiValidator($request, [
            'reason' => 'required'
        ]);
        if ($validationResult) {
            return response()->json($validationResult,422);
        }
        $reason = $request->input("reason");
        $question = Question::find($id);
        if ($question === null) {
            return response()->json(
                [
                    ResponseKeys::MESSAGE => "Böyle bir soru yok maalesef!",
                    ResponseKeys::CODE => ResponseCodes::CODE_NOT_FOUND
                ]);
        }
        try {
            DB::beginTransaction();
            $qdr = new QuestionDeleteRequest([
                'question_id' => $id,
                'creator_id' => Auth::id(),
                'learning_outcome_id' =>  $question->learning_outcome_id,
                'comment' => $reason
                ]);
            $qdr->save();
            DB::commit();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => "Soru silme isteği incelenmek üzere tarafımıza ulaşmıştır."
            ]);
        }
        catch (\Exception $exception){
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    public function approveDeleteRequest($id) {
        $fileName='';
        $question=null;
        try {
            $qdr = QuestionDeleteRequest::findOrFail($id);
            $question = Question::findOrFail($qdr->question_id);
            $explodedPath = explode("/", $question->content_url);
            $fileName= end($explodedPath);
            DB::beginTransaction();
            $qdr->update([
                "acceptor_id" => Auth::id(),
                "question_id" => null,
                "comment" => $qdr->comment . " Soru Dosyası:" . $fileName
            ]);
            // Soru silinmeden önce tüm artıklar temizlenmeli
            QuestionEvalRequest::where('question_id', $question->id)
                ->delete();
            QuestionRevisions::where('question_id', $question->id)
                ->delete();
            $question->delete();
            // Soru dosyasını silmek yerine çöpe atıyoruz
            Storage::move($question->content_url, 'public/trash/'.$fileName);
            DB::commit();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => "Soru tümüyle silinmiştir."
            ]);
        }
        catch (\Exception $exception) {
            DB::rollBack();
            if (Storage::exists('public/trash/'.$fileName)) {
                Storage::move('public/trash/'.$fileName, $question->content_url);
            }
            return response()->json($this->apiException($exception), 500);
        }
    }

    public function delete($question_id,$id) {
        try {
            $qdr = QuestionDeleteRequest::findOrFail($id);
            DB::beginTransaction();
            $qdr->delete();
            DB::commit();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => "Soru silme isteği silinmiştir."
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json($this->apiException($exception), 500);
        }
    }

    public function getDeleteRequests() {
        //Gate tanımlamaları ile daha sistematik çözülebilirdi
        $user = Auth::user();
        if($user->isAn("admin")) {
            $res = $this->getTable();

            return $this->buildDataTable($res);
        }

        $res = $this->getTable()
            ->where('users.id', '=', Auth::id());
        return $this->buildDataTable($res);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $table
     * @return mixed
     * @throws \Exception
     */
    private function buildDataTable(Builder $table)
    {
        return Datatables::of($table)
            ->orderColumn(
                "full_name",
                "created_at")
            ->editColumn('created_at', function ($a) {
                Carbon::setLocale("tr-TR");
                $d = strtotime($a->created_at) > 0 ? with(new Carbon($a->created_at))->formatLocalized("%d.%m.%Y") : '';
                return $d;
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(created_at,'%d.%m.%Y') like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    private function getTable(): Builder
    {
        $res = DB::table('users')
            ->join(DB::raw('question_delete_requests as qdr'), 'qdr.creator_id', '=', 'users.id')
            ->join(DB::raw('learning_outcomes as lo'), 'lo.id', '=', 'qdr.learning_outcome_id')
            ->leftJoin(DB::raw('users as um'), 'qdr.acceptor_id', '=', 'um.id')
            ->where("users.deleted_at", "=", null)
            ->select(
                'qdr.id',
                'qdr.question_id',
                'qdr.comment',
                'users.full_name',
                DB::raw("CONCAT(lo.code, ' ', lo.content) as learning_outcome"),
                DB::raw('um.full_name as acceptor_name'),
                'qdr.created_at');
        return $res;
    }
}