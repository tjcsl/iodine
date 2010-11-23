<table>
<tr><th>Date</th><th>Type</th></tr>
[<foreach from=$tests item=test>]
<tr><td>[<$test.time|date_format:"%b %e, %Y">]</td><td>[<$test.type>]</td></tr>
[</foreach>]
</table>
