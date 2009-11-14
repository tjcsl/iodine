[<if $usermail>]
<form method="post" class="boxform" action="[<$I2_ROOT>]suggestion">
[</if>]
[<if isSet($mailed)>]
	<br />
	[<if $mailed>]
		Your suggestion has been submitted. Thank you for your input.
	[<else>]
		There was a problem submitting your suggestion. Please contact the Intranet Developers for assistance.	
	[</if>]
[<else>]
	<script type="text/javascript">
		var emails = new Array();
		[<foreach from=$sendchoices item=choice name=loop>]
		emails[[<$smarty.foreach.loop.index>]] = "[<$choice.address>]";
		[</foreach>]
		function changeLink() {
			var newemail = emails[document.getElementById("sendchoice").selectedIndex];
			var maillink = document.getElementById("mailtolink");
			maillink.href="mailto:" + newemail;
			maillink.firstChild.data=newemail;
		}
	</script>
	How do you think improvements can be made for <select id="sendchoice" name="sendchoice" onchange="changeLink()">[<foreach from=$sendchoices item=choice>]<option name="[<$choice.name>]">[<$choice.name>][</foreach>]</select>?<br />
	[<if $usermail>]
		We will follow up with you at <strong>[<$usermail>]</strong> if needed.  If this e-mail address is incorrect, please update your preferences, or feel free to send an e-mail directly to <a id="mailtolink" href="mailto:[<$sendchoices[0].address>]">[<$sendchoices[0].address>]</a>.  If you send us an e-mail directly, don't forget to tell us who you are!<br />
		<textarea name="submit_box" style="width:98%;height:150px"></textarea><br />
		<input type="submit" value="Submit" name="submit_form" />
		</form>
	[<else>]
		<br />
		Oops...it looks like you didn't enter an e-mail address in your preferences.<br />
		To use the suggestion box, it is required that you specify an e-mail address in your preferences so we can contact you about your suggestion if needed.  However, if you would still like to send us a suggestion without sharing your e-mail with all of TJ, feel free to send an e-mail directly to <a id="mailtolink" href="mailto:intranet@tjhsst.edu">intranet@tjhsst.edu</a>.  Don't forget to tell us who you are!
[</if>][</if>]
