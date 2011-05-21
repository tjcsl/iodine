var raptureImg;

window.addEventListener("load", function() {
	var raptureWidth = 700; // the width and height of the rapture img
	var raptureHeight = 483; // these should be changed if a different img is used later
	
	var body = document.getElementsByTagName("body")[0];
	body.style.WebkitBackgroundSize = "cover"; // old Safari
	body.style.KhtmlBackgroundSize = "cover";
	body.style.MozBackgroundSize = "cover"; // old FF
	body.style.MsBackgroundSize = "cover"; // if IE ever supports it...
	body.style.OBackgroundSize = "cover"; // older Opera
	body.style.backgroundSize = "cover"; // everything up-to-date that does not fail
	
	raptureImg = document.createElement("img");
	raptureImg.src = "http://iodine.tjhsst.edu/~2012zyaro/i2/www/pics/rapture.png";
	raptureImg.style.width = raptureWidth + "px";
	raptureImg.style.height = raptureHeight + "px";
	
	var loginTable = document.getElementsByTagName("table")[0]; // get the login box
	loginTable.style.position = "relative"; // ------------------- and shift
	loginTable.style.top = "160px"; // --------------------------- it down

	raptureImg.style.position = "absolute";
	raptureImg.style.left = Math.round((window.innerWidth / 2.0) - (raptureWidth / 2.0)) + "px";
	raptureImg.style.top = (loginTable.offsetTop - raptureHeight + 20) + "px";

	body.appendChild(raptureImg);
}, false);

window.addEventListener("resize", function() {
	var loginTable = document.getElementsByTagName("table")[0];
	raptureImg.style.left = Math.round((window.innerWidth / 2.0) - (raptureWidth / 2.0)) + "px";
	raptureImg.style.top = (loginTable.offsetTop - raptureHeight + 20) + "px";
}, false);
