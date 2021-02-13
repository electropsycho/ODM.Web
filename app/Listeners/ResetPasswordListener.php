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

namespace App\Listeners;

use App\Events\ResetPasswordEvent;
use App\Models\Setting;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ResetPasswordListener
{
  /**
   * Create the event listener.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }

  /**
   * Handle the event.
   *
   * @param ResetPasswordEvent $event
   * @return void
   */
  public function handle(ResetPasswordEvent $event)
  {
    $user = $event->getUser();
    $setting = Setting::first();
    $token = app('auth.password.broker')->createToken($user);
    Mail::send("mails.ResetPassword", [
      'setting' => $setting,
      "user" => $user,
      'token' => $token
    ],
      function ($m) use ($setting, $user) {
      $m->from($setting->email, $setting->inst_name);
      $m->to($user->email, $user->full_name)->subject('Şifrenizi oluşturabilirsiniz');
    });
  }
}
