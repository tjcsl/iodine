<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title>TJHSST Intranet2[<if $title != "" >]: [<$title>][</if>]</title>
<link type="text/css" rel="stylesheet" href="[<$I2_CSS>]" />
<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
<!--[if lt IE 7.]>
<script defer type="text/javascript" src="[<$I2_ROOT>]www/pngfix.js"></script>
<![endif]-->
</head>
<body>
<div class="logo">

</div>
<div class="header">
 <div class="title"> Welcome, [<$first_name>]! </div>
 <span id="menu">
  <a href="[<$I2_ROOT>]">Home</a>

  <span class="bold">&middot;</span>
  <a href="http://sct.tjhsst.edu/">SCT</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]prefs">Preferences</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]info/credits">Credits</a>
  <span class="bold">&middot;</span>

  <a href="#">Help</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]mysqlinterface">MySQL Interface</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]ldapinterface">LDAP Interface</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]logout">Logout</a>
 </span>
</div>
<div class="date">[<$smarty.now|date_format:"%B %e, %Y">]</div>
