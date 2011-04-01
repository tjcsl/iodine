preload();
function preload() {
	/* Begin title stuff */
	document.title = "Facebook - " + document.title.substring(18);
	/* End title stuff */
}
function page_init() {
//	document.getElementById('boxcontent').style.paddingRight='284px';
	document.getElementById('mainbox').style.right='264px';
	rightbar = document.createElement("div");
	rightbar.style.right='10px';
	rightbar.style.top='96px';
	rightbar.style.width='244px';
	rightbar.style.position='absolute';
	rightbar.id='rightbar';
	document.getElementById('mainbox').parentElement.appendChild(rightbar);
	/*var peopleTxt = "<img src='http://26.media.tumblr.com/avatar_759afa21b3c4_64.png' title='webass, y u so annoying?'> ";
	peopleTxt += "<img src='http://30.media.tumblr.com/avatar_97572dc2ad28_64.png' title='Modding the eighth module  is suicide?  Challenge accepted.'> ";
	peopleTxt += "<a href='[<$I2_ROOT>]studentdirectory/info/14079'><img src='http://stevesastre.com/wp-content/uploads/2010/07/trollface_hd-64x64.jpg' title='Sreenath Are'></a>"*/
/*	for (var i = 0; i < 5; i++) {
		var possFriendId = Math.round(Math.random() * 12000) + 2000;
		peopleTxt += "<a href='[<$I2_ROOT>]studentdirectory/info/" + possFriendId + "'>";
		peopleTxt += "<img src='[<$I2_ROOT>]pictures/" + possFriendId + "' style='width:36px; height:48px;'/>";
		peopleTxt += "</a>";
	}*/
	add_rightbox('People You May Know', gen_youMayKnow());
	add_jewels();
}
function gen_youMayKnow() {
	var outputdiv=document.createElement("div");
	var i=0;
	var possible= new Array(16);
	possible[0]=new Array("Muammar Gaddafi","gaddafi.png");
	possible[1]=new Array("Osama Bin Laden","binladen.png");
	possible[2]=new Array("Elizabeth Taylor","taylor.png");
	possible[3]=new Array("Rodney Daingerfeld","daingerfeld.png");
	possible[4]=new Array("Elizabeth V. Lodal","lodal.png");
	possible[5]=new Array("Dan Tran","dantran.png");
	possible[6]=new Array("Pancho Villa","panchovilla.png");
	possible[7]=new Array("Charlie Sheen","sheen.png");
	possible[8]=new Array("John Wilkes Booth","booth.png");
	possible[9]=new Array("L. Ron Hubbard","hubbard.png");
	possible[10]=new Array("Kim Jong Il","kimjongil.png");
	possible[11]=new Array("Pat Robertson","robertson.png");
	possible[12]=new Array("Marty McFly","mcfly.png");
	possible[13]=new Array("Air Bud","airbud.png");
	possible[14]=new Array("Double Rainbow Man","doublerainbow.png");
	possible[15]=new Array("Tingle","tingle.png");
	for(i=0;i<3;i++) {
		var index=Math.floor(Math.random() * possible.length);
		var person=possible[index];
		possible.splice(index,1);
		var entry=document.createElement("div");
		entry.style.margin="10px";
		entry.style.height="50px";
		var pic=document.createElement("img");
		pic.src=i2root+"www/pics/fbicons/"+person[1];
		var picdiv=document.createElement("div");
		picdiv.appendChild(pic);
		picdiv.style.cssFloat="left";
		picdiv.style.marginRight="10px";
		entry.appendChild(picdiv);
		var textdiv=document.createElement("div");
		textdiv.innerHTML="<b>"+person[0]+"</b><br /><a href='javascript://null();'>Add as Friend</a>";
		entry.appendChild(textdiv);
		outputdiv.appendChild(entry);
		var flowfixer= document.createElement("div");
		flowfixer.style.clear="both";
		outputdiv.appendChild(flowfixer);
	}
	return outputdiv.innerHTML;
}
function add_rightbox(title, contents) {
	container=document.createElement('div');
	titlearea=document.createElement('div');
	titlearea.innerHTML=title;
	titlearea.style.backgroundColor="#F2F2F2";
	titlearea.style.color="#333333";
	titlearea.style.padding="4px 3px 5px 5px";
	titlearea.style.fontWeight="bold";
	titlearea.style.fontSize="11px";
	var seealldiv=document.createElement("div");
	seealldiv.innerHTML="<a href='javscript://null();'>see all</a>";
	seealldiv.id="seealldiv";
	seealldiv.style.cssFloat='right';
	seealldiv.style.margin='3px 7px 3px 3px';
	container.appendChild(seealldiv);
	container.style.borderTop="1px solid #E2E2E2";
	container.appendChild(titlearea);
	bodyarea=document.createElement('div');
	bodyarea.innerHTML=contents;
	container.appendChild(bodyarea);
	document.getElementById('rightbar').appendChild(container);
}
function add_jewels() {
	jeweldiv=document.createElement("div");
	jeweldiv.className="jewelbox";
	friendreq=createJewel("Friend Requests","friends.png","jewelfriends");
	jeweldiv.appendChild(friendreq);
	document.getElementById("logo").appendChild(jeweldiv);
}
function createJewel(title,img,classn) {
	jewel=document.createElement("div");
	jeweliconcontainer=document.createElement("div");
	jeweliconcontainer.innerHTML="friend reqs";
	jewel.appendChild(jeweliconcontainer);
	jewel.className=classn;
	return jewel;
}
function intrabox_onmouseover(div_id) {
}
function intrabox_onmouseout(div_id) {
}
function menu_onmouseover() {
}
function menu_onmouseout() {
}
function menuitem_onmouseover(div_id,not_1,not_2,not_3,not_4,not_5) {
}
function menuitem_onmouseout(div_id) {
}
