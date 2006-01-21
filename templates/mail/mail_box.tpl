You have [<$nmsgs>] messages in your inbox.<br />
[<if $nmsgs_to_show>]
	The latest [<$nmsgs_to_show>]  messages are:
	<table>
	<tbody>
		<tr>
			<th align="left">From</th>
			<th align="center">Subject</th>
			<th align="right">Date</th>
		</tr>
		[<if isset($messages)>]
			[<foreach from=$messages item=msg>]
			<tr>
				<td align="left">[<$msg.from>]</td>
				<td align="center">[<$msg.subject>]</td>
				<td align="right">[<$msg.date>]</td>
			</tr>
			[</foreach>]
		[</if>]
	</tbody>
	</table>
[</if>]
