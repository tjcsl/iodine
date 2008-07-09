[<if $student>]
<span style="color: red;"><strong>You are a student; students who elected not to show their schedules will not appear on this list.</strong></span><br /><br />
[</if>]
<ul>
[<foreach from=$teachers key=tchr item=periods>]
	<li>
	[<$tchr>]<br />
	<ul>
	[<foreach from=$periods key=pd item=students>]
		<li>
		Period [<$pd>]<br />
		<ul>
		[<foreach from=$students item=student>]
			<li>[<$student->name_comma>]</li>
		[</foreach>]
		</ul>
		</li>
	[</foreach>]
	</ul>
	<br />
	</li>
[</foreach>]
</ul>
