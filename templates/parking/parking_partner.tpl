<a href="[<$I2_ROOT>]parking/apply">Back to your parking application</a><br /><br />

[<if isset($message)>][<$message>]<br /><br />[</if>]

Find your parking partner: <br /><br />

[<if isSet($search_destination)>]
        [<include file="search/search_pane.tpl" choose_grades=1 grade_10=1 grade_11=1>]
[<else>]
        [<include file="search/search_results_pane.tpl">]
[</if>]
