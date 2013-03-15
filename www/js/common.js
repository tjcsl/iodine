function enable_creffet() {
	s=document.createElement('link');
	s.rel='stylesheet';s.type='text/css';s.href='/~2016jwoglom/i2/www/extra-css/fire.css';
	document.body.appendChild(s);
	document.body.className='fire';
	a=document.getElementsByTagName('a');
	for(i=0;i<a.length;i++) {
		if(typeof a[i].href!=='undefined') a[i].href+=(a[i].href.indexOf('?')!==-1?'&':'?')+'creffet=1';
	}
}
function getQueryVariable(variable) {
    var query = window.location.search.substring(1);
    var vars = query.split('&');
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split('=');
        if (decodeURIComponent(pair[0]) == variable) {
            return decodeURIComponent(pair[1]);
        }
    }
    console.log('Query variable %s not found', variable);
	return false;
}
function common_init() {
	if(getQueryVariable('creffet')!=false) enable_creffet();
	
}