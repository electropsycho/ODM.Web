<?php
/*
 * ODM.Web  https://github.com/electropsycho/ODM.Web
 * Copyright (C) 2020 Hakan GÜLEN
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sınavlarda sorulacak soru tiplerini ve içeriğini ekleyceğimiz sınıf
 * Class Question
 * @package App\Models
 */
class Question extends Model
{
    // Soru durumları
    // https://youtu.be/enuOArEfqGo ;-)
    public const WAITING_FOR_ACTION = 0; // İşeleme alınmamış
    public const IN_ELECTION = 1; // değerledirme aşamasında
    public const NOT_MUST_ASKED = 2;          // SORULMAMALI
    public const NEED_REVISION = 3;           // Revizyon gerekir
    public const REVISION_COMPLETED = 4; // değerledirme aşamasında
    public const APPROVED = 5;          // Onaylanmış soru

    protected $fillable = [
        'creator_id', 'lesson_id', 'learning_outcome_id',
        'keywords', 'difficulty', 'status',
        'content_url', 'min_required_election'
    ];

    public function evaluations()
    {
        return $this->hasMany(QuestionsEvaluation::class, "question_id");
    }

    public function revisions()
    {
        return $this->hasMany(QuestionRevisions::class, "question_id");
    }
}
