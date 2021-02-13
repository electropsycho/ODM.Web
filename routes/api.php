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

use Illuminate\Support\Facades\Route;

Route::group([
  'prefix' => 'auth'
], function ($router) {

    //Aşağıdakiler için doğrulama ihtiyacına gerek yok
    Route::post('register', 'Auth\UserManagementController@registerRequest');
    //  Route::put('password/reset', 'Auth\UserManagementController@resetPassword');
    Route::post('password/forget', 'Auth\PasswordController@forgetPassword');
    Route::post('login', 'Auth\AuthController@login');
    Route::get('institutions', 'Inst\InstitutionController@findByNameInstitutions');
    Route::get('branches', 'Branch\BranchController@getBranches');
    Route::get('general_info', 'Setting\SettingController@getGeneralInfo');
    
});

//Bütün doğrulanması gereken api istekleri bu grup altına yazılacak
//İstekler yapılırken http://tam_url/otomasyon/api/ nin arkasına ilgili istekler eklenecek
//TODO Swagger dökümantasyonu yapılabilir. Geliştircilerin hayatını kurtatır.
Route::group(['middleware' => ['jwt.auth']], function () {

    //TODO Artisan Queue Work komutu için api yazılacak
    //Route::get('mails/sync', "Setting\MailSyncController@executeMailQueue");

    //Kullanıcı ve Rol yönetim route tanımnlamaları
    Route::put('users/{id}/confirm_req', "Auth\UserManagementController@confirmNewUserReq");
    Route::get('users/current/permissions', "Auth\PermissionController@getCurrentUserPermissions");
    Route::get('users/{id}', "Auth\UserQueryController@getUser");
    Route::put('users/{id}', "Auth\UserManagementController@update");
    Route::put('users/{id}/my_info', "Auth\UserManagementController@updateMyInformation");
    Route::delete('users/{id}', "Auth\UserManagementController@delete");
    Route::put('users/{id}/reactivate', "Auth\UserManagementController@reactivate");
    Route::post('users', "Auth\UserQueryController@getUsers");
    Route::post('users/passives', "Auth\UserQueryController@getPassiveUsers");
    Route::get('users/current/roles', "Auth\RoleController@getCurrentUserRoles");
    Route::get('users/{userId}/roles', "Auth\RoleController@getUserRoles");
    Route::get('roles', "Auth\RoleController@getAllRoles");

    //BranchService Api route tanımlamaları
    Route::post('branches', "Branch\BranchController@create");
    Route::put('branches/{id}', "Branch\BranchController@update");
    Route::delete('branches/{id}', "Branch\BranchController@delete");
    Route::get('branches/with_stats', "Branch\BranchController@getBranchesWithStats");
    Route::get('branches', "Branch\BranchController@getBranches");
    Route::get('branches/{branchId}/electors', "Auth\UserQueryController@findElectorByBranchId");

    //Kazanım Api route tanımlamaları
    Route::post('learning_outcomes', "LO\LearningOutcomeController@create");
    Route::put('learning_outcomes/{id}', "LO\LearningOutcomeController@update");
    Route::delete('learning_outcomes/{id}', "LO\LearningOutcomeController@delete");
    Route::get('learning_outcomes/search/with_paging', "LO\LearningOutcomeController@searchWithPaging");
    Route::get('learning_outcomes/findByClassLevelAndLessonId', "LO\LearningOutcomeController@findByClassLevelAndLessonId");
    Route::post('learning_outcomes/find_by/content_lesson_id_class_level', "LO\LearningOutcomeController@findByContentAndLessonIdAndClassLevel");
    Route::get('learning_outcomes/find_by/content', "LO\LearningOutcomeController@findByContentWithPaging");
    Route::get('learning_outcomes/find_by', "LO\LearningOutcomeController@findBy");
    Route::get('learning_outcomes/last_saved/{size}', "LO\LearningOutcomeController@getLastSavedRecords");
    Route::get('learning_outcomes/{id}', "LO\LearningOutcomeController@findById");

    // Question Api route tanımlamaları aslında users/{id}/questions şeklinde url tanımlamaları yapılabilirdi
    Route::post('questions', "Question\QuestionController@create");
    Route::get('questions', "Question\QuestionController@findByContentAndClassLevelAndBranch");
    Route::get('questions/last_saved/{size}', "Question\QuestionController@getLastQuestions");
    Route::post('questions/list', "Question\QuestionQueryController@getQuestionList");
    Route::post('questions/delete_requests', "Question\QuestionDeleteRequestController@getDeleteRequests"); //Silme isteği api route

    //Constraints
    Route::get('questions/constraints', "Setting\SettingController@getQuestionConstraints");

    Route::get('questions/{id}', "Question\QuestionController@findById");
    Route::get('questions/{id}/file', "Question\QuestionController@getFile");
    //Evals
    Route::post('questions/{questionId}/evaluations', "Question\QuestionEvalRequestController@create");
    Route::put('questions/{questionId}/evaluations', "Question\QuestionEvalRequestController@updateQuestionEval");
    Route::get('questions/{questionId}/evaluations', "Question\QuestionEvalRequestController@findByQuestionId");
    Route::delete('questions/{questionId}/evaluations/{code}', "Question\QuestionEvalRequestController@deleteByCode");
    Route::put('questions/{questionId}/evaluations/{code}', "Question\QuestionEvalRequestController@manualCalculate");
    Route::put('questions/{questionId}/evaluations/{code}/add_electors', "Question\QuestionEvalRequestController@addElectors");
    Route::post('questions/evaluation_requests', "Question\QuestionEvalRequestController@getQuestionRequestsList");
    Route::delete('evaluations/{id}', "Question\QuestionEvalRequestController@delete");
    //Revisions
    Route::post('questions/{id}/revisions', "Question\QuestionRevisionController@create");
    Route::get('questions/{id}/revisions', "Question\QuestionRevisionController@findByQuestionId");
    //DeleteReqs
    Route::post('questions/{id}/delete_request', "Question\QuestionDeleteRequestController@create"); //Silme isteği api route
    Route::put('questions/{id}/delete_request', "Question\QuestionDeleteRequestController@approveDeleteRequest"); //Silme isteği onaylama
    Route::delete('questions/{question_id}/delete_requests/{id}', "Question\QuestionDeleteRequestController@delete"); //Silme isteği silme

    // Sınavlar ile ilgli api route tanımlamaları
    Route::post('exams/auto', 'Exam\ExamController@createFastExam');
    Route::get('exams/{id}/reports/detail', 'Exam\ExamController@getExamPDF');
    Route::get('exams/{id}/file', 'Exam\ExamController@getExamFile');
    Route::post('exams/list', 'Exam\ExamQueryController@getExams');
    Route::get('exams/purposes', 'Exam\ExamPurposeController@getPurposes');
    Route::get('exams', 'Exam\ExamQueryController@get');

    //İstatiksel api route tanımlamaları
    Route::get('stats/question_counts/total', "Stats\StatController@getQuestionCounts");
//  Route::get("stats/question_counts/by_lo/{lo_id}", "Stats\StatController@getQuestionCountByLO");
    Route::get('stats/classes', "Stats\StatController@getClasses");
    Route::get('stats/lo_count_by', "Stats\StatController@getQuestionCountByLO");


    //Birim api route tanımlamaları
    Route::get('units', "Inst\UnitController@getAllUnits");

    //Okul/Kurum api route tanımlamaları
    Route::post('institutions', "Inst\InstitutionController@create");
    Route::post('institutions/list', "Inst\InstitutionController@getInstitutions");
    Route::get('institutions/{id}', "Inst\InstitutionController@get");
    Route::put('institutions/{id}', "Inst\InstitutionController@update");
    Route::delete('institutions/{id}', "Inst\InstitutionController@delete");
    Route::get('institutions/{id}/teachers', "Auth\UserQueryController@findByInstitutionIdWithStats");

    //Ayarlar ile ilgili route tanımlamaları
    Route::get('settings', "Setting\SettingController@getSettings")->middleware('role:admin');
    Route::put('settings', "Setting\SettingController@update")->middleware('role:admin');
//    Route::post('settings/migrate_up', "Setting\SettingController@migrateUp");
});
