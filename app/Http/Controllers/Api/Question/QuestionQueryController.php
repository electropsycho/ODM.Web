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


use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

/**
 * Class QuestionQueryController
 * @package App\Http\Controllers\Api\Question
 */
class QuestionQueryController extends Controller
{
    /**
     * Bazı hatırlatmalar;
     * Question::ATTRIBUTELESS = 0; // SIFATSIZ
     * Question::UGLY = 1;          // ÇİRKİN
     * Question::BAD = 2;           // KÖTÜ
     * Question::GOOD = 3;          // İYi
     */


    /**
     * Bu fonksiyon biraz karışık görünebilir
     * ancak özünde dataTables için gerekli sorgulamalardan başka birşey yoktur
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function getQuestionList(Request $request): JsonResponse
    {
        $branchId = $request->input('branch_id');
        $classLevel = $request->input('class_level');
        $status = $request->input('question_status');
        $isDesignRequired = json_decode($request->input('is_design_required'), true);


        //TODO başlangıç ve bitiş tarihlerine göre de sınırlama yapılabilir
        $startData = $endDate = null;

        $user = Auth::user();
        $table = DB::table('questions as q')
            ->join('learning_outcomes as l', 'l.id', '=', 'q.learning_outcome_id')
            ->join('branches as b', 'b.id', '=', 'q.lesson_id')
            ->leftJoin('users as u', 'u.id', '=', 'q.creator_id') //TODO: Öksüz soruların durumu tartşılacak
            ->select('q.id',
                'q.creator_id',
                DB::raw('l.id as lo_id'),
                'l.code',
                'u.full_name',
                DB::raw('b.name as branch_name'),
                'q.status',
                'q.created_at');

        $this->checkClassLevel($classLevel, $table);
        // Kullanıcı admin değilse sadece kendi soruları listelensin ;-)
        if (!$user->isAn('admin')) {
            $table->where('u.id', Auth::id());
//                ->where('q.status', '!=', Question::NOT_MUST_ASKED);
            // Shortcut kuralı ve aynı zamanda and operatörü true & true = true olacağı için
            // status != 3 and status = 3 false döndürecek ve sonuç vermeyecektir
            $this->checkStatus($status, $table);
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
        if ($user->isA('admin')) {
            if (isset($branchId)) {
                $table->where('q.lesson_id', $branchId);
            }
            $this->checkStatus($status, $table);
        }

        if (isset($isDesignRequired)) {
            $table->where('q.is_design_required', $isDesignRequired);
        }

        return Datatables::of($table)
            ->orderColumn(
                'branch_name',
                'full_name',
                'created_at')
            ->editColumn('created_at', function ($a) {
                Carbon::setLocale("tr-TR");
                $d = strtotime($a->created_at) > 0 ? with(new Carbon($a->created_at))->formatLocalized("%d.%m.%Y") : '';
                return $d;
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(q.created_at,'%d.%m.%Y') like ?", ["%{$keyword}%"]);
            })
            ->make(true);
    }

    /**
     * @param $status
     * @param Builder $table
     */
    private function checkStatus($status, Builder $table): void
    {
        if (isset($status)) {
            $table->where('q.status', $status);
        }
    }

    /**
     * @param $classLevel
     * @param Builder $table
     */
    private function checkClassLevel($classLevel, Builder $table): void
    {
        //Sınıfa göre süzdürme yapılabilir
        if (isset($classLevel)) {
            $table->where('l.class_level', $classLevel);
        }
    }
}
