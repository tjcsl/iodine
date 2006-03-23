function addGroup(gid) {
	var table = document.getElementById("groups_table");
	var row = table.insertRow(table.rows.length-1);
	row.insertCell(0).innerHTML="&nbsp;";
	
	var groupsList = document.getElementById("groups");
	var select = document.createElement("SELECT");
	select.name="add_groups[]";
	select.className="groups_list";
	for (var i = 1; i < groupsList.options.length; i+=1) {
		var option = groupsList.options[i];
		var newOption = document.createElement("OPTION");
		newOption.value = option.value;
		newOption.text=option.text;
		if (gid == newOption.value) {
			newOption.selected = true;
		}
		select.appendChild(newOption);
	}
	row.insertCell(1).appendChild(select);
	
	var remove = document.createElement("a");
	remove.href="#";
	remove.innerHTML="remove";
	remove.onclick=function() {
		table.deleteRow(row.rowIndex);
		remove.onclick="";
		return false;
	}
	row.insertCell(2).appendChild(remove);
}
