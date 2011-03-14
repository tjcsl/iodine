<div><strong>TIME UNTIL GRADUATION</strong><br/>
[<if $underclass>]
Your class will never graduate.
[<else>]
[<if $negative>]
<span style="font-size: 20pt; color: rgb(255,0,0);"><strong>NONE!</strong></span>
[<else>]
[<if $years > 0>]
[<$years>] year[<if $years != 1>]s[</if>], 
[</if>]
[<$days>] day[<if $days != 1>]s[</if>], 
[<$hours>] hour[<if $hours != 1>]s[</if>], 
[<$mins>] minute[<if $mins != 1>]s[</if>], and 
[<$secs>] second[<if $secs != 1>]s[</if>] remain!
[</if>]
[</if>]
<br/>
<a href="[<$I2_ROOT>]seniors">Senior College Destinations</a>
</div>
