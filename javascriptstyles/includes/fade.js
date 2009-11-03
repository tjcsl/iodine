// Include this file in your javascript style files through smarty.
var ie=(document.all) ? 1 : 0;
function fadeObjectIn(what,intimestart,intimestep,howfar) {
	var thing = document.getElementById(what);
	if(thing.mouseovers==null || thing.mouseovers=='')
	{
		thing.mouseovers=1;
		if(ie==0)
			thing.style.opacity='0.6';
		else
			thing.style.filter="alpha(opacity=60)";
	}
	else
		thing.mouseovers=thing.mouseovers+1;
	if(thing.mouseoffers==null || thing.mouseoffers=='')
		thing.mouseoffers=0;
	var opacity = 0;
	if(ie==0)
		opacity = thing.style.opacity;
	else
	{
		var tempopacity = thing.style.filter;
		tempopacity = tempopacity.substring(tempopacity.indexOf("="));
		tempopacity = tempopacity.substring(0,tempopacity.indexOf(")"));
		opacity = tempopacity / 100;
	}
	var time = intimestart;
	while(opacity <= howfar)
	{
		setTimeout("fadeIn('" + opacity +"','" + what + "'," + thing.mouseoffers + ")",time);
		time=time+intimestep;
		opacity=opacity - (-0.05);
	}
}
function fadeObjectOut(what,outtimestart,outtimestep,howfar) {
	var thing = document.getElementById(what);
	if(thing.mouseoffers==null || thing.mouseoffers=='')
		thing.mouseoffers=0;
	thing.mouseoffers=thing.mouseoffers+1;
	var opacity = 0;
	if(ie==0)
		opacity = thing.style.opacity;
	else
	{
		var tempopacity = thing.style.filter;
		tempopacity = tempopacity.substring(tempopacity.indexOf("="));
		tempopacity = tempopacity.substring(0,tempopacity.indexOf(")"));
		opacity = tempopacity / 100;
	}
	var time = outtimestart;
	while(opacity >= howfar)
	{
		setTimeout("fadeOut('" + opacity + "','" + what + "'," + thing.mouseovers + ")",time);
		time=time+outtimestep;
		opacity=opacity-0.05;
	}
}

function fadeOut(opacity,what,tmo) {
	var thing = document.getElementById(what);
	if(tmo==thing.mouseovers)
		thing.style.opacity=opacity;
}
function fadeIn(opacity,what,tmo) {
	var thing = document.getElementById(what);
	if(tmo==thing.mouseoffers) {
		if(ie==0)
			thing.style.opacity=opacity;
		else
			thing.style.filter="alpha(opacity=" + (opacity*100) + ")";
	}
}
