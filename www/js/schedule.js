week_click = function() {
	if(typeof weekd == 'undefined' || (typeof weekd != 'undefined' && !weekd)) {
		weekd = true;
		$("#week_click").html(week_text);
		return week_show();
	} else {
		weekd = false;
		$("#week_click").html("Full Week");
		return week_hide();
	}
};

week_show = function() {
	$('#subPane').css({'overflow-x': 'hidden'}).addClass('exp');

	$("#schedule h2, #schedule p, #schedule div").hide();
	$("#schedule_t").show();
	$('#schedule_week').show();
	$('#week_b, #week_f, #week_today').hide();

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

	var daygetvar = "";
	var dayoffset = 0;
	if (window.location.search.indexOf('day=')!==-1) {
		dayoffset = parseInt(window.location.search.split('day=')[1], 10);
		daygetvar = "&day=" + dayoffset;
	}

	m = new Date();
	m.setDate(m.getDate() + dayoffset);
	m = getMonday(m);
	dy = m.getFullYear();
	dm = m.getMonth()+1;
	if(dm <= 9) dm = '0'+dm;
	dd = m.getDate();
	if(dd <= 9) dd = '0'+dd;
	d = parseInt(dy+''+dm+''+dd, 10);
	u = week_base_url+'?week&ajax'+daygetvar;

	u+= '&start='+(d);
	u+= '&end='+(d+5);
	$.get(u, {}, function(d) {
		//try {
			window.getd = d;
			if(d.indexOf('::START::')!==-1 && d.indexOf('::END::')!==-1) {
				weekdata = d.split('::START::</span>');
				weekdata = weekdata[1].split('<span style=\'display:none\'>::END::')[0];
				$('#schedule_week').html(weekdata);
			} else {
				$('#schedule_week').append('<p>An error occurred fetching schedules.</p>');
				window.location.href = u;
			}
		//} catch(e) {
		//	$('#schedule_week').append('<p>An error occurred fetching schedules.</p>');
		//}
	}, 'text');
};

week_hide = function() {
	$("#subPane").css({'overflow-x': ''}).removeClass('exp');
	$("#schedule_week").hide();
	$("#schedule h2, #schedule p, #schedule div").show();
	$("#schedule_t, #week_b, #week_f, #week_today").show();
};
