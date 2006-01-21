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
				<td align="left">[<$msg.from>]</td>
				<td align="center">[<$msg.subject>]</td>
				<td align="right">[<$msg.date>]</td>
			</tr>
		[</foreach>]
	</tbody>
	</table>
[</if>]
