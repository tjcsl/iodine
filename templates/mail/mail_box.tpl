[<if isset($err)>]
There was an error retrieving your email messages. Either your password for the mail system is different than your Intranet password, or the mail server is unavailable.
[<else>]
You have [<$nmsgs>] messages in your inbox, [<$nunseen>] unread.<br />
<table style="width:100%;">
<tr>
<td><a href="[<$I2_ROOT>]mail">List all messages</a></td>
<td style="text-align:right;"><a href="https://mail.tjhsst.edu/">Read/send mail</a></td>
[<if $nmsgs_show>]
	<table cellspacing="0">
	<tbody style="text-align:left;">
		<tr style="background-color:#DEDEDE">
			<th align="center">From</th>
			<th align="center">Subject</th>
			<th align="center">Date</th>
		</tr>
		[<section name=id loop=$messages max=$nmsgs_show step=-1>]
			<tr style="background-color: [<cycle values="#EEEEEE,#FFFFFF">]">
				<td class="mail_box">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->short_from>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td class="mail_box" style="width:10em;">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->short_subject>][<if $messages[id]->unread>]</span>[</if>]</td>
				<td class="mail_box" style="text-align:left;">[<if $messages[id]->unread>]<span class="mail_unread">[</if>][<$messages[id]->date|date_format:"%m/%d/%y">][<if $messages[id]->unread>]</span>[</if>]</td>
			</tr>
		[</section>]
	</tbody>
	</table>
[</if>]
[</if>]
