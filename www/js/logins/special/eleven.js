var interval;
var clockContainer, clock;
var eleven = new Date(2011, 10, 11, 11, 11, 11, 111);
// year, month, date, hour, minute, second, millis
// months start at 0, so 10 -> 11

window.addEventListener("load", function() {
	document.getElementsByClassName("middle")[0].style.marginTop = "-60px";
	document.getElementsByClassName("middle")[0].style.paddingTop = "0px";
	
	var confettiScript = document.createElement("script");
	confettiScript.type = "text/javascript";
	confettiScript.src = "/~2012zyaro/i2/www/js/logins/special/confettisnow.js";
	document.getElementsByTagName("head")[0].appendChild(confettiScript);
	
	clockContainer = document.createElement("div");
	clockContainer.style.position = "fixed";
	clockContainer.style.top = "120px";
	clockContainer.style.left = "0px";
	clockContainer.style.right = "0px";
	clockContainer.style.fontFamily = '"Droid Sans", Roboto, Arial, Helvetica, sans-serif';
	clockContainer.style.textAlign = "center";

	clock = document.createElement("div");
	clock.style.fontSize = "320px";
	clock.style.color = "#002000";
	clock.style.textAlign = "center";
	clock.style.verticalAlign = "middle";
	clock.style.marginBottom = "-36px";
	clock.style.WebkitTransitionDuration = "0.5s";

	msg = document.createElement("span");
	msg.style.fontSize = "32px";
	msg.style.color = "#001000";
	msg.innerHTML = "until 11-11-11 11:11:11.111";
	
	clockContainer.appendChild(clock);
	clockContainer.appendChild(msg);
	document.body.appendChild(clockContainer);

	incrementCountdown();
	interval = setInterval(incrementCountdown, 100);
	
	var now = new Date();
	console.log("now = " + now.getTime() + " | eleven = " + eleven.getTime() + " | now - eleven = " + (now.getTime() - eleven.getTime()));
	if (eleven.getTime() < now.getTime() && ((now.getTime() - eleven.getTime()) < 3600000)) {
		// show confetti if 11-11-11 11:11:11.111 was less than an hour ago
		// the code for this is in I2_ROOT/www/js/logins/special/confettisnow.js
		setTimeout("initsnow();", 1000);
	}
}, false);

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


	if (diff / 1000.0 <= 11 && diff / 1000.0 >= 0) {
		clock.style.fontWeight = "bold";
		var scaleFactor = ((11 - (diff / 1000.0)) / 11.0);
		clock.style.fontSize = (320 + Math.round(100 * scaleFactor)) + "px";
		clock.style.color = "rgb(0," + Math.round(255 * scaleFactor) + ",0)";
		clock.innerHTML = "<span style=\"font-size:" + (320 - Math.round(200 * scaleFactor)) + "px\">" + hr + ":" + min + ":</span>" + sec;
		if (Math.round(diff / 1000.0) == 0 && !snowing) {
			initsnow();
		}
	} else {
		clock.innerHTML = hr + ":" + min + ":" + sec;
		msg.innerHTML = "until 11-11-11 11:11:11.111";
		if (diff < 0) {
			clock.style.fontWeight = "normal";
			clock.style.color = "black";
			clock.style.fontSize = "280px";
			msg.innerHTML = "since 11-11-11 11:11:11.111<br/><br/>Come back next century!";
		}
	}
	if (!!window.snowing && snowing) {
		movesnow();
	}
}
