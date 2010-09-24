[<include file="eighth/header.tpl">]
<form action="[<$I2_ROOT>]eighth/amr_activity/modify/aid/[<$activity->aid>]" method="post">
	<table style="border: 0px; padding: 0px; margin: 0px;">
		<tr>
			<td>Name:</td>
			<td><input type="text" name="name" value="[<$activity->name|escape:"html">]" /></td>
		</tr>
		<tr>
			<td>Sponsor(s):</td>
			<td>
[<if count($activity->sponsors) > 0 >]
				<select name="sponsors[]" id="sponsors" size="[<php>] echo count($this->_tpl_vars['activity']->sponsors); [</php>]">
[<php>]
	$this->_tpl_vars['sponsors'] = EighthSponsor::id_to_sponsor($this->_tpl_vars['activity']->sponsors);
[</php>]
[<foreach from=$sponsors item='sponsor'>]
					<option value="[<$sponsor->sid>]">[<$sponsor->name_comma>]</option>
[</foreach>]
				</select>
[<else>]
				<span style="color: #FF0000; font-weight: bold;">No sponsors selected</span>
[</if>]
				<input type="button" value="Add" onclick="location.href='[<$I2_ROOT>]eighth/amr_activity/select_sponsor/aid/[<$activity->aid>]';" />
[<if count($activity->sponsors) > 0 >]
				<input type="button" value="Remove" onclick="location.href='[<$I2_ROOT>]eighth/amr_activity/remove_sponsor/aid/[<$activity->aid>]/sid/' + document.getElementById('sponsors').options[document.getElementById('sponsors').selectedIndex].value;" />
[</if>]
			</td>
		</tr>
		<tr>
			<td>Room(s):</td>
			<td>
				<script type="text/javascript">
				function hider(obj,textbox) {
					if(obj.style.display=="none"){
						obj.style.display="block";
						textbox.innerHTML="hide";
					} else {
						obj.style.display="none";
						textbox.innerHTML="show";
					}
				}
				</script>
				<a id="roomhider" onclick="hider(document.getElementById('roomdiv'),this)">show</a>
				<div id="roomdiv" style="display:none">
[<if count($activity->rooms) > 0 >]
				<select name="rooms[]" id="rooms" size="[<$activity->rooms|@count>]">
[<php>]
	$this->_tpl_vars['rooms'] = EighthRoom::id_to_room($this->_tpl_vars['activity']->rooms);
[</php>]
[<foreach from=$rooms item='room'>]
					<option value="[<$room->rid>]">[<$room->name>]</option>
[</foreach>]
				</select>
[<else>]
				<span style="color: #FF0000; font-weight: bold;">No rooms selected</span>
[</if>]
				<input type="button" value="Add" onclick="location.href='[<$I2_ROOT>]eighth/amr_activity/select_room/aid/[<$activity->aid>]';" />
[<if count($activity->rooms) > 0 >]
				<input type="button" value="Remove" onclick="location.href='[<$I2_ROOT>]eighth/amr_activity/remove_room/aid/[<$activity->aid>]/rid/' + document.getElementById('rooms').options[document.getElementById('rooms').selectedIndex].value;" />
[</if>]
				</div>
			</td>
		</tr>
		<tr>
			<td>Description:</td>
			<td><textarea name="description" rows="3" cols="30">[<$activity->description>]</textarea></td>
		</tr>
		<tr>
			<td>Restricted:</td>
			<td><input type="checkbox" name="restricted"[<if $activity->restricted >] checked="checked"[</if>] /></td>
		</tr>
		<tr>
			<td>48 Hour:</td>
			<td><input type="checkbox" name="presign"[<if $activity->presign >] checked="checked"[</if>] /></td>
		</tr>
		<tr>
			<td>One-A-Day:</td>
			<td><input type="checkbox" name="oneaday"[<if $activity->oneaday >] checked="checked"[</if>] /></td>
		</tr>
		<tr>
			<td>Both Blocks:</td>
			<td><input type="checkbox" name="bothblocks"[<if $activity->bothblocks >] checked="checked"[</if>] /></td>
		</tr>
		<tr>
			<td>Sticky:</td>
			<td><input type="checkbox" name="sticky"[<if $activity->sticky >] checked="checked"[</if>] /></td>
		</tr>
		<tr>
			<td>Special:</td>
			<td><input type="checkbox" name="special"[<if $activity->special >] checked="checked"[</if>] /></td>
		</tr>
	</table><br />
	<input type="submit" value="Modify" />
	<a href="[<$I2_ROOT>]eighth/amr_activity/remove/aid/[<$activity->aid>]"><input type="button" value="Remove" /></a>
</form>
