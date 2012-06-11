[<if $blacklisted>]
	<p>Your ability to post lost items has been revoked, likely due to your abuse of the Intranet lost &amp; found system.  Please contact [<mailto address=$iodinemail encode=hex>] for information about regaining this privilege.</p>
[<else>]
	[<if isSet($added)>]
		[<if $added>]
			Your item has been added.
		[<else>]
			An error occurred; please try again.  If you encounter this problem repeatedly, you may contact the Intramaster.
		[</if>]
	[<else>]
		<script type="text/javascript">
			function checkDescriptionLength() {
				var length = document.getElementById("description").value.length;
				var lengthLeft = 10000 - length;
				document.getElementById("descriptionLength").innerHTML = lengthLeft + " character" + ((lengthLeft == 1) ? "" : "s") + " remaining";
			}
		</script>
		Before posting an item here, please note:
		<ul>
			<li>Make sure your e-mail address and/or other contact information is made visible in your <a href="[<$I2_ROOT>]prefs">preferences</a></li>
			<li>If it is not, please mention in your item description how someone can return the item to you if it is found.</li>
			<li style="color:red;">If you post anything spammy or inappropriate, your ability to post lost items may be revoked.</li>
			<li style="color:red;">The security office does not always check this list and may not contact you if they find one of your belongings.  If you have lost one of your posessions, you should
		</ul>
		<form name="add_form" method="post" action="[<$I2_ROOT>]lostnfound/add">
			Title <small>(a quick summary)</small>:
			<br />
			<input type="text" name="title" maxlength="200" />
			<br /><br />
			Description <small>(responses longer than 10,000 characters will be truncated)</small>
			<br />
			<textarea name="text" id="description" rows="5" cols="80" maxlength="10000" oninput="checkDescriptionLength();"></textarea>
			<br />
			(<span id="descriptionLength">10000 characters remaining</span>)i
			<br /><br />
			<button type="submit">Submit</button>
		</form>
	[</if>]
[</if>]
