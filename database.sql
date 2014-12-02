DROP TABLE IF EXISTS "authAccess";
CREATE TABLE "authAccess" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "smapId" integer NOT NULL,
  "userId" integer NULL,
  "groupId" integer NULL,
  "right" integer NOT NULL
);


DROP TABLE IF EXISTS "authGroups";
CREATE TABLE "authGroups" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);


DROP TABLE IF EXISTS "authLogins";
CREATE TABLE "authLogins" (
  "userId" integer NOT NULL,
  "time" text NOT NULL
);


DROP TABLE IF EXISTS "authUsers";
CREATE TABLE "authUsers" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "groupId" integer NULL,
  "userName" text NOT NULL,
  "email" text NOT NULL,
  "password" text NOT NULL,
  "salt" text NOT NULL
);


DROP TABLE IF EXISTS "content";
CREATE TABLE "content" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "smapId" integer NOT NULL,
  "lang" text(5) NOT NULL,
  "position" integer NOT NULL,
  "text" text NOT NULL
);


DROP TABLE IF EXISTS "files";
CREATE TABLE "files" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "file" text(255) NOT NULL,
  "hash" text(255) NOT NULL
);


DROP TABLE IF EXISTS "news";
CREATE TABLE "news" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "smapId" integer NULL,
  "title" text NOT NULL,
  "announce" text NOT NULL,
  "text" text NULL,
  "date" integer NOT NULL
);


DROP TABLE IF EXISTS "personalCity";
CREATE TABLE "personalCity" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);


DROP TABLE IF EXISTS "personalDepartment";
CREATE TABLE "personalDepartment" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "city_id" integer NOT NULL,
  "name" text NOT NULL,
  FOREIGN KEY ("city_id") REFERENCES "personal_city" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);


DROP TABLE IF EXISTS "personalPeople";
CREATE TABLE "personalPeople" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "department_id" integer NOT NULL,
  "name" text NOT NULL,
  "photo" text NULL,
  "position" text NULL,
  "function" text NULL,
  "email" text NULL,
  "birthday" text NULL,
  "telephone_internal" text NULL,
  "telephone_mobile" text NULL,
  "telephone_external" text NULL,
  "change" text NULL,
  FOREIGN KEY ("department_id") REFERENCES "personal_department" ("id") ON DELETE NO ACTION ON UPDATE CASCADE
);


DROP TABLE IF EXISTS "siteMap";
CREATE TABLE "siteMap" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "pid" integer NOT NULL,
  "segment" text NULL,
  "view" text NULL,
  "layout" text NULL,
  "controller" text NULL,
  "action" text NULL,
  "title" text NULL,
  "visible" integer NULL
);