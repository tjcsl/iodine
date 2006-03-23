<span id="menu">
  <a href="[<$I2_ROOT>]">Home</a>

  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]prefs">Preferences</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]info/credits">Credits</a>
  <span class="bold">&middot;</span>

  <a href="[<$I2_ROOT>]help/[<$I2_ARGSTRING>]">Help</a>
  <span class="bold">&middot;</span>
  [<if $admin_mysql>]
  	<a href="[<$I2_ROOT>]mysqlinterface">MySQL Interface</a>
  	<span class="bold">&middot;</span>
  [</if>]
  [<if $admin_ldap>]
  	<a href="[<$I2_ROOT>]ldapinterface">LDAP Interface</a>
  	<span class="bold">&middot;</span>
  [</if>]
  [<if $admin_groups>]
  	<a href="[<$I2_ROOT>]groups">Groups</a>
	<span class="bold">&middot;</span>
  [</if>]
  <a href="[<$I2_ROOT>]toggleheader/[<$I2_ARGSTRING>]">Show/Hide Toolbar</a>
  <span class="bold">&middot;</span>
  <a href="[<$I2_ROOT>]logout">Logout</a>
</span>
