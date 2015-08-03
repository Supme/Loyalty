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
    protected
        $user_id = 0,
        $login = 'guest',
        $name = 'Guest',
        $email,
        $login_checked = false;

    public
        $isLogin = false;

    function __construct()
    {
        if ( !$this->login_checked )
        {
            parent::__construct();
            $this->login_check();
        }
    }

    public static function sec_session_start()
    {
        $session_name = 'session_id';   // Set a custom session name
        $session_lifetime = \Registry::get('_config')['user']['session_lifetime'];
        $secure = Registry::get('_config')['user']['secure'];
        // This stops JavaScript being able to access the session id.
        $httponly = true;
        // Forces sessions to only use cookies.
        if (ini_set('session.use_only_cookies', 1) === false) {
            header('Location: http://'.$_SERVER['HTTP_HOST'].'/error/403');
            exit();
        }

        ini_set('session.gc_maxlifetime', $session_lifetime);
        ini_set('session.cookie_lifetime', $session_lifetime);

        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(
            $session_lifetime,
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
        if (Registry::get('_config')['ad']['enable'])
        {
            $ad = new \Ad();
            $ad->login($login, $password);
        }

        $user = $this->get(
            'core_auth_user',
            [ 'id', 'login', 'name', 'password', 'salt' ],
            [
                'OR' => [
                    'email' => $login,
                    'login' => $login
                ]
            ]);

        // hash the password with the unique salt.
        $password = hash('sha512', $password.$user['salt']);
        if ($user) {
            // If the user exists we check if the account is locked
            // from too many login attempts
            if ($this->checkbrute($user['id']) == true) {
                // Account is locked
                // Send an email to user saying their account is locked
                $this->isLogin = false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if (trim($user['password']) === $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $this->user_id = preg_replace("/[^0-9]+/", "", $user['id']);
                    $_SESSION['user_id'] = (int)$this->user_id;
                    // XSS protection as we might print this value
                    $this->login = preg_replace("/[^a-zA-Z0-9_.\-]+/", "", $user['login']);
                    $_SESSION['userlogin'] = $this->login;
                    $_SESSION['login_string'] = hash('sha512',$password.$user_browser);

                    $this->name = $user['name'];

                    // Login successful.
                    $this->isLogin = true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $this->insert('core_auth_log',
                        [
                            'user_id' => $this->user_id,
                            '#time' => 'NOW()'
                        ]
                        );
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

    private function login_check() {

        //$this->isLogin = false;

        // Check if all session variables are set
        if (isset($_SESSION['user_id'], $_SESSION['userlogin'], $_SESSION['login_string']))
        {

            $this->user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];

            // Get the user-agent string of the user.
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            $user = $this->get('core_auth_user',['login', 'name', 'email', 'password'],['id' => $this->user_id]);
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->login = $user['login'];
            if ($this->login == $_SESSION['userlogin']) {
                $login_check = hash('sha512', $user['password'].$user_browser);
                if ($login_check == $login_string) {
                    // Logged In!!!!
                    $this->isLogin = true;
                }
            }
        }

        $this->login_checked = true;

        return $this->isLogin;
    }

    private function checkbrute($user_id)
    {
        // All login attempts are counted from the past 2 hours.
        $valid_attempts = time() - (2 * 60 * 60);

        $count = $this->count(
            'core_auth_log',
            ['AND' => [
                'user_id' => $user_id,
                'time' => $valid_attempts
                ]
            ]
        );

        // If there have been more than 5 failed logins
        if ($count > 5) {
            $this->isLogin = true;
        } else {
            $this->isLogin = false;
        }

        return $this->isLogin;
    }

// ----------- User data ----------------------------------------------------------------------
    public function getGroupName( $user_id = false )
    {
        if ( !$user_id ) $user_id = $this->user_id;

        return $this->select(
            'core_auth_group',
            [
                "[>]core_auth_user_group" => ["id" => "group_id"]
            ],
            'core_auth_group.name',
            [
                'core_auth_user_group.user_id' => $user_id
            ]
        );
    }

    public function getUserId()
    {
            return $this->user_id;
    }

    public function getUserName( $user_id = false )
    {
        if (!$user_id ){
            return $this->name;
        } else {
//ToDo
        }
    }

    public function getUserLogin( $user_id = false )
    {
        if (!$user_id ){
            return $this->login;
        } else {
//ToDo
        }
    }

    public function getUserEmail( $user_id = false )
    {
        if (!$user_id ){
            return $this->email;
        } else {
//ToDo
        }
    }

    public function getRight( $user_id = false, $sitemap_id = false)
    {
        $right['create'] = false;
        $right['read'] = false;
        $right['update'] = false;
        $right['delete'] = false;

        if ( !$user_id ) $user_id = $this->user_id;
        if ( !$sitemap_id ) $sitemap_id = \Registry::get('_page')['id'];

        if ($this->get('core_auth_user','isAdmin',['id' => $user_id]) == 1) // ToDo may by use has
        {
            $right['create'] = true;
            $right['read'] = true;
            $right['update'] = true;
            $right['delete'] = true;

        } else {
            if (!$this->has('core_auth_right', ['sitemap_id' => $sitemap_id]))
            {
                $right['create'] = false;
                $right['read'] = true;
                $right['update'] = false;
                $right['delete'] = false;
            } else {
                if (!$user_id)
                {
                    $right['create'] = false;
                    $right['read'] = false;
                    $right['update'] = false;
                    $right['delete'] = false;
                } else {
                    $rights = $this->select(
                        'core_auth_right',
                        [
                            "[>]core_auth_user_group" => ["group_id" => "group_id"],
                        ],
                        [
                            'core_auth_right.create',
                            'core_auth_right.read',
                            'core_auth_right.update',
                            'core_auth_right.delete',
                        ],
                        [
                            'AND' => [
                                'core_auth_user_group.user_id' => $user_id,
                                'core_auth_right.sitemap_id' => $sitemap_id,
                            ]
                        ]
                    );

                    foreach($rights as $r)
                    {
                        $right['create'] = $r['create'] != 0?true:false;
                        $right['read'] = $r['read'] != 0?true:false;
                        $right['update'] = $r['update'] != 0?true:false;
                        $right['delete'] = $r['delete'] != 0?true:false;
                    }
                }
            }
        }

        return $right;
    }

    public function canCreate()
    {
        return $this->getRight()['create'];
    }

    public function canRead()
    {
        return $this->getRight()['read'];
    }

    public function canUpdate()
    {
        return $this->getRight()['update'];
    }

    public function canDelete()
    {
        return $this->getRight()['delete'];
    }


//---------------------------- User action -------------------------------------------------------------------

    public function isUser($login, $email = null)
    {
        return $this->has('core_auth_user',
            [
                'OR' => [
                    'login' => $login,
                    'email' => $email
                ]
            ]
        );

    }

    public function idUser($login)
    {
        return $this->select('core_auth_user', 'id', ['login' => $login]);
    }

    public function createUser($name, $login, $email, $password)
    {
        $login = preg_replace("/[^a-zA-Z0-9._\-]+/", "", $login);

        if ($this->isUser($login, $email)) {
            // User already exist
            return false;
        } else {

            $salt = \Misc::randomString();
            $password = hash('sha512', $password . $salt);

            $userId = $this->insert('core_auth_user',
                [
                    'name' => $name,
                    'login' => $login,
                    'email' => $email,
                    'password' => $password,
                    'salt' => $salt
                ]
            );

            $defaultGroup = $this->get(
                'core_auth_group',
                'id',
                ['name' => Registry::get('_config')['user']['default_group']]
            );

            $this->insert('core_auth_user_group',
                [
                    'user_id' => $userId,
                    'group_id' => $defaultGroup
                ]
            );
        }
    }

    public function updateUser($id, $name, $login,  $email, $password)
    {
        $salt = \Misc::randomString();
        $password = hash('sha512', $password . $salt);

        return $this->update('core_auth_user',
            [
                'name' => $name,
                'login' => $login,
                'email' => $email,
                'password' => $password,
                'salt' => $salt
            ],
            ['id' => $id]

        );

    }

    // ToDo
    public function deleteUser($id)
    {

    }

    //ToDo объеденить или еще чё с $this->add
    public function __addUser($login, $name, $email, $password, $group)
    {
        $error = [];

        if (!\Validator::alnum()->validate($login) or $login == '') $error[] = 'Not valid login';
        if (!\Validator::alnum()->validate($name) or $name == '') $error[] = 'Not valid name';
        if (!\Validator::email()->validate($email)) $error[] = 'Not valid email';
        if (!\Validator::int()->validate($group)) $error[] = 'Not valid group';

        if (count($error) == 0) {
            $salt = \Misc::randomString();
            $password = hash('sha512', $password . $salt);
            $this->insert('core_auth_user',
                [
                    'group_id' => $group,
                    'login' => $login,
                    'email' => $email,
                    'name' => $name,
                    'password' => $password,
                    'salt' => $salt
                ]);
            $result = true;
        } else {
            $result['danger'] = $error;
        }

        return $result;
    }

}
