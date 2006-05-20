Login to your <a href="https://mail.tjhsst.edu/">TJ Mail Account</a> to read and send mail.<br /><br />
[<if isset($err)>]
There was an error retrieving your email messages. Either your password for the mail system is different than your Intranet password, or the mail server is unavailable.
[<else>]
You have [<$nmsgs>] messages in your inbox, [<$nunseen>] unread.<br />
[<if $nmsgs>]
	<table cellspacing="0">
	<tbody>
	<tr>
	<td>[<if $offset gt 0>]<a href="[<$I2_ROOT>]mail/[<$offset-20>]">&lt;&lt; More recent</a>[</if>]</td>
	<td></td>
	<td style="text-align: right;">[<if $offset + 20 lt $nmsgs>]<a href="[<$I2_ROOT>]mail/[<$offset+20>]">Older &gt;&gt;</a>[</if>]</td>
	</tr>
		<tr style="background-color:#DEDEDE">
			<th align="center">From</th>
			<th align="center">Subject</th>
			<th align="center">Date</th>
		</tr>
		[<section name=id loop=$messages max=$nmsgs_show start=$nmsgs-$offset step=-1>]
			<tr style="background-color: [<cycle values="#EEEEEE,#FFFFFF">]">
				<td class="mail_pane" style="width:120px;">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->from>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td class="mail_pane">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->subject>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td class="mail_pane" style="width:75px;">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->date|date_format:"%m/%d/%Y %I:%M %p">][<if $messages[id]->unread>]</span>[</if>]</td>
			</tr>
		[</section>]
	</tbody>
	</table>
[</if>]
[</if>]
