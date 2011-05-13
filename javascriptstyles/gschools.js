preload();
function preload() {
	/* Begin title stuff */
	document.title = "Google Schools: " + document.title.substring(18);
	/* End title stuff */
}
function changeToGSearch() {
}
function page_init() {
	//alert("Begin page_init() stuffs"); // for debugging

	// change "Logout" link to say "Sign out"
	document.getElementById("menu_logout").innerHTML = "Sign out";

	/* Begin top header */
	var topheader = document.createElement("div");
	topheader.className = "gtopbar";
	topheader.innerHTML = "&nbsp;&nbsp;<b>Schools</b>&nbsp;&nbsp;<a href='http://postman.tjhsst.edu' target='_blank'>Calendar</a>&nbsp;&nbsp;<a href='[<$I2_ROOT>]filecenter'>Documents</a>&nbsp;&nbsp;<a href='https://webmail.tjhsst.edu' target='_blank'>Mail</a>&nbsp;&nbsp;<a href='http://publications.tjhsst.edu/tjtoday' target='_blank'>News</a>&nbsp;&nbsp;<a href='http://tjhsst.edu' target='_blank'>Web</a>";
	document.getElementsByTagName("body")[0].appendChild(topheader);
	/* End top header */
	/* Begin side links */
	// Figure out what page we're on
	var page = "";
	if(location.href.indexOf("news") != -1 || location.href == "[<$I2_ROOT>]")
		page="news";
	else if(location.href.indexOf("eighth") != -1)
		page="eighth";
	else if(location.href.indexOf("polls") != -1)
		page="polls";
	else if(location.href.indexOf("mail") != -1)
		page="mail";
	var linksdiv = document.createElement("div");
	linksdiv.className = "glinks";
	linksdiv.id = "glinks";

	var newsbutton = document.createElement("a");
	newsbutton.href = "[<$I2_ROOT>]news";
	newsbutton.innerHTML = "News";
	var newsdiv = document.createElement("div");
	newsdiv.appendChild(newsbutton);
	newsdiv.className = (page=="news")?"glinks-selected":"glinks-unselected";

	var eighthbutton = document.createElement("a");
	eighthbutton.href = "[<$I2_ROOT>]eighth/vcp_schedule/view/uid/[<$I2_USER->uid>]";
	eighthbutton.innerHTML = "Eighth";
	var eighthdiv = document.createElement("div");
	eighthdiv.appendChild(eighthbutton);
	eighthdiv.className = (page=="eighth")?"glinks-selected":"glinks-unselected";

	var pollbutton = document.createElement("a");
	pollbutton.href = "[<$I2_ROOT>]polls";
	pollbutton.innerHTML = "Polls";
	var polldiv = document.createElement("div");
	polldiv.appendChild(pollbutton);
	polldiv.className = (page=="polls")?"glinks-selected":"glinks-unselected";

	var mailbutton = document.createElement("a");
	mailbutton.href = "https://webmail.tjhsst.edu";
	mailbutton.innerHTML = "Mail";
	var maildiv = document.createElement("div");
	maildiv.appendChild(mailbutton);
	maildiv.className = (page=="mail")?"glinks-selected":"glinks-unselected";

	linksdiv.appendChild(newsdiv);
	linksdiv.appendChild(eighthdiv);
	linksdiv.appendChild(polldiv);
	linksdiv.appendChild(maildiv);
	document.getElementsByTagName("body")[0].insertBefore(linksdiv,document.getElementById("intraboxes"));
	/* End side links */

//	alert("Glinks added!"); // for debugging

	/* Begin top right stuff */
	document.getElementById("menu").insertBefore(document.createTextNode(" "),document.getElementById("menu_home"));
	var littlebar = document.createElement("span");
	littlebar.className = "bold";
	littlebar.innerHTML = "·";
	littlebar.id="littlebar";
	document.getElementById("menu").insertBefore(littlebar,document.getElementById("menu").firstChild);
	document.getElementById("menu").insertBefore(document.createTextNode(" "),document.getElementById("menu").firstChild);
	var usernametext = document.createElement("a");
	usernametext.innerHTML = "[<$I2_USER->username>]@tjhsst.edu";
	usernametext.href = "[<$I2_ROOT>]studentdirectory/info/[<$I2_USER->uid>]";
	usernametext.className = "username";
	usernametext.style.color = "black";
	usernametext.id = "menu_username";
	document.getElementById("menu").insertBefore(usernametext,document.getElementById("menu").firstChild);
	/* End top right stuff */
	/* Begin search bar */
	var searchformdiv = document.createElement("div");
	searchformdiv.className = "searchformdiv";
	var searchform = document.createElement("form");
	searchform.method = "post";
	searchform.action = "[<$I2_ROOT>]studentdirectory/search/";
	searchform.id= "searchform";
	var textfield = document.createElement("input");
	textfield.type="text";
	textfield.id="searchbox";
	textfield.name="studentdirectory_query";
	textfield.style.width = "300px";
	textfield.style.outlineColor = "lightBlue";
	textfield.style.borderWidth = "1px";
	textfield.style.padding = "2px";
	var searchbutton = document.createElement("input");
	searchbutton.type="submit";
	searchbutton.value = "Search Directory";
	var gsearchbutton = document.createElement("input");
	gsearchbutton.type="submit";
	gsearchbutton.value = "Search Web";
	gsearchbutton.id = "gsearchbutton";
	gsearchbutton.onclick = function() {
		var form = document.getElementById("searchform");
		form.method="get";
		form.action="http://www.google.com/search";
		document.getElementById("searchbox").name="q";
	}
	searchform.appendChild(textfield);
	searchform.appendChild(searchbutton);
	searchform.appendChild(gsearchbutton);
	searchformdiv.appendChild(searchform);
	document.getElementsByTagName("body")[0].appendChild(searchformdiv);
	//document.getElementById("gsearchbutton").onclick = changeToGSearch;
	/* End search bar */
}
function intrabox_onmouseover(div_id) {
}
function intrabox_onmouseout(div_id) {
}
function menu_onmouseover() {
}
function menu_onmouseout() {
}
function menuitem_onmouseover(div_id,not_1,not_2,not_3,not_4,not_5) {
}
function menuitem_onmouseout(div_id) {
}
