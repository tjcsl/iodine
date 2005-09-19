[<include file="eighth_header.tpl">]
<span style="font-weight: bold; font-size: 125%;">[<$block->date|date_format>] - [<$block->block>] block</span><br /><br />
<table style="border: 0px; margin: 0px; padding: 0px; width: 100%;">
	<tr>
		<th style="text-align: left;">Location</th>
		<th style="text-align: left;">Activity ID</th>
		<th style="text-align: left;">Activity Name</th>
		<th style="text-align: left;">Teacher</th>
		<th style="text-align: left;">Students</th>
	</tr>
[<foreach from=$utilizations item="utilization">]
	[<php>]
	$sponsors = EighthSponsor::id_to_sponsor(explode(",", $this->_tpl_vars['utilization']['sponsors']));
	$temp_sponsors = array();
	foreach($sponsors as $sponsor) {
		$temp_sponsors[] = $sponsor->name;
	}
	$this->_tpl_vars['utilization']['sponsors'] = implode(", ", $temp_sponsors);
	[</php>]
	<tr>
		<td style="padding: 0px 5px;">[<$utilization.room>]</td>
		<td style="padding: 0px 5px;">[<$utilization.aid>]</td>
		<td style="padding: 0px 5px;">[<$utilization.name>]</td>
		<td style="padding: 0px 5px;">[<$utilization.sponsors>]</td>
		<td style="padding: 0px 5px;">[<$utilization.students>]</td>
	</tr>
[</foreach>]
</table>
