function page_init() {
	try { // because old browsers may not support getElementsByClassName
		var welcomeTxt = document.getElementsByClassName("title")[0].innerHTML;
		welcomeTxt = (welcomeTxt.substring(0, welcomeTxt.indexOf(",")) + " to the grid" + welcomeTxt.substring(welcomeTxt.indexOf(",")));
		document.getElementsByClassName("title")[0].innerHTML = welcomeTxt;
	} catch(e) {
//		alert("Your browser fails.")
	}
}
