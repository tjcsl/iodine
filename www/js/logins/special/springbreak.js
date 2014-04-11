fridayinit = function() {
	if(document.body.clientWidth > 700) {
		document.body.innerHTML+='<iframe style="'+
		'position:absolute;top:50%;right:0;margin-top:-157px;z-index:0'+
		'" width="560" height="315" src="'+
		'https://www.youtube.com/embed/kfVsfOSbJY0?autoplay=0&controls=1&modestbranding=1&showinfo=0&rel=0'+
		'" frameborder="0" allowfullscreen allowtransparency seamless></iframe>'+
		'<style>'+
		'@media(max-width: 700px) {'+
		'iframe { display: none }'+
		'}'+
		'</style>';
	}
}
var interval;
var clockContainer, clock;
var tz = " GMT-0500 (EST)";

// parse the hash
//d={};for(i in s=document.getElementsByTagName('script'))if((t=s[i].src)&&t.indexOf(u='/www/js/logins/special/countdown.js')!==-1)e=t.split(u+'#')[1];for(i in f=e.split('&'))d[(g=f[i].split('='))[0]]=g[1];
d = {'time': 'Fri Apr 11, 2014 10:47:00', 'text': 'Spring Break'};
var eleven = new Date(d.time+tz);
var etxt = d.text;
// year, month, date, hour, minute, second, millis
// months start at 0, so 10 -> 11

window.addEventListener("load", function() {
//	document.getElementsByClassName("middle")[0].style.marginTop = "-60px";
//	document.getElementsByClassName("middle")[0].style.paddingTop = "0px";
	
	var confettiScript = document.createElement("script");
	confettiScript.type = "text/javascript";
	confettiScript.src = "/www/js/logins/special/confettisnow.js";
	confettiScript.onload = function() {
		snowmax = 20;
	        //initsnow();
	};
	document.getElementsByTagName("head")[0].appendChild(confettiScript);

	document.body.style.background = "none";	
	clockContainer = document.createElement("div");
	clockContainer.style.position = "fixed";
	clockContainer.style.top = "50%";
	//clockContainer.style.left = "600px";
	clockContainer.style.right = "50px";
	clockContainer.style.height = "400px";
	clockContainer.style.marginTop = "-100px";
	clockContainer.style.fontFamily = '"Droid Sans", Roboto, Arial, Helvetica, sans-serif';
	clockContainer.style.textAlign = "center";
	clockContainer.style.zIndex = "-1";
	clock = document.createElement("div");
	clock.style.fontSize = "100px";
	clock.style.color = "#33b5e5"; //#002000";
	clock.style.textAlign = "center";
	clock.style.verticalAlign = "middle";
	clock.style.marginBottom = "-36px";
	clock.style.WebkitTransitionDuration = "0.5s";
	clock.style.MozTransitionDuration = "0.5s";
	clock.style.MsTransitionDuration = "0.5s";
	clock.style.OTransitionDuration = "0.5s";
	clock.style.transitionDuration = "0.5s";

	msg = document.createElement("span");
	msg.style.fontSize = "32px";
	msg.style.color = "#33b5e5";
	msg.innerHTML = "until " + etxt;
	clockContainer.appendChild(clock);
	clockContainer.appendChild(msg);
	document.body.appendChild(clockContainer);

	incrementCountdown();
	interval = setInterval(incrementCountdown, 100);
	
	var now = new Date();
	//console.log("now = " + now.getTime() + " | eleven = " + eleven.getTime() + " | now - eleven = " + (now.getTime() - eleven.getTime()));
	if (eleven.getTime() < now.getTime() && ((now.getTime() - eleven.getTime()) < 3600000)) {
		// show confetti if 11-11-11 11:11:11.111 was less than an hour ago
		// the code for this is in I2_ROOT/www/js/logins/special/confettisnow.js
		//setTimeout("snowmax=100;initsnow();", 1000);
		setTimeout(fridayinit, 500);
	}
}, false);
var fridaying = false;
function incrementCountdown() {
	var now = new Date();
	var hr, min, sec, diff;
	//console.log("now = " + now.getTime() + " | 11 = " + eleven.getTime()); // for debugging
	diff = eleven.getTime() - now.getTime();
	hr = Math.floor(Math.abs(diff / 1000.0 / 60.0 / 60.0));
	if (hr < 10) {
		hr = "0" + hr;
	}
	min = Math.floor(Math.abs(diff / 1000.0 / 60.0) % 60.0);
	if (min < 10) {
		min = "0" + min;
	}
	sec = Math.floor(Math.abs(diff / 1000.0) % 60.0);
	if (sec < 10) {
		sec = "0" + sec;
	}


	if (diff / 1000.0 <= 11 && diff / 1000.0 >= 2) {
		clock.style.fontWeight = "bold";
		var scaleFactor = ((11 - (diff / 1000.0)) / 11.0);
		clock.style.fontSize = (250 + Math.round(100 * scaleFactor)) + "px";
		clock.style.color = "rgb(0," + Math.round(255 * scaleFactor) + ",0)";
		clock.innerHTML = "<span style=\"font-size:" + (320 - Math.round(200 * scaleFactor)) + "px\">" + hr + ":" + min + ":</span>" + sec;
		if (Math.round(diff / 1000.0) == 0 && !snowing) {
			initsnow();
		}
	} else {
		if(!fridaying) {
		fridaying = true;
		setTimeout(fridayinit, 500);
		}
		clock.innerHTML = hr + ":" + min + ":" + sec;
		msg.innerHTML = "until " + etxt; //11-12-13 11:12:13";
			clock.style.fontWeight = "normal";
			clock.style.color = "black";
			clock.style.fontSize = "120px";
			clock.style.marginTop='50%';
		if(diff < 0) {
			msg.innerHTML = "since " + etxt + "<br/><br/>";
		}
	}
	if (!!window.snowing && snowing) {
		movesnow();
	}
}
