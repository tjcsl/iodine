This lists all the classes available during the school year. You may sort by clicking on the headers.<br /><br />
<table>
<tr>
  <th><a href="[<$I2_ROOT>]studentdirectory/roster/course">Course</a></th>
  <th><a href="[<$I2_ROOT>]studentdirectory/roster/teacher">Teacher</a></th>
  <th><a href="[<$I2_ROOT>]studentdirectory/roster/period">Period</a></th>
  <th><a href="[<$I2_ROOT>]studentdirectory/roster/room">Room</a></th>
  <th><a href="[<$I2_ROOT>]studentdirectory/roster/term">Quarters</a></th>
</tr>
[<foreach from=$courses item=course>]
<tr class="[<cycle values="c1,c2">]">
  <td><a href="[<$I2_ROOT>]studentdirectory/class/[<$course->sectionid>]">[<$course->name>]</a></td>
  <td><a href="[<$I2_ROOT>]studentdirectory/info/[<$course->teacher->iodineUidNumber>]">[<$course->teacher->name_comma>]</a></td>
  <td>[<$course->periods>]</td>
  <td>[<$course->room>]</td>
  <td>[<$course->term>]</td>
</tr>
[<foreachelse>]
No courses were found. Something is terribly, horribly wrong and you should contact the Intranetmaster immediately.
[</foreach>]
</table>
