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
	var peopleTxt = "<img src='http://26.media.tumblr.com/avatar_759afa21b3c4_64.png' title='webass, y u so annoying?'> ";
	peopleTxt += "<img src='http://30.media.tumblr.com/avatar_97572dc2ad28_64.png' title='Modding the eighth module  is suicide?  Challenge accepted.'> ";
	peopleTxt += "<a href='[<$I2_ROOT>]studentdirectory/info/14079'><img src='http://stevesastre.com/wp-content/uploads/2010/07/trollface_hd-64x64.jpg' title='Sreenath Are'></a>"
/*	for (var i = 0; i < 5; i++) {
		var possFriendId = Math.round(Math.random() * 12000) + 2000;
		peopleTxt += "<a href='[<$I2_ROOT>]studentdirectory/info/" + possFriendId + "'>";
		peopleTxt += "<img src='[<$I2_ROOT>]pictures/" + possFriendId + "' style='width:36px; height:48px;'/>";
		peopleTxt += "</a>";
	}*/
	add_rightbox('People You May Know', peopleTxt);
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
	container.style.borderTop="1px solid #E2E2E2";
	container.appendChild(titlearea);
	bodyarea=document.createElement('div');
	bodyarea.innerHTML=contents;
	container.appendChild(bodyarea);
	document.getElementById('rightbar').appendChild(container);
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
