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

alter table questions
	add is_design_required bool null after status;

alter table exams
    change status_id status int unsigned not null;

alter table exams
    add date_of_occurrence datetime null after planned_date;

alter table exams
    add purpose_id int null after creator_id;

alter table exams
    add path varchar(255) null after date_of_occurrence;


alter table exams
    add code varchar(50) null after purpose_id;

alter table settings modify captcha_enabled varchar(500) null after captcha_public_key ;

alter table settings
    add will_the_electors_be_emailed bool default false null after captcha_enabled;

UPDATE settings SET will_the_electors_be_emailed = false;

create table exam_purposes
(
    id         int          not null,
    purpose    varchar(500) not null,
    code       varchar(50)  null,
    created_at timestamp    null,
    updated_at timestamp    null,
    constraint exam_purposes_pk
        primary key (id)
);

INSERT INTO exam_purposes (id, purpose, code, created_at, updated_at)
VALUES (1, 'Araştırma Sınavı', 'ARAS', null, null);
INSERT INTO exam_purposes (id, purpose, code, created_at, updated_at)
VALUES (2, 'Pilotlama Sınavı', 'PİLS', null, null);
INSERT INTO exam_purposes (id, purpose, code, created_at, updated_at)
VALUES (3, 'MEB Merkezi Sınavı', 'MEBS', null, null);

alter table branches
    add class_levels varchar(200) null after code;
UPDATE branches
SET class_levels = "4,5,6,7,8"
WHERE id IN (1, 2, 3, 5);

UPDATE branches
SET class_levels = "4,5,6,7,8,9,10,11,12"
WHERE id IN (4, 12);

UPDATE branches
SET class_levels = "9,10,11,12"
WHERE id IN (6, 7, 8, 9, 10, 11, 13, 14);

UPDATE branches
SET class_levels = "8,12"
WHERE id = 15;


