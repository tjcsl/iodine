function add_field(name, obj) {
	var br = document.createElement("br");
	var input = document.createElement("input");
	input.type = "text";
	input.name = "pref_" + name + "[]";
	input.className = "pref_preference_input";
	var a = document.createElement("a");
	a.href = "#";
	a.onclick = function onclick(event) {
		remove_field(name, this);
	};
	a.innerHTML = "Remove";
	obj.parentNode.appendChild(br, obj);
	obj.parentNode.appendChild(input, obj);
	obj.parentNode.appendChild(a, obj);
}

function remove_field(name, obj) {
	obj.parentNode.removeChild(obj.previousSibling);
	obj.parentNode.removeChild(obj.previousSibling);
	obj.parentNode.removeChild(obj);
}
