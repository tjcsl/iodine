[<include file="eighth_header.tpl">]
Are you sure you want to move <span style="font-weight: bold;">ALL</span> the students from <span style="font-weight: bold; font-style: italic;">[<$activity_from->name>]</span> into <span style="font-weight: bold; font-style: italic;">[<$activity_to->name>]</span> on <span style="font-weight: bold; font-style: italic;">[<$block->date|date_format:"%A %b %e, %Y">] - [<$block->block>] block</span>?<br />
<a href="[<$I2_ROOT>]eighth/people_switch/commit/bid/[<$block->bid>]/aid_from/[<$activity_from->aid>]/aid_to/[<$activity_to->aid>]">Yes</a>
