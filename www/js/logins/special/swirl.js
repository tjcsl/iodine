var vidSwirl = {};
vidSwirl.started = false;
vidSwirl.vidDir = "https://iodine.tjhsst.edu/~2012zyaro/i2/";
vidSwirl.vidNames = ["fanten", "fryeeday", "orchestra_fail", "rickroll", "saxroll", "trololo", "tunak_tunak_tun"];
vidSwirl.vidExt = ".webm";
vidSwirl.mainVid;
vidSwirl.otherVids;
vidSwirl.loadedVids = 0;
vidSwirl.t = 0;
vidSwirl.container;

function initVidSwirl() {
	if (vidSwirl.started) {
		return;
	} else {
		vidSwirl.started = true;
	}
	try {
		if (!document.createElement("video")) {
			//alert("Your browser does not support this feature.");
			window.open("http://1227.com");
			return;
		}
	} catch(e) {
		//alert("Your browser does not support this feature.");
		window.open("http://1227.com");
		return;
	}
	
	if ((navigator.userAgent.indexOf("Safari") != -1 && navigator.userAgent.indexOf("Chrom") == -1) || (navigator.userAgent.indexOf("MSIE") != -1)) {
		vidSwirl.vidExt = ".mp4";
	} else if (navigator.userAgent.indexOf("Firefox") != -1) {
		vidSwirl.vidExt = ".ogv";
	}
	
//	vidSwirl.container = document.createElement("div");
	vidSwirl.container = document.body;
	if (window.innerHeight > (window.innerWidth * 3.0 / 4.0)) {
		vidSwirl.x = 0;
		vidSwirl.y = Math.round((window.innerHeight - (window.innerWidth * 3.0 / 4.0)) / 2.0);
		vidSwirl.width = window.innerWidth;
		vidSwirl.height = Math.round(window.innerWidth * 3.0 / 4.0);
	} else {
		vidSwirl.x = Math.round((window.innerWidth - (window.innerHeight * 4.0 / 3.0)) / 2.0);
		vidSwirl.y = 0;
		vidSwirl.width = Math.round(window.innerHeight * 4.0 / 3.0);
		vidSwirl.height = window.innerHeight;
	}
	vidSwirl.centerX = (window.innerWidth / 2.0);
	vidSwirl.centerY = (window.innerHeight / 2.0);
	vidSwirl.xRad = (vidSwirl.width / 2.0);
	vidSwirl.yRad = (vidSwirl.height / 2.0);
/*	vidSwirl.container.style.left = vidSwirl.x + "px";
	vidSwirl.container.style.top = vidSwirl.y + "px";
	vidSwirl.container.style.width = vidSwirl.width + "px";
	vidSwirl.container.style.height = vidSwirl.height + "px";*/
	
	vidSwirl.vidNames.sort(function() {return (Math.random() - 0.5);});
	
	vidSwirl.mainVid = document.createElement("video");
	vidSwirl.mainVid.oncanplay = vidLoaded;
	vidSwirl.mainVid.autoplay = false;
	vidSwirl.mainVid.preload = "auto";
	vidSwirl.mainVid.src = vidSwirl.vidDir + vidSwirl.vidNames[0] + vidSwirl.vidExt;
	vidSwirl.mainVid.loop = true;
	vidSwirl.mainVid.style.position = "fixed";
	vidSwirl.mainVid.style.left = Math.round(vidSwirl.x + (vidSwirl.width * 0.1)) + "px";
	vidSwirl.mainVid.style.top = Math.round(vidSwirl.y + (vidSwirl.height * 0.1)) + "px";
	vidSwirl.mainVid.style.width = Math.round(vidSwirl.width - (vidSwirl.width * 0.2)) + "px";
	vidSwirl.mainVid.style.height = Math.round(vidSwirl.height - (vidSwirl.height * 0.2)) + "px";
	vidSwirl.mainVid.style.WebkitUserSelect = "none";
	vidSwirl.mainVid.style.MozUserSelect = "none";
	vidSwirl.mainVid.style.userSelect = "none";
	vidSwirl.mainVid.style.zIndex = 999;
	vidSwirl.mainVid.load();
	
	vidSwirl.otherVids = new Array(vidSwirl.vidNames.length - 1);
	for (var i = 1; i < vidSwirl.vidNames.length; i++) {
		vidSwirl.otherVids[i - 1] = document.createElement("video");
		vidSwirl.otherVids[i - 1].onloadstart = vidLoaded();
		vidSwirl.otherVids[i - 1].preload = "auto";
		vidSwirl.otherVids[i - 1].autoplay = false;
		vidSwirl.otherVids[i - 1].src = vidSwirl.vidDir + vidSwirl.vidNames[i] + vidSwirl.vidExt;
		vidSwirl.otherVids[i - 1].loop = true;
		vidSwirl.otherVids[i - 1].style.width = "240px";
		vidSwirl.otherVids[i - 1].style.height = "180px";
		vidSwirl.otherVids[i - 1].style.position = "fixed";
		vidSwirl.otherVids[i - 1].style.WebkitUserSelect = "none";
		vidSwirl.otherVids[i - 1].style.MozUserSelect = "none";
		vidSwirl.otherVids[i - 1].style.userSelect = "none";
		vidSwirl.otherVids[i - 1].style.zIndex = 1000;
		vidSwirl.otherVids[i - 1].onmousedown = function(e) {
			var mainSrc = vidSwirl.mainVid.src;
			vidSwirl.mainVid.src = e.target.src;
			e.target.src = mainSrc;
			vidSwirl.mainVid.play();
			e.target.play();
		}
		vidSwirl.otherVids[i - 1].load();
	}
//	startVids();
//	document.body.appendChild(vidSwirl.container);
}

function vidLoaded(e) {
	if (++vidSwirl.loadedVids >= (vidSwirl.otherVids.length)) {
		startVids();
	}
}

function startVids() {
	vidSwirl.mainVid.muted = false;
	vidSwirl.mainVid.volume = 1;
	vidSwirl.container.appendChild(vidSwirl.mainVid);
	vidSwirl.mainVid.play();
	for (var i = 0; i < vidSwirl.otherVids.length; i++) {
		vidSwirl.otherVids[i].muted = false;
		vidSwirl.otherVids[i].volume = 0.1;
		vidSwirl.otherVids[i].play();
		vidSwirl.container.appendChild(vidSwirl.otherVids[i]);
	}
	
	vidSwirl.trigConst = (2.0 * Math.PI / vidSwirl.otherVids.length);
	
	vidSwirl.interval = setInterval(swirlVids, 60);
}

/*    _________
 *   /         \  
 *  /           \ 
 * |      +      |
 *  \           / 
 *   \_________/  
 */
function swirlVids() {
//	console.log("Interval at t = " + vidSwirl.t);
	for (var i = 0; i < vidSwirl.otherVids.length; i++) {
		vidSwirl.otherVids[i].style.left = Math.round(((vidSwirl.xRad) * Math.cos(vidSwirl.t + (vidSwirl.trigConst * i))) + (vidSwirl.centerX - 120)) + "px";
		vidSwirl.otherVids[i].style.top = Math.round(((vidSwirl.yRad) * Math.sin(vidSwirl.t + (vidSwirl.trigConst * i))) + (vidSwirl.centerY - 90)) + "px";
	}
	vidSwirl.t += 0.1;
	if (vidSwirl.t > (2 * Math.PI)) {
		vidSwirl.t = 0;
	}
}
