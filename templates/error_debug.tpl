<script language="javascript" src="[<$I2_ROOT>]www/js/cookie.js"></script>
<script language="javascript" src="[<$I2_ROOT>]www/js/genresize.js"></script>
<script language="javascript" src="[<$I2_ROOT>]www/js/ieemu.js"></script>
<script language="javascript">
if (moz) {
	extendElementModel();
	extendEventObject();
	emulateEventHandlers(["mousemove", "mousedown", "mouseup"]);
}
</script>
<script language="JavaScript">
var divs = Array();
divs['error'] = 
{ 
	width : getCookie('error_width', '[<if $debug && $errors>]45%[<else>]90%[</if>]'),
	height : getCookie('error_height', '')
};

divs['debug'] = 
{
	width : getCookie('debug_width', '[<if $debug && $errors>]45%[<else>]90%[</if>]'),
	height : getCookie('debug_height', '')
};

function swap(id) {
	var div = document.getElementById(id);
	if (/ minimized/.test(div.className)) {
		maximize(div);
		setCookie(id + "_open", "true");
	} else {
		minimize(div);
		setCookie(id + "_open", "false");
	}
}

function minimize(div) {
	divs[div.id].width = div.style.width;
	divs[div.id].height = div.style.height;
	div.style.width = "";
	div.style.height = "";
	div.className = div.className.replace(/ resizeMe/, "");
	div.className += " minimized";
}

function maximize(div) {
	div.style.width = divs[div.id].width;
	div.style.height = divs[div.id].height;
	div.className = div.className.replace(/ minimized/, "");
	div.className += " resizeMe";
}

</script>
[<if $errors>]
 <div class="error minimized" id="error">
  <div class="button_container"><div class="minimize" onclick="swap('error')"></div></div>
  Iodine has encountered the following errors:
  [<$errors>]
 </div>
[</if>]
[<if $debug>]
 <div class="debug minimized" id="debug">
  <div class="button_container"><div class="minimize" onclick="swap('debug')"></div></div>
  Debug messages:
  [<$debug>]
 </div>
[</if>]
<script language="javascript">
if (eval(getCookie('error_open', 'true'))) {
	maximize(document.getElementById('error'));
}
if (eval(getCookie('debug_open', 'false'))) {
	maximize(document.getElementById('debug'));
}
</script>
</body>
</html>
