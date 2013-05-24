<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 

		<title>Welcome to Intrabook</title>
		
		<link rel="shortcut icon" href="[<$I2_ROOT>]www/pics/fb/favicon.ico" />
		<link rel="icon" href="[<$I2_ROOT>]www/pics/fb/favicon.ico" />
		
		<script type="text/javascript" src="[<$I2_ROOT>]www/js/logins/special/swirl.js"></script>

		<script type="text/javascript">
			vidSwirl.vidDir = "[<$I2_ROOT>]www/vids/";
		</script>
		
		<style type="text/css">
			body {
				font-family:"lucida grande", tahoma, verdana, arial, sans-serif;
				font-size:11px;
				color:#333333;
				
				margin:0px;
				padding:0px;
			}
			#header {
				background-color:#3B5998;
				
				height:82px;
				min-width:964px;
				
				margin:0px;
			}
			#main {
				background-color:white;
				background-image:url([<$I2_ROOT>]www/pics/fb/bg_grad.png);
				background-repeat:repeat-x;
				
				height:475px;
				margin:0px;
			}
			#footer {
			}
			
			#headerContent {
				width:980px;
				margin:0px auto;
				padding-top:13px;
			}
			#logo {
				cursor:pointer;
				margin-left:18px;
				margin-top:17px;
			}
			#loginArea {
				float:right;
			}
			#loginArea label {
				cursor:pointer;
				color:white;
			}
			#loginArea td {
				padding-left:14px;
				padding-top:0px;
				padding-bottom:0px;
			}
			#loginArea input[type="text"], #loginArea input[type="password"] {
				border-style:solid;
				border-width:1px;
				border-color:#1D2A5B;
				
				padding-bottom:4px;
				
				width:142px;
			}
			#loginBtn {
				cursor:pointer;
				
				border-style:solid;
				border-width:1px;
				border-color:#29447E #29447E #1A356E;
				-webkit-box-shadow:0px 1px 0px 0px rgba(0,0,0,0.1), inset 0px 1px 0px 0px rgba(255,255,255,0.4);
				   -moz-box-shadow:0px 1px 0px 0px rgba(0,0,0,0.1), inset 0px 1px 0px 0px rgba(255,255,255,0.4);
				        box-shadow:0px 1px 0px 0px rgba(0,0,0,0.1), inset 0px 1px 0px 0px rgba(255,255,255,0.4);
				background-color:#5B74A8;
				color:white;
				font-family:"lucida grande", tahoma, verdana, arial, sans-serif;
				font-size:11px;
				font-weight:bold;
				
				padding:3px 6px 4px 6px;
			}
			#loginBtn:active {
				-webkit-box-shadow:none;
				   -moz-box-shadow:none;
				        box-shadow:none;
			}
			#loginArea .helpTxt {
				color:#98A9CA;
				cursor:pointer;
				text-decoration:none;
			}
			#loginArea .helpTxt:hover {
				text-decoration:underline;
			}
			
			#mainContent {
				width:980px;
				margin:0px auto;
			}
			
			#mapBox {
				float:left;
				width:550px;
			}
			#mapHeader {
				margin-top:40px;
				padding-left:10px;s
				
				width:450px;
				
				word-spacing:-1px;
				
				color:#0E385F;
				font-size:20px;
				font-weight:bold;
			}
			
			#signUp {
				padding:43px 0px 0px 15px;
				width:383px;
				float:right;
			}
			#signUpHeader {
				color:#0E385F;
				font-size:15px;
				
				border-bottom-style:solid;
				border-bottom-width:1px;
				border-bottom-color:#9AAFCA;
				
				width:354px;
				padding:0px 10px 10px 10px;
				margin-bottom:10px;
			}
			
			#signUpForm {
				margin-bottom:7px;
			}
			#signUpForm .label {
				color:#1D2A5B;
				text-align:right;
				font-size:13px;
				width:105px;
				padding-right:3px;
			}
			#signUpForm .label label {
				cursor:pointer;
			}
			#signUpForm input[type="text"], #signUpForm input[type="password"] {
				padding:6px;
				font-size:16px;
				width:250px;
				border-style:solid;
				border-width:1px;
				border-color:#96A6C5;
				margin:0px;
			}
			#signUpForm select {
				font-family:"lucida grande", tahoma, verdana, arial, sans-serif;
				font-size:13px;
				padding:5px;
				height:30px;
				margin:0px;
				border-style:solid;
				border-width:1px;
				border-color:#BDC7D8;
			}
			#bigBtn {
				background-color:#69A74E;
				border-style:solid;
				border-width:1px;
				border-color:#3B6E22 #3B6E22 #2C5115;
				-webkit-box-shadow:0px 1px 0px 0px rgba(0, 0, 0, 0.1), inset 0px 1px 0px 0px rgba(255,255,255,0.4);
				   -moz-box-shadow:0px 1px 0px 0px rgba(0, 0, 0, 0.1), inset 0px 1px 0px 0px rgba(255,255,255,0.4);
				        box-shadow:0px 1px 0px 0px rgba(0, 0, 0, 0.1), inset 0px 1px 0px 0px rgba(255,255,255,0.4);
				color:white;
				font-family:"lucida grande", tahoma, verdana, arial, sans-serif;
				font-weight:bold;
				padding:2px 6px;
				margin:2px 0px 0px 0px;
				width:118px;
				height:32px;
				cursor:pointer;
				
			}
			#bigBtn:active {
			
				-webkit-box-shadow:none;
				   -moz-box-shadow:none;
				        box-shadow:none;
			}
			#signUpForm .helpTxt {
				cursor:pointer;
				color:#3B5998;
				text-decoration:none;
			}
			#signUpForm .helpTxt:hover {
				text-decoration:underline;
			}
			
			#footerContent {
				width:980px;
				margin:5px auto;
			}
			#footer a {
				color:#3B5998;
				text-decoration:none;
				cursor:pointer;
			}
			#footer a:hover {
				text-decoration:underline;
			}
		</style>
<!--[if lt IE 8]>
		<style>
			#loginArea {
				position:relative;
				top:-60px;
				right:-10px;
			}
		</style>
<![endif]-->
	</head>
	<body>
		<div id="header">
			<div id="headerContent">
				<img src="[<$I2_ROOT>]www/pics/fb/logo_big.png" id="logo" alt="Intrabook"/>
				<table id="loginArea">
					<tr>
						<td>
							<label for="login_username">Username</label>
						</td>
						<td>
							<label for="login_password">Password</label>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<form name="login_form" action="[<$I2_ROOT>]" method="post">
							<td>
								<input type="text" name="login_username" id="login_username" size="25"/>
							</td>
							<td>
								<input type="password" name="login_password" id="login_password" size="25"/>
							</td>
							<td>
								<input type="submit" value="Login" id="loginBtn"/>
							</td>
						</form>
					</tr>
					<tr>
						<td title='Disallowed because we feel like it--erm...I mean..."security reasons"' style="color:#98A9CA; padding-left:10px; cursor:pointer;">
							<input type="checkbox" disabled/>
							<span style="position:relative; top:-2px;">Keep me logged in</span>
						</td>
						<td>
							<span class="helpTxt" onclick="alert('That is unfortunate, isn\'t it.');">Forgot your password?</span>
						</td>
						<td>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="main">
			<div id="mainContent">
				<div id="mapBox">
					<div id="mapHeader">
						Iodine helps you connect and share with the people in your school.
					</div>
					<br/>
					<img src="[<$I2_ROOT>]www/pics/fb/world_map.png"/>
				</div>
				<div id="signUp">
					<div id="signUpHeader">
						<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Sign Up</div>
						It's free for TJ students and always will be.
					</div>
					<table id="signUpForm">
						<tr>
							<td class="label">
								<label for="firstnameInput">First Name:</label>
							</td>
							<td>
								<input type="text" id="firstnameInput"/>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="lastnameInput">Last Name:</label>
							</td>
							<td>
								<input type="text" id="lastnameInput"/>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="emailInput">Your Email:</label>
							</td>
							<td>
								<input type="text" id="emailInput"/>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="emailInput2">Re-enter Email:</label>
							</td>
							<td>
								<input type="text" id="emailInput2"/>
							</td>
						</tr>
						<tr>
							<td class="label">
								<label for="pswdInput">New Password:</label>
							</td>
							<td>
								<input type="password" id="pswdInput"/>
							</td>
						</tr>
						<tr>
							<td class="label">
								I am:
							</td>
							<td>
								<select>
									<option>Select Sex:</option>
									<option>Female</option>
									<option>Male</option>
									<option>Robot</option>
								</select>
							</td>
						</tr>
						<tr>
							<td class="label">
								Birthday:
							</td>
							<td>
								<select>
									<option>Month:</option>
									<option>Jan</option>
									<option>Feb</option>
									<option>Mar</option>
									<option>Apr</option>
									<option>May</option>
									<option>Jun</option>
									<option>Jul</option>
									<option>Aug</option>
									<option>Sep</option>
									<option>Oct</option>
									<option>Nov</option>
									<option>Dec</option>
									<option>13</option>
								</select>
								<select>
									<option>Day:</option>
									<option>0</option>
									<option>1</option>
									<option>10</option>
									<option>11</option>
									<option>100</option>
									<option>101</option>
									<option>110</option>
									<option>111</option>
									<option>1000</option>
									<option>1001</option>
									<option>1010</option>
									<option>1011</option>
									<option>1100</option>
									<option>1101</option>
									<option>1110</option>
									<option>1111</option>
									<option>10000</option>
									<option>10001</option>
									<option>10010</option>
									<option>10011</option>
									<option>10100</option>
									<option>10101</option>
									<option>10110</option>
									<option>10111</option>
									<option>11000</option>
									<option>11001</option>
									<option>11010</option>
									<option>11011</option>
									<option>11100</option>
									<option>11101</option>
									<option>11110</option>
									<option>11111</option>
								</select>
								<select>
									<option>Year:</option>
									<option>7C8</option>
									<option>7C9</option>
									<option>7CA</option>
									<option>7CB</option>
									<option>7CC</option>
									<option>7CD</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<span class="helpTxt" onclick="alert('No reason, we just like stealing your personal information }:)');">Why do I need to provide this?</span>
							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								<button id="bigBtn" onclick="initVidSwirl();">Sign Up</button>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div id="footer">
			<div id="footerContent">
				<a href="">English (US)</a>
				&nbsp;&nbsp;
				<a href="">Assembly</a>
				&nbsp;&nbsp;
				<a href="">BASIC</a>
				&nbsp;&nbsp;
				<a href="">C++</a>
				&nbsp;&nbsp;
				<a href="">Cobol</a>
				&nbsp;&nbsp;
				<a href="">Fortran</a>
				&nbsp;&nbsp;
				<a href="">Go</a>
				&nbsp;&nbsp;
				<a href="">Java</a>
				&nbsp;&nbsp;
				<a href="">JOVIAL</a>
				&nbsp;&nbsp;
				<a href="">Lisp</a>
				&nbsp;&nbsp;
				<a href="">LOLCODE</a>
				&nbsp;&nbsp;
				<a href="">Pascal</a>
				&nbsp;&nbsp;
				<a href="">Python</a>
				&nbsp;&nbsp;
				<a href="">Ruby</a>
				&nbsp;&nbsp;
				<a href=""></a>
			</div>
		</div>
	</body>
</html>
