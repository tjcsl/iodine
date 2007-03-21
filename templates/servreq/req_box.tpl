[<if count($myreqs) > 0>]
	<p align='center'><b>Your Active Requests:</b></p>
	<table cellspacing="0">
	<tbody style="text-align:left;">
		<tr style="background-color:#DEDEDE;">
			<th align="center">Submission Date</th>
			<th align="center">Type</th>
			<th align="center">Status</th>
		</tr>
		[<foreach from=$admreqs item=req>]
			<tr class="[<cycle values="c1,c2">]">
				<td class="req_box">[<$req->reqdate|date_format:"%m/%d/%y">]</td>
				<td class="req_box" style="width:10em;">[<$req->type>]</td>
				<td class="req_box" style="text-align:left;">[<if $req->status == 1>]Completed[</if>][<if $req->status == 2>]Denied[</if>][<if $req->status == 0>]In Progress[</if>]</td>
			</tr>
		[</foreach>]
	</tbody>
	</table>
[</if>]
[<if count($admreqs) > 0>]
	<p align='center'><b>Requests Awaiting Action:</b></p>
	<table cellspacing="0">
	<tbody style="text-align:left;">
		<tr style="background-color:#DEDEDE;">
			<th align="center">Submission Date</th>
			<th align="center">Type</th>
			<th align="center">Status</th>
		</tr>
		[<foreach from=$admreqs item=req>]
			<tr class="[<cycle values="c1,c2">]">
				<td class="req_box">[<$req->reqdate|date_format:"%m/%d/%y">]</td>
				<td class="req_box" style="width:10em;">[<$req->type>]</td>
				<td class="req_box" style="text-align:left;">[<if $req->status == 1>]Completed[</if>][<if $req->status == 2>]Denied[</if>][<if $req->status == 0>]In Progress[</if>]</td>
			</tr>
		[</foreach>]
	</tbody>
	</table>
[</if>]
