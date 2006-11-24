[<if isset($return)>]
 Return value (blank means FALSE):
 <pre>
 [<$return>]
 </pre>
<br />
[</if>]
<form action="[<$I2_SELF>]" method="post">
 Code:<br />
 <textarea name="codeinterface_code" rows="5" cols="50">[<$code>]</textarea>
 <br /><input type="submit" name="codeinterface_submit" value="Submit" />
</form>
