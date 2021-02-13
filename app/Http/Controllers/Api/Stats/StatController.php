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

namespace App\Http\Controllers\Api\Stats;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\Branch;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatController extends ApiController
{
    public function getQuestionCounts() {
        // TODO Hardcoded teacher ve elector tanımları const a çekilecek
        $user = Auth::user();
        if ($user->isAn('teacher', 'elector')) {
            $user = Auth::user();
            $totalCountsQuery = DB::table('questions')
                ->select(DB::raw('count(IF(status='.Question::APPROVED.', 1, null)) as approved'),
                    DB::raw('count(IF(status='.Question::REVISION_COMPLETED.', 1, null)) as revision_completed'),
                    DB::raw('count(IF(status='.Question::NEED_REVISION.', 1, null)) as need_revision'),
                    DB::raw('count(IF(status='.Question::IN_ELECTION.', 1, null)) as in_election'),
                    DB::raw('count(IF(status='.Question::WAITING_FOR_ACTION.', 1, null)) as waiting_for_action'),
                    DB::raw('count(IF(status='.Question::NOT_MUST_ASKED.', 1, null)) as not_must_asked'),
                    DB::raw('count(id) as total'));

            // TODO sadece creator_id ye bakmak yeterli olsa gerek
            $ownQuestionCountQuery = DB::table('questions')
                ->where('creator_id', Auth::id())
                ->select(DB::raw('count(IF(status='.Question::APPROVED.', 1, null)) as own_approved'),
                    DB::raw('count(IF(status='.Question::REVISION_COMPLETED.', 1, null)) as own_revision_completed'),
                    DB::raw('count(IF(status='.Question::NEED_REVISION.', 1, null)) as own_need_revision'),
                    DB::raw('count(IF(status='.Question::IN_ELECTION.', 1, null)) as own_in_election'),
                    DB::raw('count(IF(status='.Question::WAITING_FOR_ACTION.', 1, null)) as own_waiting_for_action'),
                    DB::raw('count(IF(status='.Question::NOT_MUST_ASKED.', 1, null)) as own_not_must_asked'),
                    DB::raw('count(id) as own_total'));

            if ($user->branch->code === 'SB') {
                $brancIds = Branch::where('code', 'SB')
                    ->orWhere('code', 'İTA')
                    ->get('id');
                $totalCountsQuery->whereIn('lesson_id', $brancIds);
                $ownQuestionCountQuery->whereIn('lesson_id', $brancIds);
            } else {
                $ownQuestionCountQuery->where('lesson_id', $user->branch_id);
                $totalCountsQuery->where('lesson_id', $user->branch_id);
            }
            $ownQuestionCount = $ownQuestionCountQuery->first();
            $totalCounts = $totalCountsQuery->first();
            $res = (object)array_merge((array)$totalCounts, (array)$ownQuestionCount);
            return response()->json($res);
        }
        if ($user->isAn('admin')) {
            $totalCounts = DB::table('questions')
                ->select(DB::raw('count(IF(status='.Question::APPROVED.', 1, null)) as approved'),
                    DB::raw('count(IF(status='.Question::REVISION_COMPLETED.', 1, null)) as revision_completed'),
                    DB::raw('count(IF(status='.Question::NEED_REVISION.', 1, null)) as need_revision'),
                    DB::raw('count(IF(status='.Question::IN_ELECTION.', 1, null)) as in_election'),
                    DB::raw('count(IF(status='.Question::WAITING_FOR_ACTION.', 1, null)) as waiting_for_action'),
                    DB::raw('count(IF(status='.Question::NOT_MUST_ASKED.', 1, null)) as not_must_asked'),
                    DB::raw('count(id) as total'))
                ->first();
            $ownQuestionCount = DB::table('questions')
                ->where('creator_id', Auth::id())
                ->select(DB::raw('count(IF(status='.Question::APPROVED.', 1, null)) as own_approved'),
                    DB::raw('count(IF(status='.Question::REVISION_COMPLETED.', 1, null)) as own_revision_completed'),
                    DB::raw('count(IF(status='.Question::NEED_REVISION.', 1, null)) as own_need_revision'),
                    DB::raw('count(IF(status='.Question::IN_ELECTION.', 1, null)) as own_in_election'),
                    DB::raw('count(IF(status='.Question::WAITING_FOR_ACTION.', 1, null)) as own_waiting_for_action'),
                    DB::raw('count(IF(status='.Question::NOT_MUST_ASKED.', 1, null)) as own_not_must_asked'),
                    DB::raw('count(id) as own_total'))
                ->first();
            $res = (object)array_merge((array)$totalCounts, (array)$ownQuestionCount);
            return response()->json($res);

        }
        return response()->json();
    }

//    public function getQuestionCountByLO($lo_id) {
//        $loQuestionCount = DB::select("select (IF(is_passed, count(id), 0)) as valid_count,
//                                                    (IF(is_passed = 0, count(id), 0)) as non_valid_count,
//                                                    count(id) as total_count
//                                            from questions
//                                            where learning_outcome_id = :lo_id", ["lo_id" => $lo_id])[0];
//        return response()->json($loQuestionCount);
//    }

    public function getClasses() {
        if (Auth::user()->isAn("teacher", "elector")) {
            $classes = DB::select("select lo.class_level,
                                            (IF(status, count(q.id), 0)) as valid_count,
                                            (IF(!q.status, count(q.id), 0)) as non_valid_count,
                                            count(q.id) as total_count from questions as q
                                        inner join learning_outcomes lo on q.learning_outcome_id = lo.id
                                        where q.lesson_id = :branchId
                                        group by lo.class_level
                                        order by lo.class_level", ["branchId" => Auth::user()->branch_id]);
            return response()->json($classes);
        }
        elseif (Auth::user()->isAn("admin")) {
            $classes = DB::select("select lo.class_level,
                                            (IF(status, count(q.id), 0)) as valid_count,
                                            (IF(!q.status, count(q.id), 0)) as non_valid_count,
                                            count(q.id) as total_count from questions as q
                                        inner join learning_outcomes lo on q.learning_outcome_id = lo.id
                                        group by lo.class_level
                                        order by lo.class_level");
            return response()->json($classes);
        }
        else
            return response()->json([ResponseKeys::MESSAGE => "Herhangi bir yetkiniz yok"]);
    }

    public function getQuestionCountByLO(Request $request) {
        if ($request->query("is_any_question_lo")) {
            if (Auth::user()->isAn("teacher", "elector")) {
                $loQuestionCount = DB::select('select 
                                                   CONCAT(lo.code, " ", lo.content) as learning_outcome,
                                                    lo.id as learning_outcome_id,
                                                    (IF(status, count(q.id), 0)) as valid_count,
                                                    (IF(status = 0, count(q.id), 0)) as non_valid_count,
                                                    count(q.id) as total_count
                                            from questions as q
                                            inner join learning_outcomes lo on q.learning_outcome_id = lo.id
                                            where lo.class_level = :classLevel and lo.branch_id = :branchId
                                            group by lo.id',
                    ["classLevel" => $request->query("class_level"),
                        "branchId" => Auth::user()->branch_id]);
                return response()->json($loQuestionCount);
            } elseif (Auth::user()->isAn("admin")) {
                //TODO Sınıf seviyesi ile birlikte ders bilgisi de gönderilecek
                $loQuestionCount = DB::select('select 
                                                    CONCAT(lo.code, " ", lo.content) as learning_outcome,
                                                    lo.id as learning_outcome_id,
                                                    (IF(status, count(q.id), 0)) as valid_count,
                                                    (IF(status = 0, count(q.id), 0)) as non_valid_count,
                                                    count(q.id) as total_count
                                            from questions as q
                                            inner join learning_outcomes lo on q.learning_outcome_id = lo.id
                                            where lo.class_level = :classLevel
                                            group by lo.id',
                    ["classLevel" => $request->query("class_level")]);
                return response()->json($loQuestionCount);
            }
        }
        else {
            if (Auth::user()->isAn("teacher", "elector")) {
                $loQuestionCount = DB::select('select 
                                                   CONCAT(lo.code, " ", lo.content) as learning_outcome,
                                                    lo.id as learning_outcome_id,
                                                    (IF(status, count(q.id), 0)) as valid_count,
                                                    (IF(status = 0, count(q.id), 0)) as non_valid_count,
                                                    count(q.id) as total_count
                                            from questions as q
                                            right outer join learning_outcomes lo on q.learning_outcome_id = lo.id
                                            where lo.class_level = :classLevel and lo.branch_id = :branchId
                                            group by lo.id',
                    ["classLevel" => $request->query("class_level"),
                        "branchId" => Auth::user()->branch_id]);
                return response()->json($loQuestionCount);
            } elseif (Auth::user()->isAn("admin")) {
                //TODO Sınıf seviyesi ile birlikte ders bilgisi de gönderilecek
                $loQuestionCount = DB::select('select 
                                                    CONCAT(lo.code, " ", lo.content) as learning_outcome,
                                                    lo.id as learning_outcome_id,
                                                    (IF(status, count(q.id), 0)) as valid_count,
                                                    (IF(status = 0, count(q.id), 0)) as non_valid_count,
                                                    count(q.id) as total_count
                                            from questions as q
                                            right outer join learning_outcomes lo on q.learning_outcome_id = lo.id
                                            where lo.class_level = :classLevel
                                            group by lo.id',
                    ["classLevel" => $request->query("class_level")]);
                return response()->json($loQuestionCount);
            }
        }
    }
}
