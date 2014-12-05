Loyalty
=======
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/Supme/Loyalty?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Easy MVC intranet site.

Requery:

* PHP 5.4
* SQLite

In Twig templates you must use:

* {{ _user.isLogin }} (bool) User is login?
* {{ _user.read }} (bool) User have right on function read on this page
* {{ _user.add }} (bool) User have right on function add on this page
* {{ _user.edit }} (bool) User have right on function edit on this page
* {{ _user.delete }} (bool) User have right on function delete on this page
* {{ _user.userName }} (text) User name
* {{ _user.groupName }} (text) User group name
* {{ _page.id }} (int) Site map id this page
* {{ _page.segment }} (text) Address segment called script

In PHP:

Registry::get(var);

var:
* _db
* _auth (
  * ->read
  * ->add
  * ->edit
  * .......
* _page
  * ['id']
  * ['pid']
  * ['segment']
  * ['title']
  * ['visible']
  * .......
* _params

Comming soon...

