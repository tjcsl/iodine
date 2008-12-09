var ccwin = null;
var slider_moving = null;
var red_fg = 0, green_fg = 0, blue_fg = 0, red_bg = 0, green_bg = 0, blue_bg = 0;
var red_fg_slider_bar = null, red_bg_slider_bar = null, green_fg_slider_bar = null, green_bg_slider_bar = null, blue_fg_slider_bar = null, blue_bg_slider_bar = null;
var change_slider_colors = true;
function show_color_chooser() {
	var e = event;
	if(ccwin == null) {
		ccwin = document.createElement("div");
		ccwin.id = "color_chooser_window";
		ccwin.onmousedown = function (e) {
			if(e.target.className == "slider") {
				slider_moving = e.target;
			}
			else if(e.target.className == "slider_bar") {
				var bar_name = document.getElementById(e.target.id.replace(/_bar/, ""));
				bar_name.style.left = (e.clientX - ccwin.offsetLeft - 3) + "px";
				eval(bar_name.id.replace(/_slider/, "") + " = " + (e.clientX - ccwin.offsetLeft - 27));
				update_color_chooser_display();
			}
		}
		ccwin.onmousemove = function (e) {
			var mouse_x = e.clientX - ccwin.offsetLeft, mouse_y = e.clientY - ccwin.offsetTop;
			if(slider_moving != null && 24 < mouse_x && mouse_x < 281 && slider_moving.offsetTop - 10 < mouse_y && mouse_y < slider_moving.offsetTop + slider_moving.offsetHeight + 10) {
				slider_moving.style.left = (mouse_x - 3) + "px";
				eval(slider_moving.id.replace(/_slider/, "") + " = " + (mouse_x - 25));
				update_color_chooser_display();
			}
		}
		ccwin.onmouseup = function(e) {
			slider_moving = null;
		}
		/* Red Foreground Slider */
		red_fg_slider_bar = document.createElement("div");
		red_fg_slider_bar.className = "slider_bar";
		red_fg_slider_bar.id = "red_fg_slider_bar";
		ccwin.appendChild(red_fg_slider_bar);
		var red_fg_slider = document.createElement("div");
		red_fg_slider.className = "slider";
		red_fg_slider.id = "red_fg_slider";
		ccwin.appendChild(red_fg_slider);

		/* Green Foreground Slider */
		green_fg_slider_bar = document.createElement("div");
		green_fg_slider_bar.className = "slider_bar";
		green_fg_slider_bar.id = "green_fg_slider_bar";
		ccwin.appendChild(green_fg_slider_bar);
		var green_fg_slider = document.createElement("div");
		green_fg_slider.className = "slider";
		green_fg_slider.id = "green_fg_slider";
		ccwin.appendChild(green_fg_slider);

		/* Blue Foreground Slider */
		blue_fg_slider_bar = document.createElement("div");
		blue_fg_slider_bar.className = "slider_bar";
		blue_fg_slider_bar.id = "blue_fg_slider_bar";
		ccwin.appendChild(blue_fg_slider_bar);
		var blue_fg_slider = document.createElement("div");
		blue_fg_slider.className = "slider";
		blue_fg_slider.id = "blue_fg_slider";
		ccwin.appendChild(blue_fg_slider);

		/* Red Background Slider */
		red_bg_slider_bar = document.createElement("div");
		red_bg_slider_bar.className = "slider_bar";
		red_bg_slider_bar.id = "red_bg_slider_bar";
		ccwin.appendChild(red_bg_slider_bar);
		var red_bg_slider = document.createElement("div");
		red_bg_slider.className = "slider";
		red_bg_slider.id = "red_bg_slider";
		ccwin.appendChild(red_bg_slider);

		/* Green Background Slider */
		green_bg_slider_bar = document.createElement("div");
		green_bg_slider_bar.className = "slider_bar";
		green_bg_slider_bar.id = "green_bg_slider_bar";
		ccwin.appendChild(green_bg_slider_bar);
		var green_bg_slider = document.createElement("div");
		green_bg_slider.className = "slider";
		green_bg_slider.id = "green_bg_slider";
		ccwin.appendChild(green_bg_slider);

		/* Blue Background Slider */
		blue_bg_slider_bar = document.createElement("div");
		blue_bg_slider_bar.className = "slider_bar";
		blue_bg_slider_bar.id = "blue_bg_slider_bar";
		ccwin.appendChild(blue_bg_slider_bar);
		var blue_bg_slider = document.createElement("div");
		blue_bg_slider.className = "slider";
		blue_bg_slider.id = "blue_bg_slider";
		ccwin.appendChild(blue_bg_slider);

		/* RGB Display */
		var rgb_slider_display = document.createElement("div");
		rgb_slider_display.className = "slider_display";
		rgb_slider_display.id = "rgb_slider_display";
		ccwin.appendChild(rgb_slider_display);

		/* Select Button */
		var select_button = document.createElement("div");
		select_button.id = "color_chooser_select_button";
		select_button.innerHTML = "Select";
		select_button.onmouseup = function(e) {
			ccwin.style.display = "none";
		};
		ccwin.appendChild(select_button);

		/* Cancel Button */
		var cancel_button = document.createElement("div");
		cancel_button.id = "color_chooser_cancel_button";
		cancel_button.innerHTML = "Cancel";
		cancel_button.onmouseup = function(e) {
			ccwin.style.display = "none";
		};
		ccwin.appendChild(cancel_button);

		document.body.appendChild(ccwin);
	}
	ccwin.style.display = "block";
	ccwin.style.left = e.clientX + "px";
	ccwin.style.top = (e.clientY - 150) + "px";
	update_color_chooser_display();
}

function update_color_chooser_display() {
	var rgb = document.getElementById("rgb_slider_display");
	rgb.innerHTML = "Foreground: #" + (red_fg < 16 ? "0" : "") + red_fg.toString(16) + (green_fg < 16 ? "0" : "") + green_fg.toString(16) + (blue_fg < 16 ? "0" : "") + blue_fg.toString(16) + "<br />Background: #" + (red_bg < 16 ? "0" : "") + red_bg.toString(16) + (green_bg < 16 ? "0" : "") + green_bg.toString(16) + (blue_bg < 16 ? "0" : "") + blue_bg.toString(16);
	rgb.style.backgroundColor = "#" + (red_bg < 16 ? "0" : "") + red_bg.toString(16) + (green_bg < 16 ? "0" : "") + green_bg.toString(16) + (blue_bg < 16 ? "0" : "") + blue_bg.toString(16);
	rgb.style.color = "#" + (red_fg < 16 ? "0" : "") + red_fg.toString(16) + (green_fg < 16 ? "0" : "") + green_fg.toString(16) + (blue_fg < 16 ? "0" : "") + blue_fg.toString(16);
	if(change_slider_color) {
		red_fg_slider_bar.style.backgroundColor = "#" + (red_fg < 16 ? "0" : "") + red_fg.toString(16) + "0000";
		red_bg_slider_bar.style.backgroundColor = "#" + (red_bg < 16 ? "0" : "") + red_bg.toString(16) + "0000";
		green_fg_slider_bar.style.backgroundColor = "#00" + (green_fg < 16 ? "0" : "") + green_fg.toString(16) + "00";
		green_bg_slider_bar.style.backgroundColor = "#00" + (green_bg < 16 ? "0" : "") + green_bg.toString(16) + "00";
		blue_fg_slider_bar.style.backgroundColor = "#0000" + (blue_fg < 16 ? "0" : "") + blue_fg.toString(16);
		blue_bg_slider_bar.style.backgroundColor = "#0000" + (blue_bg < 16 ? "0" : "") + blue_bg.toString(16);
	}
}
