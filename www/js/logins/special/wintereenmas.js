window.onload = function() {
	if (window.innerHeight < 550 || (navigator.userAgent.indexOf("MSIE") != -1 && navigator.userAgent.indexOf("MSIE 9") == -1)) {
		return;
	}
	
	var today = new Date();
	if (today.getMonth() == 0) {
		// the <div> that will contain all this
		var wemasPanel = document.createElement("div");
		wemasPanel.style.position = "fixed";
		wemasPanel.style.left = "0px";
		wemasPanel.style.bottom = "0px";
		
		// the <div> that will contain the text
		var wemasTxtPanel = document.createElement("div");
		wemasTxtPanel.style.position = "absolute";
		wemasTxtPanel.style.left = "130px";
		wemasTxtPanel.style.bottom = "113px";
		
		wemasTxtPanel.style.backgroundColor = "#5379A8";
		
		wemasTxtPanel.style.borderStyle = "solid";
		wemasTxtPanel.style.borderWidth = "10px";
		wemasTxtPanel.style.borderColor = "white";
		wemasTxtPanel.style.WebkitBorderRadius = "20px";
		wemasTxtPanel.style.MozBorderRadius = "20px";
		wemasTxtPanel.style.borderRadius = "20px";
		
		wemasTxtPanel.style.color = "white";
		wemasTxtPanel.style.fontSize = "36pt";
		wemasTxtPanel.style.fontFamily = "Arial, Helvetica, sans-serif";
		wemasTxtPanel.style.fontWeight = "bold";
		
		wemasTxtPanel.style.padding = "5px 15px 5px 24px";
		
		if (today.getDate() >= 25 && today.getDate() <= 31) {
			wemasTxtPanel.innerHTML = "Happy&nbsp;Wintereenmas!";
		} else {
			wemasTxtPanel.innerHTML = (25 - today.getDate()) + "&nbsp;days&nbsp;until&nbsp;Wintereenmas";
		}
		
		wemasPanel.appendChild(wemasTxtPanel);
		
		// pic of Ethan as Wintereenmas king
		var ethanImg = document.createElement("img");
		if (today.getDate() >= 25 && today.getDate() <= 31) {
			ethanImg.src = "www/pics/wemas/ethan_king.png";
			wemasTxtPanel.style.left = "123px";

		} else {
			ethanImg.src = "www/pics/wemas/ethan_cape.png";
		}
		ethanImg.style.position = "absolute";
		ethanImg.style.left = "0px";
		ethanImg.style.bottom = "0px";
		
		wemasPanel.appendChild(ethanImg);
		
		document.getElementsByTagName("body")[0].appendChild(wemasPanel);
	}
}
