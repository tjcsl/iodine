[<include file="eighth/header.tpl">]
<h2>Choose an output format for [<$title>]:</h2>
<ul>
[<if !$user>]
	<li><a href="[<$I2_ROOT>]eighth/[<$module>]/print/format/print[<$args>]" style="font-weight: bold; font-size: 125%;">Print</a></li>
[</if>]
[<*	<li><a href="[<$I2_ROOT>]eighth/[<$module>]/print/format/html[<$args>]">HTML</a></li> *>]
	<li><a href="[<$I2_ROOT>]eighth/[<$module>]/print/format/pdf[<$args>]">PDF</a></li>
	<li><a href="[<$I2_ROOT>]eighth/[<$module>]/print/format/ps[<$args>]">PostScript</a></li>
	<li><a href="[<$I2_ROOT>]eighth/[<$module>]/print/format/dvi[<$args>]">DVI</a></li>
[<if !$user>]
	<li><a href="[<$I2_ROOT>]eighth/[<$module>]/print/format/tex[<$args>]">LaTeX</a></li>
	<li><a href="[<$I2_ROOT>]eighth/[<$module>]/print/format/rtf[<$args>]">RTF</a></li>
[</if>]
</ul>
