[<include file="eighth/header.tpl">]
[<foreach from=$data item=oc>]
[<$oc.time>]: <a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$oc.cid>]">[<$cids[$oc.cid]>]</a> moved <a href="[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$oc.uid>]">[<$oc.username>]</a> from <a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.fromaid>]">[<$oc.fromaid>]</a> to <a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$oc.bid>]/aid/[<$oc.toaid>]">[<$oc.toaid>]</a><br />
[</foreach>]
