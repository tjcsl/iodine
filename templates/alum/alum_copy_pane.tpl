<script type="text/javascript">
var alum_page = "[<$I2_ROOT>]alum/pswd/";

function sendPass(){
	var newpass = document.getElementById("pass2").value;
	var verpass = document.getElementById("pass3").value;
	var email = document.getElementById("email").value;
	if(newpass != verpass){
		alert("Your passwords don't match; please try typing your new password again so both boxes match.");
		return;
	}
	if(email.indexOf("@tjhsst")!=-1){
		alert("You must supply a non-TJ e-mail address.");
		return;
	}
	http.open('POST', alum_page, true);
	http.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	http.onreadystatechange = function() {
		if(http.readyState == 4 && http.status == 200) {
			if(http.responseText != 1){
				alert("Something went wrong making your account or you're trying for a second time. Please contact Lee Burton at lburton@tjhsst.edu or 301 910 0246 at any time.");
			}
			else {
				alert("Your data is on the Alumni Intranet now. Visit it at https://alumni.tjtech.org/.");
			}
		}
	};
	http.send('newpass='+newpass+'&email='+email);
}
</script>
This page allows you to add yourself to <a href="https://alumni.tjtech.org/">alumni intranet (https://alumni.tjtech.org/)</a>.  Your username will be the email your specify below, and/or any other non-tj email in the current intranet database.<br />
Alumni Password <input type="password" id="pass2" /><br/>
Type Again <input type="password" id="pass3" /><br/>
Non-TJ E-mail <input type="text" id="email" /><br/>
<input type="submit" onclick="sendPass();" value="Get an Alumni Account">
