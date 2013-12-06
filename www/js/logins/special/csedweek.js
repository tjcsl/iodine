//Configuration Options
var interval = 100;
var stepsize = 6;
var codefile = i2root+"/www/js/logins/special/csedweek.txt";

//Globals
var codediv;
var code = "";
var index=0;
var ajaxRequest;

//Ajax
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
		code = ajaxRequest.responseText;
		index=0;
		window.setInterval(step,100);
	}
}

//Setup
function initcode() {
	codediv = document.createElement("div");
	codediv.id="codediv";
	codediv.innerHTML="";
	codediv.style.position="absolute";
	codediv.style.top="0px";
	codediv.style.left="570px";
	codediv.style.right="0px";
	codediv.style.bottom="0px";
	codediv.style.color="#00ff00";
	codediv.style.fontFamily="monospace";
	codediv.style.overflow="hidden";
	codediv.style.whiteSpace="pre";
	document.getElementsByTagName("body")[0].appendChild(codediv);
	ajaxRequest.open("GET",codefile,true);
	ajaxRequest.send(null);
}

//Stepping
function step() {
	codediv.innerHTML = codediv.innerHTML+code.substring(index,index+stepsize);
	index+=stepsize;
	codediv.scrollTop = codediv.scrollHeight;
}

window.onload=initcode;
