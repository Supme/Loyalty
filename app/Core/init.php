<?php
/**
 * @package ly.
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License	
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

namespace App\Core;

class init extends \Db
{
    function isInstalled()
    {
        return true; //$this->tables("core_%")==11?true:false;
    }

    function install()
    {
        $this->query('
CREATE TABLE "core_auth_group" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "name" text NOT NULL
);');
        $this->query('
CREATE TABLE "core_files" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "file" text(255) NOT NULL,
  "hash" text(255) NOT NULL
);');
        $this->query('
CREATE TABLE "core_lang_locales" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "code" text NOT NULL,
  "name" text NOT NULL
);');
        $this->query('
CREATE TABLE "core_lang_translation" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "locales_id" integer NOT NULL,
  "key" text NOT NULL,
  "value" text NOT NULL
);');
        $this->query('
CREATE TABLE "core_sitemap" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "pid" integer NOT NULL,
  "segment" text NULL,
  "layout" text NULL,
  "view" text NULL,
  "module" text NULL,
  "controller" text NULL,
  "action" text NULL,
  "title" text NULL,
  "visible" integer NULL
);');
        $this->query('
CREATE TABLE "core_news" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "smap_id" integer NULL,
  "title" text NOT NULL,
  "announce" text NOT NULL,
  "text" text NULL,
  "date" integer NOT NULL
);');
        $this->query('
CREATE TABLE "core_content" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "smap_id" integer NOT NULL,
  "lang_id" integer NOT NULL,
  "position" integer NOT NULL,
  "text" text NOT NULL
);');
        $this->query('
CREATE TABLE "core_auth_access" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "smap_id" integer NOT NULL,
  "user_id" integer NULL,
  "group_id" integer NULL,
  "right" integer NOT NULL
);');
        $this->query('
CREATE TABLE "core_auth_login" (
  "user_id" integer NOT NULL,
  "time" text NOT NULL
);');
        $this->query('
CREATE TABLE "core_auth_user" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "group_id" integer NULL,
  "name" text NOT NULL,
  "email" text NOT NULL,
  "password" text NOT NULL,
  "salt" text NOT NULL
);');

        // Create groups and administrator account
        $this->insert("core_auth_user",[
            "group_id"  =>  0,
            "name"      =>  "Test User",
            "email"     =>  "test@example.com",
            "password"  =>  "7332643791a38934abb539323d4d77eff744f6e33876fc95424c0f9f9c65ef5ffcf37d8c15f928facacd9b1152b05575b6654f55b1132e2ac80ef54c68450c85",
            "salt"      =>  "f9aab579fc1b41ed0c44fe4ecdbfcdb4cb99b9023abb241a6db833288f4eea3c02f76e0d35204a8695077dcf81932aa59006423976224be0390395bae152d4ef",
            ]);

        $this->insert("core_auth_group",[
            "id"    =>  0,
            "name"  =>  "Administrators",
            ]);

        $this->insert("core_auth_group",[
            "id"    =>  1,
            "name"  =>  "Editors",
        ]);

        $this->insert("core_auth_group",[
            "id"    =>  2,
            "name"  =>  "Members",
        ]);

        // Site map data
        $this->insert("core_sitemap",[
            "id"            => 1,
            "pid"           => 0,
            "segment"       => "main",
            "layout"        => "base",
            "view"          => "main",
            "module"        => "Core",
            "controller"    => "main",
            "action"        => "index",
            "title"         => "Main page",
            "visible"       => true,
        ]);

        $this->insert("core_sitemap",[
            "id"            => 2,
            "pid"           => 1,
            "segment"       => "news",
            "layout"        => "base",
            "view"          => "news",
            "module"        => "Core",
            "controller"    => "news",
            "action"        => "index",
            "title"         => "News page",
            "visible"       => true,
        ]);

        $this->insert("core_sitemap",[
            "id"            => 3,
            "pid"           => 1,
            "segment"       => "content",
            "layout"        => "base",
            "view"          => "single",
            "module"        => "Core",
            "controller"    => "content",
            "action"        => "index",
            "title"         => "Content page",
            "visible"       => true,
        ]);

        $this->insert("core_sitemap",[
            "id"            => 4,
            "pid"           => 1,
            "segment"       => "login",
            "layout"        => "base",
            "view"          => "login",
            "module"        => "Core",
            "controller"    => "login",
            "action"        => "index",
            "title"         => "Account",
            "visible"       => true,
        ]);

        // Text demo data
        $this->insert("core_content",[
            "smap_id"           => 1,
            "lang_id"       => 1,
            "position"        => 1,
            "text"          => "<h1>Loyalty programm<br></h1><p>Intranet portal for DMBasis company.<br></p>",
        ]);

        $this->insert("core_content",[
            "smap_id"           => 1,
            "lang_id"       => 1,
            "position"        => 2,
            "text"          => "<h3>Block 1</h3><p>This is text of block.</p>",
        ]);

        $this->insert("core_content",[
            "smap_id"           => 1,
            "lang_id"       => 1,
            "position"        => 3,
            "text"          => "<h3>Block 2</h3><p>This is text of block.</p>",
        ]);

        $this->insert("core_content",[
            "smap_id"           => 1,
            "lang_id"       => 1,
            "position"        => 4,
            "text"          => "<h3>Block 3</h3><p>This is text of block.</p>",
        ]);

        $this->insert("core_content",[
            "smap_id"           => 3,
            "lang_id"       => 1,
            "position"        => 1,
            "text"          => '<h1><span style="color: rgb(0, 255, 0);" data-mce-style="color: #00ff00;">Ежемесячные отчеты об использовании</span></h1><p style="text-align: justify;" data-mce-style="text-align: justify;">&nbsp;Клиент должен представлять своему Торговому посреднику ежемесячный отчет об использовании или нулевом использовании в течение 10 (десяти) дней после последнего дня каждого месяца или в день, установленный по договоренности между Клиентом и его Торговым посредником. Уполномоченный представитель Клиента должен засвидетельствовать точность и полноту ежемесячного отчета об использовании или нулевом использовании.</p><ol><li style="text-align: justify;" data-mce-style="text-align: justify;"><span style="color: rgb(255, 0, 0);" data-mce-style="color: #ff0000;"><strong>Ежемесячная отчетность.</strong></span> Клиент должен предоставлять своему Торговому посреднику ежемесячные отчеты об использовании на основе данных об использовании за месяц. Торговый посредник Клиента должен отправить в Microsoft ежемесячный отчет об использовании на минимальную сумму в 100 долларов США за месяц (или эквивалентную сумму в применимой валюте по состоянию на дату вступления в силу) не позже 6 (шестого) месяца после даты вступления SPLA в силу. Торговый посредник Клиента должен предоставить ему подробные сведения о формате и процедуре представления отчетов. Клиент обязан предоставить все применимые сведения, которые требуется указать в ежемесячном отчете об использовании. Каждый ежемесячный отчет об использовании, который Торговый посредник Клиента отправляет в Microsoft, должен содержать, по крайней мере, следующую информацию:</li></ol><p><strong>(i)&nbsp;&nbsp;&nbsp; </strong>общее количество лицензий на каждый Продукт, которые Клиент использовал за предыдущий календарный месяц;</p><p><strong>(ii)&nbsp;&nbsp; </strong>имя и адрес Пользователя, если доход от этого Пользователя превысил 1000 долларов США в месяц (или эквивалентную сумму в применимой валюте по состоянию на дату вступления в силу);</p><p><strong>(iii)&nbsp; </strong>название страны, в которой находится данный Пользователь.</p><p>Клиент должен включить данные об использовании Продуктов своими Аффилированными лицами и Торговыми посредниками, распространяющими Программные услуги, и сводку по этим данным в ежемесячный отчет об использовании. Аффилированные лица Клиента и его Торговые посредники, распространяющие Программные услуги, не предоставляют ежемесячных отчетов об использовании непосредственно Торговому посреднику Клиента. Клиент не должен включать в ежемесячные отчеты об использовании демонстрации Пользователям, оценки Пользователями, оценки и тестирования Продуктов Клиентом либо администрирование и обслуживание сервера.</p><ol><li><strong>Нулевое использование.</strong> Клиент обязан представлять отчет о нулевом использовании своему Торговому посреднику. Отчет о нулевом использовании предоставляется только в случае Нулевого использования.</li><li><strong>Редакции отчетов.</strong> Все корректировки и редакции отчетов, уменьшающие размер лицензионных платежей для Microsoft (например, в связи с ошибками в заказах), должны быть представлены Клиентом Торговому посреднику в месячном отчете об использовании в течение 60 (шестидесяти) дней с даты начального счета. Клиент должен представить подробное объяснение корректировки или редакции в&nbsp;скорректированном ежемесячном отчете об использовании.</li><li><strong>Заключительный месячный отчет об использовании.</strong> Клиент обязан представить своему Торговому посреднику заключительный ежемесячный отчет об использовании или нулевом использовании в течение 30 (тридцати) дней со дня прекращения или окончания срока действия настоящего SPLA. Этот отчет должен включать данные об использовании Продуктов Клиентом до даты прекращения или окончания срока действия.</li><li><strong>Получение Продуктов и документации к программному обеспечению.</strong> Документацию к программному обеспечению и оригинальные носители или программное обеспечение с Продуктами можно заказать у Торгового посредника Клиента. Microsoft может ограничить количество копий оригинальных носителей, программного обеспечения и Документации к программному обеспечению, которые Торговый посредник Клиента может заказать в Microsoft. Microsoft предоставит Клиенту необходимые коды для установки, переустановки и копирования Продуктов в соответствии с условиями настоящего Соглашения.</li></ol>',
        ]);

        return $this->isInstalled(); //ToDo Проверить все ли правильно отработало и вернуть ошибку в случае неудачи
    }
} 