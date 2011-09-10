function addimage() {
	var table=document.body.childNodes[7];
	var image = document.createElement("img");
	image.src=i2root+"www/pics/logins/special/wtc.png";
	table.style.marginTop="0px";
	image.style.display="block";
	image.style.marginLeft="auto";
	image.style.marginRight="auto";
	image.style.marginBottom="-4px";
	table.insertBefore(image,table.firstChild);
}
window.onload=addimage;
