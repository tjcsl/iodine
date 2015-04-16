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
      * The default expiry time, in hours.
      */
    private static $DEFAULT_EXP = 4;

    /**
      * If there is a given token request, show the
      * SSO accept page. Otherwise, print a valid token.
      */
	function init_pane() {
        global $I2_QUERY;
        if(empty($I2_QUERY['req'])) {
            $this->template_args['error'] = "SSO token generated: ".self::gen_token();
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
        $redir = self::process_token($req);
        $this->template_args['sso'] = $req;
        $this->template_args['exphrs'] = (int)(($req['exp'] - time()) / (60*60));
        $this->template_args['redir'] = $redir;        
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
        return time() + 60*60*self::$DEFAULT_EXP;
    }

    /**
      * Check whether a given SSO token is valid.
      * Returns true for valid, false for expired,
      * and null for invalid.
      */
    static function valid_token($sso) {
        $tok = self::token_info($sso);
        var_dump($tok);
        if(!isset($tok) || sizeof($tok) < 2) return null;
        return time() > $tok['exp'];
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
      * Process the token request and return the
      * URL to return to, with SSO token attached.
      */
    static function process_token($dat) {
        if(empty($dat['return'])) return null;
        if(substr($dat['return'], 0, 8) != "https://") throw new I2Exception("Insecure protocol not allowed.");
        $sso = self::gen_token();
        return $dat['return']."?sso=".urlencode($sso);
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
        parse_str($PLAIN_str, $data);
        return $data;
    }

    /**
      * Decode a SSO token, returning an array
      * in the form [$user, $pass].
      */
    static function decode_token($sso) {
        $arr = self::token_info($sso);
        return array($arr['username'], $arr['password']);
    }

    /********** Token requests **********/

    /**
      * Decode information about the token request.
      */
    static function decode_req($req) {
        if(!isset($req) || sizeof($req) < 1) return array();
        parse_str(base64_decode($req), $arr);
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
                $ret = array("valid_token" => self::valid_token($I2_QUERY['sso']));
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
	}

	function get_name() {
		return "SSO";
	}
}
?>
