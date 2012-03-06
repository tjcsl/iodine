preload();
function preload() {
}
function page_init() {
}
[<include file="../javascriptstyles/includes/fade.js">]
function intrabox_onmouseover(div_id) {
}
function intrabox_onmouseout(div_id) {
}
function menu_onmouseover() {
}
function menu_onmouseout() {
	fadeObjectIn('menu_news',35,15,1.05);
	fadeObjectIn('menu_eighth',35,15,1.05);
	fadeObjectIn('menu_polls',35,15,1.05);
	fadeObjectIn('menu_prefs',35,15,1.05);
	fadeObjectIn('menu_suggest',35,15,1.05);
	fadeObjectIn('menu_cred',35,15,1.05);
	fadeObjectIn('menu_help',35,15,1.05);
	fadeObjectIn('menu_logout',35,15,1.05);
}
function menuitem_onmouseover(div_id,not_1,not_2,not_3,not_4,not_5,not_6,not_7) {
	fadeObjectIn(div_id,0,25,1.05);
	fadeObjectOut(not_1,0,20,0.5);
	fadeObjectOut(not_2,0,20,0.5);
	fadeObjectOut(not_3,0,20,0.5);
	fadeObjectOut(not_4,0,20,0.5);
	fadeObjectOut(not_5,0,20,0.5);
	fadeObjectOut(not_6,0,20,0.5);
	fadeObjectOut(not_7,0,20,0.5);
}
function menuitem_onmouseout(div_id) {
}
