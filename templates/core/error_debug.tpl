<script type="text/javascript" src="[<$I2_ROOT>]www/js/cookie.js"></script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/genresize.js"></script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/ieemu.js"></script>
<script type="text/javascript">
if (moz) {
	extendElementModel();
	extendEventObject();
	emulateEventHandlers(["mousemove", "mousedown", "mouseup"]);
}
</script>
<script type="text/javascript">
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
		setCookie(id + "_open", "true", "/");
	} else {
		minimize(div);
		setCookie(id + "_open", "false", "/");
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
<!--[if gte IE 5.5]>
<![if lt IE 7]>
<script type="text/javascript">
function getYPos(id) {
	var div = document.getElementById(id);
	var element = document.documentElement;
	var body = document.body;
	return (element.clientHeight ? element.clientHeight : body.clientHeight)
		+ (element.scrollTop ? element.scrollTop : body.scrollTop)
		- div.offsetHeight;
}


function getXPos(id) {
	var div = document.getElementById(id);
	var element = document.documentElement;
	var body = document.body;
	return (element.clientWidth ? element.clientWidth : body.clientWidth) 
		+ (element.scrollLeft ? element.scrollLeft : body.scrollLeft )
		- div.offsetWidth;
}
</script>
<style type="text/css">
div#error, div#debug {
	position:absolute;
	right: auto;
	bottom: auto;
}

div#error {
	left: 0px;
	top: expression(getYPos('error') + 'px');
}

div#debug {
	left: expression(getXPos('debug') + 'px');
	top:  expression(getYPos('debug') + 'px');
}
</style>
<![endif]>
<![endif]-->
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
<script type="text/javascript">
if (eval(getCookie('error_open', 'true'))) {
	var error_div = document.getElementById("error");
	if (error_div) {
		maximize(error_div);
	}
}
if (eval(getCookie('debug_open', 'false'))) {
	var debug_div = document.getElementById("debug");
	if (debug_div) {
		maximize(debug_div);
	}
}
</script>
</body>
</html>
