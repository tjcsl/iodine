<?php
/**
  * The SSO module, which allows Iodine to act as a 
  * Single Sign On provider and tokens to be shared
  * with applications instead of usernames and passwords.
  * @copyright 2015 The Intranet Development Team
  * @package modules
  * @subpackage auth
  */
/**

TO IMPLEMENT SINGLE SIGN-ON IN YOUR APPLICATION:
* Create a request token.
    * A request token includes the following information:
        * title: Application title
        * author: Application author (optional)
        * return: Return/callback URL
        * time: The current epoch time
        * exp: The time the token will expire (usually time()+120 or similar)
        * method: GET or POST (defaults to POST)
    * Make an array/dictionary of this information and convert it to a URL-encoded query string
  (http_build_query in PHP, urllib.parse.urlencode in Python)
    * Encode the query string with base64 to generate the request token.
* Redirect to $I2_ROOT/sso?req=$requesttoken
    * The user will be redirected to the Iodine login page, if they do not currently have a session active.
    * The user will see a screen stating:
        * "The application $title by $author would like to access your Intranet account."
        * "If you fully trust this application and developer, press the OK button below."
        * If the "Cancel" button is pressed, they will be sent to the Iodine login page.
    * The user accepts the authorization request, and is redirected back to the callback URL specified.
        * If method is "GET", then the access key is the "sso" query value.
        * If method is "POST", then the access key is the "sso" POST value.
* The application now has the access key, but needs to verify that it is correct.
    * Send a GET or POST request to $I2_ROOT/ajax/sso/valid_key?sso=$accesskey
    * A JSON object will be returned containing information about the token.
        * $json["sso"]["valid_key"]:
            * null = Invalid token
            * false = The key has expired
            * true = The key is valid and has not expired
        * $json["sso"]["token_exp"]: The expiry time in epoch seconds
        * $json["sso"]["token_time"]: The epoch time when the token was created
        * $json["sso"]["time"]: The current server time
        * $json["sso"]["username"]: The username of the user that the token was generated for
* To use your now valid access key:
    * Send a POST request to $I2_ROOT/ with login_sso=$accesskey to begin an Iodine session
        * Save and pass the PHPSESSID and IODINE_PASS_VECTOR cookies to all further requests
    * Send a request to any API endpoint with the GET or POST argument login_sso=$accesskey and follow
      the redirect to get the contents of that page.
        * This does not require saving cookies
        * Get information on the logged-in user by requesting $I2_ROOT/ajax/studentdirectory/info (JSON) or
          $I2_ROOT/api/studentdirectory/info (XML).
* Any questions, comments, or issues with the SSO module should be directed to 2016jwoglom@tjhsst.edu

PROCESS:
* Third party application generates request token (reqtok)
  * Redirects to $I2_ROOT/sso?req=$tok
* User logs in to Iodine
  * Password decrypted from session, combined with username and encrypted (credtok)
  * Secret token generated containing encrypted credentials (sectok)
  * Access token assigned to secret token in database (acckey)
  * User hits accept
  * Access token sent to application
* Third party recieves identifier
  * Sends request to $I2_ROOT/ajax/sso/valid_key?sso=$acckey
    * If JSON object contains valid_key: true, authentication was completed successfully
    * $acckey may be used to log in to the Iodine session for the time period allocated with the token (usually 1hr)
*/
class SSO extends Module {

	private $template_args = [];

    /**
      * The default expiry time, in seconds: 1 hour
      */
    private static $DEFAULT_EXP = 3600;

    static function check_enable() {
        if(i2config_get('enabled', FALSE, 'sso') != true) {
            throw new I2Exception("SSO module is disabled.");
        }
    }

    /**
      * If there is a given token request, show the
      * SSO accept page. Otherwise, print a valid token.
      */
	function init_pane() {
        global $I2_QUERY, $I2_USER;
        try {
            self::check_enable();
        } catch(I2Exception $e) {
            redirect("");
            return;
        }
        if(empty($I2_QUERY['req'])) {
            if($I2_USER->is_group_member('admin_all')) {
                $this->template_args['error'] = "acckey generated: ".self::gen_acckey();
            } else {
                $this->template_args['error'] = "No SSO request token was given.";
            }
            return "Single Sign-on";
        }

        // Decode request token
        $req = self::find_req();
        if(time() > $req['exp']) {
            $this->template_args['error'] = "The given SSO request token has expired.";
            return "Single Sign-on";
        }
        if(empty($req) || empty($req['return'])) {
            $this->template_args['error'] = "An invalid SSO request token was entered.";
            return "Single Sign-on";
        }
        try {
            // Process request token
            $redir = self::process_reqtok($req);
            if(isset($redir[2]) && $redir[2] == "GET") {
                $redirhtml = self::redirect_get($redir);
            } else {
                $redirhtml = self::redirect_post($redir);
            }
            $this->template_args['sso'] = $req;
            $this->template_args['exphrs'] = round(($req['exp'] - time()) / (60*60));
            $this->template_args['redir'] = $redirhtml;
        } catch(Exception $e) {
            $this->template_args['error'] = "An error occurred decoding the token.";
        }
        return "Single Sign-on";
    }

    /**
      * Return the plain password of the current
      * logged in user.
      */
    static function get_plain() {
        global $I2_USER;
        $str = $_SESSION['i2_password'];
        $key = $_SESSION['i2_auth_passkey'].substr(md5($_SERVER['REMOTE_ADDR']),0,16);
        $iv = $_COOKIE['IODINE_PASS_VECTOR'];
        $PLAIN_pwd = Auth::decrypt($str, $key, $iv);
        return $PLAIN_pwd;
    }

    /**
      * Get SSO key from preferences.
      */
    static function get_decrypt_key() {
        $cfgkey = i2config_get('key', null, 'sso');
        if($cfgkey == null) return null;
        return $cfgkey;
    }

    /**
      * Get the default expiry time.
      */
    static function gen_default_exp() {
        return time() + self::$DEFAULT_EXP;
    }

    /**
      * Check if the sectok given by a acckey is valud.
      */
    static function valid_acckey($key) {
        $tok = self::grab_sectok($key);
        if($tok == null || $tok == false) return false;
        return self::valid_sectok($tok);
    }

    /**
      * Check whether a given SSO token is valid.
      * Returns true for valid, false for expired,
      * and null for invalid.
      */
    static function valid_sectok($sso) {
        $tok = self::sectok_info($sso);
        if(!isset($tok) || sizeof($tok) < 2) return null;
        return $tok['exp'] > time();
    }

    /**
      * Generate a token given the expiry time
      * (in epoch seconds)
      */
    static function gen_sectok($exp=null) {
        global $I2_USER;
        $PLAIN_pwd = self::get_plain();
        $PLAIN_data = base64_encode(http_build_query(array("time" => time(), "exp" => $exp, "username" => $I2_USER->username, "password" => $PLAIN_pwd)));
        list($Nret, $Nkey, $Niv) = Auth::encrypt($PLAIN_data, self::get_decrypt_key());
        $sso = base64_encode(http_build_query(array("ret" => $Nret, "iv" => $Niv)));
        d("ssogen:".$sso);
        return $sso;
    }

    /**
      * Generate an access token (acckey) given
      * the expiry time. This is what is sent
      * to the application -- the secret TOKEN
      * is saved in the database and is accessed
      * using the key.
      */
    static function gen_acckey($exp=null) {
        global $I2_SQL;
        if(empty($exp) || $exp == null) $exp = self::gen_default_exp();
        $tok = self::gen_sectok($exp);
        d("sectok: $tok");
        $key = openssl_random_pseudo_bytes(64);
        $key = substr(base64_encode($key), 0, 64);
        $key = preg_replace("/[^a-zA-Z0-9]+/", "", $key);
        d("key: $key");
        d("exp: $exp");
        $I2_SQL->query("INSERT INTO sso_sessions VALUES(%s, %s, %s)", $key, $tok, $exp);
        return $key;
    }


    /**
      * Process the token request and return an array
      * with the URL to return to, and SSO token.
      */
    static function process_reqtok($dat) {
        if(empty($dat['return'])) return null;
        if(substr($dat['return'], 0, 8) != "https://") throw new I2Exception("Insecure protocol not allowed.");
        $sso = self::gen_acckey();
        $args = array($dat['return'], $sso, isset($dat['method']) ? $dat['method'] : "POST");
        return $args;
	}

    /**
      * Returns the HTML code for a button that 
      * redirects with a GET request.
      */
    static function redirect_get($args) {
        return "<button onclick=\"location.href = '" . addslashes($args[0] . "?sso=" . urlencode($args[1])) . "'\">OK</button>";
    }

    
    /**
      * Returns the HTML code for a button that
      * makes a POST request.
      */
    static function redirect_post($args) {
        return '<form method="POST" action="' . htmlspecialchars($args[0]) . '" style="display: inline">' .
               '<input type="hidden" name="sso" value="' . htmlspecialchars($args[1]) . '" />' .
               '<input type="submit" value="OK" />' .
               '</form>';
    }

    /**
      * Return the crypto information stored in a
      * sectok (ret and iv strings) OR credtok (username and password).
      */
    static function token_cryptinfo($sso) {
        parse_str(base64_decode($sso), $arr);
        return $arr;
    }

    /**
      * Return the secure token given the public key
      */
   static function grab_sectok($key) {
       global $I2_SQL;
       $res = $I2_SQL->query("SELECT token FROM sso_sessions WHERE acckey=%s;", $key)->fetch_single_value();
       d("GRAB_TOKEN");
       d_r($res);
       return $res;
   }

   /**
     * Return the secured information stored in a
     * token given the public key.
     */
   static function key_info($key) {
       $tok = self::grab_sectok($key);
       return self::sectok_info($tok);
   }
    
    /**
      * Return the secured information stored in a
      * token (username, password, time, and exp fields)
      */
    static function sectok_info($sso) {
        $crypt = self::token_cryptinfo($sso);
        if(!(isset($crypt['ret']) && isset($crypt['iv']))) {
            warn("Invalid token.");
            return;
        }
        $PLAIN_str = Auth::decrypt($crypt['ret'], self::get_decrypt_key(), $crypt['iv']);
        return self::token_cryptinfo($PLAIN_str);
    }

    /**
      * Decode a SSO token, returning an array
      * in the form [$user, $pass].
      */
    static function decode_credtok($sectok) {
        self::check_enable();
        $arr = self::sectok_info($sso);
        return array($arr['username'], $arr['password']);
    }

    /********** Token requests **********/

    /**
      * Decode information about the token request.
      */
    static function decode_req($req) {
        try {
            SSO::check_enable();
        } catch(I2Exception $e) {
            warn("SSO module is not enabled.");
            return;
        }
        if(!isset($req) || sizeof($req) < 1) return array();
        parse_str(base64_decode($req), $arr);
        if(empty($arr) || empty($arr['exp'])) return null;
        $arr['exptime'] = $arr['exp'] - time();
        return $arr;
    }

    /**
      * Generate a request token.
      */
    static function gen_req($data) {
        return base64_encode(http_build_query($data));
    }

    /**
      * Find the request token in the querystring.
      */
    static function find_req() {
        global $I2_QUERY;
        if(isset($I2_QUERY['req'])) {
            return self::decode_req($I2_QUERY['req']);
        }
        return null;
    }

    /**
      * AJAX JSON API
      */
    function ajax() {
        global $I2_QUERY, $I2_ARGS;
        // [api, sso]
        if(isset($I2_ARGS[2])) {
            if($I2_ARGS[2] == "valid_key" && isset($I2_QUERY['sso'])) {
                $tok = self::grab_sectok($I2_QUERY['sso']);
                $dat = self::sectok_info($tok);
                $ret = array("valid_key" => self::valid_sectok($tok), "token_exp" => $dat['exp'], "token_time" => $dat['time'], "time" => time(), "username" => $dat['username']);
            } else if($I2_ARGS[2] == "gen_key") {
                $ret = array("gen_key" => self::gen_acckey($I2_QUERY['exp']));
            } else if($I2_ARGS[2] == "decode_req" && isset($I2_QUERY['req'])) {
                $ret = array("decode_req" => self::decode_req($I2_QUERY['req']));
            }
            echo json_encode(array("sso" => $ret));
        }
    }

	function display_pane($disp) {
        $disp->disp("sso.tpl", $this->template_args);
	}

	function init_box() {
		return "SSO";
	}

	function display_box($disp) {
        $disp->disp("sso.tpl", $this->template_args);
	}

	function get_name() {
		return "SSO";
	}
}
?>
