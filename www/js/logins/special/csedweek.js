//Configuration Options
var interval = 100;
var stepsize = 12;
//var codefile = i2root+"/www/js/logins/special/csedweek.txt";
var git = "/git/?p=intranet2.git;a=blob_plain;f=modules/";
var files = [git+"eighth/eighth.mod.php5",git+"news/news.mod.php5",git+"dayschedule/dayschedule.mod.php5",git+"studentdirectory/studentdirectory.mod.php5",git+"news/news.mod.php5",i2root+"www/js/logins/special/csedweek-ion.txt"];
var codefile = files[Math.floor((+new Date/100)%files.length)];
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
			console.log("Sorry, won't work. Going back to normal iodine");
		}
	}
}
ajaxRequest.onreadystatechange = function(){
	if(ajaxRequest.readyState == 4){
		code = ajaxRequest.responseText;
		index=0;
		codeint = window.setInterval(step,100);
	}
}

//Setup
function initcode() {
	codediv = document.createElement("div");
	codediv.id="codediv";
	codediv.innerHTML="<a href='http://csedweek.org' style='color:inherit;text-decoration:none'><div style='position:fixed;top:0;width:100%;z-index:1;font-family:inherit;white-space:inherit;background:black'>/*\n * Happy Computer Science Education Week from the Sysadmins!\n */\n</div></a>";
	codediv.style.position="absolute";
	codediv.style.top="0px";
	codediv.style.left="570px";
	codediv.style.right="0px";
	codediv.style.bottom="0px";
	codediv.style.color="#00ff00";
	codediv.style.fontFamily="monospace";
	codediv.style.overflow="hidden";
	codediv.style.whiteSpace="pre";
	codediv.style.paddingTop="60px";
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
