<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="Description" content="The TJ Intranet allows students at the Thomas Jefferson High School for Science and Technology to sign up for activities, access files, and perform other tasks." />
    <meta name="keywords" content="TJHSST, TJ Intranet, Intranet, Intranet2, Thomas Jefferson High School" />
    <meta name="robots" content="index, follow" />
    <meta name="author" content="The Intranet Development Team" />
    <link rel="image_src" href="[<$I2_ROOT>]www/pics/styles/i3/logo-light.png" />
    <link rel="author" href="http://www.tjhsst.edu/admin/livedoc/index.php/Iodine#Intranet_Credits" />
    <link rel="canonical" href="[<$I2_ROOT>]" />
    <link rel="icon" type="image/gif" href="[<$I2_ROOT>]www/gc/iewin.gif" />
    <link rel="shortcut icon" type="image/gif" href="[<$I2_ROOT>]www/gc/iewin.gif" />
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=1" />
    <style type="text/css">
    .debug { display: none; }
    * {
        color: white
    }
    input {
        color: red;
        font-size: 16px;
    }
    .gif_td {
        width: 300px;
    }
    .sched_td {
        width: 300px;
    }
    .header_th {
        height: 98px;
    }
    table input {
    	z-index: 999;
    }
    .smt {
        background-color: purple;
        font-size: 32px;
        padding: 0 75px;
	z-index: 999;
    }
    @media (max-width: 1090px) {
        .gif_td {
            width: 0px !important;
            display: none;
        }
        .center_td {
            width: 500px;
        }
    }
    @media (max-width: 785px) {
        .sched_td > div {
            position: fixed;
            bottom: 0;
            left: 0;
            height: 200px;
            width: 100%;
        }
        .center_td {
            width: 100%;
        }
    }
    @media (max-width: 888px) {
        img.globe {
            display: none;
        }
    }
    @media (max-width: 777px) {
        span.letters {
            zoom: 0.75;
        }
    }
    @media (max-width: 676px) {
        img.tjold {
            zoom: 0.5;
        }
    }
    @media (max-width: 572px) {
        .header_th {
            position: absolute;
            left: 0;
            width: 100%;
            top: -15px;
        }
    }
    @media (max-width: 472px) {
        .header_th {
            margin-left: -15px;
        }
    }
    @media (max-height: 660px) {
    @media (max-width: 785px) {
	.sched_td div {
		position: absolute;
		top: 900px;
	}
    }
    }
    </style>
    <title>===TJ INTRANET: Login===</title>
<script type="text/javascript" src="[<$I2_ROOT>]www/js/jquery.min.js">/* woo hoo jquery */</script>
<script type="text/javascript">
var titlesi = 0;
setInterval(function() {
    var m = "TJ INTRANET: Login",
    titles = [
        "===="+m+"====",
        ">==="+m+"===<",
        "=>=="+m+"==<=",
        ">=>="+m+"=<=<",
        "=>=>"+m+"<=<=",
        "==>="+m+"=<==",
        "===>"+m+"<==="
    ];
    document.title = titles[titlesi++];
    if(titlesi >= titles.length) titlesi = 0;
}, 250);
blink=setInterval(function(){$("blink,.blink").each(function(){$(this).css('visibility',($(this).css('visibility')=='hidden'?'visible':'hidden'))})},400);
noadslogin = function() {
        document.cookie = "noadslogin=true; expires="+new Date(+new Date()+(1000*60*15)).toGMTString();
        console.log("No more login ads for you!");
        $(".ad1, .ad2").remove();
}
if(document.cookie.indexOf('noadslogin=true') == -1) {
console.log("Ads incoming");
    setTimeout(function() {
        adint = setInterval(function() {
            var t = Math.floor(Math.random() * ($(window).height() - 200)/2)+100;
            var l = Math.floor(Math.random() * ($(window).width() - 200));
	      $(".ad1").show()
	      .css({'top': t, 'left': l})
	      .click(function() { $(this).remove(); noadslogin(); });
        }, 3000);
	var time = 20;
	addec = setInterval(function() {
	    timed = --time;
	    if(timed < 10) timed = "0"+timed;
	    if(time >= 0) $(".ad1 span").html("00:00:"+timed);
	    else $(".ad1").remove();
	}, 1000);
	if(navigator.userAgent.indexOf('Windows') != -1) {
		$(".ad1 em").html("&nbsp; Windows");
	} else if(navigator.userAgent.indexOf("Macintosh") != -1) {
		$(".ad1 em").html("Mac OS X");
	} else if(navigator.userAgent.indexOf("Linux") != -1) {
		$(".ad1 em").html("&nbsp; &nbsp; &nbsp; Linux &nbsp;");
	} else $(".ad1 em").html("&nbsp; MS BOB");
    }, 3000)
} else console.log("No ads for you!");
    </script>
    <style>
.ad1, .ad2 {
position: fixed;
/*top: -999px;
left: -999px;
*/
display: none;
top: 20%;
left: 50%;
margin-left: -141px;
width: 282px;
height: 258px;
cursor: pointer;
}
.ad1 {
	background-image: url('[<$I2_ROOT>]www/gc/shxp.gif');
}
.ad1 span {
position: absolute;
top: 124px;
left: 170px;
color: black;
background-color: rgb(235,232,215);
font-size: 13px;
font-family: serif;
padding-right: 5px;
}
.ad1 em {
position: absolute;
top: 163px;
left: 51px;
color: black;
background-color: rgb(235,232,215);
font-size: 12px;
font-family: serif;
font-style: normal;
}
b.links {
z-index: 99;
}
</style>
</head>
<body>
<div id="seinfeld-cont">
<audio id="seinfeld" preload=auto volume="0.5" controls[<if $jlam>] autoplay=1[</if>]>
<source src="[<$I2_ROOT>]www/gc/seinfeld.mp3" type="audio/mpeg" />
<source src="[<$I2_ROOT>]www/gc/seinfeld.ogg" type="audio/ogg" />
</audio><br />
<a href="?&customtheme=login-gc&jlam=1" style="color:red">AUTOPLAY!!!</a>
</div>
<style>
#seinfeld-cont {
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 25px;
}
</style>
<div class="ad1">
<span>00:00:20</span>
<em>Windows</em>
</div>
<body bgcolor=black background="[<$I2_ROOT>]www/gc/stars.bmp">
<table width="100%" height="80%">
<tr>
<td>
    <img src="[<$I2_ROOT>]www/gc/tjold.gif" class="tjold" align=left />
</td>
<th class="header_th">
<center style="font-size: 64px">
        
        <img src="[<$I2_ROOT>]www/gc/earth.gif" class="globe" />
        &nbsp;&nbsp;
        <span class="letters">
            <img src="[<$I2_ROOT>]www/gc/l_t.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_j.gif" valign="bottom" />
            &nbsp;&nbsp;
            <img src="[<$I2_ROOT>]www/gc/l_i.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_n.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_t.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_r.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_a.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_n.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_e.gif" />
            <img src="[<$I2_ROOT>]www/gc/l_t.gif" />
        </span>
        &nbsp;&nbsp;
        <img src="[<$I2_ROOT>]www/gc/earth.gif" class="globe" />
        
</center>
</th>
<td>

    <img src="[<$I2_ROOT>]www/gc/tjold.gif" class="tjold" align=right />

</tr>
</table>
<table width="100%" height="100%" align="center" border=10>

<tr>
<th colspan=3 height=50>
<marquee behavior="alternate" scrollamount=10><img src="[<$I2_ROOT>]www/gc/const.gif" /></marquee>

</th>
</tr>
<tr>
<td class="gif_td" valign=top>
<marquee direction=right style="width:300px" height="500" scrollamount=5 scrolldelay="0.2">
<center style="width:800px">
<img src="[<$I2_ROOT>]www/gc/2.gif"> &nbsp; &nbsp; &nbsp; 
<img src="[<$I2_ROOT>]www/gc/3.gif"> &nbsp; &nbsp; &nbsp;
<img src="[<$I2_ROOT>]www/gc/4.gif" style="zoom:0.5"> &nbsp; &nbsp; &nbsp;
<img src="[<$I2_ROOT>]www/gc/5.gif"> &nbsp; &nbsp; &nbsp;
<img src="[<$I2_ROOT>]www/gc/6.gif"> &nbsp; &nbsp; &nbsp;
<img src="[<$I2_ROOT>]www/gc/7.gif"> &nbsp; &nbsp; &nbsp;
<img src="[<$I2_ROOT>]www/gc/genie.gif">
</center>
</marquee>
</td>
<td class="center_td" valign=top>
<center>
<b class='links'><a href="http://web.archive.org/web/19961116031639/http://www.tjhsst.edu/">TJ WEBSITE</a> &nbsp; &nbsp;
<a href="http://webmail.tjhsst.edu">WEBMAIL</a> &nbsp; &nbsp; <a href="http://postman.tjhsst.edu">CALENDAR</a></b>
<form action="[<$I2_ROOT>]" style="display:inline" method="post">
    <table width="100%" border=5>
    <tr>
    <th valign="center">
        <span style="font-size:32px;">LOGIN</span>
        <br /><br />
        <img src="[<$I2_ROOT>]www/gc/ie.gif" /> &nbsp; 
        <img src="[<$I2_ROOT>]www/gc/2computers.gif" /> &nbsp;
        <img src="[<$I2_ROOT>]www/gc/ns.gif" />
    </th>
    </tr>
    <tr><td>
        <center>
        <b>USERNAME</b> <input name="login_username"><br /></center>
    </td></tr><tr><td>
        <center><b>PASSWORD <img src="[<$I2_ROOT>]www/gc/new.gif" class=blink /></b> <input name="login_password" type="password" />
        </center>
    </td></tr><tr><td>
    <center>
        <noscript><input type="submit" value="LOGIN" class="smt" /></noscript>
        <button class="smt">
        <img src="[<$I2_ROOT>]www/gc/floppy.gif" /><br />
        LOGIN
        </button>
        
    </center>
    </td></tr>
    </table>
</form>
<marquee direction="right" behavior="alternate" scrollspeed=4>

<div id="verisign_box" class="box" title="Click to Verify - This site chose VeriSign SSL for secure confidential communications." style="display:inline-block !important">
                <script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=iodine.tjhsst.edu&amp;size=S&amp;use_flash=NO&amp;use_transparent=YES&amp;lang=en"></script><br/>
            </div>
<img name="seal" border="true" src="https://seal.verisign.com/getseal?at=0&amp;sealid=2&amp;dn=iodine.tjhsst.edu&amp;lang=en" oncontextmenu="return false;" alt="Click to Verify - This site has chosen an SSL Certificate to improve Web site security">
<img name="seal" border="true" src="https://seal.verisign.com/getseal?at=0&amp;sealid=2&amp;dn=iodine.tjhsst.edu&amp;lang=en" oncontextmenu="return false;" alt="Click to Verify - This site has chosen an SSL Certificate to improve Web site security">
<img name="seal" border="true" src="https://seal.verisign.com/getseal?at=0&amp;sealid=2&amp;dn=iodine.tjhsst.edu&amp;lang=en" oncontextmenu="return false;" alt="Click to Verify - This site has chosen an SSL Certificate to improve Web site security">


</marquee>
<img src="[<$I2_ROOT>]www/gc/laptop.gif" /> &nbsp; &nbsp; &nbsp;

</center>
</td>
<td class="sched_td" valign=top>
<div style="background-color: pink;padding: 10px">
<center>
<iframe src="[<$I2_ROOT>]ajax/dayschedule?&iframe&afd" seamless=seamless allowtransparency=true width="310" height="325"></iframe>
</center>
</div>

</td>
</table>
<style type="text/css">
/* ie */
div.ie6 {
        position: fixed;
        bottom: 0;
        background-color: white;
        color: black;
        background-image: url('[<$I2_ROOT>]www/gc/excl.gif');
        background-position: left center;
        background-repeat: no-repeat;
        width: 100%;
        height: 80px;
}
div.ie6 span {
	color: black;
}
.ie6h {
        color: #0090ff;
        font-size: 32px;
        padding: 40px 5px;
}
.ie6 marquee { height: 80px; }

@media (max-width: 925px) {
        div.ie6 .ie6h { font-size: 20px }
}
/*450*/
@media (max-width: 764px) {
        div.ie6 { zoom: 0.7; height: 130px; }
        div.ie6 marquee { height: 130px; }
        div.ie6 img { display: none; }
	div.ie6 {
background-image: url('www/gc/ie.gif');
background-position: right 5px;
background-size: 125px 44px;
background-repeat: no-repeat;
}
	div.ie6 > marquee {
background-image: url('www/gc/ie.gif');
background-position: left 5px;
background-size: 125px 44px;
background-repeat: no-repeat;
}
}
@media (max-width: 765px) {
	div.ie6 {
		position: absolute;
		bottom: -80px;
		left: 0;
	}
	.sched_td > div {
		position: absolute;
	}
}
.ie6 span, .ie6 blink {
        color: black;
}
@media (max-height: 704px) {
        div.ie6 {
                position: fixed;
                bottom: 0;
		left: 0;
		height: 60px;
        }
    }
</style>
<div class="ie6" onclick="document.getElementById('seinfeld').play()">
<marquee direction=left behaviour="alternate" behavior="alternate">
<table>
<tr><td>
<img src="[<$I2_ROOT>]www/gc/ie.gif" />
</td><td><center>
<a href="javascript:;">
<span class="ie6h"><!--[if lte IE 7]><blink>A WINNER IS YOU, IE USER!!</blink><br /><![endif]-->
YOU ARE USING AN <blink>UNSUPPORTED BROWSER!!</blink></span><br />
<span>Upgrade to the fastest and most secure internet browsing experience -- Internet Explorer 6. CLICK HERE NOW</span>
</a>
</center></td><td><img src="[<$I2_ROOT>]www/gc/ie.gif" />
</marquee>
</div>
<!--[if IE lt 7]>
<style>
body { background-image: url([<$I2_ROOT>]www/gc/winner.jpg); background-repeat: repeat; }
</style>
<![endif]-->
</body>
</html>
