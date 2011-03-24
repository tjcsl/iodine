[<if $user->uid == $I2_USER->uid>]
<strong>This is YOUR info page.  All of your information will ALWAYS be visible to you.</strong><br />
In order to choose what info can be seen by OTHER users, please setup your <a href="[<$I2_ROOT>]prefs">preferences</a>.<br /><br />
[<elseif $I2_USER->grade == "staff">]
<strong>As a member of the TJ faculty, you are permitted to see all information, regardless of privacy settings.  Please be aware of this.</strong><br /><br />
[</if>]
[<if $is_admin>]
<strong>This person is an Intranet Administrator, please contact [<if $user->sex == "M">]him[<elseif $user->sex == "F">]her[<else>]him/her[</if>] with any problems you encounter.</strong><br /><br />
[</if>]
<table>
<tr>
<td valign="top">
<img src="[<$I2_ROOT>]pictures/[<$user->uid>]" vspace="2" alt="Student Picture" /><br />
<a href="[<$I2_ROOT>]studentdirectory/pictures/[<$user->uid>]">View pictures from all years</a>
[<if $im_an_admin>]
<br /><br />
Username: [<$user->iodineuid>]<br />
<a href="[<$I2_ROOT>]groups/view/[<$user->uid>]">View this user's groups</a>
[</if>]
[<if $user->grade != 'staff'>]
[<if $homecoming_may_vote>]<br /><br /><strong><a href="[<$I2_ROOT>]homecoming/vote/[<$user->uid>]">Vote for this person<br />for homecoming court</a></strong>[</if>]
[</if>]
</td>
<td valign="top">
[<$user->fullname>][<if $user->grade != 'staff'>], Grade [<$user->grade>][<else>], on staff[</if>]<br />
[<if $user->bdate>]Born [<$user->bdate>]<br />[</if>]
[<if $user->counselor>]Counselor: [<$user->counselor_name>]<br />[</if>]
<br />
[<if $user->homePhone || $user->phone_cell || count($user->phone_other)>]
Phone number(s):
 <ul class="none">
 [<if $user->homePhone>][<foreach from=$user->phone_home item=phone>]<li><a href="tel:[<$user->phone_home|replace:' ':''>]">[<$phone>] (Home)</a></li>[</foreach>][</if>]
 [<if $user->phone_cell>]<li><a href="tel:[<$user->phone_cell|replace:' ':''>]">[<$user->phone_cell>] (Cell)</a></li>[</if>]
 [<if count($user->phone_other)>][<foreach from=$user->phone_other item=phone_other>]<li><a href="tel:[<$user->phone_other|replace:' ':''>]">[<$phone_other>] (Other)</a></li>[</foreach>][</if>]
 </ul>
[</if>]
[<if $user->street>]
 [<$user->street>]<br />
 [<$user->l>], [<$user->st>] [<$user->postalCode>]<br />
 [<if $user->address2_street>]
  2nd address:<br />
  [<$user->address2_street>]<br />
  [<$user->address2_city>], [<$user->address2_state>] [<$user->address2_zip>]<br />
 [</if>]
 [<if $user->address3_street>]
  3rd address:<br />
  [<$user->address3_street>]<br />
  [<$user->address3_city>], [<$user->address3_state>] [<$user->address3_zip>]<br />
 [</if>]
[</if>]
<br />
[<if $user->mail>]
E-mail address(es): 
[<foreach from=$maillist item="email" name="emails">]
	[<if $smarty.foreach.emails.last and not $smarty.foreach.emails.first>]
		and
	[<elseif not $smarty.foreach.emails.first>]
		,
	[</if>]
	[<mailto address=$email encode="hex">]
	[</foreach>]
<br />
[</if>]
[<if $user->grade != 'staff'>]
[<if $I2_USER->grade == 'staff'>]
<br />
To view this user's portfolio click <a href="https://shares.tjhsst.edu/PORTFOLIO/[<$user->graduationyear>]/[<$user->iodineuid>]/">here</a>.
<br />
[</if>]
[</if>]
<br />

[<include file="studentdirectory/im_status.tpl">]


[<if count($user->webpage) > 1>]Webpages:[</if>]
[<if count($user->webpage) == 1>]Webpage:[</if>]
[<if count($user->webpage)>]
 <ul>
 [<foreach from=$user->webpage key=id item=webpage>]
  <li><a href="[<$webpage|escape:'html'>]" id="webpage_display_[<$id>]">[<$webpage>]</a></li>
 [</foreach>]
 </ul>
 <script src="[<$I2_ROOT>]www/js/ajax.js"></script>
 <script>
 http = createRequestObject();
 http.onreadystatechange = function(aEvt) {
        if(http.readyState == 4 && http.status == 200) {
                count = parseInt(http.responseText.split('\n')[0]);
                for(i = 0; i < count; i++)
                {
                   document.getElementById('webpage_display_' + i).innerHTML = http.responseText.split('\n')[i + 1];
                }
        }
 };
 sendReq(http, 'webpage_title/[<$user->username>]');
</script>
[</if>]
[<if $user->locker>]Locker Number: [<$user->locker|escape:'html'>]<br />[</if>]
[<if $user->college>]<br />College: [<$user->college>]<br />[</if>]
[<if $user->major>]Major: [<$user->major>]<br />[</if>]
</td>
</tr>
</table>

[<* Disabled to appease the administration. -- AES 6/14/06 *>]
[<* Reenabled for members of admin_all, did not ask administration, but it should be ok. -- 11/11/09 *>]
[<* Redisabled for admin_all because of potential problems. -- 11/14/09 *>]
[<if $user->street && $user->show_map>]
 <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAPYn494d47HCCt6Y72eRhjRTyVqJo5zK-dNka2EGhn8GD1IZjtBQvAgUtM7M1VxbN0qo5YqjV4SFu5g" type="text/javascript"></script>
 <script type="text/javascript">
 var map=null;
 var userLocation = '[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]';
 var mapcanvas=null;
 var mapbutton=null;
 var directions=null;
 var directionsPanel=null;
 var geocoder=null;
 var marker=null;
 //var geoXML=null;
 //loadLatitude();
 var maphidden=1;
 var dirhidden=1;
 var markerhidden=1;
 var pathhidden=1;
 function initialize() {
 	if(GBrowserIsCompatible()) {
		if(!mapcanvas)
			mapcanvas=document.getElementById("map_canvas");
 		mapcanvas.style.width="100%";
 		mapcanvas.style.height="400px";
 		mapcanvas.style.visibility="visible";
		if(!mapbutton)
			mapbutton=document.getElementById("map_button");
 		mapbutton.onclick=hideMap;
 		mapbutton.childNodes[0].data="hide map";
		if(!map) {
 			map = new GMap2(mapcanvas);
 			map.setMapType(G_NORMAL_MAP);
 			map.addControl(new GSmallMapControl());
		}
		if(!geocoder) {
	 		geocoder = new GClientGeocoder();
		}
 		showAddress(userLocation,0);
		if(!directions) {
 			directionsPanel = document.getElementById("map_directions");
 			directions = new GDirections(map, directionsPanel);
		}
 		maphidden=0;
 	}
 }
 function initializeNoMarker() {
 	if(GBrowserIsCompatible()) {
		if(!mapcanvas)
			mapcanvas=document.getElementById("map_canvas");
 		mapcanvas.style.width="100%";
 		mapcanvas.style.height="400px";
 		mapcanvas.style.visibility="visible";
		if(!mapbutton)
			mapbutton=document.getElementById("map_button");
 		mapbutton.onclick=hideMap;
 		mapbutton.childNodes[0].data="hide map";
		if(!map) {
 			map = new GMap2(mapcanvas);
 			map.setMapType(G_NORMAL_MAP);
 			map.addControl(new GSmallMapControl());
		}
		if(!geocoder) {
 			geocoder = new GClientGeocoder();
		}
 		showAddress(userLocation,1);
		if(!directions) {
 			directionsPanel = document.getElementById("map_directions");
 			directions = new GDirections(map, directionsPanel);
		}
 		maphidden=0;
 	}
 }
 function hideMap() {
 	mapcanvas.style.width="0";
 	mapcanvas.style.height="0";
 	mapcanvas.style.visibility="hidden";
 	mapbutton.onclick=initialize;
 	mapbutton.childNodes[0].data="show map";
	maphidden=1;
 	if(dirhidden==0) {
 		directionsPanel.style.visibility="hidden";
 		directionsPanel.style.width="0";
 		directionsPanel.style.height="0";
 		dirhidden=1;
 	}
	if(pathhidden==0) {
		directions.clear();
		pathhidden=1;
	}
 }
 function makeDirections(address1,address2) {
 	if(maphidden==1) {
 		initializeNoMarker();
 	}
	if(markerhidden==0) {
		map.removeOverlay(marker);
		markerhidden=1;
	}
 	if(directions) {
 		if(dirhidden==1) {
 			directionsPanel.style.visibility="visible";
 			directionsPanel.style.width="";
 			directionsPanel.style.height="";
 		}
 		dirhidden=0;
 		directions.load("from: " + address1 + " to: " + address2);
		pathhidden=0;
 	}
 }
 function showAddress(address,nomarker) {
 	if(geocoder) {
 		geocoder.getLatLng(
 			address,
 			function(point) {
 				if (!point) {
 					alert(address + " not found");
 				} else {
 					map.setCenter(point, 13);
 					if(nomarker==0) {
						if(markerhidden==0) {
							map.removeOverlay(marker);
							markerhidden
						}
 						marker = new GMarker(point);
 						map.addOverlay(marker);
 						marker.openInfoWindowHtml("[<if $I2_USER->name == $user->name>]Your house[<else>][<$user->name>][</if>]<br />"+address);
						markerhidden=0;
 					}
 				}
 			}
 		);
 	}
 }
 // Google Latitude Support. Good luck getting this accepted. :P
 // Currently it doesn't work for some reason, which is why it's commented out.
 // If you can figure out why, go ahead. This code was working a while ago,
 // but Google changed something in their code, and now it not work. :(
/* function loadLatitude() {
 	if(!geoXML) {
 		geoXML= new GGeoXml("https://www.google.com/latitude/apps/badge/api?user=[<$user->latitude>]&type=kml");
 		setTimeout("latitudeNotify();",1);
 	}
	/*if(!geoXML.hasLoaded()) {
 		document.getElementById("map_button").childNodes[0].data="loading Google Latitude data...";
 	}/
 }
 function latitudeNotify() {
	if(geoXML.hasLoaded()) {
 		//document.getElementById("map_button").childNodes[0].data="Google Latitude data loaded.";
		setTimeout("resetMessage();",1000);
	} else {
 		//document.getElementById("map_button").childNodes[0].data="Google Latitude data loading...";
 		setTimeout("latitudeNotify();",10);
 	}
 }
 function resetMessage() {
 	//document.getElementById("map_button").childNodes[0].data="hide map";
 }
 function showGoogleLatitude() {
	if(maphidden==1) {
		initializeNoMarker();
		setTimeout("realLatitude();",500);
	} else
	 	realLatitude();
 }
 function realLatitude() {
 	map.addOverlay(geoXML);
 	document.getElementById("map_button").childNodes[0].data=""+geoXML.getDefaultCenter();
 	map.setCenter(geoXML.getDefaultCenter(),12);
 }
 function fromUserToMe() {
 	makeDirections(""+geoXML.getDefaultCenter().lat()+","+geoXML.getDefaultCenter().lng(),""+geoXML.getDefaultCenter().lat()+","+geoXML.getDefaultCenter().lng());
 }
 function fromHomeToMe() {
 	makeDirections('[<$I2_USER->street>], [<$I2_USER->l>], [<$I2_USER->st>] [<$I2_USER->postalCode>]',""+geoXML.getDefaultCenter().lat()+","+geoXML.getDefaultCenter().lng());
 }
 function fromSchoolToMe() {
 	makeDirections("6560 Braddock Rd, Alexandria, VA 22312",""+geoXML.getDefaultCenter().lat()+","+geoXML.getDefaultCenter().lng());
 }
 function getMeHome() {
	makeDirections(""+geoXML.getDefaultCenter().lat()+","+geoXML.getDefaultCenter().lng(),userLocation);
 }
 function getMeSchool() {
 	makeDirections(""+geoXML.getDefaultCenter().lat()+","+geoXML.getDefaultCenter().lng(),"6560 Braddock Rd, Alexandria, VA 22312");
 }*/
 </script>
 <a id="map_button" onclick="initialize()">show map</a><br />
 Get directions:
 [<if $I2_USER->uid != $user->uid>]
  [<if isset($I2_USER->street) >]
  <a onclick="makeDirections('[<$I2_USER->street>], [<$I2_USER->l>], [<$I2_USER->st>] [<$I2_USER->postalCode>]','[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]')">from your home</a>
  or [</if>]<a onclick="makeDirections('6560 Braddock Rd, Alexandria, VA 22312','[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]')">from school</a>
 [<else>]
 <a onclick="makeDirections('[<$user->street>], [<$user->l>], [<$user->st>] [<$user->postalCode>]','6560 Braddock Rd, Alexandria, VA 22312')">to school</a>
 [</if>]
 [<* This is shorted out until someone wants to get that js up there working *>]
 [<if 1==0 && $user->latitude !=0>]
  [<if $I2_USER->uid == $user->uid>]
   <br />Get directions from me:
   <a onclick="getMeHome()">to home</a>
   or
   <a onclick="getMeSchool()">to school</a>
  [<else>]
   <br />Get directions to me:
   [<if $I2_USER->latitude != 0>]
    <a onclick="fromUserToMe()">from your location</a>
    or
   [</if>]
   <a onclick="fromHomeToMe()">from your home</a>
   or
   <a onclick="fromSchoolToMe()">from school</a>
  <br />Find <a onclick="showGoogleLatitude()">Current location (Google Latitude)</a>
  [</if>]
 [</if>]
 <div id="map_canvas" style="visibility: hidden"></div>
 <div id="map_directions" style="visibility: hidden"></div>
 <br />
[</if>]

[<if $mode != 'off' && $schedule && count($schedule) > 0 >]
[<if ($mode == 'skeletal' && $user->grade != 'staff') || $mode == 'full' >]
<div style="float: left; margin-right: 50px;">
 <br /><span class="bold">Classes:</span><br />
 <table cellspacing="0">
  <thead>
   <tr>
    <th>Pd</th>
    <th>Name</th>
    <th>Rm</th>
    [<if $mode == 'full'>]
    <th>Teacher</th>
    [</if>]
    <th>Quarter(s)</th>
   </tr>
  </thead>
  <tbody>
 [<if $mode == 'full'>]
 [<foreach from=$schedule item=class>]
   <tr class="[<cycle values="c1,c2">]">
    <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$class->sectionid>]">[<$class->periods>]</a></td>
    <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/class/[<$class->sectionid>]">[<$class->name>]</a></td>
    <td class="directory-table" style="text-align:center;">[<$class->room>]</td>
    <td class="directory-table"><a href="[<$I2_ROOT>]studentdirectory/info/[<$class->teacher->uid>]">[<$class->teacher->sn>]</a></td>
    <td class="directory-table">[<$class->term>]</td>
   </tr>
 [</foreach>]
 [<else>]
 [<foreach from=$schedule item=class>]
   <tr class="[<cycle values="c1,c2">]">
    <td class="directory-table">[<$class->period>]</td>
    <td class="directory-table">[<$class->name>]</td>
    <td class="directory-table" style="text-align:center;">[<$class->room>]</td>
    <td class="directory-table">[<$class->term>]</td>
   </tr>
 [</foreach>]
 [</if>]
  </tbody>
 </table>
</div>
[</if>]
[</if>]

[<if $eighth>]
 <br /><span class="bold">Eighth Periods:</span><br />
 <table cellspacing="0">
  <tr>
   <th>Date</th>
   <th>Activity</th>
   <th>Room(s)</th>
  </tr>
 [<foreach from=$eighth item=activity>]
  <tr class="[<cycle values="c1,c2">]">
   <td class="directory-table">[<$activity->block->date|date_format>], [<$activity->block->block>] Block</td>
   <td class="directory-table">[<if $activity->aid != 999>]<a href="[<$I2_ROOT>]eighth/vcp_schedule/roster/bid/[<$activity->bid>]/aid/[<$activity->aid>]">[<$activity->name_full_r>]</a>[<else>]HAS NOT SELECTED AN ACTIVITY[</if>]</td>
   <td class="directory-table">[<$activity->block_rooms_comma>]</td>
  </tr>
 [</foreach>]
 </table>
[</if>]
