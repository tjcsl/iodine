<script type="text/javascript">
	var openedRow = null;
	var mobileNoScroll = false;

	function filterList(txt) {
		txt = txt.toLowerCase();
		txt = txt.split(" or ");
		// code for "AND" operator; not useful if text is not split every space
		/*for (var i = 0; i < txt.length - 2; i++) {
			if (txt[i + 1] == "and") {
				txt[i] = txt[i] + " " + txt[i + 2];
				txt.splice((i + 1), 2);
			}
		}*/
		
		var currentList = document.getElementById("activityList");
		var listItems = currentList.childNodes;
		for (var i = 0; i < listItems.length; i++) {
			if(listItems[i].nodeType != 1 || listItems[i].className.indexOf('activityRow') == -1) {
				continue;
			}
			
			var selected = false;
			
			for (var j = 0; j < txt.length; j++) {
				var negated = false;
				var query = txt[j];
				while (query.charAt(0) == "-") {
					query = query.substring(1);
					negated = !negated;
				}

				if ((listItems[i].innerHTML.toLowerCase().indexOf(query) != -1) || // check the activity name
				    (listItems[i].getAttribute("data-room") != null && listItems[i].getAttribute("data-room").toLowerCase().indexOf(query) != -1) || // check the room number
				    (listItems[i].getAttribute("data-sponsor") != null && listItems[i].getAttribute("data-sponsor").toLowerCase().indexOf(query) != -1) ||
				    (listItems[i].getAttribute("data-flags") != null && listItems[i].getAttribute("data-flags").toLowerCase().split(" ").indexOf(query) != -1)) { // check the flags
					selected = !negated;
					break;
				} else {
					selected = negated;
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
		document.getElementById('aid_box').value = row.getAttribute("data-aid");
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

	function scrollToSection(sectionName) {
		if(!mobileNoScroll) {
			var al = document.getElementById('activityList');
			var se = document.getElementById(sectionName);
			al.scrollTop = se.offsetTop-al.offsetTop;
		} else {
			document.getElementById(sectionName).scrollIntoView(true);
		}
	}
</script>
<form name="activity_select_form" action="[<$I2_ROOT>]eighth/vcp_schedule/change/uid/[<$uid>]/bids/[<$bids>][<if $start_date != NULL>]/start_date/[<$start_date>][</if>]" method="post">
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
		<input size="50" id='filterActivities' type="search" results="0" placeholder=" Search for an activity" oninput="filterList(value);" onsearch="filterList(value);"/>
		<span style="display:none;">&larr; OMG SEARCH BOX!</span>
		<div style="margin-top:4px;">
			<b>Color Key:</b>
			[<if empty($manybids)>]<span class="selectedAR even actkey"[<if $selected>] onclick="scrollToSection('section_selected');" style="cursor:pointer;"[</if>]>your choice</span>[</if>]
			<span class="generalAR odd actkey"[<if $general>] onclick="scrollToSection('section_general');" style="cursor:pointer;"[</if>]>normal</span>
			<span class="fillingAR odd actkey"[<if $filling>] onclick="scrollToSection('section_filling');" style="cursor:pointer;"[</if>]>almost full</span>
			<span class="fullAR odd actkey"[<if $full>] onclick="scrollToSection('section_full');" style="cursor:pointer;"[</if>]>full</span>
			<span class="cancelledAR odd actkey"[<if $cancelled>] onclick="scrollToSection('section_cancelled');" style="cursor:pointer;"[</if>]>cancelled</span>
			<span class="restrictedAR odd actkey"[<if $restricted>] onclick="scrollToSection('section_restricted');" style="cursor:pointer;"[</if>]>restricted</span>
			<span class="favoriteAR odd actkey"[<if $favorites>] onclick="scrollToSection('section_favs');" style="cursor:pointer;"[</if>]>favorite</span>
		</div>
	</div>
</form>

<script type="text/javascript">
	// If the client is a mobile device,
	// get rid of the scrolling on the activities list
	// since that's a bit hard to use on iOS or Android
	
	if(navigator.userAgent.indexOf('Mobile')!=-1) {
		mobileNoScroll = true;
		var al = document.getElementById('activityList');
		al.style.height = 'auto';
		al.style.overflow = 'visible';
	}
</script>
