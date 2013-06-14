var savedList;
function makeSearchable(listElem, textField) {
	savedList = listElem.cloneNode(true);
	textField.setAttribute("onchange", "filterList(value);");
	textField.setAttribute("oninput", "filterList(value);");
	textField.setAttribute("onsearch", "filterList(value);");
}

function filterList(txt) {
	txt = txt.toLowerCase();
	txt = txt.split(" or ");
	
	var currentList = document.getElementById("activity_list");
	currentList.innerHTML = "";
	
        var listItems = savedList.getElementsByTagName("option");
	var listItems = savedList.options;
	for (var i = 0; i < listItems.length; i++) {
		for (var j = 0; j < txt.length; j++) {
			if (listItems[i].innerHTML.toLowerCase().indexOf(txt[j]) != -1) {
				currentList.appendChild(listItems[i].cloneNode(true));
				break;
			}
		}
	}
	
        currentList.innerHTML = newList.innerHTML;
}
