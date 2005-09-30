<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title>TJHSST Intranet2[<if $title != "" >]: [<$title>][</if>]</title>
<link type='text/css' rel='stylesheet' href="[<$I2_CSS|default:'/www/styles/default.css'>]" />
<link type='text/css' rel='stylesheet' href="[<$I2_ROOT>]/www/styles/global.css" /> 
</head>
<body>
<div class="logo">

</div>
<div class="header">
 <div class="title"> Welcome, [<$first_name>]! </div>
 <span id="menu">
  <a href="[<$I2_ROOT>]">Home</a>

  <span class="bold">&middot;</span>
  <a href="#">SCT</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]prefs">Preferences</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]credits">Credits</a>
  <span class="bold">&middot;</span>

  <a href="#">Help</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]logout">Logout</a>
 </span>
</div>
<div class="date">[<$smarty.now|date_format:"%B %e, %Y">]</div>
