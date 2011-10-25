<script type="text/javascript">
	var openedRow = null;

	function filterList(txt) {
		txt = txt.toLowerCase();
		txt = txt.split(" or ");
		
		var currentList = document.getElementById("activityList");
		var listItems = currentList.childNodes;
		for (var i = 0; i < listItems.length; i++) {
			if(listItems[i].nodeType != 1 || listItems[i].className.indexOf('activityRow') == -1) {
				continue;
			}
			
			var selected = false;
			
			for (var j = 0; j < txt.length; j++) {
				if ((listItems[i].innerHTML.toLowerCase().indexOf(txt[j]) != -1) || // check the activity name
				    (listItems[i].getAttribute("data-room") != null && listItems[i].getAttribute("data-room").toLowerCase().indexOf(txt[j]) != -1) || // check the room number
				    (listItems[i].getAttribute("data-sponsor") != null && listItems[i].getAttribute("data-sponsor").toLowerCase().indexOf(txt[j]) != -1)) { // check the sponsor
					selected = true;
					break;
				}
			}

			if (!selected) {
				listItems[i].style.display = "none";
				if (listItems[i] == openedRow) {
					activityRowClear();
				}
			} else {
				listItems[i].style.display = "block";
			}
		}
	}

	function nextRealSibling(row) {
		while(row && row.nodeType != 1)
			row = row.nextSibling;
		return row;
	}

	function activityRowSelect(row) {
		activityRowClear();
		
		openedRow = row;
		if(!row.baseClassName)
			row.baseClassName = row.className;
		row.className += " clickedAR";
		var ns = nextRealSibling(row.nextSibling);
		ns.style.height = nextRealSibling(ns.firstChild).scrollHeight+'px';
		document.getElementById('aid_box').value = row.className.substring(0, row.className.indexOf('_AID'));
	}

	function activityRowClear() {
		if(openedRow) {
			openedRow.className = openedRow.baseClassName;
			nextRealSibling(openedRow.nextSibling).style.height = '0';
			openedRow = null;
		}
	}

	function activityRowClicked(row) {
		if(openedRow != row) {
			activityRowSelect(row);
		} else {
			activityRowClear();
		}
	}
</script>
<!-- 	Note to I2 devs: identifying information (###_AID) is stored in the class name rather than in the ID because rows can be repeated and thus ###_AID would not be unique. 
	I could have used a nonstandard attribute or added a nonce to the ###_AID formation, but I think both of those would have been uglier than the current solution.		-->
<form name="activity_select_form" action="[<$I2_ROOT>]eighth/vcp_schedule/change/uid/[<$uid>]/bids/[<$bids>][<if $start_date != NULL>]/start_date/[<$start_date>][</if>]" method="post">
	<input type="hidden" name="aid" id="aidInput" />
	<div id="activityList">
		[<if $selected>]
			[<include file="eighth/vcp_schedule_choose_section.tpl"
				sTitle="Selected"
				sID="selected"
				activities=$selected>]
		[</if>]
		[<if $favorites>]
			[<include file="eighth/vcp_schedule_choose_section.tpl"
				sTitle="Favorites"
				sID="favs"
				activities=$favorites>]
		[</if>]
		[<if $filling>]
			[<include file="eighth/vcp_schedule_choose_section.tpl"
				sTitle="Almost Full"
				sID="filling"
				activities=$filling>]
		[</if>]
		[<if $general>]
			[<include file="eighth/vcp_schedule_choose_section.tpl"
				sTitle="All Activities"
				sID="general"
				activities=$general>]
		[</if>]
		[<if $full>]
			[<include file="eighth/vcp_schedule_choose_section.tpl"
				sTitle="Filled"
				sID="full"
				activities=$full>]
		[</if>]
		[<if $restricted>]
			[<include file="eighth/vcp_schedule_choose_section.tpl"
				sTitle="Restricted"
				sID="restricted"
				activities=$restricted>]
		[</if>]
		[<if $cancelled>]
			[<include file="eighth/vcp_schedule_choose_section.tpl"
				sTitle="Cancelled"
				sID="cancelled"
				activities=$cancelled>]
		[</if>]

	</div>
	<div style="padding:.5em;">
		<input type="text" name="aid" id="aid_box" maxlength="4" size="4" value="[<$selected_aid>]" />
		<input type="submit" name="submit" value="Change"/>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<script type="text/javascript">
			if (document.createElement("input").placeholder == null) { // if placeholder text will not show up,
				document.write("<label for=\"filterActivities\">Search</label>"); // ----------------- display a label
			}
		</script>
		<input size="50" id='filterActivities' type="search" results="0" placeholder=" Search for an activity" onchange="filterList(value);" onkeyup="filterList(value);" onsearch="filterList(value);"/>
		<span style="display:none;">&larr; OMG SEARCH BOX!</span>
		<div style="margin-top:4px;">
			<b>Color Key:</b>
			[<if empty($manybids)>]<span class="selectedAR even actkey"[<if $selected>] onclick="document.getElementById('section_selected').scrollIntoView(true);" style="cursor:pointer;"[</if>]>your choice</span>[</if>]
			<span class="generalAR odd actkey"[<if $general>] onclick="document.getElementById('section_general').scrollIntoView(true);" style="cursor:pointer;"[</if>]>normal</span>
			<span class="fillingAR odd actkey"[<if $filling>] onclick="document.getElementById('section_filling').scrollIntoView(true);" style="cursor:pointer;"[</if>]>almost full</span>
			<span class="fullAR odd actkey"[<if $full>] onclick="document.getElementById('section_full').scrollIntoView(true);" style="cursor:pointer;"[</if>]>full</span>
			<span class="cancelledAR odd actkey"[<if $cancelled>] onclick="document.getElementById('section_cancelled').scrollIntoView(true);" style="cursor:pointer;"[</if>]>cancelled</span>
			<span class="restrictedAR odd actkey"[<if $restricted>] onclick="document.getElementById('section_restricted').scrollIntoView(true);" style="cursor:pointer;"[</if>]>restricted</span>
			<span class="favoriteAR odd actkey"[<if $favorites>] onclick="document.getElementById('section_favs').scrollIntoView(true);" style="cursor:pointer;"[</if>]>favorite</span>
		</div>
	</div>
</form>

<script type="text/javascript">
	// If the client is a mobile device,
	// get rid of the scrolling on the activities list
	// since that's a bit hard to use on iOS or Android
	
	if(navigator.userAgent.indexOf('Mobile')!=-1) {
		var al = document.getElementById('activityList');
		al.style.height = 'auto';
		al.style.overflow = 'visible';
	}
</script>
