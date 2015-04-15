<?php
/**
 * @package Loyality Portal
 * @author Supme
 * @copyright Supme 2014
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

class Auth extends Db
{
    public
        $login = 'guest',
        $name = 'Guest',
        $isLogin = false,
        $group,
        $right = false,
        $read,
        $add,
        $edit,
        $delete,
        $user_id,
        $group_id;

    function __construct()
    {
        parent::__construct();

        $this->sec_session_start();

        if($this->login_check()){
            $this->access_check(Registry::get('_page')['id']);
        }

    }

    private function sec_session_start()
    {
        $session_name = 'sec_session_id';   // Set a custom session name
        $secure = Registry::get('_config')['user']['secure'];
        // This stops JavaScript being able to access the session id.
        $httponly = true;
        // Forces sessions to only use cookies.
        if (ini_set('session.use_only_cookies', 1) === false) {
            header("Location: ../error/403");
            exit();
        }
        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params($cookieParams["lifetime"],
            $cookieParams["path"],
            $cookieParams["domain"],
            $secure,
            $httponly);
        // Sets the session name to the one set above.
        session_name($session_name);
        session_start();            // Start the PHP session
        session_regenerate_id();    // regenerated the session, delete the old one.
    }

    public function login($login, $password)
    {
        // Using prepared statements means that SQL injection is not possible.
        $query = $this->pdo->prepare("
              SELECT t1.id AS id, t1.login AS login, t1.name AS name, t1.password AS password, t1.salt AS salt, t2.name AS 'group'
              FROM core_auth_user t1
              LEFT JOIN core_auth_group t2 ON t1.group_id = t2.id
              WHERE t1.email = ? OR t1.login = ?
              LIMIT 1");

        $result = $query->execute([$login, $login]);    // Execute the prepared query.

        // get variables from result.
        $query->bindColumn('id',$this->user_id);
        $query->bindColumn('login', $this->login);
        $query->bindColumn('name', $this->name);
        $query->bindColumn('group', $this->group);
        $query->bindColumn('password', $db_password);
        $query->bindColumn('salt', $salt);
        $query->fetch();

        // hash the password with the unique salt.
        $password = hash('sha512', $password.$salt);
        if ($result) {
            // If the user exists we check if the account is locked
            // from too many login attempts
            if ($this->checkbrute($this->user_id) == true) {
                // Account is locked
                // Send an email to user saying their account is locked
                $this->isLogin = false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if (trim($db_password) === $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $this->user_id = preg_replace("/[^0-9]+/", "", $this->user_id);
                    $_SESSION['user_id'] = (int)$this->user_id;
                    // XSS protection as we might print this value
                    $this->login = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $this->login);
                    $_SESSION['userlogin'] = $this->login;
                    $_SESSION['login_string'] = hash('sha512',$password.$user_browser);
                    // Login successful.
                    $this->isLogin = true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $this->pdo->exec("INSERT INTO core_auth_login(user_id, time) VALUES ('$this->user_id', '$now')");
                    $this->isLogin = false;
                }
            }
        } else {
            // No user exists.
            $this->isLogin = false;
        }

        return $this->isLogin;
    }

    public function logout()
    {
        // Unset all session values
        $_SESSION = [];

        // get session parameters
        $params = session_get_cookie_params();

        // Delete the actual cookie.
        setcookie(session_name(),
            '', time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]);

        // Destroy session
        session_destroy();

        $this->login = 'guest';
        $this->name = 'Guest';
        $this->isLogin = false;
    }

    private function checkbrute()
    {
        // Get timestamp of current time
        $now = time();

        // All login attempts are counted from the past 2 hours.
        $valid_attempts = $now - (2 * 60 * 60);

        if ($query = $this->pdo->prepare("SELECT COUNT(*) FROM core_auth_login WHERE user_id = ? AND time > ?")
        ) {
            // Execute the prepared query.
            $query->execute([$this->user_id, $valid_attempts]);

            // If there have been more than 5 failed logins
            if ($query->fetch()[0] > 5) {
                $this->isLogin = true;
            } else {
                $this->isLogin = false;
            }
        }

        return $this->isLogin;
    }

    private function login_check() {

        $this->isLogin = false;

        // Check if all session variables are set
        if (isset($_SESSION['user_id'], $_SESSION['userlogin'], $_SESSION['login_string']))
        {

            $this->user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];

            // Get the user-agent string of the user.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            $query = $this->pdo->prepare("
            SELECT t1.login AS login, t1.name AS name, t1.password AS password, t1.group_id AS group_id, t2.name AS group_name
              FROM core_auth_user t1
              LEFT JOIN core_auth_group t2 ON t1.group_id = t2.id
              WHERE t1.id = ?
            ");

                $query->execute([$this->user_id]);
                $query->bindColumn('login', $this->login);
                $query->bindColumn('name', $this->name);
                $query->bindColumn('password', $db_password);
                $query->bindColumn('group_id', $this->group_id);
                $query->bindColumn('group_name', $this->group);
                $query->fetch();

                $password = trim($db_password);
                if ($this->login == $_SESSION['userlogin']) {
                    $login_check = hash('sha512', $password.$user_browser);
                    if ($login_check == $login_string) {
                        // Logged In!!!!
                        $this->isLogin = true;

                    }
                }
        }

        return $this->isLogin;
    }

    // ToDo перенести все эти права доступа в отдельное место
    public function access_check($smap_id)
    {
        // Super admin user or group?
        if($this->group_id == 0 or $this->user_id == 0){
            $this->read = true;
            $this->add = true;
            $this->edit = true;
            $this->delete = true;
        } else {

            $query = $this->pdo->prepare("SELECT right FROM core_auth_access WHERE (user_id = ? OR group_id = ?) AND smap_id = ?");
            $query->execute([$this->user_id, $this->group_id, $smap_id]);
            $query->bindColumn('right', $this->right);
            $query->fetch();

            switch ($this->right){
                case 0:
                    $this->read = false;
                    $this->add = false;
                    $this->edit = false;
                    $this->delete = false;
                    break;
                case 1:
                    $this->read = true;
                    $this->add = false;
                    $this->edit = false;
                    $this->delete = false;
                    break;
                case 2:
                    $this->read = true;
                    $this->add = true;
                    $this->edit = false;
                    $this->delete = false;
                    break;
                case 3:
                    $this->read = true;
                    $this->add = true;
                    $this->edit = true;
                    $this->delete = false;
                    break;
                case 4:
                    $this->read = true;
                    $this->add = true;
                    $this->edit = true;
                    $this->delete = true;
                    break;
            }

        }
        return $this->right;
    }

    public function getGroups()
    {
        return $this->select('core_auth_group', ['id','name']);
    }

}
