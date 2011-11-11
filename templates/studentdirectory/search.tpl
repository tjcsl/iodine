[<if $query=='sex:lots' || $query=='sex: lots' || $query=='sex :lots'>]
<iframe width="425" height="349" src="http://www.youtube.com/embed/QH2-TGUlwu4?autoplay=1" frameborder="0" allowfullscreen></iframe>
[<else>]
[<if $query == 'do a barrel roll'>]
<script type="text/javascript">
	document.body.style.WebkitTransitionDuration = "5s";
	document.body.style.MozTransitionDuration = "5s";
	document.body.style.MsTransitionDuration = "5s";
	document.body.style.OTransitionDuration = "5s";
	document.body.style.transitionDuration = "5s";
	document.body.style.WebkitTransitionTimingFunction = "linear";
	document.body.style.MozTransitionTimingFunction = "linear";
	document.body.style.MsTransitionTimingFunction = "linear";
	document.body.style.OTransitionTimingFunction = "linear";
	document.body.style.transitionTimingFunction = "linear";
	setTimeout(function() {
		document.body.style.WebkitTransform = "rotate(360deg)";
		document.body.style.MozTransform = "rotate(360deg)";
		document.body.style.MsTransform = "rotate(360deg)";
		document.body.style.OTransform = "rotate(360deg)";
		document.body.style.transform = "rotate(360deg)";
	}, 200);
</script>
[</if>]
[<if $info>]
[<include file="search/search_results_pane.tpl" results_destination="StudentDirectory/info/">]
[<else>]
There were no results matching your query.
[</if>]
[</if>]
