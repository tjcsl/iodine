<table border="1">
  <caption>Seniors attending with non-FCPS dates</caption>
  <tr><th>Name</th><th>Date's Name</th><th>Date's Description</th></tr>
[<foreach from=$fardate item=date>]
  <tr><th>[<$date.name>]</th><td>[<$date.date>]</td><td>[<$date.desc>]</td></tr>
[</foreach>]
</table><br /><br />
<table border="1">
  <caption>Seniors attending with FCPS dates</caption>
  <tr><th>Name</th><th>Date's Name</th><th>Date's School</th><th>Date's Grade</th></tr>
[<foreach from=$fcpsdate item=date>]
  <tr><th>[<$date.name>]</th><td>[<$date.date>]</td><td>[<$date.school>]</td><td>[<$date.grade>]</td></tr>
[</foreach>]
</table><br /><br />
<table border="1">
  <caption>Seniors attending with non-senior TJ dates</caption>
  <tr><th>Name</th><th>Date's Name</th><th>Date's Grade</th></tr>
[<foreach from=$tjdate item=date>]
  <tr><th>[<$date.name>]</th><td>[<$date.date>]</td><td>[<$date.grade>]</td></tr>
[</foreach>]
</table><br /><br />
Other seniors attending:<br />
<ol>
[<foreach from=$nodate item=p>]
  <li>[<$p>]</li>
[</foreach>]
</ol><br /><br />
Seniors not attending:<br />
<ol>
[<foreach from=$notgoing item=p>]
  <li>[<$p>]</li>
[</foreach>]
</ol>
