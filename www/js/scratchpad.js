////////////////////////////////////////////////////////////////////////////////
//                   JAVASCRIPT FOR SCRATCHPAD                                //
// Author: Joshua Cranmer <jcranmer@tjhsst.edu>                               //
// Last Updated: 01/25/09                                                     //
// Exported functions:                                                        //
//   NONE                                                                     //
// Local use functions:                                                       //
// + makeTab(name, value)                                                     //
//   Arguments:                                                               //
//     + table: the table itself whose first body is to be sorted.            //
//     + direction: 1 if it should be sorted ascending, -1 if descending      //
//     + index: the zero-based index of the cell in the table to sort on      //
//     + func: an optional argument that is the function which returns the    //
//       value to be sorted on given the cell. It is given the cell and it    //
//       returns the value to sort on.                                        //
// -------------------------------------------------------------------------- //
// Note that the sort function will sort the entire body of the table, so     //
// make sure that any column headers or footers are in thead/tfoot blocks.    //
// Also note that the function will only sort the first tbody and not any     //
// subsequent ones, which is useful for if only the first part of the table   //
// is to be sorted.                                                           //
////////////////////////////////////////////////////////////////////////////////

var chflag=0;
var http;
if (window.ActiveXObject) {
	var ms = new ActiveXObject("Msxml2.XMLHTTP");
	http = ms ? ms : new ActiveXObject("Microsoft.XMLHTTP");
} else if (window.XMLHttpRequest) {
	http = new XMLHttpRequest();
}
window.onload = function(){
	// Set up tab functions
	tabInfo.list = document.getElementById("tabs");
	tabInfo.values = new Array();
	tabInfo.names = new Array();
	tabInfo.num = 0;
	for (var i=0;i<tabInfo.list.childNodes.length;i++) {
		tabInfo.list.childNodes.item(i).onclick = selectTab;
	}
	
	// load the text dynamically
	http.onreadystatechange = function() {
		try {
		if(http.readyState == 4 && http.status == 200) {
			var all = http.responseText.split("&");
			for (var i=0;i<all.length;i++) {
				var junk = all[i].split("=");
				makeTab(decodeURIComponent(junk[0]),decodeURIComponent(junk[1]));
			}
			tabInfo.all = all; // This is our changeflag
			document.getElementById("text").value = tabInfo.values[0];
			tabInfo.list.childNodes.item(0).className = "active";
		}
		} catch (e) {
			window.onunload = null;
		}
	};
	http.open('GET', load_page, true);
	http.send(null);
}

window.onunload = function(){
	if (tabInfo.all == null) // We did not download anything
		return;
	for (var i=0;i<tabInfo.list.childNodes.length;i++) {
		if (tabInfo.list.childNodes.item(i).className == "active") {
			tabInfo.values[i] = document.getElementById("text").value;
			break;
		}
	}
	http.open('POST', save_page,false);
	http.setRequestHeader('Content-Type','text/plain');
	http.onreadystatechange = function () {};
	var arr = [];
	for (var i=0;i<tabInfo.names.length;i++) {
		var str = encodeURIComponent(tabInfo.names[i]);
		str += '=';
		if (tabInfo.values[i].length > 1000) {
			tabInfo.values[i] = tabInfo.values[i].substring(0,1000);
		}
		str += encodeURIComponent(tabInfo.values[i]);
		arr[i] = str;
	}
	http.send(arr.join('&'));
}

// TAB FUNCTIONS
var tabInfo = new Object();
function makeTab(name, value) {
	if (tabInfo.num == 5) {
		alert("You can only have 5 tabs.");
		return;
	}

	var tab = document.createElement("li");
	tab.index = tabInfo.num;
	tab.appendChild(document.createTextNode(name));

	var close = document.createElement("img");
	close.src = close_source;
	close.setAttribute("onclick","deleteTab(this)");
	tab.appendChild(close);

	tab.onclick = selectTab;
	tabInfo.list.insertBefore(tab,tabInfo.list.lastChild);
	tabInfo.names[tabInfo.num] = name;
	tabInfo.values[tabInfo.num] = value;
	tabInfo.num++;
}
function editTab(tab) {
	if (tab.editing == true)
		return;
	tab.editing = true;
	tabInfo.edit = tab;

	var name = tab.firstChild.nodeValue;
	var input = document.createElement("input");
	input.setAttribute("type","text");
	input.setAttribute("value",name);
	input.setAttribute("size",name.length+5);
	input.onblur = uneditTab;
	input.onkeypress = checkEnter;
	
	tab.replaceChild(input, tab.firstChild);
	input.focus();
}
function uneditTab() {
	var tab = tabInfo.edit;
	tab.editing = false;

	var name = tab.firstChild.value;
	if (name == "")
		name = tab.firstChild.getAttribute("value");

	if (name.length > 20) {
		alert("The tab name is too long. It will be truncated.");
		name = name.substring(0,20);
	}
	tab.replaceChild(document.createTextNode(name),tab.firstChild);
	tab.className = "active";
	tabInfo.names[tab.index] = name;
	chflag = 1;
}
function checkEnter(ev) {
	var key = ev.which ? ev.which : ev.keyCode;
	if (key == 13)
		uneditTab();
}
function deleteTab(img) {
	var tab = img.parentElement;
	tabInfo.values.splice(tab.index, 1);
	tabInfo.names.splice(tab.index, 1);
	tabInfo.num--;
	for (var t = tab.nextSibling; t != tabInfo.list.lastChild; t = t.nextSibling)
		t.index--;
	if (tab.nextSibling != tabInfo.list.lastChild && tab.className == "active") {
		tab.nextSibling.className = "active";
		document.getElementById("text").value = tabInfo.values[tab.index];
	}
	tab.parentElement.removeChild(tab);
	chflag = 1;
}
function selectTab(ev) {
	var tab = ev.target;
	if (tab == tabInfo.list.lastChild) {
		makeTab("Tab "+tabInfo.list.childNodes.length,'');
	} else if (tab.className == "active") {
		editTab(tab);
	} else if (tab.tagName.toLowerCase() == "li") {
		var index;
		for (var i=0;i<tabInfo.list.childNodes.length;i++) {
			if (tabInfo.list.childNodes.item(i).className == "active") {
				tabInfo.list.childNodes.item(i).className = "";
				tabInfo.values[i] = document.getElementById("text").value;
			} else if (tabInfo.list.childNodes.item(i) == tab) {
				index = i;
			}
		}
		tab.className = "active";
		document.getElementById("text").value = tabInfo.values[index];
	}
}
