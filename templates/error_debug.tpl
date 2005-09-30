<script language="JavaScript">
function minimize(name) {
	var div = document.getElementById(name);
	if (/ minimized/.test(div.className)) {
		div.style.width = [<if $debug && $errors>]style="45%"[<else>]style="90%"[</if>];
		div.style.height = "";
		div.className = div.className.replace(/ minimized/, "");
	} else {
		div.style.width = "";
		div.style.height = "";
		div.className += " minimized";
	}
}
</script>
[<if $errors>]
 <div class="error" [<if $debug>]style="width:45%;"[<else>]style="width:90%;"[</if>] id="error">
  <div class="minimize" onclick="minimize('error')"></div>
  Iodine has encountered the following errors: <br /><br />
  [<$errors>]
 </div>
[</if>]
[<if $debug>]
 <div class="debug minimized" id="debug">
  <div class="button_container"><div class="minimize" onclick="minimize('debug')"></div></div>
  Debug messages: <br /><br />
  [<$debug>]
 </div>
[</if>]
