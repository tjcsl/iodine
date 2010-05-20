//Ideal situation:
// This is where chat for intranet will be implemented. It's basically a javascript IRC client
// Which connects to a specific server, allowing it to communicate in a way that's already pretty
// well-defined. Also, it saves us the effort of designing a better protocol, not to mention
// getting iodine up to the speed necessary to run an AJAX chat in real time.
//Current situation:
// It's an AJAX chat client within intranet.

var container = document.getElementById("chat_container");
container.innerHTML="Loading.";
var chatarea = document.getElementById("chat_area");

/* Null Communication */
function incomingMessage(obj,event) {
	return;
}
function sendMessage(message) {
	return;
}
/* End Null Communication */

/* Testing Communication with alert() */
/*
function incomingMessage(obj,event) {
	if((event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!=13)
		return true;
	responseRecieved(obj.value);
	return false;
}
function sendMessage(message) {
	alert(message);
}
*/
/* End Testing transmission */

// Because we'll use the irc protocol for communication, but don't have a specified tunnel system,
// we define several methods of tunneling the connections here, and decide which one to use based
// on the target browser and configuration.
/* AJAX transmission */
/*
var ajaxChatRequest;

try {
	ajaxChatRequest= new XMLHttpRequest();
} catch (e) {
	try {
		ajaxChatRequest = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			ajaxChatRequest = new ActiveXObject("Microsoft.XMLHTTP");
		} catch(e) {
			alert("Sorry, won't work. Going back to normal iodine");
			window.location="../";
		}
	}
}

ajaxChatRequest.onreadystatechange = function(){
	if(ajaxChatRequest.readyState == 4){
		responseRecieved(ajaxChatRequest.responseText);
	}
}

function sendMessage(message) {
	ajaxChatRequest.open("POST","http://www.google.com/",true);
	ajaxChatRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajaxChatRequest.setRequestHeader("Content-length", message.length);
	ajaxChatRequest.setRequestHeader("Connection", "close");
	ajaxChatRequest.send(message);
}
*/
/* End AJAX */

/* Begin IRC functions */

//var senderindicatorregex = new RegExp("something that matches the strings:  :user!username@host.webaddress");

function initSession() {
	container.innerHTML="Welcome "+name;
	//addchatwindow("#tjhsst");
	
	var inbox = document.createElement('textarea');
	inbox.style.width="100%";
	inbox.setAttribute('onkeypress','incomingMessage(this,event)');
	inbox.id="#server";
	container.appendChild(inbox);
	
	sendMessage("NICK i2-" + username);
	sendMessage("USER i2-" + username + " 4 * : " +name);
}
initSession();
function responseRecieved(responseContent) {
	if(responseContent.substr(0,4).toUpperCase()=="PING")
	{
		sendMessage("PONG"+responseContent.substr(4));
		return;
	}
	//figure out sender really later on
	//var correctwindow="disp-"+senderid;
	//var correctchat="disp-"+"onlyone";
	//var textdivobj;
	addchatwindow("#newchan");
}
function formatAndSend(textobject,event) {
	if((event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!=13)
		return true;
	var message = "PRIVMSG ";
	message += textobject.id.substr(13);
	message += " :";
	message += textobject.value;
	sendMessage(message);
	setTimeout(function(){textobject.value=""},1);
	return false;
}
function addchatwindow(targetname) {
	// targetname is the name of the channel or user with which the chat window should correspond
	if(document.getElementById("chat_bigholderbox"+targetname))
		return; //Already exists
	var bigholder=document.createElement("div");
	bigholder.id="chat_bigholderbox"+targetname;
	bigholder.style.position="relative";
	bigholder.style.visibility="visible";
	bigholder.style.cssFloat="left";
	bigholder.style.width="255px";
	bigholder.style.height="100%";
	var holder=document.createElement("div");
	holder.style.position="absolute";
	holder.style.bottom="0";
	holder.style.width="250px";
	var text=document.createElement("div");
	text.id="chat_textbox"+targetname;
	var textinside=document.createTextNode("");
	text.appendChild(textinside);
	var input=document.createElement("textarea");
	input.id="chat_inputbox"+targetname;
	input.style.width="100%";
	input.setAttribute('onkeypress','formatAndSend(this,event)');
	holder.appendChild(text);
	holder.appendChild(input);
	//var bigholder=document.createElement("div");
	bigholder.appendChild(holder);
	chatarea.appendChild(bigholder);
}
