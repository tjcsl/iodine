// TJHSST Intranet login page clover snow script
// modded by Zachary "Gamer_Z." Yaro
if(navigator.appName == 'Microsoft Internet Explorer') {
	var ie=true;
} else {
	var ie=false;
}
var mobile=false;
if(navigator.userAgent.toLowerCase().indexOf("android") != -1) {
	var android=true;
	var mobile=true;
} else {
	var android=false;
}
if(navigator.userAgent.toLowerCase().indexOf("chrome") != -1) {
	var chrome=true;
} else {
	var chrome=false;
}
if(chrome || navigator.userAgent.toLowerCase().indexOf("firefox/4") != -1) {
	var fastbrowser=true;
} else {
	var fastbrowser=false;
}
//Config
//Number of flakes
if(mobile) {
	var snowmax=20;
} else if (fastbrowser) {
	var snowmax=60;
} else {
	var snowmax=50;
}
//Colors possible for flakes
var snowcolor=["#00BB00","#00FF00","#88FF88","#BBFFBB"];
//Fonts possible for flakes
var snowtype=["Arial"];
//Character to be used for flakes
var snowletter=["&#9827","&#9752"];
//Speed multiplyer for the snow falling
if(fastbrowser) { // They have more elements and do piling. This increases the amount of time it takes for significant slowdown.
	var sinkspeed=0.5;
} else {
	var sinkspeed=1
}
if(mobile) {
	//Maximum size of snowflakes
	var snowmaxsize=44;
	//Miniumum size of snowflakes
	var snowminsize=16;
} else {
	//Maximum size of snowflakes
	var snowmaxsize=22;
	//Miniumum size of snowflakes
	var snowminsize=8;
}


//Range of snow flake sizes
var snowsizerange=snowmaxsize-snowminsize;
//Array for snowflakes
var snowflakes = [];
//Array of snow flake y coordinates
var snowy = [];

//Screen width (set to default)
var screenwidth=1000;
//Screen height (set to default)
var screenheight=1000;
//Real Screen width
var realscreenwidth;
//Real Screen height
var realscreenheight;

//Temporary variables
var newx;
var snowsize;
//Div holding everything, removes scroll bars.
var container;

var today = new Date(); // what day is it?


window.onresize=resize;
resize();
function resize() {
	if(document.all) {
		realscreenwidth = document.documentElement.clientWidth;
		screenwidth = realscreenwidth-40;
		realscreenheight = document.documentElement.clientHeight;
		screenheight = realscreenheight-37;
	} else {
		realscreenwidth = window.innerWidth;
		screenwidth = realscreenwidth-40;
		realscreenheight=window.innerHeight;
		screenheight = realscreenheight-37;
	}
}

function initsnow() {
	container=document.createElement("div");
	container.style.position="absolute";
	container.style.top="0px";
	container.style.left="0px";
	container.style.width="100%";
	container.style.height="100%";
	container.style.overflow="hidden";
	container.style.zIndex="-1";
	document.body.appendChild(container);
	
	for (var i=0; i<=snowmax; i++) {
		snowflakes[i]=document.createElement("span");
		//snowflakes[i].id="flake_"+i;
		if((typeof(snowletter)=='object')&&(snowletter instanceof Array)) {
			snowflakes[i].innerHTML=snowletter[Math.floor(Math.random()*(snowletter.length))];
		} else {
			snowflakes[i].innerHTML=snowletter;
		}
		snowflakes[i].style.color=snowcolor[Math.floor(Math.random()*snowcolor.length)];
		//snowflakes[i].style.fontFamily=snowtype[Math.floor(Math.random()*snowtype.length)];
		snowflakes[i].style.fontFamily="Arial";
		snowsize=Math.floor(Math.random()*snowsizerange)+snowminsize;
		snowflakes[i].size=snowsize;
		snowflakes[i].style.fontSize=snowsize+"pt";
		//alert(snowflakes[i].style.fontSize);
		snowflakes[i].style.position="absolute";
		snowflakes[i].x=Math.floor(Math.random()*screenwidth);
		snowy[i]=Math.floor(Math.random()*screenheight);
		snowflakes[i].style.left=snowflakes[i].x + "px";
		snowflakes[i].style.top=snowy[i] + "px";
		snowflakes[i].fall=sinkspeed*snowsize/5;
		snowflakes[i].style.zIndex="-2";
		container.appendChild(snowflakes[i]);
	}
	setTimeout("movesnow()",30);
	
}
function movesnow() {
	for (var i=0; i<=snowmax; i++) {
		snowy[i]+=snowflakes[i].fall;
		if(snowy[i] >=screenheight) {
			snowy[i]=-snowflakes[i].size;
		}
		
		snowflakes[i].style.top = snowy[i]+"px";
		snowflakes[i].style.left = (snowflakes[i].x+10*Math.sin(snowy[i]/9))+"px";
	}
	setTimeout("movesnow()",60);
}
window.onload=initsnow;
