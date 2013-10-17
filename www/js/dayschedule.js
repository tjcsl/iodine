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
