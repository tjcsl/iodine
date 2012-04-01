<h1>Preferences</h1>

<p>On the preferences page, you can update your personal information, as well as your notification, privacy, and other Intranet settings.  Click the links below to find out more about particular sections.</p>

<a href="[<$I2_ROOT>]info/prefs/personalinfo" onclick="event.preventDefault();toggleCollapsed(document.getElementById('info_personal'));">Personal information</a>
<div class="collapsible collapsed" style="margin-left:0.5em;" id="info_personal">[<include file='info/prefs/personalinfo.tpl'>]</div>

<a href="[<$I2_ROOT>]info/prefs/privacy" onclick="event.preventDefault();toggleCollapsed(document.getElementById('info_privacy'));">Privacy options</a>
<div class="collapsible collapsed" id="info_privacy">[<include file='info/prefs/privacy.tpl'>]</div>

<a href="[<$I2_ROOT>]info/prefs/disp" onclick="event.preventDefault();toggleCollapsed(document.getElementById('info_disp'));">Display options</a>
<div class="collapsible collapsed" id="info_disp">[<include file='info/prefs/disp.tpl'>]</div>

<script type="text/javascript">
	var collapsibles = document.getElementsByClassName("collapsible");
	for(var i = 0; i < collapsibles.length; i++) {
		var headers = collapsibles[i].getElementsByTagName("h1");
		for(var j = 0; j < headers.length; j++) {
			collapsibles[i].removeChild(headers[j]);
		}
	}
</script>
