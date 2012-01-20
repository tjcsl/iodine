<script type='text/javascript'>
var blockarray = [];
[<foreach from=$blocklist item=block name="blockloop">]
blockarray['[<$block.bid>],[<$block.gid>]']=[[<$block.bid>],[<$block.gid>],'[<$block.aidlist>]'];
[</foreach>]
function changeForm() {
	var select = document.getElementById('blocklist');
	var opts = select.options;
	var key=opts[select.selectedIndex].value;
	document.getElementById('bid').value=blockarray[key][0];
	document.getElementById('newbid').value=blockarray[key][0];
	document.getElementById('gid').value=blockarray[key][1];
	document.getElementById('aidlist').innerHTML=blockarray[key][2];
}
</script>
[<include file="eighth/header.tpl">]
<select id='blocklist' size=10 onchange="changeForm()">
[<foreach from=$blocklist item=block>]
<option value="[<$block.bid>],[<$block.gid>]">[<$block.bid>]</option>
[</foreach>]
</select>
<form id="reslistform" action="[<$I2_ROOT>]eighth/restrictionlists/" method="POST">
<!--Block id:--!><input type="hidden" value="" name='bid' id='bid'/>
New Block id:<input type="text" value="" name='newbid' id='newbid'/>
Group id:<input type="text" value="" name='gid' id='gid'/><br />
Activities Restricted:<br/>
<textarea name='aidlist' id='aidlist'>
</textarea><br/>
<input type='submit' name="Add" value="Add" onclick="document.getElementById('reslistform').action='[<$I2_ROOT>]eighth/restrictionlists/add'" />
<input type='submit' name="Edit" value="Edit" onclick="document.getElementById('reslistform').action='[<$I2_ROOT>]eighth/restrictionlists/edit'" />
<input type='submit' name="Delete" value="Delete" onclick="document.getElementById('reslistform').action='[<$I2_ROOT>]eighth/restrictionlists/delete'" />
</form>
