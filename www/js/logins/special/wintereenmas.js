function winterEenMas() {
	
	// the <div> that will contain all this
	var wemasPanel = document.createElement("div");
	wemasPanel.style.position = "fixed";
	wemasPanel.style.left = "0px";
	wemasPanel.style.bottom = "0px";
	
	// do not celebrate Wintereenmas if the window is too small or if it has been disabled
	if (window.innerHeight < 550 || !localStorage || !localStorage.wemas || localStorage.wemas != "1") {
		var wemasEnableBtn = document.createElement("button");
		wemasEnableBtn.style.position = "absolute";
		wemasEnableBtn.style.left = "2px";
		wemasEnableBtn.style.bottom = "2px";
		wemasEnableBtn.style.opacity = "0.85";
		wemasEnableBtn.innerHTML = "Enable Wintereenmas in this browser";
		wemasEnableBtn.onclick = function() {
			localStorage.wemas = "1";
			location.reload();
		}
		wemasPanel.appendChild(wemasEnableBtn);
	} else {
		// the <div> that will contain the text
		var wemasTxtPanel = document.createElement("div");
		wemasTxtPanel.style.position = "absolute";
		wemasTxtPanel.style.left = "123px";
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
		
		wemasTxtPanel.innerHTML = "Happy&nbsp;<a href='http://wintereenmas.com' target='_blank' style='color:white; text-decoration:none;'>Wintereenmas</a>!";
		
		wemasPanel.appendChild(wemasTxtPanel);
		
		// pic of Ethan as Wintereenmas king
		var ethanImg = document.createElement("img");
		ethanImg.src = "www/pics/wemas/ethan_king.png";
		ethanImg.style.position = "absolute";
		ethanImg.style.left = "0px";
		ethanImg.style.bottom = "0px";
		
		wemasPanel.appendChild(ethanImg);

		var wemasDisableBtn = document.createElement("button");
		wemasDisableBtn.style.position = "absolute";
		wemasDisableBtn.style.left = "2px";
		wemasDisableBtn.style.bottom = "2px";
		wemasDisableBtn.style.whiteSpace = "nowrap";
		wemasDisableBtn.style.opacity = "0.6";
		wemasDisableBtn.innerHTML = "Disable Wintereenmas in this browser";
		wemasDisableBtn.onclick = function() {
			localStorage.wemas = "0";
			location.reload();
		}
		wemasPanel.appendChild(wemasDisableBtn);
	}
		
	document.getElementsByTagName("body")[0].appendChild(wemasPanel);
}

window.onload = winterEenMas;
