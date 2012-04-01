[<include file="../javascriptstyles/includes/clippy.js">]

function page_init() {
	if(navigator.userAgent.toLowerCase().indexOf("mobile") != -1) {
		return;
	}
	clippy.init();
	
	var module = location.href.replace(i2root, "").toLowerCase().split("/");
	
	var moduleMsg = "It looks like you\'re ";
	var helpURL = i2root + "info/";
	var helpYesTxt = "";
	var helpNoTxt = "";
	
	console.log(module);
	
	switch(module[0]) {
		case "birthdays":
			moduleMsg += "viewing birthdays";
			helpURL += "birthdays";
			helpYesTxt = "viewing birthdays";
			helpNoTxt = "view birthdays";
		break;
		case "filecenter":
			moduleMsg += "accessing files";
			helpURL += "filecenter";
			helpYesTxt = "accessing files";
			helpNoTxt = "access files";
		break;
		case "eighth":
			switch(module[1]) {
				case "vcp_schedule":
					if(!!module[2] && module[2] == "absences") {
						moduleMsg += "dealing with eighth period absences";
						helpURL += "eighth/clear";
						helpYesTxt = "dealing with eighth period absences";
						helpNoTxt = "deal with eighth period absences";
					} else {
						moduleMsg += "signing up for eighth period";
						helpURL += "eighth/signup";
						helpYesTxt = "signing up for eighth period";
						helpNoTxt = "sign up for eighth period";
					}
				break;
				default:
					moduleMsg += "signing up for eighth period";
					helpURL += "eighth";
					helpYesTxt = "signing up for eighth period";
					helpNoTxt = "sign up for eighth period";
				break;
			}
		break;
		case "news":
			switch(module[1]) {
				case "request":
					moduleMsg += "trying to submit a news request";
					helpURL += "news/request";
					helpYesTxt = "submitting the news post";
					helpNoTxt = "type the news post";
				break;
				default:
					moduleMsg += "reading news";
					helpURL += "news";
					helpYesTxt = "reading news";
					helpNoTxt = "read news";
				break;
			}
		break;
		case "polls":
			switch(module[1]) {
				case "vote":
					moduleMsg += "voting on a poll";
					helpURL += "polls/vote";
					helpYesTxt = "voting on the poll";
					helpNoTxt = "vote on the poll";
				break;
				default:
					moduleMsg += "trying to access polls";
					helpURL += "polls";
					helpYesTxt = "accessing polls";
					helpNoTxt = "access polls";
				break;
			}
		break;
		case "prefs":
			moduleMsg += "modifying your preferences";
			helpURL += "prefs";
			helpYesTxt = "modifying preferences";
			helpNoTxt = "change preferences";
		break;
		case "studentdirectory":
			switch(module[1]) {
				case "info":
					moduleMsg += "stalking someone";
					helpURL += "studentdirectory/info";
					helpYesTxt = "stalking people";
					helpNoTxt = "stalk people";
				break;
				default:
					moduleMsg += "searching the directory";
					helpURL += "studentdirectory";
					helpYesTxt = "searching the directory";
					helpNoTxt = "search the directory";
				break;
			}
		break;
		case "info":
			moduleMsg += "getting help";
			helpURL = location.href;
			helpYesTxt = "getting help";
			helpNoTxt = "get help";
		break;
		default:
			moduleMsg += "using the Intranet";
			helpURL += "core";
			helpYesTxt = "using the Intranet";
			helpNoTxt = "use the Intranet";
		break;
	}
	
	moduleMsg += ".  Can I help you with that?";
	moduleMsg += "<ul><a href=\"" + helpURL + "\"><li>Get help with " + helpYesTxt + "</li></a><a href=\"#\" onclick=\"clippy.hide();\"><li>Just " + helpNoTxt + " without help</li></a></ul>";
	
	if(module[0] == "info" && module[1] == "credits") {
		moduleMsg = "These guys are awesome.";
	} else if(module[0] == "suggestion") {
		moduleMsg = "Suggestions are fantastic...as long as they are not about getting rid of me.  I got enough of that ten years ago.";
	}
	
	clippy.displayMessage(moduleMsg);
}

function intrabox_onmouseover(div_id) {
}
function intrabox_onmouseout(div_id) {
}
function menu_onmouseover() {
}
function menu_onmouseout() {
}
function menuitem_onmouseover(div_id,not_1,not_2,not_3,not_4,not_5,not_6,not_7) {
}
function menuitem_onmouseout(div_id) {
}
