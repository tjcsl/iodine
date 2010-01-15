<p><a href="[<$I2_ROOT>]studentdirectory/section/[<$class->classid>]">Click here to view a list of all sections of [<$class->name>].</a></p>
<p>Students in <a href="[<$I2_ROOT>]studentdirectory/info/[<$class->teacher->uid>]">[<$class->teacher->name>]</a>'s [<$class->name>], period [<$class->periods>]:</p>
<table cellspacing="0">
 <thead>
  <tr>
   <th>Name</th>
   <th>Email</th>
   <th>AIM Status</th>
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
    <td class="directory-table">
    [<foreach from=$student->aim item=username key=k>]
      <img src="[<$im_icons>][<$aim[$username]>]" alt="" /> <a href="aim:goim?screenname=[<$username|escape:'url'>]">[<$username|escape:'html'>]</a>
    [</foreach>]
    </td>
  </tr>
 </tbody>
[</foreach>]
</table>
<br />
Other classes taught by [<$class->teacher->name>]:
<table cellspacing="0">
 <thead>
  <tr>
   <th>Period</th>
   <th>Name</th>
   <th>Room(s)</th>
   <th>Quarter(s)</th>
  </tr>
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
