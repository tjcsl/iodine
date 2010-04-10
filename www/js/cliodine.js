var ctrlpressed=false;
var ltext;
var rtext;
var cursor;
var inputspan;
function pressed(e) {
	if( e.which>=32 && e.which<=126) {
		var character=String.fromCharCode(e.which)
		var letter=character.toLowerCase();
	}
	if(letter) {
		//alert(letter+"_:_"+character+"__"+ctrlpressed);
		ltext.innerHTML=ltext.innerHTML+character;
	} else if (e.which==8) {
		if(ltext.innerHTML.length >0) {
			ltext.innerHTML=ltext.innerHTML.substring(0,ltext.innerHTML.length-1);
		}
	}
}
function specialdown(e) {
	if(e.which==17)
		ctrlpressed=true;
}
function specialup(e) {
	if(e.which==17)
		ctrlpressed=false;
}
function newprompt() {
	inputspan = document.createElement("span");
	 inputspan.id="inputspan";
	 var promptstatic = document.createElement("span");
	  promptstatic.id="promptstatic";
	  var j = document.createTextNode(username + "@iodine:~$ ");
	  promptstatic.appendChild(j);
	 inputspan.appendChild(promptstatic);
	 ltext = document.createElement("span");
	  ltext.id="ltext";
	 inputspan.appendChild(ltext);
	 cursor= document.createElement("span");
	  cursor.id="cursor";
	 inputspan.appendChild(cursor);
	 rtext = document.createElement("span");
	  rtext.id="rtext";
	 inputspan.appendChild(rtext);
	document.getElementById("terminal").appendChild(inputspan);
}
function init() {
	document.onkeypress=function(e){ pressed(e)};
	document.onkeydown= function(e){ specialdown(e)};
	document.onkeyup=   function(e){ specialup(e)};
	newprompt();
}
