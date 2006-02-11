[<if isset($err)>]
There was an error retrieving your email messages. Either your password for the mail system is different than your Intranet password, or the mail server is unavailable.
[<else>]
You have [<$nmsgs>] messages in your inbox.<br />
<a href="[<$I2_ROOT>]mail">View your messages</a><br />
[<if $nmsgs_show>]
	The latest [<$nmsgs_show>] messages are:
	<table id="mail_box_table" style="text-align:left;width:100%;">
	<tbody style="text-align:left;">
		<tr>
			<th align="left">From</th>
			<th align="center">Subject</th>
			<th align="right">Date</th>
		</tr>
		[<section name=id loop=$messages max=$nmsgs_show step=-1>]
			<tr>
				<td align="left">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->short_from>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td align="left" style="width:10em;">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->short_subject>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td align="left">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->date|date_format:"%m/%d/%y">][<if $messages[id]->unread>]</span>[</if>]</td>
			</tr>
		[</section>]
	</tbody>
	</table>
[</if>]
[</if>]
