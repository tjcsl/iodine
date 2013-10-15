[<foreach from=$schedule item=s>]
	<p>
		<span class='period'>
			[<$s.pd>]: 
		</span>
		<span class='times'>
			[<$s.start>] - [<$s.end>]
		</span>
	</p>
[</foreach>]

