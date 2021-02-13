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

namespace App\Http\Controllers\Api\Auth;

use App\Events\NewUserReqReceived;
use App\Events\ResetPasswordEvent;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Utils\ResponseCodes;
use App\Http\Controllers\Utils\ResponseContents;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Rules\Recaptcha;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserManagementController extends ApiController
{

    /**
     * Kullanıcı açma isteğini işleyecek fonksiyon
     * @param Request $request
     * @param Recaptcha $recaptcha
     * @return JsonResponse
     */
    public function registerRequest(Request $request, Recaptcha $recaptcha)
    {
        //Doğrulama apisine gelen veri doğrrulana için gönderidiliyor
        //Doğruılama geçilemezse bu fonksiyon otomatik geriye dönüyor
        //Yani bir sonraki satırdaki işleme geçmiyor
        $validationResult =  $this->apiValidator($request, [
            'email' => 'required|string|email|max:255',
            "full_name" => "required",
            "inst_id" => "required",
            "branch_id" => "required",
            "phone" => "required",
//            'recaptcha' => ['required', $recaptcha]
        ]);

        if ($validationResult) {
            return response()->json($validationResult, 422);
        }

        // Kullanıcı için hem pasiflerde hemde aktiflerde arama yapılacaktır
        // aramaya telefonda eklenmiştir.
        $req = User::withTrashed()
            ->where("email", $request->input("email"))
            ->orWhere("phone", $request->input("phone"))
            ->first();

        if ($req) {
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_WARNING,
                ResponseKeys::MESSAGE => "Bu mail adresiyle ve telefonla halihazırda bir hesabınız veya daha önce bir istek yapılmış veya varolan kaydınız pasif hale getirilmiş olabilir lütfen yöneticilerden yardım isteyiniz."
            ], 409);
        }


        $requestedUser = $request->only("full_name", "inst_id", "branch_id", "email", "phone");
        try {
            DB::beginTransaction();
            $model = new User($requestedUser);
            $model->save();
            $model->assign('teacher');
            $branchId = (int)$requestedUser['branch_id'];
            //SB ve TAR branşlarına İnklap Tarihi otomatikman eklenşyor
            if ($branchId === 5 || $branchId === 10) {
                $model->lessons()
                    ->attach([
                        $branchId => ['is_main' => true],
                        15 => ['is_main' => false]
                    ]);
            }else {
                $model->lessons()
                    ->attach($branchId, ['is_main' => true]);
            }


            //Burada kullanıca şifre oluşturma maili atılıyor
            event(new NewUserReqReceived($model));
            DB::commit();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => "Kullanıcı oluşturma isteğiniz tarafımıza ulaştı ve size bir e-posta attık lütfen e-postaadresinizi kontrol etmeyi unutmayınız."
            ], 201);
        } catch (Exception $exception) {
            DB::rollback();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_ERROR,
                ResponseKeys::MESSAGE => "Kullanıcı oluşturma işlmei alınamadı!",
                "exception" => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * Yeni kayıt isteği yapan sistem kullanıcısına onay verecek fonksiyon
     * @param $id
     * @return JsonResponse
     */
    public function confirmNewUserReq($id)
    {
        try {
            DB::beginTransaction();
            User::find($id)->update([
                "activator_id" => Auth::id()
            ]);
            $newUser = User::find($id);
            $relatedIds = $newUser->lessons()->allRelatedIds();

            foreach ($relatedIds as $relatedId){
                $newUser->lessons()
                    ->updateExistingPivot($relatedId, ['creator_id' => Auth::id()]);
            }
            //Bu sırada kullanıcıya şifre email olarak atıldı.
            //Bu işlem normalde java gibi dillerde asenkron yapılabilirdi
            event(new ResetPasswordEvent($newUser));
            DB::commit();
            return response()->json([ResponseKeys::MESSAGE => "Kullanıcı kayıt isteği onaylandı."], 200);
        } catch (Exception $exception) {
            DB::rollback();
            return response()->json([ResponseKeys::MESSAGE => "Kullanıcı kayıt onayı yapılamadı!",
                ResponseKeys::EXCEPTION => $exception->getMessage()], 500);
        }
    }

    /**
     * Kullanıcı güncelleme api fonk.
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validationResult = $this->apiValidator($request, [
            'branch_id' => 'required',
            "inst_id" => "required",
            "full_name" => "required",
            "phone" => "required",
            "email" => "required",
            "role" => "required"
        ]);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }
        $data = $request->only("branch_id",
            "inst_id",
            "full_name",
            "phone",
            "email");
        $role = $request->input("role");
        try {
            DB::beginTransaction();
            $user = User::find($id);
            $user->retract($user->getRoles());
            $user->update($data);
            $user->assign($role);
            DB::commit();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => "Kullanıcı güncelleme başarılı olmuştur."
            ]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_ERROR,
                ResponseKeys::MESSAGE => "Kullanıcı güncelleme başarısız oldu!",
                ResponseKeys::EXCEPTION => $this->apiException($exception)], 500);
        }
    }

    public function updateMyInformation(Request $request, $id) {
        $validationResult = $this->apiValidator($request, [
            "inst_id" => "required",
            "full_name" => "required",
            "phone" => "required",
            "email" => "required"
        ]);
        if ($validationResult) {
            return response()->json($validationResult, 422);
        }
        $data = $request->only(
            "inst_id",
            "full_name",
            "phone",
            "email");
        try {
            DB::beginTransaction();
            $user = User::find($id);
            $user->update($data);
            DB::commit();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_SUCCESS,
                ResponseKeys::MESSAGE => "Bilgileriniz başarıyla güncellenmiştir."
            ]);
        }
        catch (Exception $exception) {
            DB::rollBack();
            return response()->json([
                ResponseKeys::CODE => ResponseCodes::CODE_ERROR,
                ResponseKeys::MESSAGE=> "Bilgilerinizin güncellemenmesi başarısız oldu!",
                ResponseKeys::EXCEPTION => $this->apiException($exception)], 500);
        }
    }

    /*
     * Bu fonksiyon kullanıcıyı tekrar aktifleştirmrk için kullanılacak
     */
    public function reactivate($id)
    {
        try {
            DB::beginTransaction();
            User::withTrashed()
                ->where('id', $id)
                ->restore();
            DB::commit();
            return response()->json([
                ResponseKeys::MESSAGE => "Kullanıcı başarıyla aktifleştirildi"]);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json([ResponseKeys::MESSAGE=> "Kullanıcı aktifleştirilemedi!",
                ResponseKeys::EXCEPTION => $this->apiException($exception)], 500);
        }
    }

    /**
     * Kullanıcı silme işlemi bu işlem hard delete değil soft delete işlemidir.
     * Yani silinen kullanıcı users tablosu içinde var olmaya devam edecektir.
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function delete($id) {
        try{
            DB::beginTransaction();
            $user = User::find($id);
            $user->delete();
            DB::commit();
            return response()->json([ResponseKeys::MESSAGE => "Kullanıcı pasif hale getirildi"]);
        }
        catch (Exception $exception) {
            DB::rollBack();
            return response()->json([ResponseKeys::MESSAGE=> "Kullanıcı pasifleştirilemedi!",
                ResponseKeys::EXCEPTION => $this->apiException($exception)], 500);
        }
    }
}
