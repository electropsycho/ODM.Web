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

namespace App\Http\Controllers\Web\Auth;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\ResponseKeys;
use App\Models\Setting;
use App\Traits\ValidationTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CustomPasswordResetController extends Controller
{
    use ValidationTrait;

    public function showResetForm(Request $request, $token = null)
    {
        $settings = Setting::first();
        return view('auth.password_reset')->with(
            [
                'token' => $token,
                'email' => $request->email,
                'city' => $settings->city,
                'web_address' => $settings->web_address
            ]
        );
    }

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->MESSAGES, $this->ATTRIBUTES);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

//        $broker = app('auth.password.broker');

        $user = $this->broker()->getUser($credentials);
        if ($user === null) {
            return response([
                ResponseKeys::MESSAGE =>  "Kullanıcı e-posta adresi bulunamadı!"
            ], 404);
        }

        $isExist =  $this->broker()->tokenExists($user, $credentials["token"]);

        if ($isExist) {
            $result =  $this->broker()->reset($credentials, function ($user, $pass){
                $this->resetPassword($user, $pass);
            });
            if ($result === PasswordBroker::PASSWORD_RESET) {
                $settings = Setting::first();
                return view('app', ['city' => $settings->city]);
            }
            return redirect()
                ->back()
                ->withInput($request->only('email'))
                ->withErrors([ResponseKeys::MESSAGE => "Şifre değiştirme işlemi yapılamadı."]);
        }
        return redirect()
            ->back()
            ->withInput($request->only('email'))
            ->withErrors([ResponseKeys::MESSAGE =>  "Şifre değiştirme bağlantısının süresi geçmiş olabilir.\nŞifremi unuttum diyerek tekrar bağlantı alabilirsiniz!"]);
    }

    protected function resetPassword($user, $password): void
    {
        $user->password = Hash::make($password);
        $user->activation_date = Carbon::now();
        $user->setRememberToken(Str::random(60));
        $user->save();
    }

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'max:16',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$^+=!*()@%&\.]).{8,16}$/'
            ]
        ];
    }

    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}
