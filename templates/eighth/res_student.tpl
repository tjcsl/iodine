[<include file="eighth/header.tpl">]
<table><tr><td>
[<include file="eighth/include_list_open.tpl">]
[<include file="eighth/activity_selection.tpl" op='user' bid=$block->bid field='aid'>]
[<include file="eighth/block_selection.tpl" header="FALSE" title='' method='res_student' op='activity' field='bid' bid=$block->bid>]
[<include file="eighth/include_list_close.tpl">]
</td><td style="vertical-align: top;">
[<if isSet($lastuser)>]<b>Rescheduled [<$lastuser->fullname>]</b>[</if>]
[<if isSet($search_destination)>]
[<include file="search/search_pane.tpl">]
[<elseif isSet($results_destination)>]
[<include file="search/search_results_pane.tpl">]
[</if>]
</td></tr></table>
