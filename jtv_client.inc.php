<?php
include('jtv_constants.inc.php');

/*

Requires the OAuth PECL extension. Install it with pecl install oauth. See http://pecl.php.net/package/oauth for more info.

How to use:

    How to be authenticated as a user:
        1. Put your oauth keys in jtv_constants.inc.php
        2. Call start_user_authentication, which will recirect the user to Justin.tv to log in. $redirect should be a page on your site that implements step 3.
        3. Create a page that calls recieve_user_authentication, which will return true on success.
        4. Do authenticated requests with the get and post functions.


    See the test directory for an example usage.


*/

class BaseJtvClient {

    //user functions

    public static function start_user_authorization($redirect){
        trigger_error("Method not implemented");
    }

    public static function recieve_user_authorization(){
        trigger_error("Method not implemented");
    }

    public static function get($path, $data=null, $prefix=JTV_API_PREFIX) {
        trigger_error("Method not implemented");
    }

    public static function post($path, $data, $prefix=JTV_API_PREFIX) {
        trigger_error("Method not implemented");
    }

    //access functions
    public static function is_authorized(){
        global $_SESSION;
        return $_SESSION['JTV_AUTHORIZED'];
    }

    protected static function get_access_token(){
        global $_SESSION;
        return $_SESSION['JTV_ACCESS_TOKEN'];
    }

    protected static function set_access_token($token){
        global $_SESSION;
        $_SESSION['JTV_ACCESS_TOKEN'] = $token;
        $_SESSION['JTV_AUTHORIZED'] = true;
    }

    protected static function get_access_token_secret(){
        global $_SESSION;
        return $_SESSION['JTV_ACCESS_TOKEN_SECRET'];
    }

    protected static function set_access_token_secret($token){
        global $_SESSION;
        return $_SESSION['JTV_ACCESS_TOKEN_SECRET'] = $token;
    }

    protected static function get_request_token(){
        global $_SESSION;
        return $_SESSION['JTV_REQUEST_TOKEN'];
    }

    protected static function set_request_token($token){
        global $_SESSION;
        $_SESSION['JTV_REQUEST_TOKEN'] = $token;
    }

    protected static function get_request_token_secret(){
        global $_SESSION;
        return $_SESSION['JTV_REQUEST_TOKEN_SECRET'];
    }

    protected static function set_request_token_secret($token) {
        global $_SESSION;
        $_SESSION['JTV_REQUEST_TOKEN_SECRET'] = $token;
    }

    
    //helper functions
    private static function make_oauth(){
        trigger_error("Method not implemented");
    }

    protected static function array_to_get_vars($arr) {
        if (!$arr) {
            return '';
        }
        $pieces = array();
        foreach ($arr as $k => $v) {
          array_push($pieces, urlencode($k).'='.urlencode($v));
        }
        return implode('&', $pieces);
    }
}

class JtvClient extends BaseJtvClient {
    
    public static function start_user_authorization($redirect){
        $oauth = self::make_oauth();
        $req_token = $oauth->getRequestToken(JTV_REQUEST_TOKEN_URL);
        if (!empty($req_token)) {
            self::set_request_token($req_token['oauth_token']);
            self::set_request_token_secret($req_token['oauth_token_secret']);
            header('Location: '.JTV_AUTHORIZE_URL.'?'.self::array_to_get_vars(array('oauth_token' => $req_token['oauth_token'], 'oauth_callback' => $redirect)));
            return true;
        }
        return false;
    }

    public static function recieve_user_authorization(){
        $oauth = self::make_oauth();
        $oauth->setToken(self::get_request_token(), self::get_request_token_secret());
        $access_token = $oauth->getAccessToken(JTV_ACCESS_TOKEN_URL, null, $_GET['oauth_token']);
        if (!empty($access_token)) {
            self::set_access_token($access_token['oauth_token']);
            self::set_access_token_secret($access_token['oauth_token_secret']);
            return true;
        }
        return $access_token;
    }
    
    public static function get($path, $data=null, $prefix=JTV_API_PREFIX) {
        $url = JTV_HOST.$prefix.$path.".json?".self::array_to_get_vars($data);
        if (JTV_DEBUG == true) print_r($url);
        $oauth = self::make_oauth();
        $result = $oauth->fetch($url, null, OAUTH_HTTP_METHOD_GET);
        if ($result) {
            $last_response = $oauth->getLastResponse();
            $res = json_decode($last_response, true);
            if ($res) {
                return $res;
            } else {
                return $last_response;
            }
        }
        return $result;
    }

    public static function post($path, $data, $prefix=JTV_API_PREFIX) {
        $url = JTV_HOST.$prefix.$path.".json";
        $oauth = self::make_oauth();
        $result = $oauth->fetch($url, $data, OAUTH_HTTP_METHOD_POST);
        if ($result) {
            $last_response = $oauth->getLastResponse();
            $res = json_decode($last_response, true);
            if ($res) {
                return $res;
            } else {
                return $last_response;
            }
        }
        return $result;
    }

    private static function make_oauth(){
        $oauth = new OAuth(JTV_CONSUMER_KEY, JTV_CONSUMER_SECRET, OAUTH_SIG_METHOD_HMACSHA1);
        if (JTV_DEBUG == true) $oauth->enableDebug();
        if (self::is_authorized()) {
            $oauth->setToken(self::get_access_token(), self::get_access_token_secret());
        }
        return $oauth;
    }
}

?>
