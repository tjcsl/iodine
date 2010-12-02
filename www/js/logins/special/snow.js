//TJHSST Intranet login page snow script
//Config
//Number of flakes
var snowmax=50;
//Colors possible for flakes
var snowcolor=new Array("#aaaacc","#ddddFF","#ccccDD");
//Fonts possible for flakes
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS");
//Character to be used for flakes
var snowletter="*";
//Speed multiplyer for the snow falling
var sinkspeed=1;
//Maximum size of snowflakes
var snowmaxsize=22;
//Miniumum size of snowflakes
var snowminsize=8;

//Should the snow pile up?
var pile=false;
//Should we use fast piling?
var fastpile=true;
//Resolution of snow depth data
var heightbuckets = 200;
//End Config


//Range of snow flake sizes
var snowsizerange=snowmaxsize-snowminsize;
//Array for snowflakes
var snowflakes = new Array();
//Array of snow flake y coordinates
var snowy = new Array();

//Screen width (set to default)
var screenwidth=1000;
//Screen height (set to default)
var screenheight=1000;
//Real Screen width
var realscreenwidth;
//Real Screen height
var realscreenheight;

//The array of the depths of the snow
var snowheight = new Array();
//The height of the fast fill snow
var fastfillheight;
//Graphics control
var graphics;
//The multiplyer to find the correct bucket
var heightacc = heightbuckets/screenwidth;
//Temporary variables
var newx;
var bucket;
var snowsize;
//Div holding everything, removes scroll bars.
var container;

//Non-denominational Red Deer-Pulled Guy
var santaexists=true; //It's true! I've met him. He's a pretty cool guy.
var santalink="www/pics/santa_xsnow.png";
var santawidth=210;
var santaheight=83;
var santaspeed=5;
var santax=-santawidth;
var santa;

function set_flakes() {
	var regex = new RegExp("[\\?&]flake=([^&#]*)");
	var results = regex.exec( window.location.href );
	if( results != null )
		snowletter=results[1];
}

window.onresize=resize;
resize();
function resize() {
	if(document.all) {
		realscreenwidth = document.documentElement.clientWidth;
		screenwidth = realscreenwidth-40;
		realscreenheight = document.documentElement.clientHeight;
		if(pile) {
			screenheight = realscreenheight-5;
		} else {
			screenheight = realscreenheight-37;
		}
	} else {
		realscreenwidth = window.innerWidth;
		screenwidth = realscreenwidth-40;
		realscreenheight=window.innerHeight;
		if(pile) {
			screenheight = realscreenheight-5;
		} else {
			screenheight = realscreenheight-37;
		}
	}
	if(pile) {
		heightacc = heightbuckets/screenwidth;
	}
	if(fastpile) {
		fastfillheight=150;
	}
}

function initsnow() {
	set_flakes();
	container=document.createElement("div");
	container.style.position="absolute";
	container.style.top="0px";
	container.style.left="0px";
	container.style.width="100%";
	container.style.height="100%";
	container.style.overflow="hidden";
	document.body.appendChild(container);
	if(santaexists) {
		santa=document.createElement("img");
		santa.src=santalink;
		santa.style.position="absolute";
		santa.style.top=Math.floor(Math.random()*screenheight)+"px";
		santa.style.zIndex="-1";
		container.appendChild(santa);
	}
	if(pile) {
		for (var i=0; i<heightbuckets; i++) {
			snowheight[i] = screenheight;
		}
	}
	if(fastpile) {
		var background=document.createElement("canvas");
		background.style.position="absolute";
		background.style.left="0px";
		background.style.top="0px";
		background.style.width=realscreenwidth+"px";
		background.style.height=realscreenheight +"px";
		background.style.zIndex="-1";
		graphics=background.getContext("2d");
		document.body.appendChild(background);
		graphics.lineWidth=2;
		graphics.strokeStyle="#FFFFFF";
	}
	for (var i=0; i<=snowmax; i++) {
		snowflakes[i]=document.createElement("span");
		//snowflakes[i].id="flake_"+i;
		snowflakes[i].innerHTML=snowletter;
		snowflakes[i].style.color=snowcolor[Math.floor(Math.random()*snowcolor.length)];
		snowflakes[i].style.fontFamily=snowtype[Math.floor(Math.random()*snowtype.length)];
		snowsize=Math.floor(Math.random()*snowsizerange)+snowminsize;
		if(pile) {
			snowflakes[i].size=snowsize-5;
		} else {
			snowflakes[i].size=snowsize;
		}
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
	if(pile) {
		setTimeout("movesnow_pile()",30);
	} else if (fastpile) {
		setTimeout("movesnow_fastpile()",30);
	} else {
		setTimeout("movesnow_nopile()",30);
	}
}
function movesnow_pile() {
	if (santaexists) {
		santax+=santaspeed;
		if(santax>=screenwidth+santawidth) {
			santax=-santawidth;
			santa.style.top=Math.floor(Math.random()*screenheight)+"px";
		}
		santa.style.left=santax+"px";
	}
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
				container.appendChild(snowflakes[i]);
			}
		}
	}
	setTimeout("movesnow_pile()",60);
}
function movesnow_nopile() {
	if (santaexists) {
		santax+=santaspeed;
		if(santax>=screenwidth+santawidth) {
			santax=-santawidth;
			santa.style.top=Math.floor(Math.random()*screenheight)+"px";
		}
		santa.style.left=santax+"px";
	}
	for (var i=0; i<=snowmax; i++) {
		snowy[i]+=snowflakes[i].fall;
		if(snowy[i] >=screenheight) {
			snowy[i]=-snowflakes[i].size;
		}
		
		snowflakes[i].style.top = snowy[i]+"px";
		snowflakes[i].style.left = (snowflakes[i].x+10*Math.sin(snowy[i]/9))+"px";
	}
	setTimeout("movesnow_nopile()",60);
}
function movesnow_fastpile() {
	if (santaexists) {
		santax+=santaspeed;
		if(santax>=screenwidth+santawidth) {
			santax=-santawidth;
			santa.style.top=Math.floor(Math.random()*screenheight)+"px";
		}
		santa.style.left=santax+"px";
	}
	for (var i=0; i<=snowmax; i++) {
		snowy[i]+=snowflakes[i].fall;
		if(snowy[i] >=screenheight) {
			snowy[i]=-snowflakes[i].size;
			//incrementsnowheight;
			fastfillheight-=0.02;
			graphics.moveTo(0,fastfillheight);
			graphics.lineTo(realscreenwidth+25,fastfillheight);
			graphics.stroke();
		}
		
		snowflakes[i].style.top = snowy[i]+"px";
		snowflakes[i].style.left = (snowflakes[i].x+10*Math.sin(snowy[i]/9))+"px";
	}
	setTimeout("movesnow_fastpile()",60);
}

window.onload=initsnow;
