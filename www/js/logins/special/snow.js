//Config
var snowmax=70;
var snowcolor=new Array("#aaaacc","#ddddFF","#ccccDD");
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS");
var snowletter="*";
var sinkspeed=0.8;
var snowmaxsize=22;
var snowminsize=8;

var snowsizerange=snowmaxsize-snowminsize;
var snowflakes = new Array();
var snowy = new Array();

var screenwidth=1000;
var screenheight=1000;

var snowheight = new Array();
var heightbuckets = 200;
var heightacc = heightbuckets/screenwidth;
var newx;
var bucket;
var snowsize;

var pile=false;

window.onresize=resize;
resize();
function resize() {
	if(document.all) {
		screenwidth = document.documentElement.clientWidth -40;
		if(pile) {
			screenheight = document.documentElement.clientHeight-5;
		} else {
			screenheight = document.documentElement.clientHeight-25;
		}
	} else {
		screenwidth = window.innerWidth-40;
		if(pile) {
			screenheight = window.innerHeight-5;
		} else {
			screenheight = window.innerHeight-25;
		}
	}
	heightacc = heightbuckets/screenwidth;
}

function initsnow() {
	for (var i=0; i<heightbuckets; i++) {
		snowheight[i] = screenheight;
	}
	for (var i=0; i<=snowmax; i++) {
		snowflakes[i]=document.createElement("span");
		//snowflakes[i].id="flake_"+i;
		snowflakes[i].innerHTML=snowletter;
		snowflakes[i].style.color=snowcolor[Math.floor(Math.random()*snowcolor.length)];
		snowflakes[i].style.fontFamily=snowtype[Math.floor(Math.random()*snowtype.length)];
		snowsize=Math.floor(Math.random()*snowsizerange)+snowminsize;
		snowflakes[i].size=snowsize-5;
		snowflakes[i].style.fontSize=snowsize+"pt";
		//alert(snowflakes[i].style.fontSize);
		snowflakes[i].style.position="absolute";
		snowflakes[i].x=Math.floor(Math.random()*screenwidth);
		snowy[i]=Math.floor(Math.random()*screenheight);
		snowflakes[i].style.left=snowflakes[i].x + "px";
		snowflakes[i].style.top=snowy[i] + "px";
		snowflakes[i].fall=sinkspeed*snowsize/5;
		snowflakes[i].style.zIndex="-1";
		document.body.appendChild(snowflakes[i]);
	}
	if(pile) {
		setTimeout("movesnow_pile()",30);
	} else {
		setTimeout("movesnow_nopile()",30);
	}
}
function movesnow_pile() {
	for (var i=0; i<=snowmax; i++) {
		snowy[i]+=snowflakes[i].fall;
		
		snowflakes[i].style.top = snowy[i]+"px";
		newx=(snowflakes[i].x+10*Math.sin(snowy[i]/9));
		snowflakes[i].style.left = newx+"px";
		bucket=Math.floor((newx+(snowflakes[i].size/2))*heightacc);
		if(snowy[i] + snowflakes[i].size > snowheight[bucket]) {
			if((snowheight[bucket+1]-snowheight[bucket] < 5 && snowheight[bucket-1]-snowheight[bucket] < 5) || snowy[i]>= screenheight) {
				snowheight[bucket]=(snowy[i]<snowheight[bucket]?snowy[i]:snowheight[bucket]);
				snowflakes[i]=document.createElement("span");
				snowflakes[i].innerHTML=snowletter;
				snowflakes[i].style.color=snowcolor[Math.floor(Math.random()*snowcolor.length)];
				snowflakes[i].style.fontFamily=snowtype[Math.floor(Math.random()*snowtype.length)];
				snowsize=Math.floor(Math.random()*snowsizerange)+snowminsize;
				snowflakes[i].size=snowsize-5;
				snowflakes[i].style.fontSize=snowsize+"pt";
				snowflakes[i].style.position="absolute";
				snowflakes[i].x=Math.floor(Math.random()*screenwidth);
				snowy[i]=-snowflakes[i].size;
				snowflakes[i].style.left=snowflakes[i].x + "px";
				snowflakes[i].style.top=snowy[i] + "px";
				snowflakes[i].fall=sinkspeed*snowsize/5;
				snowflakes[i].style.zIndex="-1";
				document.body.appendChild(snowflakes[i]);
			}
		}
	}
	setTimeout("movesnow_pile()",60);
}
function movesnow_nopile() {
	for (var i=0; i<=snowmax; i++) {
		snowy[i]+=snowflakes[i].fall;
		if(snowy[i]+snowflakes[i].size >=screenheight) {
			snowy[i]=-snowflakes[i].size;
		}
		
		snowflakes[i].style.top = snowy[i]+"px";
		newx=(snowflakes[i].x+10*Math.sin(snowy[i]/9));
		snowflakes[i].style.left = newx+"px";
		bucket=Math.floor((newx+(snowflakes[i].size/2))*heightacc);
	}
	setTimeout("movesnow_nopile()",60);
}

window.onload=initsnow;
