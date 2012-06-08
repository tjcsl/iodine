<div id="MMcontainer">
	<div id="MMclock" class="MMhide">
		<!-- Majora's Mask Clock -->
		<!-- For TJHSST Iodine by Dylan Ladwig -->
		<img src="[<$I2_ROOT>]/www/pics/majora/background.png" alt="Background" />
		<img src="[<$I2_ROOT>]/www/pics/majora/first.png" id="MMday1" alt="First Day" class="MMhide" />
		<img src="[<$I2_ROOT>]/www/pics/majora/second.png" id="MMday2" alt="Second Day" class="MMhide" />
		<img src="[<$I2_ROOT>]/www/pics/majora/final.png" id="MMday3" alt="Final Day" class="MMhide" />
		<img src="[<$I2_ROOT>]/www/pics/majora/moon.png" id="MMmoon" alt="Moon" class="MMhide" />
		<img src="[<$I2_ROOT>]/www/pics/majora/sun.png" id="MMsun" alt="Sun" class="MMhide" />
		<img src="[<$I2_ROOT>]/www/pics/majora/starburst.png" id="MMstarburst" alt="Starburst" />
	</div>
	<div id="MMfinished" class="MMhide">
		<h3>Finished!</h3>
	</div>
</div>
<script type="text/javascript">
	function setPositionCircle(id,x,y,radius,angle) {
		document.getElementById(id).style.left = (x+Math.cos(angle)*radius)+"px";
		document.getElementById(id).style.top  = (y+Math.sin(angle)*radius)+"px";
	}
	function setPositionEllipse(id,x,y,hw,hh,angle) {
		document.getElementById(id).style.left = (x+Math.cos(angle)*hw)+"px";
		document.getElementById(id).style.top  = (y+Math.sin(angle)*hh)+"px";
	}
	function updateClock() {
		var target = new Date([<$time>]); // this should be the day of "FINAL DAY"
		var today = new Date();

		var daysLeft=Math.ceil((target.getTime()-today.getTime())/(1000*60*60*24));
		if(daysLeft<0||daysLeft>2) {
			document.getElementById('MMclock').style.display="none";
			document.getElementById('MMfinished').style.display="block";
			if(daysLeft>2)
				document.getElementById('MMfinished').innerText=(daysLeft-2)+" more days until the clock starts.";
		} else {
			document.getElementById('MMclock').style.display="inline-block";
			document.getElementById('MMfinished').style.display="none";
		}

		document.getElementById('MMday1').style.display = (daysLeft==2?'block':'none');
		document.getElementById('MMday2').style.display = (daysLeft==1?'block':'none');
		document.getElementById('MMday3').style.display = (daysLeft==0?'block':'none');

		var minutes = today.getMinutes()+today.getSeconds()/60;
		var hours = today.getHours()+minutes/60;

		setPositionCircle('MMstarburst',128-32/2,75-32/2,  35,(minutes-15)*Math.PI/30);
			
		setPositionEllipse('MMsun', 128-36/2,75-40/2,  80,50,(hours%12-12)*Math.PI/12);
		setPositionEllipse('MMmoon',128-36/2,75-36/2,  80,50,(hours%12-12)*Math.PI/12);

		document.getElementById('MMsun').style.display = (today.getHours()<12?'block':'none');
		document.getElementById('MMmoon').style.display = (today.getHours()>=12?'block':'none');

	}
	updateClock()

	setInterval(updateClock,1000);
</script>
