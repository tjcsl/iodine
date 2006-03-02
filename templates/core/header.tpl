<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title>TJHSST Intranet2[<if $title != "" >]: [<$title>][</if>]</title>
<link type="text/css" rel="stylesheet" href="[<$I2_CSS>]" />
<link rel="shortcut icon" href="[<$I2_ROOT>]www/favicon.ico" />
<link rel="icon" href="[<$I2_ROOT>]www/favicon.ico" />
<!--[if IE]>
<script src="[<$I2_ROOT>]www/js/ie7-core.js" type="text/javascript"></script>
<script src="[<$I2_ROOT>]www/js/ie7-graphics.js" type="text/javascript"></script>
<![endif]-->
</head>
<body>
<div class="logo"></div>

<div class="header">
 <div class="title"> Welcome, [<$first_name>]! </div>
 [<include file='core/menu.tpl'>]
</div>
<div class="date">[<$smarty.now|date_format:"%B %e, %Y">]</div>
