[<include file="eighth/header.tpl">]
<h3>Enter Room Name: (e.x. 115)</h2>
<form name="typebyroom" action="[<$I2_ROOT>]eighth/vp_room/byroom/type/q/">
    <input name="q" value="" /><br />
    <input name="qbefore" type="checkbox" value="1" checked /> Show All Matches ("gym" matches "Main Gym", "227" matches "225-227 (60)")<br />
    <input type="submit" value="Search" />
</form>
<h3>or Choose Specific Room:</h3>
    <button onclick="location.href='[<$I2_ROOT>]eighth/vp_room/byroom/type/rid/'">View Room Listing</button>
