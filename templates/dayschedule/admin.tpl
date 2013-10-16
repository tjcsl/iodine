[<if !isset($page) || $page == 'home'>]
	<ul>
		<li><a href="[<$I2_ROOT>]dayschedule/admin/summaries">Edit Summaries</a></li>
		<li><a href="[<$I2_ROOT>]dayschedule/admin/pretty">Edit Display Summaries</a></li>
		<li><a href="[<$I2_ROOT>]dayschedule/admin/schedules">Edit Schedules</a></li>
	</ul>
[<elseif $page == 'summaries'>]
	<form action="[<$I2_ROOT>]dayschedule/admin/summariesedit" method="post">
		<select name="summaries">
		[<foreach from=$summaries item=$s>]
			[<$s>]
		[</foreach>]
[</if>]