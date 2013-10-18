Date.prototype.yyyymmdd = function() {
   var yyyy = this.getFullYear().toString();
   var mm = (this.getMonth()+1).toString();
   var dd = this.getDate().toString();
   return yyyy + (mm[1]?mm:"0"+mm[0]) + (dd[1]?dd:"0"+dd[0]);
};
day_jump = function(days) {
	/*var qd = location.search.indexOf('date=') != -1 ? location.search.split('date=')[1] : null;*/
	var qd = currentdate;
	//var dobj = (qd != null ? new Date(qd.substring(0,4), parseInt(qd.substring(4,6))-1, qdsubstring(6,8)) : new Date());
	var dobj = new Date(['','January','February','March','April','May','June','July','August','September','October','November','December'][qd.substring(4,6)] + ' ' + qd.substring(6,8) + ', ' + qd.substring(0,4));
	var newdobj = new Date(dobj.getTime()+(86400000 * days));
	// TODO: Ajax requests
	// the & is needed for the i2_query bug on the main page
	location.href = '?&date=' + newdobj.yyyymmdd();
}
times = [];
gettd = function(time) {
	if(parseInt(time.split(':')[0]) < 4) time = (parseInt(time.split(':')[0])+12) + ':' + time.split(':')[1];
	var d = new Date(), e = (''+d).split(' ');
	return new Date(e[0]+' '+e[1]+' '+e[2]+' '+e[3]+' '+time+':00 '+e[5]+' '+e[6]);
}
get_times_array = function() {
	$p = $('.schedule-tbl .schedule-day');
	$p.each(function() {
		times.push([$(this).attr('data-type'), gettd($(this).attr('data-start')), gettd($(this).attr('data-end'))]);

	});
}

check_current_pd = function(d) {
	for(i=0; i<times.length; i++) {
		if(+d > times[i][1] && times[i][2] > +d) {
			return times[i][0];
		}
	}
}

select_current_pd = function() {
	get_times_array();
	var c = check_current_pd(new Date());
	$('.schedule-tbl .schedule-day').removeClass('now');
	$('.schedule-tbl .schedule-day[data-type="'+c+'"]').addClass('now');
}

init_dayschedule = function() {
	select_current_pd();
	setInterval(select_current_pd, 30000)
}
