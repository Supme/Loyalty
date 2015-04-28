DROP TABLE IF EXISTS "cake_log";
CREATE TABLE "cake_log" (
  "id" integer NULL PRIMARY KEY AUTOINCREMENT,
  "people_id" integer NULL,
  "method" integer NULL,
  "comment" text NULL,
  "date" integer NULL,
  FOREIGN KEY ("people_id") REFERENCES "personal_people" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION
);


DROP TABLE IF EXISTS "core_auth_group";
CREATE TABLE 'core_auth_group' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'name' TEXT(100) DEFAULT NULL
, "isAdmin" integer(1) NULL);


DROP TABLE IF EXISTS "core_auth_log";
CREATE TABLE "core_auth_log" (
'user_id' INTEGER DEFAULT NULL PRIMARY KEY,
'time' INTEGER DEFAULT NULL
);


DROP TABLE IF EXISTS "core_auth_right";
CREATE TABLE 'core_auth_right' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'group_id' INTEGER REFERENCES 'core_auth_group' ('id'),
'sitemap_id' INTEGER DEFAULT NULL REFERENCES 'core_sitemap' ('id'),
'create' INTEGER(1) NOT NULL ,
'read' INTEGER(1) NOT NULL ,
'update' INTEGER(1) NOT NULL ,
'delete' INTEGER(1) NOT NULL 
);


DROP TABLE IF EXISTS "core_auth_user";
CREATE TABLE 'core_auth_user' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'name' TEXT(120) DEFAULT NULL,
'email' TEXT(120) DEFAULT NULL,
'login' TEXT DEFAULT NULL,
'password' TEXT DEFAULT NULL,
'salt' TEXT DEFAULT NULL
, "isAdmin" integer(1) NULL);


DROP TABLE IF EXISTS "core_auth_user_group";
CREATE TABLE "core_auth_user_group" (
  "id" integer NULL PRIMARY KEY AUTOINCREMENT,
  "user_id" integer NULL,
  "group_id" integer NULL,
  FOREIGN KEY ("group_id") REFERENCES "core_auth_group" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY ("user_id") REFERENCES "core_auth_user" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION
);


DROP TABLE IF EXISTS "core_content";
CREATE TABLE "core_content" (
  "id" integer NULL PRIMARY KEY AUTOINCREMENT,
  "sitemap_id" integer NULL,
  "lang_id" integer NULL,
  "time" integer NULL,
  "visible" integer NULL,
  FOREIGN KEY ("sitemap_id") REFERENCES "core_sitemap" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION,
  FOREIGN KEY ("lang_id") REFERENCES "core_lang_locale" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION
);


DROP TABLE IF EXISTS "core_content_data";
CREATE TABLE "core_content_data" (
  "id" integer NULL PRIMARY KEY AUTOINCREMENT,
  "data_id" integer NULL,
  "key" text NULL,
  "value" text NULL,
  FOREIGN KEY ("data_id") REFERENCES "core_content" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);


DROP TABLE IF EXISTS "core_fs_file";
CREATE TABLE 'core_fs_file' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'folder_id' INTEGER,
'name' TEXT DEFAULT NULL,
'type' TEXT DEFAULT NULL,
'realpath' TEXT DEFAULT NULL,
'size' INTEGER DEFAULT NULL,
'modification' INTEGER DEFAULT NULL,
'width' INTEGER DEFAULT NULL,
'height' INTEGER DEFAULT NULL,
'trash' INTEGER(1) DEFAULT 0
);


DROP TABLE IF EXISTS "core_fs_folder";
CREATE TABLE 'core_fs_folder' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT REFERENCES 'core_fs_file' ('folder_id'),
'pid' INTEGER REFERENCES 'core_fs_folder' ('id'),
'name' TEXT DEFAULT NULL,
'trash' INTEGER(1) DEFAULT 0
);


DROP TABLE IF EXISTS "core_fs_right";
CREATE TABLE 'core_fs_right' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'group_id' INTEGER NOT NULL  REFERENCES 'core_auth_group' ('id'),
'folder_id' INTEGER REFERENCES 'core_fs_folder' ('id'),
'file_id' INTEGER DEFAULT NULL REFERENCES 'core_fs_file' ('id'),
'create' INTEGER(1) DEFAULT NULL,
'read' INTEGER(1) DEFAULT NULL,
'update' INTEGER(1) DEFAULT NULL,
'delete' INTEGER(1) DEFAULT NULL
);


DROP TABLE IF EXISTS "core_lang_locale";
CREATE TABLE 'core_lang_locale' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT REFERENCES 'core_lang_translation' ('locale_id'),
'code' TEXT(3) DEFAULT NULL,
'name' TEXT(50) DEFAULT NULL
);


DROP TABLE IF EXISTS "core_lang_translation";
CREATE TABLE "core_lang_translation" (
  "id" integer NULL PRIMARY KEY AUTOINCREMENT,
  "locale_id" integer NULL,
  "key" text NULL,
  "value" text NULL,
  FOREIGN KEY ("locale_id") REFERENCES "core_lang_locale" ("id")
);


DROP TABLE IF EXISTS "core_sitemap";
CREATE TABLE "core_sitemap" (
  "id" integer NULL PRIMARY KEY AUTOINCREMENT,
  "pid" integer NULL,
  "segment" text NULL,
  "layout" text NULL,
  "view" text NULL,
  "module" text NULL,
  "controller" text NULL,
  "action" text NULL,
  "title" text NULL,
  "menu" integer NULL,
  FOREIGN KEY ("pid") REFERENCES "core_sitemap" ("id") ON DELETE NO ACTION ON UPDATE NO ACTION
);


DROP TABLE IF EXISTS "personal_department";
CREATE TABLE 'personal_department' (
'id' INTEGER DEFAULT NULL PRIMARY KEY AUTOINCREMENT,
'office_id' INTEGER NOT NULL  REFERENCES 'personal_office' ('id'),
'name' TEXT DEFAULT NULL
);


DROP TABLE IF EXISTS "personal_office";
CREATE TABLE 'personal_office' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'name' TEXT DEFAULT NULL
);


DROP TABLE IF EXISTS "personal_people";
CREATE TABLE 'personal_people' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'department_id' INTEGER NOT NULL  REFERENCES 'personal_department' ('id'),
'name' TEXT DEFAULT NULL,
'photo' TEXT DEFAULT NULL,
'position' TEXT DEFAULT NULL,
'function' TEXT DEFAULT NULL,
'email' TEXT DEFAULT NULL,
'birthday' TEXT DEFAULT NULL,
'telephone_internal' TEXT DEFAULT NULL,
'telephone_mobile' TEXT DEFAULT NULL,
'telephone_external' TEXT DEFAULT NULL,
'change' TEXT DEFAULT NULL
);


DROP TABLE IF EXISTS "sender_book";
CREATE TABLE 'sender_book' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'name' TEXT DEFAULT NULL
);


DROP TABLE IF EXISTS "sender_campaign";
CREATE TABLE 'sender_campaign' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'name' TEXT DEFAULT NULL,
'subject' TEXT DEFAULT NULL,
'message' TEXT DEFAULT NULL,
'time' INTEGER DEFAULT NULL
);


DROP TABLE IF EXISTS "sender_param";
CREATE TABLE 'sender_param' (
'id' INTEGER DEFAULT NULL PRIMARY KEY AUTOINCREMENT,
'recipient_id' INTEGER NOT NULL  REFERENCES 'sender_recipient' ('id'),
'key' TEXT DEFAULT NULL,
'value' TEXT DEFAULT NULL
);


DROP TABLE IF EXISTS "sender_recipient";
CREATE TABLE 'sender_recipient' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'campaign_id' INTEGER NOT NULL  REFERENCES 'sender_campaign' ('id')
);


DROP TABLE IF EXISTS "sender_statistic";
CREATE TABLE 'sender_statistic' (
'id' INTEGER PRIMARY KEY AUTOINCREMENT,
'book_id' INTEGER NOT NULL  REFERENCES 'sender_book' ('id'),
'status' TEXT DEFAULT NULL,
'time' INTEGER DEFAULT NULL
);


DROP TABLE IF EXISTS "sqlite_sequence";
CREATE TABLE sqlite_sequence(name,seq);

