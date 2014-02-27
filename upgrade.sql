alter table posts add locked char(1) default 'N';
alter table users add moderator char(1) default 'N';
alter table posts add edited_at int(18) unsigned after modified_at;