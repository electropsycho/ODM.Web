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

namespace App\Http\Controllers\Api\Exam;


use App\Http\Controllers\ApiController;
use App\Http\Controllers\Utils\ResponseCodes;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Question;
use App\Models\Setting;
use App\Traits\RandomCodeGenerator;
use Barryvdh\DomPDF\Facade as PDF;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ExamController extends ApiController
{
    use RandomCodeGenerator;

    public function createFastExam(Request $request)
    {
        $validationResult = $this->apiValidator($request, [
            'title' => 'required',
            'planned_date' => 'required',
            'class_level' => 'required|integer',
            'purpose_id' => 'required|integer',
            'lessons' => 'required|array|min:1',
            'lessons.*.question_count' => 'required|integer|min:1',
            'lessons.*.id' => 'required|integer',
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        $lessons = $request->input('lessons');
        $code = $this->getRandomCode();

        try {
            DB::beginTransaction();
            // Önce sınavı oluşturalım
            //TODO sınıf seviyesi sorgulara bir kriter olarak girmeli
            $exam = $this->createExam($request, $code);

            // Sonra soruları derslere göre rastgele getirelim
            $questionSelectionRes = $this->selectAndSaveQuestionsInExam($lessons, $exam);
            if (!$questionSelectionRes) {
                DB::rollBack();
                return response()->json([
                    ResponseKeys::CODE => ResponseCodes::CODE_WARNING,
                    ResponseKeys::MESSAGE => 'Verilen kriterlere göre soru bulunamıyor!'
                ]);
            }
            DB::commit();
            return response()->json($exam);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->apiException($e);
        }
    }

    public function getExamFile($id) {
        $exam = Exam::find($id);
        if ($exam->path && Storage::disk('local')->exists($exam->path)) {
            return response()->download(Storage::disk('local')->path($exam->path));
        }
        //Zip dosyasını oluşturmaya başlayalım
        $zipArchive = new ZipArchive();
        $fileName = 'Exam-' . $exam->code . '.zip';
        $filePath = 'public/exams/' . $fileName;
        $fullPath = Storage::disk('local')->path($filePath);
        $res = $zipArchive->open($fullPath, ZipArchive::CREATE) === true;

        //Zip dosyası oluşturma başarılı ise dosyaları zip içine atalım
        if ($res) {
            $examQuestions = ExamQuestion::where('exam_id', $exam->id)
                ->join('questions as q', 'q.id', '=', 'question_id')
                ->join('branches as b', 'b.id', '=', 'q.lesson_id')
                ->select('q.id', 'q.content_url', 'b.code as branchCode')
                ->get();

            foreach ($examQuestions as $question) {
                $explodedPath = explode("/", $question->content_url);
                $fileName = end($explodedPath);
                $sp = Storage::disk('local')->path($question->content_url);
                if (file_exists($sp)) {
                    $zipArchive->addFile($sp, $question->branchCode.'/'.$fileName);
                } else {
                    throw new \RuntimeException("Zip dosyasını oluşturma başarısız oldu.");
                }
            }
            // Son olarak raporu zip dosyasına atalım
            $pathRes = $this->createExamReport($id);
            $zipArchive->addFile(Storage::disk('local')->path($pathRes[0]), $pathRes[1]);
            // Zip dosyasını kapatalım
            $zipArchive->close();


            //Sınavın yolunu kaydedelim
            $exam->path = $filePath;
            $exam->status = Exam::PLANNED;
            $exam->save();
        }
        return response()->download($fullPath);
    }

    public function create(Request $request) {
        $validationResult = $this->apiValidator($request, [
            'title' => 'required',
            'planned_date' => 'required',
            'class_level' => 'required',
            'purpose_id' => 'required',
            'questions' => 'required|array'
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }
    }

//    public function getExamPDF(Request $request, $id): string
//    {
//        return $this->createExamReport($id);
//    }

    /**
     * @param Request $request
     * @param string $code
     * @return Exam
     */
    private function createExam(Request $request, string $code): Exam
    {
        $exam = new Exam([
            'creator_id' => Auth::id(),
            'purpose_id' => $request->input('purpose_id'),
            'code' => $code,
            'title' => $request->input('title'),
            'class_level' => $request->input('class_level'),
            'planned_date' => $request->input('planned_date'),
            'description' => $request->input('description'),
            'status' => Exam::CREATED
        ]);
        $exam->save();
        return $exam;
    }

//    /**
//     * @param string $code
//     * @param Exam $exam
//     * @return false|string[]
//     */
//    private function createZipFile(string $code, Exam $exam)
//    {
//        //Zip dosyasını oluşturmaya başlayalım
//        $zipArchive = new ZipArchive();
//        $fileName = 'Exam-' . $code . '.zip';
//        $filePath = 'exams/' . $fileName;
//        $fullPath = Storage::disk('local')->path('public/' . $filePath);
//        $res = $zipArchive->open($fullPath, ZipArchive::CREATE) === true;
//
//        //Zip dosyası oluşturma başarılı ise dosyaları zip içine atalım
//        if ($res) {
//            $examQuestions = ExamQuestion::where('exam_id', $exam->id)
//                ->join('questions as q', 'q.id', '=', 'question_id')
//                ->select('q.id', 'q.content_url')
//                ->get();
//
//            foreach ($examQuestions as $question) {
//                $explodedPath = explode("/", $question->content_url);
//                $fileName = end($explodedPath);
//                $sp = Storage::disk('local')->path($question->content_url);
//                if (file_exists($sp)) {
//                    $zipArchive->addFile($sp, $fileName);
//                } else {
//                    throw new \RuntimeException("Zip dosyasını oluşturma başarısız oldu.");
//                }
//            }
//            // Zip dosyasını kapatalım
//            $zipArchive->close();
//
//            //Sınavın yolunu kaydedelim
//            $exam->path = $filePath;
//            $exam->status = Exam::PLANNED;
//            $exam->save();
//        }
//        return $fullPath;
//    }

    /**
     * Soruları otomatik olarak seçerek sınav içine kaydeder
     * @param array|null $lessons
     * @param Exam $exam
     */
    private function selectAndSaveQuestionsInExam(?array $lessons, Exam $exam): bool
    {
        $examQuestions = collect([]);
        //Her bir dersi tek tek dönelim
        foreach ($lessons as $lesson) {
            //Her ders için belirtilen sayında soruyu seçelim
            $questions = DB::table('questions as q')
                ->join('learning_outcomes as lo', 'q.learning_outcome_id', '=', 'lo.id')
                ->inRandomOrder()
                ->where('lesson_id', $lesson['id'])
                ->where('lo.class_level', $exam['class_level'])
                ->where('status', Question::APPROVED)
                ->select(
                    'q.id',
                    'q.content_url'
                )
                ->limit($lesson['question_count'])
                ->get();

            // Sorular geldikten sonra sınavla ilişkilendirelim
            foreach ($questions as $question) {
                $examQuestion = [
                    'exam_id' => $exam->id,
                    'question_id' => $question->id
                ];
                $examQuestions->push($examQuestion);
            }
        }
        if ($examQuestions->count() <= 0) {
            return false;
        }
        ExamQuestion::insert($examQuestions->toArray());
        return true;
    }

    /**
     * @param $id
     * @return string[]
     */
    private function createExamReport($id): array
    {
        $settings = Setting::first();
        $exam = DB::table('exams as e')
            ->join('exam_purposes as ep', 'e.purpose_id', '=', 'ep.id')
            ->join('users as u', 'e.creator_id', '=', 'u.id')
            ->where("e.id", $id)
            ->select('e.id',
                'u.full_name as creator',
                'ep.purpose', 'e.code',
                DB::raw('(case status 
                    when ' . Exam::CREATED . ' then "Oluşturulmuş"
                    when ' . Exam::PLANNED . ' then "Planlanmış"
                    when ' . Exam::CANCELED . ' then "İptal edilmiş"
                    when ' . Exam::COMPLETED . ' then "Tamamlanmış"
                    else "Belirsiz"
                    end) as status'),
                'title', 'class_level',
                'planned_date')
            ->first();

        $questions = ExamQuestion::where('exam_id', $id)
            ->join('questions as q', 'question_id', '=', 'q.id')
            ->join('learning_outcomes as lo', 'q.learning_outcome_id', '=', 'lo.id')
            ->join('users as u', 'creator_id', '=', 'u.id')
            ->select('q.id',
                DB::raw('(case q.difficulty 
            when 1 then "Çok Kolay"
            when 2 then "Kolay"
            when 3 then "Normal"
            when 4 then "Zor"
            when 5 then "Çok Zor"
            else "Belirsiz"
            end) as difficulty'),
                'q.correct_answer',
                'q.is_design_required',
                DB::raw('IF(q.is_design_required, "Gerekli", "Gerekli Değil") as idr_text'),
                'u.full_name as creator',
                DB::raw('CONCAT(lo.code, " ", lo.content) as learning_outcome'),
                DB::raw('SUBSTRING_INDEX(q.content_url, "/", -1) as file_name')
            )
            ->orderBy('q.lesson_id')
            ->get();

//        return view('reports.exams.ExamReport', ['settings' => $settings, 'exam' => $exam]);
        PDF::setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled', true,
            'tempDir' => Storage::disk('local')->path('public/trash/')
        ]);
//        $memLogo = 'data:image/png;base64,'.base64_encode(File::get(public_path('images/MEM-Logo.png')));
//        $odmLogo = 'data:image/png;base64,'.base64_encode(File::get(public_path('images/Logo.png')));

        $pdf = PDF::loadView('reports.exams.ExamReport', [
            'settings' => $settings,
            'exam' => $exam,
            'questions' => $questions
        ]);
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed'=> TRUE
            ]
        ]);
        $pdf->getDomPDF()->setHttpContext($context);
//        return $pdf->download($exam->code . '-Exam-Report.pdf');
        $reportFileName = 'Sınav Raporu-'.$exam->code.'.pdf';
        $reportFilePath = 'public/trash/' . $reportFileName;
        Storage::put($reportFilePath, $pdf->output());
        return [$reportFilePath, $reportFileName];
    }


}
