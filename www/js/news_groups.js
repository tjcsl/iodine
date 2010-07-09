function news_addGroup(gid) {
	var table = document.getElementById("news_groups_table");
	var row = table.insertRow(table.rows.length-1);
	row.insertCell(0).innerHTML="&nbsp;";
	
	var groupsList = document.getElementById("groups");
	var select = document.createElement("SELECT");
	select.name="add_groups[]";
	select.className="groups_list";
	//Different method for ie5- and ie6+. Both work in everything else.
	var method=0;
	if(navigator.appName == 'Microsoft Internet Explorer') {
		if (navigator.appName == 'Microsoft Internet Explorer')
		{
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				if(parseFloat( RegExp.$1 )>5){
					method=1;
				}
		}
	}
	if(method==0) {//Everyone else + IE5-
		for (var i = 1; i < groupsList.options.length; i+=1) {
			var option = groupsList.options[i];
			var newOption = document.createElement("OPTION");
			newOption.value = option.value;
			/*newOptiontext=document.createTextNode(option.text);
			newOption.appendChild(newOptiontext);*/
			newOption.text=option.text;
			if (gid == newOption.value) {
				newOption.selected = true;
			}
			select.appendChild(newOption);
		}
	} else {//IE6+
		for (var i = 1; i < groupsList.options.length; i+=1) {
			var option = groupsList.options[i];
			var newOption = document.createElement("OPTION");
			newOption.value = option.value;
			newOptiontext=document.createTextNode(option.text);
			newOption.appendChild(newOptiontext);
			//newOption.text=option.text;
			if (gid == newOption.value) {
				newOption.selected = true;
			}
			select.appendChild(newOption);
		}
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
