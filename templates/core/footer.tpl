<div id="MMcontainer" class="MMhide">
		<div id="MMinner">
			<div id="MMclock">
				<!-- Majora's Mask Clock -->
				<!-- For TJHSST Iodine by Dylan Ladwig -->
				<img src="https://www.tjhsst.edu/~2011dladwig/majoraclock/first.png" id="MMday1" alt="First Day" class="MMhide" />
				<img src="https://www.tjhsst.edu/~2011dladwig/majoraclock/second.png" id="MMday2" alt="Second Day" class="MMhide" />

				<img src="https://www.tjhsst.edu/~2011dladwig/majoraclock/final.png" id="MMday3" alt="Final Day" class="MMhide" />
				<img src="https://www.tjhsst.edu/~2011dladwig/majoraclock/moon.png" id="MMmoon" alt="Moon" class="MMhide" />
				<img src="https://www.tjhsst.edu/~2011dladwig/majoraclock/sun.png" id="MMsun" alt="Sun" class="MMhide" />
				<img src="https://www.tjhsst.edu/~2011dladwig/majoraclock/starburst.png" id="MMstarburst" alt="Starburst" />
			</div>
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
			var target = new Date(2011,5,19); // this should be the day after "FINAL DAY"
			var today = new Date();

			var daysLeft=Math.ceil((target.getTime()-today.getTime())/(1000*60*60*24));
			if(daysLeft<=3 && daysLeft>0) {
				document.getElementById('MMcontainer').style.display='block';
			} else {
				document.getElementById('MMcontainer').style.display='none';
				return;
			}

			document.getElementById('MMday1').style.display = (daysLeft==3?'block':'none');
			document.getElementById('MMday2').style.display = (daysLeft==2?'block':'none');
			document.getElementById('MMday3').style.display = (daysLeft==1?'block':'none');

			var minutes = today.getMinutes()+today.getSeconds()/60;
			var hours = today.getHours()+minutes/60;

			setPositionCircle('MMstarburst',128-32/2,75-32/2,  35,(minutes-15)*Math.PI/30);
			
			setPositionEllipse('MMsun', 128-36/2,75-40/2,  80,50,(hours%12-12)*Math.PI/12);
			setPositionEllipse('MMmoon',128-36/2,75-36/2,  80,50,(hours%12-12)*Math.PI/12);

			document.getElementById('MMsun').style.display = (today.getHours()<12?'block':'none');
			document.getElementById('MMmoon').style.display = (today.getHours()>=12?'block':'none');

		}
		updateClock()

		setInterval(updateClock,100);
	</script>

<embed src="http://www.tjhsst.edu/~2011pgodofsk/zelda/1-08%20Clock%20Town_%20Day%201.mp3" loop="true" autostart="true" type="text/mpeg" hidden="true" volume="70"/>
