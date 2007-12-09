<p><a href="[<$I2_ROOT>]studentdirectory/section/[<$class->classid>]">Click here to view a list of all sections of [<$class->name>].</a></p>
<p>Students in <a href=[<$I2_ROOT>]studentdirectory/info/[<$class->teacher->uid>]>[<$class->teacher->name>]</a>'s [<$class->name>], period [<$class->period>]:</p>
<table cellspacing="0">
 <thead>
  <tr>
   <th>Name</th>
   <th>Email</th>
   [<if $aimkey>]
   <th>AIM Status</th>
   [</if>]
  </tr>
 </thead>
[<foreach from=$students item=student>]
 <tbody>
  <tr class="[<cycle values="c1,c2">]">
   <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/info/[<$student->uid>]">[<$student->fullname_comma>] ([<$student->grade>])</a></td>
   <td class="directory-table">
    [<if count($student->mail)>]
	  	[<if count($student->mail) == 1>]
			[<assign var="mail" value=$student->mail>]
		[<else>]
			[<assign var="mail" value=$student->mail.0>]
		[</if>]
		[<mailto address=$mail encode="hex">]
    [<else>]
     &nbsp;
    [</if>]
    </td>
    [<if $aimkey>]
    <td class="directory-table">
    [<foreach from=$student->aim item=username key=k>]
    <img src="http://api.oscar.aol.com/presence/icon?k=[<$aimkey>]&t=[<$username>]" /> <a href="aim:goim?screenname=[<$username>]">[<$username|escape:'html'>]</a>
    [</foreach>]
    </td>
    [</if>]
  </tr>
 </tbody>
[</foreach>]
</table>
<br />
Other classes taught by [<$class->teacher->name>]:
<table cellspacing="0">
 <thead>
  <th>Period</th>
  <th>Name</th>
  <th>Room(s)</th>
  <th>Quarter(s)</th>
 </thead>
 <tbody>
 [<foreach from=$class->other_classes() item=otherclass>]
  <tr class="[<cycle values="c1,c2">]">
   <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$otherclass->sectionid>]">[<$otherclass->periods>]</a></td>
   <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$otherclass->sectionid>]">[<$otherclass->name>]</a></td>
   <td class="directory-table">[<$otherclass->room>]</td>
   <td class="directory-table">[<$otherclass->term>]</td>
  </tr>
 [</foreach>]
 </tbody>
</table>
