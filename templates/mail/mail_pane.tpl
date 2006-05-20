Login to your <a href="https://mail.tjhsst.edu/">TJ Mail Account</a> to read and send mail.<br /><br />
[<if isset($err)>]
There was an error retrieving your email messages. Either your password for the mail system is different than your Intranet password, or the mail server is unavailable.
[<else>]
You have [<$nmsgs>] messages in your inbox.<br />
[<if $nmsgs>]
	<table cellspacing="0">
	<tbody>
	<tr>
	<td>[<if $goleft>]<a href="[<$I2_ROOT>]mail/[<$offset-20>]">&lt;&lt; More recent</a>[</if>]</td>
	<td></td>
	<td style="text-align: right;">[<if $goright>]<a href="[<$I2_ROOT>]mail/[<$offset+20>]">Older &gt;&gt;</a>[</if>]</td>
	</tr>
	<tr style="background-color:#DEDEDE">
		<th align="center">From</th>
		<th align="center">Subject</th>
		<th align="center">Date</th>
	</tr>
	[<foreach from=$messages item=msg>]
		<tr bgcolor="[<cycle values="#EEEEEE,#FFFFFF">]">
			<td class="mail_pane" style="width:120px;">[<if $msg->unread>]<span class="mail_unread">[</if>][<$msg->from>][<if $msg->unread>]</span>[</if>]</td>
			<td class="mail_pane">[<if $msg->unread>]<span class="mail_unread">[</if>][<$msg->subject>][<if $msg->unread>]</span>[</if>]</td>
			<td class="mail_pane" style="width:75px;">[<if $msg->unread>]<span class="mail_unread">[</if>][<$msg->date|date_format:"%I:%M %p, %d %B %Y">][<if $msg->unread>]</span>[</if>]</td>
		</tr>
	[</foreach>]
	</tbody>
	</table>
[</if>]
[</if>]
