window.onload=init;
function init() {
var i = document.createElement("span");
var j = document.createTextNode("[<$I2_USER->username>]@iodine:/$ ");
i.appendChild(j);
document.getElementById("terminal").appendChild(i);
}
