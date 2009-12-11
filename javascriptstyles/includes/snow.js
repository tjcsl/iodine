//Config
var snowmax=65;
var snowcolor=new Array("#aaaacc","#ddddFF","#ccccDD");
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS");
var snowletter="*";
var sinkspeed=0.8;
var snowmaxsize=22;
var snowminsize=8;

var snowsizerange=snowmaxsize-snowminsize;
var snowflakes = new Array();

var screenwidth=1000;
var screenheight=1000;

window.onresize=resize;
resize();
function resize() {
	if(document.all) {
		screenwidth = document.documentElement.clientWidth -25;
		screenheight = document.documentElement.clientHeight -25;
	} else {
		screenwidth = window.innerWidth-25;
		screenheight = window.innerHeight-25;
	}
}

function initsnow() {
	for (var i=0; i<=snowmax; i++) {
		snowflakes[i]=document.createElement("span");
		//snowflakes[i].id="flake_"+i;
		snowflakes[i].innerHTML=snowletter;
		snowflakes[i].style.color=snowcolor[Math.floor(Math.random()*snowcolor.length)];
		snowflakes[i].style.fontFamily=snowtype[Math.floor(Math.random()*snowtype.length)];
		snowflakes[i].size=Math.floor(Math.random()*snowsizerange)+snowminsize;
		snowflakes[i].style.fontSize=snowflakes[i].size+"pt";
		//alert(snowflakes[i].style.fontSize);
		snowflakes[i].style.position="absolute";
		snowflakes[i].x=Math.floor(Math.random()*screenwidth);
		snowflakes[i].y=Math.floor(Math.random()*screenheight);
		snowflakes[i].style.left=snowflakes[i].x + "px";
		snowflakes[i].style.top=snowflakes[i].y + "px";
		snowflakes[i].fall=sinkspeed*snowflakes[i].size/5;
		snowflakes[i].style.zIndex="-1";
		document.body.appendChild(snowflakes[i]);
	}
	setTimeout("movesnow()",30);
}
function movesnow() {
	for (var i=0; i<=snowmax; i++) {
		snowflakes[i].y+=snowflakes[i].fall;
		if(snowflakes[i].y+snowflakes[i].size >=screenheight) {
			snowflakes[i].y=0;
		}
		
		snowflakes[i].style.top = snowflakes[i].y+"px";
		snowflakes[i].style.left = (snowflakes[i].x+10*Math.sin(snowflakes[i].y/9))+"px";
	}
	setTimeout("movesnow()",60);
}
// In your onload method, add initsnow()
