var ctrlpressed=false;
var ltext;
var rtext;
var cursor;
var inputspan;
var idoffset=0;
var timer;

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
		cursor.setAttribute("class","darkcursor");
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
		clearTimeout(timer);
		brighten();
	} else if (e.which==8) { // Backspace
		if(ltext.innerHTML.length >0) {
			ltext.innerHTML=ltext.innerHTML.substring(0,ltext.innerHTML.length-1);
		}
		clearTimeout(timer);
		brighten();
	} else if (e.which==13) { // Enter
		var str=ltext.innerHTML+cursor.innerHTML+rtext.innerHTML;
		str=str.replace("&nbsp;"," ");
		str=str.replace(" ","/");
		str=i2root+"cliodine/"+str;
		ajaxRequest.open("GET",str,true);
		ajaxRequest.send(null);
	} else if (e.keyCode>=37&&e.keyCode<=40) { // Arrow Keys
		switch (e.keyCode) {
			case 37:
				if(ltext.innerHTML.length>0) {
					rtext.innerHTML=cursor.innerHTML+rtext.innerHTML;
					cursor.innerHTML=ltext.innerHTML.charAt(ltext.innerHTML.length-1);
					ltext.innerHTML=ltext.innerHTML.substr(0,ltext.innerHTML.length-1);
				}
				break;
			case 38:
				if(rtext.innerHTML.length>0) {
					ltext.innerHTML=ltext.innerHTML+cursor.innerHTML;
					cursor.innerHTML=rtext.innerHTML.charAt(0);
					rtext.innerHTML=rtext.innerHTML.substr(1,rtext.innerHTML.length);
				}
				break;
		}
		clearTimeout(timer);
		brighten();
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
	 inputspan.setAttribute("class","inputspan");
	 var promptstatic = document.createElement("span");
	  promptstatic.id="promptstatic"+idoffset;
	  var j = document.createTextNode(username + "@iodine:~$ ");
	  promptstatic.appendChild(j);
	 inputspan.appendChild(promptstatic);
	 ltext = document.createElement("span");
	  ltext.id="ltext"+idoffset;
	  ltext.setAttribute("class","ltext");
	 inputspan.appendChild(ltext);
	 cursor= document.createElement("span");
	  cursor.id="cursor"+idoffset;
	  cursor.setAttribute("class","cursor");
	  cursor.setAttribute("width","8px");
	  cursor.innerHTML="&nbsp";
	 inputspan.appendChild(cursor);
	 rtext = document.createElement("span");
	  rtext.id="rtext"+idoffset;
	  rtext.setAttribute("class","rtext");
	 inputspan.appendChild(rtext);
	document.getElementById("terminal").appendChild(inputspan);
	document.getElementById("terminal").appendChild(document.createElement("br"));
}
function brighten() {
	cursor.setAttribute("class","cursor");
	timer=setTimeout("darken()",500);
}
function darken() {
	cursor.setAttribute("class","darkcursor");
	timer=setTimeout("brighten()",500);
}
function init() {
	document.onkeypress=function(e){ pressed(e)};
	document.onkeydown= function(e){ specialdown(e)};
	document.onkeyup=   function(e){ specialup(e)};
	newprompt();
	timer=setTimeout("darken()",500);
}
