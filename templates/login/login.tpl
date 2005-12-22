<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>TJHSST Intranet2: Login</title>
<link rel="stylesheet" type="text/css" href="[<$I2_ROOT>]www/extra-css/login.css" />
</head>
<body>
 <div id="login_background">
  <table class="centering_table">
   <tr><td class="centering_td">
   [<if $failed eq 1>]
    <div id="login_failed">
     Your login as [<$uname>] failed.  Maybe your password is incorrect?<br />
    </div>
   [<elseif $failed eq 2>]
    <div id="login_failed">
     Your password and username were correct, but you don't appear to exist in our database. If this is a mistake, please contact the intranetmaster about it.
    </div>
   [<elseif $failed>]
    <div id="login_failed">
     An unidentified error has occurred. Please contact the intranetmaster and tell him you received this error message immediately.
    </div>
   [</if>]
    <form action='[<$I2_SELF>]' method='post'>
     <table id="login_box">
      <tr>
      	<td>Username:</td>
	<td><input name='login_username' type='text' value='[<$uname>]'/><br /></td>
      </tr>
      <tr>
        <td>Password:</td>
	<td><input name='login_password' type='password'/><br /></td>
      </tr>
      <tr>
        <td><input type='submit' value='Login'/></td>
      </tr>
     </table>
    </form>
     [<* You are accessing the intranet of the Thomas Jefferson High School for Science and Technology.  Any unauthorized or improper use of this system may result in civil and criminal penalties and administrative or disciplinary action, as appropriate.  By proceeding, you indicate awareness of and consent to being monitored and logged. 
     *>]  </td></tr>
  </table>
 </div>
