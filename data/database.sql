-- Adminer 4.1.0 SQLite 3 dump

DROP TABLE IF EXISTS "ACL";
CREATE TABLE ACL
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  action VARCHAR(100),
  groupId INTEGER,
  rule INTEGER
);


DROP TABLE IF EXISTS "Groups";
CREATE TABLE Groups
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(100)
);


DROP TABLE IF EXISTS "SiteMap";
CREATE TABLE "SiteMap" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "parent" integer NOT NULL,
  "segment" text NULL,
  "view" text NULL,
  "layout" text NULL,
  "controller" text NULL
, "action" text NULL, "title" text NULL);


DROP TABLE IF EXISTS "Users";
CREATE TABLE Users
(
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  groupId INTEGER,
  name VARCHAR(100),
  email VARCHAR(100),
  password VARCHAR(100)
);


DROP TABLE IF EXISTS "personal_city";
CREATE TABLE "personal_city" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);


DROP TABLE IF EXISTS "personal_department";
CREATE TABLE "personal_department" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "city_id" integer NOT NULL,
  "name" text NOT NULL,
  FOREIGN KEY ("city_id") REFERENCES "personal_city" ("id") ON DELETE CASCADE ON UPDATE CASCADE
);


DROP TABLE IF EXISTS "personal_people";
CREATE TABLE "personal_people" (
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


-- 