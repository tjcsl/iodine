var imageel;
var imageobj;
var imagediv;
function resizeie() {
	winW=640;
	winH=480;
	if (parseInt(navigator.appVersion)>3) {
		if (navigator.appName=="Netscape") {
			winW = window.innerWidth;
			winH = window.innerHeight;
		}
		if (navigator.appName.indexOf("Microsoft")!=-1) {
			winW = document.documentElement.clientWidth;
			winH = document.documentElement.clientHeight;
		}
	}
	var width;
	if(winW/imageobj.width<winH/imageobj.height) {
		imageel.style.width=parseInt(winW)+"px";
		imageel.style.height=parseInt(imageobj.height*(winW/imageobj.width))+"px";
		width=winW
	} else {
		imageel.style.width=parseInt(imageobj.width*(winH/imageobj.height))+"px";
		imageel.style.height=parseInt(winH)+"px";
		width=parseInt(imageobj.width*(winH/imageobj.height));
	}
	imagediv.style.left=((winW-width)/2)+"px";
}
function setbg() {
	var image=document.body.background;
	document.body.background="";
	if (navigator.appName == 'Microsoft Internet Explorer') {
		// Guess who doesn't support CSS3 correctly?
		winW=640;
		winH=480;
		if (parseInt(navigator.appVersion)>3) {
			if (navigator.appName=="Netscape") {
				winW = window.innerWidth;
				winH = window.innerHeight;
			}
			if (navigator.appName.indexOf("Microsoft")!=-1) {
				winW = document.documentElement.clientWidth;
				winH = document.documentElement.clientHeight;
			}
		}
		imageel=document.createElement("img");
		imagediv= document.createElement('div');
		imageel.src=image;
		imageobj = new Image();
		imageobj.src=image;
		var width;
		if(winW/imageobj.width<winH/imageobj.height) {
			imageel.style.width=parseInt(winW)+"px";
			imageel.style.height=parseInt(imageobj.height*(winW/imageobj.width))+"px";
			width=winW
		} else {
			imageel.style.width=parseInt(imageobj.width*(winH/imageobj.height))+"px";
			imageel.style.height=parseInt(winH)+"px";
			width=imageobj.width*(winH/imageobj.height);
		}
		imagediv.style.zIndex="-100";
		imagediv.style.position="absolute";
		imagediv.style.top="0px";
		imagediv.style.left=((winW-width)/2)+"px";
		imagediv.appendChild(imageel);
		document.body.appendChild(imagediv);
		window.onresize=resizeie;
	} else {
		document.body.style.background="url("+image+") fixed center no-repeat";
		document.body.style.backgroundSize='contain';
	}
}
window.onload=setbg;
