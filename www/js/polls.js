////////////////////////////////////////////////////////////////////////////////
//                              Polls magic javascript                        //
////////////////////////////////////////////////////////////////////////////////

var root = document.location.href;
root = root.substring(0,root.indexOf("polls"));
function addQuestion(event) {
	event.preventDefault();
	var table = document.getElementById("poll_question_list");
	var body = table.tBodies.item(0);
	var id = body.rows.length;
	if (id != 0)
		id = parseInt(body.rows.item(id-2).firstChild.firstChild.nodeValue)+1;

	var row = document.createElement("tr");
	var cell = document.createElement("th");
	cell.setAttribute("rowspan","2");
	var input = document.createElement("input");
	input.name = "question[]";
	input.type = "hidden";
	input.value = id;
	cell.appendChild(document.createTextNode(id));
	cell.appendChild(input);
	row.appendChild(cell);

	cell = document.createElement("td");
	input = document.createElement("input");
	input.name = "q_"+id+"_name";
	input.type = "text";
	cell.appendChild(input);
	row.appendChild(cell);

	var select = document.createElement("select");
	select.name = "q_"+id+"_type";
	var option = document.createElement("option");
	option.appendChild(document.createTextNode("Standard"));
	option.setAttribute("value","standard");
	select.appendChild(option);
	option = document.createElement("option");
	option.appendChild(document.createTextNode("Approval"));
	option.setAttribute("value","approval");
	select.appendChild(option);
	option = document.createElement("option");
	option.setAttribute("value","split_approval");
	option.appendChild(document.createTextNode("Split approval"));
	select.appendChild(option);
	option = document.createElement("option");
	option.appendChild(document.createTextNode("Free response"));
	option.setAttribute("value","free_response");
	select.appendChild(option);
	cell = document.createElement("td");
	cell.appendChild(select);
	row.appendChild(cell);
	
	cell = document.createElement("td");
	input = document.createElement("input");
	input.name = "q_"+id+"_lim";
	input.type = "text";
	input.size = 3;
	input.value = 0;
	input.maxLength = 3;
	cell.appendChild(input);
	row.appendChild(cell);

	cell = document.createElement("td");
	input = document.createElement("a");
	var img = document.createElement("img");
	img.onclick = deleteRow;
	img.src = root+"www/pics/pollx.gif";
	input.appendChild(img);
	cell.appendChild(input);
	row.appendChild(cell);
	body.appendChild(row);

	row = document.createElement("tr");
	cell = document.createElement("td");
	cell.setAttribute("colspan","4");
	var list = document.createElement("ul");
	var item = document.createElement("li");
	var a = document.createElement("a");
	a.onclick = addAnswer;

	a.appendChild(document.createTextNode("Add an answer choice"));
	item.appendChild(a);
	list.appendChild(item);
	cell.appendChild(list);
	row.appendChild(cell);
	body.appendChild(row);
}

function deleteRow(event) {
	event.preventDefault();
	var img = event.target;
	var table = document.getElementById("poll_question_list");
	var body = table.tBodies.item(0);
	body.removeChild(img.parentNode.parentNode.parentNode.nextSibling);
	body.removeChild(img.parentNode.parentNode.parentNode);
}

function addAnswer(event) {
	event.preventDefault();
	var cell = event.target;
	var list = cell.parentNode.parentNode;
	var item = document.createElement("li");

	var input = document.createElement("a");
	input.onclick = deleteAnswer;
	input.appendChild(document.createTextNode("Delete"));
	item.appendChild(input);
	item.appendChild(document.createTextNode("\u00A0\u00A0\u00A0"));

	var id = list.childNodes.length-1;
	var q = list.parentNode.parentNode.previousSibling.firstChild.firstChild.nodeValue;
	if (id != 0) {
		var temp = list.childNodes.item(id);
		if (temp.lastChild.nodeType == 3)
			temp = temp.lastChild.previousSibling
		else
			temp = temp.lastChild;
		id = parseInt(temp.name.substring(temp.name.lastIndexOf("_")+1))+1;
	}
	input = document.createElement("input");
	input.setAttribute("type","hidden");
	input.value = id;
	input.name = "a_"+q+"[]";
	item.appendChild(input);

	input = document.createElement("input");
	input.name = "a_"+q+"_"+id;
	item.appendChild(input);
	list.appendChild(item);
}

function deleteAnswer(event) {
	event.preventDefault();
	var e = event.target;
	e.parentNode.parentNode.removeChild(e.parentNode);
}

function polls_addGroup(event) {
	event.preventDefault();
	var table = document.getElementById("polls_groups_table");
	var body = table.tBodies.item(0);
	var index = body.lastChild.previousSibling;
	index = index.firstChild;
	if (index.nodeType != 1)
		index = index.nextSibling;
	index = parseInt(index.firstChild.value)+1;
	var row = body.insertRow(table.rows.length-2);

	var box = document.createElement("input");
	box.type = "hidden";
	box.name = "groups[]";
	box.value = index;
	row.insertCell(0).appendChild(box); // The empty cell for entabulation
	
	var groupsList = document.getElementById("polls_groups");
	var select = document.createElement("select");
	select.name="group_gids[]";
	select.className="groups_list";
	for (var i = 1; i < groupsList.options.length; i+=1) {
		var option = groupsList.options[i];
		var newOption = document.createElement("option");
		newOption.value = option.value;
		newOption.text=option.text;
		select.appendChild(newOption);
	}
	row.insertCell(1).appendChild(select);

	box = document.createElement("input");
	box.type = "checkbox";
	box.name = "vote[]";
	row.insertCell(2).appendChild(box);

	box = document.createElement("input");
	box.type = "checkbox";
	box.name = "modify[]";
	row.insertCell(3).appendChild(box);

	box = document.createElement("input");
	box.type = "checkbox";
	box.name = "results[]";
	row.insertCell(4).appendChild(box);

	var remove = document.createElement("a");
	remove.appendChild(document.createTextNode("remove"));
	remove.onclick = deleteGroup;
	row.insertCell(5).appendChild(remove);
}

function polls_deleteGroup(event) {
	event.preventDefault();
	var row = event.target.parentNode.parentNode;
	var table = row.parentNode;
	if (table.childNodes.length == 2) {
		alert("Must have at least one group for permissions!");
		return;
	}
	table.removeChild(row);
}
