preload();
function preload() {
	/* Begin title stuff */
	document.title = "Facebook - " + document.title.substring(18);
	/* End title stuff */
}
function changeToGSearch() {
}
function page_init() {
	document.getElementById('boxcontent').style.paddingRight='284px';
	rightbar = document.createElement("div");
	rightbar.style.right='20px';
	rightbar.style.top='30px';
	rightbar.style.width='244px';
	rightbar.style.position='absolute';
	rightbar.id='rightbar';
	document.getElementById('mainbox').appendChild(rightbar);
	add_rightbox('People You May Know',"This is a testing rightbox");
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
