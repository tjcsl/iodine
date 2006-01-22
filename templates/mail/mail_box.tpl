You have [<$nmsgs>] messages in your inbox.<br />
<a href="[<$I2_ROOT>]mail">View your messages</a><br />
[<if $nmsgs_to_show>]
	The latest [<$nmsgs_to_show>] messages are:
	<table>
	<tbody>
		<tr>
			<th align="left">From</th>
			<th align="center">Subject</th>
			<th align="right">Date</th>
		</tr>
		[<foreach from=$messages item=msg>]
			<tr>
				<td align="left">[<if $msg.unread>]<b>[</if>][<$msg.from>][<if $msg.unread>]</b>[</if>]</td>
				<td align="center">[<if $msg.unread>]<b>[</if>][<$msg.subject>][<if $msg.unread>]</b>[</if>]</td>
				<td align="right">[<if $msg.unread>]<b>[</if>][<$msg.date>][<if $msg.unread>]</b>[</if>]</td>
			</tr>
		[</foreach>]
	</tbody>
	</table>
[</if>]
