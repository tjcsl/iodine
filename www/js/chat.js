//Ideal situation:
// This is where chat for intranet will be implemented. It's basically a javascript IRC client
// Which connects to a specific server, allowing it to communicate in a way that's already pretty
// well-defined. Also, it saves us the effort of designing a better protocol, not to mention
// getting iodine up to the speed necessary to run an AJAX chat in real time.
//Current situation:
// It's an AJAX chat client within intranet.

var container = document.getElementById("chat_container");
container.innerHTML="Loading.";

// Because we'll use the irc protocol for communication, but don't have a specified tunnel system,
// we define several methods of tunneling the connections here, and decide which one to use based
// on the target browser and configuration.
/* AJAX transmission */

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

/* End AJAX */

/* Begin IRC functions */

//var senderindicatorregex = new RegExp("something that matches the strings:  :user!username@host.webaddress");

function initSession() {
	container.innerHTML="Welcome "+name;
	sendMessage("NICK " + username);
	sendMessage("USER " + username + "4 * : " +name);
}
initSession();
function responseRecieved(responseContent) {
	if(responseContent.substr(0,4).toUpperCase()=="PING")
	{
		sendMessage("PONG"+responseContent.substr(4));
		return;
	}
	
}
