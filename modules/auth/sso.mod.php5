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
    private $DEFAULT_EXP = 24;

    /**
      * If there is a given token request, show the
      * SSO accept page. Otherwise, print a valid token.
      */
	function init_pane() {
        $req = self::find_req();
        if(empty($req['return'])) {
            $this->template_args['error'] = "SSO token generated: ".self::get_token();
        }
        $redir = self::process_token($req);
        $this->template_args['sso'] = $req;
        $this->template_args['redir'] = $redir;        
        return "Single-Sign On";
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
      * Generate a token given the expiry time (in hours)
      */
    static function get_token($exp=null) {
        global $I2_USER;
        if(empty($exp)) $exp = self::$DEFAULT_EXP;
        $PLAIN_pwd = self::get_plain();
        list($Nret, $Nkey, $Niv) = Auth::encrypt($PLAIN_pwd, i2config_get('key', null, 'sso'));
        $sso = base64_encode(http_build_query(array("username" => $I2_USER->username, "ret" => $Nret, "iv" => $Niv, "time" => time(), "exp" => $exp)));
        return $sso;
    }

    /**
      * Process the token and return the URL to
      * return to, with SSO token attached.
      */
    static function process_token($dat) {
        if(empty($dat['return'])) return null;
        if(substr($dat['return'], 0, 8) != "https://") throw new I2Exception("Insecure protocol not allowed.");
        $sso = self::get_token();
        return $dat['return']."?sso=".urlencode($sso);
	}

    /**
      * Return the information stored in a token
      * (the username, time, ret, and iv strings).
      */
    static function token_info($sso) {
        parse_str(base64_decode($sso), $arr);
        return $arr;
    }

    /**
      * Decode a SSO token, returning an array
      * in the form [$user, $pass].
      */
    static function decode_token($sso) {
        $arr = self::token_info($sso);
        $PLAIN_pwd = Auth::decrypt($arr['ret'], i2config_get('key', null, 'sso'), $arr['iv']);
        return array($arr['username'], $PLAIN_pwd);
    }

    /**
      * Decode information about the token request.
      */
    static function decode_req($req) {
        if(!isset($req) || sizeof($req) < 1) return array();
        parse_str(base64_decode($req), $arr);
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
