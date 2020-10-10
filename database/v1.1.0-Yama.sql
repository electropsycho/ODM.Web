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


CREATE TRIGGER checkMinMaxElectorCount
    BEFORE UPDATE
    ON settings
    FOR EACH ROW
BEGIN
    IF NEW.min_elector_count < 2 THEN
        SET NEW.min_elector_count = 2;
    end if;
    IF NEW.max_elector_count > 10 THEN
        SET NEW.max_elector_count = 10;
    end if;
END;


create table user_permitted_lesson
(
    user_id    int unsigned null,
    lesson_id  int unsigned null,
    creator_id int unsigned null,
    is_main    bool         null,
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
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

INSERT INTO user_permitted_lesson (user_id, lesson_id, is_main, created_at, updated_at)
SELECT u.id, u.branch_id, true, NOW(), NOW()
from users as u;

INSERT INTO user_permitted_lesson (user_id, lesson_id, is_main, created_at, updated_at)
SELECT u.id, 15, false, NOW(), NOW()
FROM users as u
WHERE u.branch_id IN (5, 10);

create table help_desk
(
    id int unsigned auto_increment,
    parent_id int unsigned null,
    token varchar(255) not null,
    creator_id int unsigned null,
    status int null comment 'open, closed, in_progress vs...',
    type int null comment 'cevap, şikayet, istek vs..',
    title varchar(500) null,
    comment varchar(5000) null,
    created_at timestamp null,
    updated_at timestamp null,
    constraint help_desk_pk
        primary key (id)
) CHARACTER SET = utf8mb4 COLLATE = utf8mb4_turkish_ci;

alter table help_desk
    add constraint help_desk_help_desk_id_fk
        foreign key (parent_id) references help_desk (id);

