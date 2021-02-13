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
    add min_required_election int null
        comment 'Bu alan değerlendirmenin gerçekleşmesi için en az kaç değ. olacağına karar verir'
        after content_url;

update questions as u
set u.min_required_election = 3
where u.min_required_election is null;

alter table settings
    add min_elector_count int null after captcha_enabled;

alter table settings
    add max_elector_count int null after min_elector_count;


update settings
set min_elector_count = 2,
    max_elector_count=10
where settings.min_elector_count IS NULL;


create table user_permitted_lesson
(
    user_id    int unsigned not null,
    lesson_id  int unsigned not null,
    creator_id int unsigned null,
    is_main    tinyint(1)   null,
    created_at timestamp    null,
    updated_at timestamp    null,
    constraint user_permitted_lesson_pk
        primary key (user_id, lesson_id),
    constraint user_permitted_lesson_branches_id_fk
        foreign key (lesson_id) references branches (id),
    constraint user_permitted_lesson_users_id_fk
        foreign key (user_id) references users (id),
    constraint user_permitted_lesson_users_id_fk_2
        foreign key (creator_id) references users (id)
) ENGINE=InnoDB CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_turkish_ci;

INSERT INTO user_permitted_lesson (user_id, lesson_id, is_main, created_at, updated_at)
SELECT u.id, u.branch_id, true, NOW(), NOW()
from users as u;

INSERT INTO user_permitted_lesson (user_id, lesson_id, is_main, created_at, updated_at)
SELECT u.id, 15, false, NOW(), NOW()
FROM users as u
WHERE u.branch_id IN (5, 10);

create table help_desk
(
    id         int unsigned auto_increment,
    parent_id  int unsigned  null,
    token      varchar(255)  not null,
    creator_id int unsigned  null,
    status     int           null comment 'open, closed, in_progress vs...',
    type       int           null comment 'cevap, şikayet, istek vs..',
    title      varchar(500)  null,
    comment    varchar(5000) null,
    created_at timestamp     null,
    updated_at timestamp     null,
    constraint help_desk_pk
        primary key (id)
) ENGINE=InnoDB CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_turkish_ci;

alter table help_desk
    add constraint help_desk_help_desk_id_fk
        foreign key (parent_id) references help_desk (id);

