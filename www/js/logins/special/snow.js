//TJHSST Intranet login page snow script
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
var snowcolor=new Array("#aaaacc","#ddddFF","#ccccDD");
// Number of snowflake characters in following array
var numsnowletters=3;
//Fonts possible for flakes
var snowtype=new Array("Arial Black","Arial Narrow","Times","Comic Sans MS");
//Character to be used for flakes
if(!ie && !android){ // IE doesnt' like it for some reason, and android doesn't either.
	var snowletter=new Array("❄","❅","❆");
}else{
	var snowletter="*";
}
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

//Should the snow pile up?
var pile=false;
//Should we use fast piling?
var fastpile=true;
// use real piling in faster browsers
if (fastbrowser) {
	pile = true;
	fastpile = false;
}
//IE cannot do canvas
fastpile=fastpile&&(!ie);
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
var santalink=i2root+"www/pics/santa_xsnow.gif";
var santawidth=210;
var santaheight=83;
var santaspeed=5;
var santax=-santawidth;
var santa;

// Tron Menorah
var today = new Date(); // what day is it?
var chanukahDay = 0; // which day of Chanukah?
var menorah;
var candles;
if (today.getMonth() == 11 && today.getDate() <= 9 && today.getHours() < 18) {
	chanukahDay = (today.getDate() - 1); // how convenient!
	if (today.getHours() > 18) { // because each "day" starts at sunset
		chanukahDay ++;
	}
	candles = new Array(chanukahDay);
}


function set_flakes() {
	var regex = new RegExp("[\\?&]flake=([^&#]*)");
	var results = regex.exec( window.location.href );
	if( results != null )
		snowletter=url_decode(results[1]);
}

function url_decode(utftext) {
	//
	// Credit for the base for this function
	// goes to the people at webtoolkit
	// http://www.webtoolkit.info/
	// Pretty cool code, imho.
	//
	utftext=unescape(utftext);
	var string = "";
	var i = 0;
	var c = c1 = c2 = 0;
	
	while ( i < utftext.length ) {
		c = utftext.charCodeAt(i);
		if (c < 128) {
			string += String.fromCharCode(c);
			i++;
		} else if((c > 191) && (c < 224)) {
			c2 = utftext.charCodeAt(i+1);
			string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
			i += 2;
		} else {
			c2 = utftext.charCodeAt(i+1);
			c3 = utftext.charCodeAt(i+2);
			string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
			i += 3;
		}
	}
	string = string.replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
	return string;
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
	if (menorah) {
		var loginTable = document.getElementsByTagName("table")[0];
		menorah.style.left = (loginTable.offsetLeft + 37) + "px";
		menorah.style.top = (loginTable.offsetTop - 253) + "px";
		for (var i = 0; i < candles.length; i++) {
			candles[i].style.top = (menorah.offsetTop + 15) + "px";
			if (i < 4) {
				candles[i].style.left = ((menorah.offsetLeft + 283) - (26 * (i))) + "px";
			} else {
				candles[i].style.left = ((menorah.offsetLeft + 104) - (25 * (i - 4))) + "px";
			}
		}
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
	container.style.zIndex="-1";
	document.body.appendChild(container);
	if(santaexists) {
		santa=document.createElement("img");
		santa.src=santalink;
		santa.style.position="absolute";
		santa.style.top=Math.floor(Math.random()*screenheight-santaheight)+"px";
		santa.style.zIndex="-1";
		container.appendChild(santa);
	}
	if (chanukahDay != 0) { // no Chanukah = no candles
		menorah = document.createElement("img");
		menorah.src = i2root+"www/pics/menorah.png";
		menorah.title = "Happy Chanukah!"
		menorah.style.position = "absolute";
		var loginTable = document.getElementsByTagName("table")[0]; // get the login box
		loginTable.style.position = "relative"; // ------------------- and shift
		loginTable.style.top = "100px"; // --------------------------- it down
		
		menorah.style.left = (loginTable.offsetLeft + 37) + "px"; // position the menorah
		menorah.style.top = (loginTable.offsetTop - 253) + "px"; //  on top of the login box
		menorah.style.zIndex = "101"; // --------------------------- and make sure it is on top

		container.appendChild(menorah); // then add it to the container

		for (var i = 0; i < candles.length; i++) { // -------- for each candle
			candles[i] = document.createElement("img"); // create an <img>
			candles[i].src = i2root+"www/pics/flame.gif"; // ---- get the candle animation
//			candles[i].style.width = "14px";
//			candles[i].style.height = "16px";

			candles[i].style.position = "absolute";
			candles[i].style.top = (menorah.offsetTop + 15) + "px"; // set the top so it lines up with the holders
			if (i < 4) {
				candles[i].style.left = ((menorah.offsetLeft + 283) - (26 * (i))) + "px";
			} else {
				candles[i].style.left = ((menorah.offsetLeft + 104) - (25 * (i - 4))) + "px";
			}
//			candles[i].style.backgroundColor = "cyan";
			candles[i].style.zIndex = "100"; // make sure it is on top of most stuff, but under the menorah

			container.appendChild(candles[i]); // add it to the container too
		}
	}
	if(pile) {
		for (var i=0; i<heightbuckets; i++) {
			snowheight[i] = screenheight;
		}
	}
	if(fastpile) {
		if(!ie) {
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
	}
	for (var i=0; i<=snowmax; i++) {
		snowflakes[i]=document.createElement("span");
		//snowflakes[i].id="flake_"+i;
		if((typeof(snowletter)=='object')&&(snowletter instanceof Array)) {
			snowflakes[i].innerHTML=snowletter[Math.floor(Math.random()*(numsnowletters))];
		} else {
			snowflakes[i].innerHTML=snowletter;
		}
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
			santa.style.top=Math.floor(Math.random()*screenheight-santaheight)+"px";
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
			var tempsnowletter=snowflakes[i].innerHTML;
			if((snowheight[bucket+1]-snowheight[bucket] < 5 && snowheight[bucket-1]-snowheight[bucket] < 5) || snowy[i]>= screenheight) {
				snowheight[bucket]=(snowy[i]<snowheight[bucket]?snowy[i]:snowheight[bucket]);
				snowflakes[i]=document.createElement("span");
				snowflakes[i].innerHTML=tempsnowletter;
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
			santa.style.top=Math.floor(Math.random()*screenheight-santaheight)+"px";
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
var i=0;
function movesnow_fastpile() {
	if (santaexists) {
		santax+=santaspeed;
		if(santax>=screenwidth+santawidth) {
			santax=-santawidth;
			santa.style.top=Math.floor(Math.random()*screenheight-santaheight)+"px";
		}
		santa.style.left=santax+"px";
	}
	for (i=0; i<=snowmax; i++) {
		snowy[i]+=snowflakes[i].fall;
		if(snowy[i] >=screenheight) {
			snowy[i]=-snowflakes[i].size;
			//incrementsnowheight;
			setTimeout("iterfastpile()",10);
		}
		
		snowflakes[i].style.top = snowy[i]+"px";
		snowflakes[i].style.left = (snowflakes[i].x+10*Math.sin(snowy[i]/9))+"px";
	}
	setTimeout("movesnow_fastpile()",60);
}
var count=0;
function iterfastpile() {
	if(count<5) {
		count++;
		return;
	}
	count=0;
	if(!ie) {
		fastfillheight-=0.02*5;
		graphics.moveTo(0,fastfillheight);
		graphics.lineTo(realscreenwidth,fastfillheight);
		graphics.stroke();
	}
}

window.onload=initsnow;
