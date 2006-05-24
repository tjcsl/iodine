[<if isset($err)>]
There was an error retrieving your email messages. Either your password for the mail system is different than your Intranet password, or the mail server is unavailable.
[<else>]
You have [<$nmsgs>] messages in your inbox.<br />
<a href="[<$readmail_url>]">Read and send messages</a><br />
[<if count($messages) > 0>]
	<table cellspacing="0">
	<tbody style="text-align:left;">
		<tr style="background-color:#DEDEDE;">
			<th align="center">From</th>
			<th align="center">Subject</th>
			<th align="center">Date</th>
		</tr>
		[<foreach from=$messages item=msg>]
			<tr class="[<cycle values="c1,c2">]">
				<td class="mail_box">[<if $msg->unread>]<span class="mail_unread">[</if>][<$msg->short_from>][<if $msg->unread>]</span>[</if>]</td>
				<td class="mail_box" style="width:10em;">[<if $msg->unread>]<span class="mail_unread">[</if>][<$msg->short_subject>][<if $msg->unread>]</span>[</if>]</td>
				<td class="mail_box" style="text-align:left;">[<if $msg->unread>]<span class="mail_unread">[</if>][<$msg->date|date_format:"%m/%d/%y">][<if $msg->unread>]</span>[</if>]</td>
			</tr>
		[</foreach>]
	</tbody>
	</table>
[</if>]
[</if>]
