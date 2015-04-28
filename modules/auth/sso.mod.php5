<?php
/**
  * The SSO module, which allows Iodine to act as a 
  * Single Sign On provider and tokens to be shared
  * with applications instead of usernames and passwords.
  * @copyright 2015 The Intranet Development Team
  * @package modules
  * @subpackage auth
  */
class SSO extends Module {

	private $template_args = [];

    /**
      * The default expiry time, in seconds: 1 hour
      */
    private static $DEFAULT_EXP = 1*60*60;

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
        global $I2_QUERY;
        try {
            self::check_enable();
        } catch(I2Exception $e) {
            redirect("");
            return;
        }
        if(empty($I2_QUERY['req'])) {
            if($I2_USER->is_group_member('admin_all')) {
                $this->template_args['error'] = "SSO token generated: ".self::gen_token();
            } else {
                $this->template_args['error'] = "No SSO request token was given.";
            }
            return "Single Sign-on";
        }
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
            $redir = self::process_token($req);
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
    static function get_sso_key() {
        return i2config_get('key', null, 'sso');
    }

    /**
      * Get the default expiry time.
      */
    static function gen_default_exp() {
        return time() + self::$DEFAULT_EXP;
    }

    /**
      * Check whether a given SSO token is valid.
      * Returns true for valid, false for expired,
      * and null for invalid.
      */
    static function valid_token($sso) {
        $tok = self::token_info($sso);
        if(!isset($tok) || sizeof($tok) < 2) return null;
        return $tok['exp'] > time();
    }

    /**
      * Generate a token given the expiry time
      * (in epoch seconds)
      */
    static function gen_token($exp=null) {
        global $I2_USER;
        if(empty($exp) || $exp == null) $exp = self::gen_default_exp();
        $PLAIN_pwd = self::get_plain();
        $PLAIN_data = base64_encode(http_build_query(array("time" => time(), "exp" => $exp, "username" => $I2_USER->username, "password" => $PLAIN_pwd)));
        list($Nret, $Nkey, $Niv) = Auth::encrypt($PLAIN_data, self::get_sso_key());
        $sso = base64_encode(http_build_query(array("ret" => $Nret, "iv" => $Niv)));
        d("ssogen:".$sso);
        return $sso;
    }

    /**
      * Process the token request and return an array
      * with the URL to return to, and SSO token.
      */
    static function process_token($dat) {
        if(empty($dat['return'])) return null;
        if(substr($dat['return'], 0, 8) != "https://") throw new I2Exception("Insecure protocol not allowed.");
        $sso = self::gen_token();
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
      * token (ret and iv strings).
      */
    static function token_cryptinfo($sso) {
        parse_str(base64_decode($sso), $arr);
        return $arr;
    }
    
    /**
      * Return the secured information stored in a
      * token (username, password, time, and exp fields)
      */
    static function token_info($sso) {
        $crypt = self::token_cryptinfo($sso);
        $PLAIN_str = Auth::decrypt($crypt['ret'], self::get_sso_key(), $crypt['iv']);
        return self::token_cryptinfo($PLAIN_str);
    }

    /**
      * Decode a SSO token, returning an array
      * in the form [$user, $pass].
      */
    static function decode_token($sso) {
        self::check_enable();
        $arr = self::token_info($sso);
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
            if($I2_ARGS[2] == "valid_token" && isset($I2_QUERY['sso'])) {
                $dat = self::token_info($I2_QUERY['sso']);
                $ret = array("valid_token" => self::valid_token($I2_QUERY['sso']), "token_exp" => $dat['exp'], "token_time" => $dat['time'], "time" => time(), "username" => $dat['username']);
            } else if($I2_ARGS[2] == "gen_token") {
                $ret = array("gen_token" => self::gen_token($I2_QUERY['exp']));
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
