<?php
/***
 */
$managerpswd = 	"MANAGERPSWDHERE";

$search =	"username";
$class =	"senior";
$path =		"/home/wyang/200809pics_seniorformal/lifetouch/images";
$file =		"noid00000.jpg";
/***
 */
$ldapconn = ldap_connect('iodine-ldap.tjhsst.edu');
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
$bind = ldap_bind($ldapconn,"cn=Manager,dc=tjhsst,dc=edu",$managerpswd);
$udn = ldap_search($ldapconn, 'ou=people,dc=tjhsst,dc=edu', "iodineuid={$search}", array('dn'));
$info = ldap_get_entries($ldapconn, $udn);
if($info['count']==0)
	continue;
echo "dn: cn={$class}Photo,";
echo $info[0]["dn"] . "\r\n";
echo "cn: {$class}Photo\r\n";
echo "objectClass: iodinePhoto\r\njpegPhoto::";
echo base64_encode(`convert "{$path}"/"{$file}" -format "jpeg" -resize 172x228 -`);
echo "\r\n\r\n";
?>
