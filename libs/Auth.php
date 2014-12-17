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

class Auth extends Model
{
    public
        $userName = 'Guest',
        $isLogin = false,
        $groupName,
        $right = false,
        $read,
        $add,
        $edit,
        $delete,
        $userId,
        $groupId;

    function __construct() {
        parent::__construct();
        //$this->db = Registry::get('_db');

        $this->sec_session_start();

        if($this->login_check()){
            $this->access_check(Registry::get('_page')['id']);
        }

    }

    private function sec_session_start() {
        $session_name = 'sec_session_id';   // Set a custom session name
        $secure = SECURE;
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

    public function login($email, $password) {
        $db_password = '';
        $salt = '';
        // Using prepared statements means that SQL injection is not possible.
        $query = $this->database->pdo->prepare("
              SELECT t1.id AS id, t1.userName AS userName, t1.password AS password, t1.salt AS salt, t2.name AS groupName
              FROM authUsers t1
              LEFT JOIN authGroups t2 ON t1.groupId = t2.id
              WHERE t1.email = ?
              LIMIT 1");

        $result = $query->execute([$email]);    // Execute the prepared query.

        // get variables from result.
        $query->bindColumn('id',$this->userId);
        $query->bindColumn('userName', $this->userName);
        $query->bindColumn('groupName', $this->groupName);
        $query->bindColumn('password', $db_password);
        $query->bindColumn('salt', $salt);
        $query->fetch();

        // hash the password with the unique salt.
        $password = hash('sha512', $password.$salt);
        if ($result) {
            // If the user exists we check if the account is locked
            // from too many login attempts
            if ($this->checkbrute($this->userId) == true) {
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
                    $this->userId = preg_replace("/[^0-9]+/", "", $this->userId);
                    $_SESSION['user_id'] = (int)$this->userId;
                    // XSS protection as we might print this value
                    $this->userName = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $this->userName);
                    $_SESSION['username'] = $this->userName;
                    $_SESSION['login_string'] = hash('sha512',$password.$user_browser);
                    // Login successful.
                    $this->isLogin = true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $this->database->pdo->exec("INSERT INTO authLogins(userId, time) VALUES ('$this->userId', '$now')");
                    $this->isLogin = false;
                }
            }
        } else {
            // No user exists.
            $this->isLogin = false;
        }

        return $this->isLogin;
    }

    public function logout() {
        // Unset all session values
        $_SESSION = array();

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

        $this->userName = 'Guest';
        $this->isLogin = false;
    }

    private function checkbrute() {
        // Get timestamp of current time
        $now = time();

        // All login attempts are counted from the past 2 hours.
        $valid_attempts = $now - (2 * 60 * 60);

        if ($query = $this->database->pdo->prepare("SELECT COUNT(*) FROM authLogins WHERE userId = ? AND time > ?")
        ) {
            // Execute the prepared query.
            $query->execute([$this->userId, $valid_attempts]);

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
        $db_password = '';
        $this->isLogin = false;

        // Check if all session variables are set
        if (isset($_SESSION['user_id'],
        $_SESSION['username'],
        $_SESSION['login_string'])) {

            $this->userId = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];

            // Get the user-agent string of the user.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            if ($query = $this->database->pdo->prepare("
            SELECT t1.username AS userName, t1.password AS password, t1.groupId AS groupId, t2.name AS groupName
              FROM authUsers t1
              LEFT JOIN authGroups t2 ON t1.groupId = t2.id
              WHERE t1.id = ?
              ")
            ) {
                $query->execute([$this->userId]);
                $query->bindColumn('userName', $this->userName);
                $query->bindColumn('password', $db_password);
                $query->bindColumn('groupId', $this->groupId);
                $query->bindColumn('groupName', $this->groupName);
                $query->fetch();

                $password = trim($db_password);
                if ($this->userName == $_SESSION['username']) {
                    $login_check = hash('sha512', $password.$user_browser);
                    if ($login_check == $login_string) {
                        // Logged In!!!!
                        $this->isLogin = true;

                    }
                }
            }
        }
        return $this->isLogin;
    }

    public function access_check($smapId){
        // Super admin user or group?
        if($this->groupId == 0 or $this->userId == 0){
            $this->read = true;
            $this->add = true;
            $this->edit = true;
            $this->delete = true;
        } else {

            $query = $this->database->pdo->prepare("SELECT right FROM authAccess WHERE (userId = ? OR groupId = ?) AND smapId = ?");
            $query->execute([$this->userId, $this->groupId, $smapId]);
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
    private function esc_url($url) {

        if ('' == $url) {
            return $url;
        }

        $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

        $strip = array('%0d', '%0a', '%0D', '%0A');
        $url = (string) $url;

        $count = 1;
        while ($count) {
            $url = str_replace($strip, '', $url, $count);
        }

        $url = str_replace(';//', '://', $url);

        $url = htmlentities($url);

        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039;', $url);

        if ($url[0] !== '/') {
            // We're only interested in relative links from $_SERVER['PHP_SELF']
            return '';
        } else {
            return $url;
        }
    }
}
