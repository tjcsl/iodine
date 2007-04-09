////////////////////////////////////////////////////////////////////////////////
//                   JAVASCRIPT DYNAMIC TABLE SORTING                         //
// Author: Joshua Cranmer <jcranmer@tjhsst.edu>                               //
// Last Updated: 04/08/07                                                     //
// Exported functions:                                                        //
// + sortTable(table, direction, index [, func])                              //
//   Arguments:                                                               //
//     + table: the table itself whose first body is to be sorted.            //
//     + direction: 1 if it should be sorted ascending, -1 if descending      //
//     + index: the zero-based index of the cell in the table to sort on      //
//     + func: an optional argument that is the function which returns the    //
//       value to be sorted on given the cell. It is given the cell and it    //
//       returns the value to sort on.                                        //
// -------------------------------------------------------------------------- //
// Note that the sort function will sort the entire body of the table, so     //
// make sure that any column headers or footers are in thead/tfoot blocks.    //
// Also note that the function will only sort the first tbody and not any     //
// subsequent ones, which is useful for if only the first part of the table   //
// is to be sorted.                                                           //
// -------------------------------------------------------------------------- //
// IMPORTANT: The function will not work properly if cellspans are used.      //
////////////////////////////////////////////////////////////////////////////////

function value(row) {
	var cell = row.cells.item(value.index);
	return parseFloat(value.func(cell));
}
function compare(row1, row2) {
	return (value(row1)-value(row2))*value.direction;
}
function compareString(row1, row2) {
	return  value.func(row1.cells.item(value.index)) >
		value.func(row2.cells.item(value.index)) ?
			value.direction : -value.direction;
}
function sortTable(table, index, direction) {
	value.index = index;
	value.direction = direction;
	if (arguments.length > 3) {
		value.func = arguments[3];
	} else {
		value.func = function(cell) {return cell.firstChild.nodeValue;}
	}
	var body = table.tBodies.item(0);
	var rows = new Array(body.rows.length);
	for (var i=0;i < body.rows.length; i++) {
		rows[i] = body.rows.item(i);
	}
	if (/^[0-9]+(\.[0-9]+)?$/.
	    test(value.func(rows[0].cells.item(value.index)))) {
		rows.sort(compare);
	} else {
		rows.sort(compareString);
	}
	for (var i=0;i<rows.length;i++) {
		body.appendChild(rows[i]);
	}
}
