[<if $query == "sex:lots" || $query == "sex: lots" || $query == "sex :lots">]
<iframe width="425" height="349" src="http://www.youtube.com/embed/QH2-TGUlwu4?autoplay=1" frameborder="0" allowfullscreen></iframe><br/>
[<else>]
[<if $query == "do a barrel roll">]
<script type="text/javascript">
	window.addEventListener("load", function() {
		document.body.style.WebkitTransitionDuration = "2s";
		document.body.style.MozTransitionDuration = "2s";
		document.body.style.MsTransitionDuration = "2s";
		document.body.style.OTransitionDuration = "2s";
		document.body.style.transitionDuration = "2s";
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
	}, false);
</script>
[<else>]
[<if $query == "i am sad" || $query == "I am sad">]
<script type="text/javascript">
	window.addEventListener("load", function() {
		var box = document.getElementById("boxcontent");
		// make a smiley face out of the first five links
		box.getElementsByTagName("a")[5].style.display = "inline-block";
		box.getElementsByTagName("a")[5].style.marginTop = "10em";
		// left eye
		box.getElementsByTagName("a")[0].style.position = "absolute";
		box.getElementsByTagName("a")[0].style.left = "8.25em";
		box.getElementsByTagName("a")[0].style.top = "3.1em";
		box.getElementsByTagName("a")[0].style.width = "6em";
		box.getElementsByTagName("a")[0].style.display = "block";
		box.getElementsByTagName("a")[0].style.textAlign = "center";
		
		// right eye
		box.getElementsByTagName("a")[1].style.position = "absolute";
		box.getElementsByTagName("a")[1].style.left = "18.25em";
		box.getElementsByTagName("a")[1].style.top = "3.1em";
		box.getElementsByTagName("a")[1].style.width = "6em";
		box.getElementsByTagName("a")[1].style.display = "block";
		box.getElementsByTagName("a")[1].style.textAlign = "center";
		
		// mouth
		box.getElementsByTagName("a")[2].style.position = "absolute";
		box.getElementsByTagName("a")[2].style.left = "0.25em";
		box.getElementsByTagName("a")[2].style.top = "14em";
		box.getElementsByTagName("a")[2].style.WebkitTransform = "rotate(35deg)";
		box.getElementsByTagName("a")[2].style.MozTransform = "rotate(35deg)";
		box.getElementsByTagName("a")[2].style.MsTransform = "rotate(35deg)";
		box.getElementsByTagName("a")[2].style.OTransform = "rotate(35deg)";
		box.getElementsByTagName("a")[2].style.transform = "rotate(35deg)";
		box.getElementsByTagName("a")[2].style.display = "block";

		box.getElementsByTagName("a")[3].style.position = "absolute";
		box.getElementsByTagName("a")[3].style.left = "6.7em";
		box.getElementsByTagName("a")[3].style.top = "16em";
		box.getElementsByTagName("a")[3].style.display = "block";

		box.getElementsByTagName("a")[4].style.position = "absolute";
		box.getElementsByTagName("a")[4].style.left = "24em";
		box.getElementsByTagName("a")[4].style.top = "13.75em";
		box.getElementsByTagName("a")[4].style.WebkitTransform = "rotate(-35deg)";
		box.getElementsByTagName("a")[4].style.MozTransform = "rotate(-35deg)";
		box.getElementsByTagName("a")[4].style.MsTransform = "rotate(-35deg)";
		box.getElementsByTagName("a")[4].style.OTransform = "rotate(-35deg)";
		box.getElementsByTagName("a")[4].style.transform = "rotate(-35deg)";
		box.getElementsByTagName("a")[4].style.display = "block";
		
		// add message
		var msgs = ["Stop being sad and be awesome instead", "Don't worry; be happy"];
		var msg = document.createElement("div");
		msg.style.fontSize = "105%";
		msg.innerHTML = "<br/>" + msgs[Math.floor(Math.random() * msgs.length)] + " :)";

		box.insertBefore(msg, box.getElementsByTagName("br")[box.getElementsByTagName("br").length - 2]);
	}, false);
</script>
[<else>]
[<if $query == "can has sleep" || $query == "can has sleep?">]
<iframe width="560" height="315" src="http://www.youtube.com/embed/gvdf5n-zI14?autoplay=1" frameborder="0" allowfullscreen></iframe><br/>
[</if>]
[</if>]
[</if>]
[</if>]
[<if $info>]
[<include file="search/search_results_pane.tpl" results_destination="StudentDirectory/info/">]
[<else>]
There were no results matching your query.
[</if>]
