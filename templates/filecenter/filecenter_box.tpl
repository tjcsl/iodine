[<*<a href="[<$I2_ROOT>]filecenter/lan/">View your Windows (S:) files</a><br />
	<a href="ftp://[<$I2_USER->username>]@technology.tjhsst.edu">Alternate S: Access (best in IE for drag+drop)</a><br />
<a href="[<$I2_ROOT>]filecenter/portfolio/[<$grad_year>]/[<$i2_username>]/">View your portfolio files</a><br />*>]
[<if $I2_USER->objectclass == 'tjhsstStudent'>]<a href="http://shares.tjhsst.edu/[<$tj01path>]">View your Windows (M:) files</a><br />[</if>]
[<if $I2_USER->objectclass == 'tjhsstStudent'>]<a href="http://shares.tjhsst.edu/portfolio/[<$grad_year>]/[<$i2_username>]">View your portfolio</a><br />[</if>]
[<if $I2_USER->objectclass == 'tjhsstStudent'>]<a href="http://shares.tjhsst.edu/upload2.php">Upload to Windows files</a><br />[</if>]
<a href="http://shares.tjhsst.edu/">View all Windows files</a><br />
<a href="[<$I2_ROOT>]filecenter/main/students/[<$grad_year>]/[<$i2_username>]/">Access your UNIX files</a><br />
<a href="[<$I2_ROOT>]filecenter/csl/user/[<$csl_username>]/">Access your old Systems Lab files</a><br />
[<if $I2_USER->objectclass == 'tjhsstTeacher'>]
  	<a href="http://shares.tjhsst.edu/Staff">Staff Intranet</a>
[</if>]
