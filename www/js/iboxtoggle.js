$(document).ready(function() {
	$(".iboxtoggle").click(function() {
		$i = $("#intraboxes");
		if($i.css("display") == "none") {
                        $(this).css("left", "256px")
                               .html("<");
			$i.removeClass("hideib")
			  .css("left","-257px")
			  .addClass("show")
			  .css("left", "1px");
		} else {
			$i.css("left", "-257px")
			setTimeout(function() {
			$i.addClass("hideib")
			  .removeClass("show")
			  .css("left", "1px");
			}, 400);
			$(this).css("left", 0)
			       .html(">");
		}
	});
});
