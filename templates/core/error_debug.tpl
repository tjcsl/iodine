<script type="text/javascript" src="[<$I2_ROOT>]www/js/cookie.js"></script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/genresize.js"></script>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/ieemu.js"></script>
<script type="text/javascript">
	var default_width = '[<if $debug && $errors>]45%[<else>]90%[</if>]';
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
<script type="text/javascript" src="[<$I2_ROOT>]www/js/debug.js"></script>
</body>
</html>
