Login to your <a href="https://mail.tjhsst.edu">TJ Mail Account</a> to send mail.<br /><br />
You have [<$nmsgs>] messages in your inbox.<br />
[<if $nmsgs>]
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
				<td align="center">[<if $msg.unread>]<b>[</if>]<a href="[<$I2_ROOT>]mail/message/[<$msg.number>]">[<$msg.subject>]</a>[<if $msg.unread>]</b>[</if>]</td>
				<td align="right">[<if $msg.unread>]<b>[</if>][<$msg.date>][<if $msg.unread>]</b>[</if>]</td>
			</tr>
		[</foreach>]
	</tbody>
	</table>
[</if>]
