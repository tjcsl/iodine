// This requires jQuery

if(typeof window.cached_req == 'undefined') window.cached_req = {};
week_base_url = i2root + "ajax/bellschedule";

schedule_reset = function() {
	week_inc = 0;
	return week_show(0);
};
schedule_click = function(day, box, cache) {
	is_week = $('#subPane').hasClass('exp') || $('#schedule_week').hasClass('exp');
	if(is_week) {
		// get the number of days until next monday
		if(typeof week_inc == 'undefined') {
			week_inc = 0;
		}
		tod = new Date();
		mondiff = 7;
		if(parseInt(day) > 0) { // next week
			week_inc++;
		} else if(parseInt(day) < 1) { // last week
			week_inc--;
		}
		if(day < 7 && day > -7) day = 0;
		return week_show(mondiff * week_inc);
	} else {
		return day_click(day, box, cache);
	}
}

week_click = function(day, box, cache) {
	weekd = (typeof weekd == 'undefined' || (typeof weekd != 'undefined' && !weekd));
	if(weekd) {
		$("#week_click").html("Back");
		return week_show(day);
	} else {
		$("#week_click").html("Week");
		return week_hide();
	}
};

week_stripjs = function(d) {
	debug_start = '<script type="text/javascript" src="'+i2root+'www/js/cookie.js"></script>';
	if(d.indexOf(debug_start) !== -1) {
		d = d.split(debug_start)[0];
	}
	d = d.replace(/<script type="text\/javascript" src/g, '<script type="text/javascript" src_disabled');
	d = d.replace(/<script type='text\/javascript' src/g, '<script type=\'text/javascript\' src_disabled');
	d = d.replace(/<script src/g, '<script src_disabled');
	return d;
};
function getMonday(d) {
	d = new Date(d);
	var day = d.getDay();
	var offset;
	if(day === 0){
		offset = 1;
	} else if(day == 6){
		offset = 2;
	} else {
		offset = -1 * (day - 1);
	}
	return new Date(d.setDate(d.getDate() + offset));
}
week_show = function(dayoffset) {


	$sp = $('#schedule_week').parent();
	if($sp.hasClass('boxcontent')) {
		h = $sp.css('height');
		$('#schedule_week').addClass('exp');
		$sp.css('height', h);
	}

	$('#subPane').css({'overflow-x': 'hidden'}).addClass('exp');
	$("#schedule h2, #schedule p, #schedule div").hide();
	$("#schedule_t, #schedule_week").show();

	if(dayoffset != 0) {
		$('#week_thiswk').show();
	} else {
		$('#week_thiswk').hide();
	}

	/* $('#week_b, #week_f, #week_today').hide(); */
	$('#week_today').hide();

	var daygetvar = "";
	if(typeof dayoffset == 'undefined') dayoffset = 0;

	if (window.location.search.indexOf('day=')!==-1) {
		dayoffset = parseInt(window.location.search.split('day=')[1], 10);
	}
	daygetvar = "&day=" + dayoffset;
	window.location.hash = '#offset=' + dayoffset;
	m = new Date();
	m.setDate(m.getDate() + dayoffset);
	m = getMonday(m);
	dy = m.getFullYear();
	dm = m.getMonth()+1;
	if(dm <= 9) dm = '0'+dm;
	dd = m.getDate();
	if(dd <= 9) dd = '0'+dd;
	d = parseInt(dy+''+dm+''+dd, 10);
	u = week_base_url+'?&week&ajax'+daygetvar;

	u+= '&start='+(d);
	u+= '&end='+(d+5);
	if(typeof window.cached_req[u] == 'undefined') {
		$.get(u, {}, function(d) {
			d = week_stripjs(d);
			window.getd = d;
			window.cached_req[u] = d;
			week_showget(d, u);
		}, 'text');
	} else {
		week_showget(window.cached_req[u], u);
	}
};

week_showget = function(d) {
	if(d.indexOf('::START::')!==-1 && d.indexOf('::END::')!==-1) {
		weekdata = d.split('::START::</span>');
		weekdata = weekdata[1].split('<span style=\'display:none\'>::END::')[0];
		$('#schedule_week').html(weekdata);
	} else {
		$('#schedule_week').append('<p>An error occurred fetching schedules.</p>');
		window.location.href = u;
	}

};
week_hide = function() {
	$("#subPane").css({'overflow-x': ''}).removeClass('exp');

	$sp = $('#schedule_week').parent();
	if($sp.hasClass('boxcontent')) {
		$('#schedule_week').removeClass('exp');
		$sp.css('height', 'auto');
	}
	window.location.hash = '';
	$("#schedule_week, #week_thiswk").hide();
	$("#schedule h2, #schedule p, #schedule div").show();
	$("#schedule_t, #week_b, #week_f, #week_today").show();
};

schedule_click_box = function(day) {
	return schedule_click(day, true);
};

day_click = function(day, box, cache) {
	if(typeof cache=='undefined') cache = false;
	links_content = '<br /><br />'+$('<div>').append($('ul#links').clone()).html()+'<br />';

	u = week_base_url+'?day='+day;
	window.location.hash = '#day='+day;
	if(day == 0) {
		window.location.hash = '';
	}

	if(typeof box != 'undefined') {
		u = u + '&box';
	}
	if(typeof window.cached_req[u] == 'undefined') {
		$.get(u, {}, function(d) {
			d = week_stripjs(d);
			window.cached_req[u] = d;
			if(!cache) day_clickget(d,box);
		}, 'html');
	} else if(!cache) {
		day_clickget(window.cached_req[u],box);
	}
};

day_clickget = function(d, box) {
	if(typeof box == 'undefined') {
		$('div#schedule').parent().html(d);
	} else {
		$('div#schedule_box').parent().html(d);
	}

	if(typeof links_content != 'undefined') {
		$('#subPane').append(links_content);
	}
};

day_preload = function() {
	for(i=-1; i<7; i++) {
		day_click(i, null, true);
	}
	window.location.hash = '';

};

day_parsehash = function() {
	h = window.location.hash.replace(/#/, '');
	if(h.indexOf('day=') !== -1) {
		day_click(h.split('day=')[1]);
	} else if(h.indexOf('offset=') !== -1) {
		week_click(0);
		schedule_click(h.split('offset=')[1]);
	}
};


