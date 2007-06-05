<script type="text/javascript">
var alum_page = "[<$I2_ROOT>]alum/pswd/";

function sendPass(){
	var oldpass = document.getElementById("pass1").value;
	var newpass = document.getElementById("pass2").value;
	var verpass = document.getElementById("pass3").value;
	var email = document.getElementById("email").value;
	if(newpass != verpass){
		alert("You messed up; try typing your new password again so both boxes match.");
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
				alert("Something went wrong making your account. Please try again later.");
			}
			else {
				alert("Cool. Your data is on the Alumni Intranet now.");
			}
		}
	};
	http.send('oldpass='+oldpass+'&newpass='+newpass+'&email='+email);
}
</script>
Old Password <input type="password" id="pass1" /><br/>
New Password <input type="password" id="pass2" /><br/>
Type Again <input type="password" id="pass3" /><br/>
Non-TJ E-mail <input type="text" id="email" /><br/>
<input type="submit" onClick="sendPass();" value="Get an Alumni Account">
