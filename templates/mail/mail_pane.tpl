Login to your <a href="https://mail.tjhsst.edu/">TJ Mail Account</a> to send mail.<br /><br />
You have [<$nmsgs>] messages in your inbox.<br />
[<if $nmsgs>]
	<table style="width:100%;">
	 <tr><td>[<if $offset gt 0>]<a href="[<$I2_ROOT>]mail/[<$offset-20>]">[</if>]&lt;-- More recent[<if $offset gt 0>]</a>[</if>]</td>
	 <td align="right">[<if $offset + 20 lt $nmsgs>]<a href="[<$I2_ROOT>]mail/[<$offset+20>]">[</if>]Older --&gt;[<if $offset+20 lt $nmsgs>]</a>[</if>]</td></tr>
	</table>
	<table id="mail_pane_table">
	<tbody>
		<tr>
			<th align="left">From</th>
			<th align="center">Subject</th>
			<th align="right">Date</th>
		</tr>
		[<section name=id loop=$messages max=$nmsgs_show start=$offset+$nmsgs_show step=-1>]
			<tr>
				<td align="left">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->from>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td align="left">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->subject>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td align="left">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->date|date_format:"%I:%M %p, %d %B %Y">][<if $messages[id]->unread>]</span>[</if>]</td>
			</tr>
		[</section>]
	</tbody>
	</table>
[</if>]
