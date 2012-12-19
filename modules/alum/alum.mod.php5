<?php
/**
 * @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
 * @copyright 2007 The Intranet 2 Development Team
 * @since 1.0
 * @package modules
 * @subpackage alum
 * @filesource
 */

class Alum implements Module {

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return FALSE;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}

	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	function init_pane() {
		global $I2_USER,$I2_ARGS,$I2_SQL;

		return 'Alumni Intranet';

	}

	function display_pane($display) {
		global $I2_ARGS, $I2_LOG, $I2_SQL;
		global $I2_LDAP, $I2_USER;
		if(isset($I2_ARGS[1])){
			if($I2_ARGS[1] == 'pswd') {
				Display::stop_display();
				$newpass = $_REQUEST['newpass'];
				$email = $_REQUEST['email'];
				$success=false;

				$validkeys[]="cn";
				$validkeys[]="sn";
				$validkeys[]="displayName";
				$validkeys[]="startpage";
				$validkeys[]="nickname";
				$validkeys[]="givenName";
				$validkeys[]="middlename";
				$validkeys[]="street";
				$validkeys[]="l";
				$validkeys[]="st";
				$validkeys[]="postalCode";
				$validkeys[]="mobile";
				$validkeys[]="telephoneNumber";
				$validkeys[]="gender";
				$validkeys[]="header";
				$validkeys[]="chrome";
				$validkeys[]="perm-showaddress";
				$validkeys[]="perm-showtelephone";
				$validkeys[]="perm-showbirthday";
				$validkeys[]="perm-showschedule";
				$validkeys[]="perm-showpictures";
				$validkeys[]="perm-showeighth";
				$validkeys[]="perm-showmap";
				$validkeys[]="birthday";
				$validkeys[]="homePhone";
				$validkeys[]="aim";
				$validkeys[]="yahoo";
				$validkeys[]="msn";
				$validkeys[]="googleTalk";
				$validkeys[]="jabber";
				$validkeys[]="xfire";
				$validkeys[]="skype";
				$validkeys[]="webpage";
				$validkeys[]="style";
				$validkeys[]="graduationYear";
				$validkeys[]="perm-showaddress-self";
				$validkeys[]="perm-showtelephone-self";
				$validkeys[]="perm-showbirthday-self";
				$validkeys[]="perm-showschedule-self";
				$validkeys[]="perm-showeighth-self";
				$validkeys[]="perm-showmap-self";
				$validkeys[]="perm-showpictures-self";
				$validkeys[]="objectClass";
				//$validkeys[]="iodineUidNumber";
				//$validkeys[]="iodineUid";
				//$validkeys[]="pass";
				//$validkeys[]="mail";

				//check emails with data gotten
				//Get data,read from file, check if done already, find lowest num, write to file
				//send to mrow, receive from mrow
				$numrows=$I2_SQL->query('SELECT COUNT(*) FROM alum WHERE name=%s', $I2_USER->iodineuid)->fetch_single_value();
				if($numrows==0){

					$arr=$I2_LDAP->search_base(LDAP::get_user_dn($I2_USER))->fetch_all_arrays(Result::ASSOC);	
					$fp = fsockopen("ssl://beta.tjhsstalumni.org", 443, $errno, $errstr);
					if (!$fp) {
						$I2_LOG->log_error("$errstr ($errno)");
					} else {
						$I2_SQL->query('INSERT INTO alum set name=%s',$I2_USER->iodineuid);
						$mysqluid=$I2_SQL->query('SELECT id FROM alum WHERE name=%s',$I2_USER->iodineuid)->fetch_single_value();
						$data="";
						$count=0;
						$arr=$arr[LDAP::get_user_dn($I2_USER)];
						foreach($arr as $key => $value){
							if(!is_numeric($key)){
								if(in_array($key,$validkeys)){
									if(is_array($value)){
										$tmp="";
										$count2=0;
										foreach($value as $values){
											if($count2>0){
												$tmp.="^,^";
											}
											$tmp.=$values;
											$count2++;
										}
										$value=$tmp;
									}
									if($count>0){
										$data .= "&";
									}
									$data .= $key."=".$value;
									$count++;
									$arr2[$key]=$value;
								}
							}
						}
						if(is_array($arr["mail"])){
							foreach($arr["mail"] as $val){
								$mailer[]=$val;
							}
						}else{
							$mailer[]=$arr["mail"];
						}
						$mailer[]=$email;
						$semifinalmail=array_unique($mailer);
						$count=0;
						$finalmail="";
						foreach($semifinalmail as $mails){
							if(!stristr($mails,"tjhsst.edu")){
								if($count>0){
									$finalmail.="^,^";
								}
								$finalmail.=$mails;
								$count++;
							}
						}
						$arr2["mail"]=$finalmail;
						$data.="&mail=$finalmail";
						$arr2["pass"]=$newpass;
						$data.="&pass=$newpass";
						//$validkeys[]="iodineUidNumber";
						//$validkeys[]="iodineUid";
						$arr2["iodineUid"]=$mysqluid;
						$data.="&iodineUid=$mysqluid";
						$arr2["iodineUidNumber"]=$mysqluid;
						$data.="&iodineUidNumber=$mysqluid";
						$I2_LOG->log_error(print_r($arr2,true));
						//a=hi&b=foo
						$http_out  = "POST /trans/index.php HTTP/1.0\r\n";
						$http_out .= "Host: beta.tjhsstalumni.org\r\n";
						$http_out .= "User-Agent: iodineiscool \r\n";
						$http_out .= "Content-Type: application/x-www-form-urlencoded\r\n";
						$http_out .= 'Content-Length: ' . strlen($data) . "\r\n";
						$http_out .= "\r\n$data\r\n\r\n";
						$I2_LOG->log_error($http_out);


						fputs($fp, $http_out, strlen($http_out));  // send request SOAP
						$http_inbuf="";
						while (!feof($fp)) {
							$http_in = fgets($fp, 4096);
							if(stristr($http_in,"Yes")){
								$success=true;
								//$I2_LOG->log_error("Success!");
							}
							if(stristr($http_in,"No")){
								$success=false;
							}
							$http_inbuf.=$http_in;
							//$I2_LOG->log_error($http_in);	
						}



						fclose($fp);
					}
/*
$info["cn"] = "Lee Burton";
$info["sn"] = "Burton";
$info["displayName"] = "Lee Reed Burton";
$info["startpage"] = "news";
$info["iodineUid"] = "1000";
$info["nickname"] = "lburton";
$info["givenName"] = "Lee";
$info["middlename"] = "Reed";
$info["mail"] = "lburton@mrow.org^,^lburton@tjhsst.edu";
$info["street"] = "6806 Springfield Dr";
$info["l"] = "Mason Neck";
$info["st"] = "VA";
$info["postalCode"] = "22079";
$info["mobile"] = "3019100246";
$info["telephoneNumber"] = "3019168098";
$info["gender"] = "M";
$info["header"] = "TRUE";
$info["chrome"] = "TRUE";
$info["perm-showaddress"] = "FALSE";
$info["perm-showtelephone"] = "FALSE";
$info["perm-showbirthday"] = "FALSE";
$info["perm-showschedule"] = "FALSE";
$info["perm-showpictures"] = "FALSE";
$info["perm-showeighth"] = "FALSE";
$info["perm-showmap"] = "FALSE";
$info["birthday"] = "19890827";
$info["homePhone"] = "7035502058";
$info["aim"] = "sg1bc";
$info["yahoo"] = "unixdiff";
$info["msn"] = "lee@sg1net.com";
$info["googleTalk"] = "sg1net";
$info["jabber"] = "sg1net@gmail.com";
$info["xfire"] = "wnxsg1";
$info["skype"] = "sg1net";
$info["webpage"] = "https://pictars.mrow.org^,^http://mrow.org";
$info["style"] = "modern";
$info["graduationYear"] = "2007";
$info["perm-showaddress-self"] = "TRUE";
$info["perm-showtelephone-self"] = "TRUE";
$info["perm-showbirthday-self"] = "TRUE";
$info["perm-showschedule-self"] = "TRUE";
$info["perm-showeighth-self"] = "TRUE";
$info["perm-showmap-self"] = "TRUE";
$info["perm-showpictures-self"] = "TRUE";
$info["iodineUidNumber"] = "1000";
$info["objectclass"] = "tjhsstStudent";
$info["pass"] = "test";
 */
				}

				if($success){
					echo 1;
				} else {
					echo 0;
				}
				exit;
			}
		}
		else {		
			$display->disp('alum_copy_pane.tpl');
		}
	}

	function init_box() {
		return FALSE;
	}

	function display_box($display) {
		return FALSE;
	}

	function get_name() {
		return 'Alum';
	}
}

?>

