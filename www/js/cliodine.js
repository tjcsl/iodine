var ctrlpressed=false;
var ltext;
var rtext;
var cursor;
var inputspan;
var idoffset=0;

var ajaxRequest;

try {
	ajaxRequest= new XMLHttpRequest();
} catch (e) {
	try {
		ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e) {
			alert("Sorry, won't work. Going back to normal iodine");
			window.location="../";
		}
	}
}

ajaxRequest.onreadystatechange = function(){
	if(ajaxRequest.readyState == 4){
		var i = document.createElement("div");
		i.innerHTML=ajaxRequest.responseText;
		document.getElementById("terminal").appendChild(i);
		idoffset+=1;
		newprompt();
	}
}

function pressed(e) {
	if( e.which>=32 && e.which<=126) {
		var character=String.fromCharCode(e.which)
		var letter=character.toLowerCase();
	}
	if(letter) {
		//alert(letter+"_:_"+character+"__"+ctrlpressed);
		ltext.innerHTML=ltext.innerHTML+character;
	} else if (e.which==8) { // Backspace
		if(ltext.innerHTML.length >0) {
			ltext.innerHTML=ltext.innerHTML.substring(0,ltext.innerHTML.length-1);
		}
	} else if (e.which==13) { // Enter
		var str=ltext.innerHTML+cursor.innerHTML+rtext.innerHTML;
		str=str.replace(" ","/");
		ajaxRequest.open("GET",str,true);
		ajaxRequest.send();
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
	 inputspan.id="inputspan"+idoffset;
	 var promptstatic = document.createElement("span");
	  promptstatic.id="promptstatic"+idoffset;
	  var j = document.createTextNode(username + "@iodine:~$ ");
	  promptstatic.appendChild(j);
	 inputspan.appendChild(promptstatic);
	 ltext = document.createElement("span");
	  ltext.id="ltext"+idoffset;
	 inputspan.appendChild(ltext);
	 cursor= document.createElement("span");
	  cursor.id="cursor"+idoffset;
	 inputspan.appendChild(cursor);
	 rtext = document.createElement("span");
	  rtext.id="rtext"+idoffset;
	 inputspan.appendChild(rtext);
	document.getElementById("terminal").appendChild(inputspan);
	document.getElementById("terminal").appendChild(document.createElement("br"));
}
function init() {
	document.onkeypress=function(e){ pressed(e)};
	document.onkeydown= function(e){ specialdown(e)};
	document.onkeyup=   function(e){ specialup(e)};
	newprompt();
}
