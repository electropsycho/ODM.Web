<?php
/**
 * Bu yazılım Elektrik Elektronik Teknolojileri Alanı/Elektrik Öğretmeni Hakan GÜLEN tarafından geliştirilmiş olup geliştirilen bütün kaynak kodlar
 * Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International (CC BY-NC-SA 4.0) ile lisanslanmıştır.
 * Ayrıntılı lisans bilgisi için https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode.tr sayfasını ziyaret edebilirsiniz.2019
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Soruların değerlendirmelerini tutacak sınıf
 * Yani değerlendirme komisyonu soru sorulmaya değer ise
 * sorulmasını sağlayacaklar değilse sorumuz kendisini
 * veritabanın indeksiz sayfalarında bulacak ;-)
 * Class QuestionsEvaluation
 * @package App\Models
 */
class QuestionsEvaluation extends Model
{
  protected $fillable = [
    "question_id", "elector_id",
    "point", "comment"
  ];
}
