function getYPos(id) {
	var div = document.getElementById(id);
	var element = document.documentElement;
	var body = document.body;
	return (element.clientHeight ? element.clientHeight : body.clientHeight)
		+ (element.scrollTop ? element.scrollTop : body.scrollTop)
		- div.offsetHeight;
}

function getXPos(id) {
	var div = document.getElementById(id);
	var element = document.documentElement;
	var body = document.body;
	return (element.clientWidth ? element.clientWidth : body.clientWidth) 
		+ (element.scrollLeft ? element.scrollLeft : body.scrollLeft )
		- div.offsetWidth;
}
