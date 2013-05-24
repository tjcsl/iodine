
/* not currently used */
function parse_bgs() {
    try {
        bid = window.location.search.split('bid=')[1];
    } catch(e) {}
    if(window.location.search.indexOf('bid=')==-1) {
        try {
            bid = (function(){for(i in j=(c=document.cookie).split(';')) if((k=j[i].split('='))[0]=='background'&&!!k[1]) for(l=0;l<(m=document.getElementsByTagName('optgroup')[1].children).length;l++) if(unescape(m[l].value)==unescape(k[1])) return m[l].id.split('bg')[1];})();
        } catch(e) {}
    }
    if(typeof bid != 'undefined' && typeof document.getElementById('bg'+bid) != 'undefined') {
        document.getElementById('bg0').setAttribute('selected', false);
        document.getElementById('bg'+bid).setAttribute('selected', true);
    } else {
        document.getElementById('bg0').setAttribute('selected', true);
    }
}

chrome_app = function() {
    if (!!chrome && !!chrome.app && !chrome.app.isInstalled) {
        var chromeLink = document.createElement("a");
        chromeLink.href = "[<$I2_ROOT>]www/chrome/iodine_chrome_app.crx";
        chromeLink.type = "application/x-chrome-extension";

        var chromeBox = document.createElement("div");
        chromeBox.className = "box";
        chromeBox.style.padding = "4px";

        chromeBox.innerHTML = "<img src=\"[<$I2_ROOT>]www/pics/chrome_icon_42.png\" style=\"float:left; margin-right:4px;\" alt=\"Google Chrome logo\"/>Install the TJ Intranet app for Chrome";

        chromeLink.appendChild(chromeBox);
        document.getElementById("mainPane").appendChild(chromeLink);
    }
}

function isCapslock(e){
    e = (e) ? e : window.event;
    var charCode = false;
    if (e.which) {
        charCode = e.which;
    } else if (e.keyCode) {
        charCode = e.keyCode;
    }
    var shifton = false;
    if (e.shiftKey) {
        shifton = e.shiftKey;
    } else if (e.modifiers) {
        shifton = !!(e.modifiers & 4);
    }
    if (charCode >= 97 && charCode <= 122 && shifton) {
        return true;
    }
    if (charCode >= 65 && charCode <= 90 && !shifton) {
        return true;
    }
    return false;

}
checkcl = function(e) {
	$e = $('.login_msg span.cl');
	if(isCapslock(e)) {
		if($e.length < 1) {
			$('.login_msg').append('<span class=\'cl\'><br />Caps Lock is on. Consider turning it off.</span>');
		}
		$e.show();
	} else {
		$e.hide();
	}
};
