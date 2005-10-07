<html>
<head>
<title>TJHSST Intranet2: Login</title>
<style type='text/css' media='all'>@import "[<$I2_ROOT>]/www/styles/login.css";</style>
</head>
<body>
 <div id="login_box">
  <table BORDER="0" CELLPADDING="1" CELLSPACING="0" WIDTH="100%" HEIGHT="100%">
   <tr><td valign="center"><center>
   [<if $failed>]
    Your login as [<$uname>] failed.  Maybe your password is incorrect?
    [</if>]
    <form action='[<$I2_SELF>]' method='post'>
     <table>
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
     *>]  </center></td></tr>
  </table>
 </div>
