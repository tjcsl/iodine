<a href="[<$I2_ROOT>]mail">Main mail page</a><br /><br />

<table>
<tbody>
	<tr><td>Date:</td><td>[<$date>]</td></tr>
	<tr><td>From:</td><td>[<$from>]</td></tr>
	<tr><td>To:</td><td>[<foreach from=$to item=recipient>][<$recipient>]<br />[</foreach>]</td></tr>
	<tr><td>Subj:</td><td>[<$subject>]</td></tr>
</tbody>
</table>
<br />
[<$body>]
