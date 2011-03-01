<a href="http://fcps.blackboard.com/">FCPS Blackboard</a><br />
<a href="[<$I2_ROOT>]info/resources">Proxy and Library Databases</a><br />
<a href="http://academics.tjhsst.edu/careercenter/">College &amp; Career Center</a><br />
[<if $I2_USER->objectclass == 'tjhsstTeacher' || $I2_USER->is_group_member('admin_calc')>]<a href="[<$I2_ROOT>]findcalc">Identify Lost Calculator</a><br />[</if>]
[<php>]
$this->assign('mode',i2config_get('mode','full','roster'));
[</php>]
[<if $mode == 'full' >]
<a href="[<$I2_ROOT>]studentdirectory/roster">School Roster</a><br />
[</if>]
<a href="http://leadership.tjhsst.edu/sga">TJ SGA</a><br />
<a href="http://postman.tjhsst.edu/">Postman (calendar)</a><br />
<a href="[<$I2_ROOT>]groups/">Groups</a><br />
<a href="[<$I2_ROOT>]polls/">Polls</a><br />
<a href="[<$I2_ROOT>]bugzilla/">Report a bug (Intranet or the CSL)</a><br />
<a href="http://colonialathletics.org">TJ Athletics</a><br />
