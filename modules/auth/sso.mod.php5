<?php

class SSO extends Module {

	private $template_args = [];

	function init_pane() {
        /*$str = base64_encode(openssl_random_pseudo_bytes(12));
        $key = base64_encode(openssl_random_pseudo_bytes(5));
        list($ret, $key2, $iv) = Auth::encrypt($str, $key);
*/
        //$dec = Auth::decrypt($I2_QUERY['str'], i2config_get('key', null, 'sso'), $I2_QUERY['iv']);
        //$dat = json_decode($dec);
        //print_r($dat);

        $req = self::find_req();
        if(empty($req['return'])) {
            $this->template_args['error'] = "SSO token generated: ".self::get_token();
        }
        $redir = self::process_token($req);
        $this->template_args['sso'] = $req;
        $this->template_args['redir'] = $redir;        
        return "Single-Sign On";
    }


    static function get_plain() {
        global $I2_USER;
        $str = $_SESSION['i2_password'];
        $key = $_SESSION['i2_auth_passkey'].substr(md5($_SERVER['REMOTE_ADDR']),0,16);
        $iv = $_COOKIE['IODINE_PASS_VECTOR'];
        $PLAIN_pwd = Auth::decrypt($str, $key, $iv);
        return $PLAIN_pwd;
    }

    static function get_token() {
        global $I2_USER;
        $PLAIN_pwd = self::get_plain();
        list($Nret, $Nkey, $Niv) = Auth::encrypt($PLAIN_pwd, i2config_get('key', null, 'sso'));
        $sso = base64_encode(http_build_query(array("username" => $I2_USER->username, "ret" => $Nret, "iv" => $Niv)));
        return $sso;
    }

    static function process_token($dat) {
        if(empty($dat['return'])) return null;
        if(substr($dat['return'], 0, 8) != "https://") throw new I2Exception("Insecure protocol not allowed.");
        $sso = self::get_token();
        return $dat['return']."?sso=".urlencode($sso);
	}

    static function token_info($sso) {
        parse_str(base64_decode($sso), $arr);
        return $arr;
    }

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
