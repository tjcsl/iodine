<iframe id="sgdframe" style="width: 100%; height: 600px; border: none;" src="[<$I2_ROOT>]sgd/form" ></iframe>
[<* commented out <script type="text/javascript">
var sgdframe = document.getElementById('sgdframe');
// If the user is already logged into sgd, they will get a blank page. This stops that.
function checkifloggedin() {
	if (sgdframe.firstChild == null) {
		sgdframe.src="https://sun.tjhsst.edu/sgd/";
	} else {
		alert("Last child is " + sgdframe.lastChild + ", trying again");
		setTimeout("checkifloggedin()",2000);
	}
}
setTimeout("checkifloggedin()",5000);
</script> *>]
