[<if $error>]
[<$error>]
[<else>]
<div style="font-size:16pt; font-weight:bold; border:1px solid; padding:4px 4px 4px 10px; margin:4px 7px 4px 0px; vertical-align:top; height:100%;">[<$temperature>]&#176;F&nbsp;<span style="font-size:normal; font-weight:normal;">(feels like [<$windchill>]&#176;F)</span></div>
<table id="weather_box" style="margin-left:3px;">
	<tr><td><b>Humidity:</b></td><td>[<$humidity>]%</td></tr>
	<tr><td><b>Pressure:</b></td><td>[<$barometer>]" and [<$bar_fall>]</td></tr>
	<tr><td><b>Winds:</b></td><td>From [<$wind_dir>] @ [<$wind>] mph</td></tr>
	<!--<tr><td><b>Precipitation:</b></td><td>[<$rain>]"/hr&nbsp;([<$rain_int>]" today)</td></tr>-->
	<tr><td><b>Rainfall:</b></td><td>[<$rain_int>]" today</td></tr>
</table>
[</if>]
