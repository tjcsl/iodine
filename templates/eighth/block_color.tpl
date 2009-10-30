<style type="text/css">
table#colorpicker span {
	display: block;
	float: right;
	width: 32px;
	height: 32px;
	background-color: attr(type, color);
}
table#colorpicker input {
	margin-top: 10px;
}
table#colorpicker th {
	text-align: center;
}
</style>
</head>
<form action="[<$I2_ROOT>]eighth/prn_attendance/format/bid/[<$bid>]" method="post">
<table id="colorpicker">
	<tr>
		<th colspan="4">Select a color for the block letter</th>
	</tr><tr>
		<td><span type="black"></span><input type="radio" name="color" value="0-0-0" checked="checked"/></td>
		<td><span type="darkorange"></span><input type="radio" name="color" value="1-0.56-0"/></td>
		<td><span type="#C8A2C8"></span><input type="radio" name="color" value="0.78-0.64-0.78"/></td>
		<td><span type="deeppink"></span><input type="radio" name="color" value="1-0.08-0.58"/></td>
	</tr>
	<tr>
		<td><span type="#FFBF00"></span><input type="radio" name="color" value="1-0.76-0"/></td>
		<td><span type="blue"></span><input type="radio" name="color" value="0-0-1"/></td>
		<td><span type="green"></span><input type="radio" name="color" value="0-0.5-0"/></td>
		<td><span type="salmon"></span><input type="radio" name="color" value="0.98-0.5-0.45"/></td>
	</tr>
</table>
<!--<input type="checkbox" name="outline" id="outline" disabled="disabled"/><label for="outline">Outline</label><br />-->
<input type="submit" value="Next page" />
</form>
<script type="text/javascript">
var table = document.getElementById('colorpicker');
var rows = table.rows;
for (var i = 1; i < rows.length; i++) {
	var row = rows.item(i);
	for (var j=0; j < row.cells.length; j++) {
		var cell = row.cells.item(j);
		var span = cell.firstChild;
		span.style.backgroundColor = span.getAttribute("type");
	}
}
</script>
