<form action="[<$I2_ROOT>]birthdays" method="post">
 <div id="date_prompt">
 See birthdays near <input type="text" name="date" value="[<$date|date_format:"%m.%d.%Y">]"/> <input type="submit" value="Go!"/>
 </div>
</form>

[<foreach from=$birthdays item=birthday>]
 <div class="birthdate[<if $birthday.date == $date>] red[</if>]">[<$birthday.date|date_format:"%a. %b %e, %Y">]</div>
 [<if count($birthday.people)>]
  <ul>
  [<foreach from=$birthday.people item=person>]
   <li>
    <a href="[<$I2_ROOT>]studentdirectory/info/[<$person.uid>]">[<$person.name>]</a>, [<$person.grade>]th grade, [<if $birthday.date < $today>]turned[<else>]turns[</if>] [<$person.age>]
   </li>
  [</foreach>]
  </ul>
 [<else>]
  <ul><li>No birthdays on this day.</li></ul>
 [</if>]
[</foreach>]
