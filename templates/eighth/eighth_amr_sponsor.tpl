[<include file="eighth/eighth_header.tpl">]
<form action="[<$I2_ROOT>]eighth/amr_sponsor/modify/sid/[<$sponsor->sid>]" method="post">
	First Name: <input type="text" name="fname" value="[<$sponsor->fname>]"><br />
	Last Name: <input type="text" name="lname" value="[<$sponsor->lname>]"><br /><br />
	<input type="submit" value="Modify"><br />
	<a href="[<$I2_ROOT>]eighth/amr_sponsor/remove/sid/[<$sponsor->sid>]"><input type="button" value="Remove"></a>
</form>
