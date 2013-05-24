<script type="text/javascript" src="[<$I2_ROOT>]www/js/news_groups.js"></script>
[<if isset($edited)>]
Your news post has been changed.<br />
<a href="[<$I2_ROOT>]news">Back to news</a><br />
[</if>]
<form action="[<$I2_SELF>]" method="post">
 <input type="hidden" name="edit_form" value="1" />
 <table cellpadding="0" width="100%">
 <tr><td width="20%">Title:</td><td><input type="text" name="edit_title" value="[<$newsitem->title|replace:'"':'&quot;'>]"[<*'*>] size="30" /></td></tr>
 <tr><td>Expiration date:</td><td><input type="text" name="edit_expire" size="30" value="[<$newsitem->expire>]"/></td></tr>
 <tr><td width="20%">Visible:</td><td><input type="checkbox" name="edit_visible"[<if $newsitem->visible>] checked="checked"[</if>] /></td><td>Public:</td><td><input type="checkbox" name="edit_public"[<if $newsitem->public>] checked="checked"[</if>] /></td></tr>
 </table>
 <table id="news_groups_table" cellpadding="0" width="100%">
  <tr>
   <td width="20%">Groups:</td>
   <td width="1%">
    [<if count($newsitem->groups) == 0>]
    <select id="groups" class="groups_list" name="add_groups[]">
     [<foreach from=$groups item=group>]
      <option value="[<$group->gid>]">[<$group->name>]</option>
     [</foreach>]
    </select>
    [<else>]
    <select id="groups" class="groups_list" name="add_groups[]">
     [<foreach from=$groups item=group>]
      <option value="[<$group->gid>]"[<if $group->gid == $newsitem->groups[0]->gid>] selected="selected"[</if>]>[<$group->name>]</option>
     [</foreach>]
    </select>
    [</if>]
   </td>
   <td>&nbsp;</td>
  </tr>
  <tr>
   <td>&nbsp;</td>
   <td><a href="#" onclick="news_addGroup(); return false">Add another group</a></td>
   <td>&nbsp;</td>
  </tr>
 </table>
 <script type="text/javascript">
  [<section name=i loop=$newsitem->groups start=1>]
   news_addGroup([<$newsitem->groups[i]->gid>]);
  [</section>]
 </script>
 Text: <br />
 [<include file="richedit/editor.tpl">]
 <input type="hidden" id="text" name="edit_text" />
 <script type="text/javascript">
	window.addEventListener("load", function() {
		formfield = document.getElementById("RichForm").contentWindow.document;
		if(!formfield) {
			formfield = document.getElementById("RichForm").contentDocument;
		}
		formfield.designMode='on';
		fillinarea();
	}, false);
	function fillinarea() {
		formfield.body.innerHTML = "[<$newsitem->text|replace:'"':"'"|replace:"\n":'<br />'>]";
    var subhead = formfield.getElementsByTagName("head")[0];
		var css = formfield.createElement("link");
		css.setAttribute("rel", "stylesheet");
		css.setAttribute("type", "text/css");
		css.setAttribute("href", "[<$I2_CSS>]");
		subhead.appendChild(css);
	}
 </script>
 <input type="submit" value="Submit" name="submit" onclick="doonsubmit()" />
</form>
