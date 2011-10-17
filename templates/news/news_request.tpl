[<if $usermail>]
<form method="post" class="boxform" action="[<$I2_ROOT>]news/request">
[</if>]
[<if isSet($mailed)>]
	[<if $mailed>]
		Your request has been submitted for approval.
	[<else>]
		There was a problem submitting your request. Please contact the Intranet Developers for assistance.	
	[</if>]
[<else>]
	Do you want to post an informational news article or announcement on Intranet? This page allows you to easily submit news for approval.<br />
	<p style="color:red;font-weight:bold;">Due to a recent policy change, all news posts must be submitted or approved by staff members before they will be posted.</p>
	[<if $usermail>]
		We will follow up with you at <strong>[<$usermail>]</strong> if needed.  If this e-mail address is incorrect, please update your preferences, or feel free to send an e-mail directly to [<mailto address=$iodinemail encode=hex >].  If you send us an e-mail directly, make sure you include the title, content, expiration date, and other notes for your news post, and don't forget to tell us who you are!<br />
		<br />
		<strong>Specifications for News Posts:</strong><br />
		To increase the chances that your post comes up quickly, please mind the following:<br />
		<ol>
		<li>Use correct English grammar, punctuation, and spelling; <strong>do not</strong> use all caps; keep posts concise when possible, and use active voice for better clarity.  Posts get sent out to a few hundred people via email and are viewed several thousand times. Therefore, please be at least a little professional when requesting them.</li>
		<li>If you'd like us to attach a file to the post or put in an image, please leave a note to us in the note field. If you put a publicly-accessible URL there, we'll copy the file over to intranet's servers for hosting.</li>
		<li>If you have a link to an external website in your post, make sure that it can be accessed without having to register for that site. Facebook links that require you to have registered cannot be used for this reason, and may be omitted. This is to improve compliance with the FCPS Network User Guidelines.</li>
		<li>If you are talking about a club, activity, or event in your post, please put the location and time in your post body. Otherwise people will have no idea where or when it is.  Please also try to make your post unique; we do not need twenty posts all titled "Free food!"</li>
		<li>If there's a well-defined group, such as "The class of 2012" or "Only male students" that you'd like to limit your post to, add that as a note in the notes field.  If we have that group in the system, then we will post it to that group; otherwise, we will e-mail you to try to get it worked out.</li>
		<li>DO NOT make requests for lost-and-found-type notices.  Due to volume, we won't post these.  Instead, go to lost-and-found in the school.</li>
		<li>Keep it SFW.</li>
		</ol>
		<strong>We reserve the power to edit requests at our discretion.  If your news post does not comply with our requirements, we may modify it or ask you to revise it.</strong><br/><br/>
		Title*:<br /><input type="text" name="submit_title" style="width:98%" required="required"/><br />
		Expiration Date* (the news post will be removed at midnight on this date):<br /><input type="text" name="submit_expdate" style="width:98%" required="required"/><br />
		Contents*:<br />
		<textarea name="submit_box" style="width:98%;height:150px" required="required"></textarea><br />
		Notes:<br />
		<textarea name="notes_box" style="width:98%;height:35px"></textarea><br />
		<input type="submit" value="Submit" name="submit_form" />
		</form>
	[<else>]
		<br />
		Oops...it looks like you didn't enter an e-mail address in your preferences.<br />
		To use the request box, it is required that you specify an e-mail address in your preferences so we can contact you about your request if needed.  However, if you would still like to send us a request without putting your e-mail on the Intranet, feel free to send an e-mail directly to <a id="mailtolink" href="mailto:[<$iodinemail>]">[<$iodinemail>]</a> with the title, contents, expiration date, and other notes about your post.  Don't forget to tell us who you are!
[</if>][</if>]
