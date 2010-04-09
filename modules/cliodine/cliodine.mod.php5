<?php
/**
* Just contains the definition for the {@link Module} CLIodine.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package modules
* @subpackage Info
* @filesource
*/

/**
* A module to make a cli-ish interface for iodine. This basically overrides display, except it doesn't.
* @package modules
* @subpackage CLIodine
*/
class CLIodine implements Module {
	//Static responses.
	var $singles;
	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function init_mobile() {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return FALSE;
	}

	/**
	* Unused; Not supported for this module.
	*/
	function init_cli() {
		return "cliodine";
	}

	/**
	* Unused; Not supported for this module.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_cli($disp) {
		return "<div>Sorry, no recursion!</div>\n";
	}

	/**
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_box($disp) {
		return FALSE;
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		global $I2_ARGS,$I2_ROOT,$I2_USER;
		if( !isset($I2_ARGS[1]) ){
			echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
			echo "<html>\n";
			echo "<head>\n";
			echo "<title>CLIodine</title>\n";
			echo "<script type=\"text/javascript\">\n";
			echo "var username='{$I2_USER->username}';\n";
			/*echo "}\n";
			echo "\n";
			echo "\n";
			echo "\n";
			echo "\n";
			echo "\n";
			echo "\n";
			echo "\n";
			echo "\n";*/
			echo "</script>\n";
			echo "<script type='text/javascript' src='".$I2_ROOT."www/js/cliodine.js'></script>\n";
			echo "<link href='".$I2_ROOT."www/extra-css/cliodine.css' rel='stylesheet' />\n";
			echo "</head>\n";
			echo "<body onload=\"init()\">\n";
			echo "<div class='console' id='terminal'>\n";
			echo "</div>\n";
			echo "</body>\n";
			echo "</html>";
		} else if ( isset($I2_ARGS[1])) {
			if($this->do_special()) { //handle special stuff
			} else if(get_i2module($I2_ARGS[1])) {
				$mod= new $I2_ARGS[1];
				$cmd=$mod->init_cli();
				if(!$cmd) {
					echo "<div>The module ".$I2_ARGS[1]." is not implemented for CLIodine.<br /></div>\n";
				} else {
					$str=$mod->display_cli($disp);
					echo $str."\n";
				}
			} else {
				echo "<div>".$I2_ARGS[1].": command not found<br /></div>\n";
			}
		}
		Display::stop_display();
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'CLIodine';
	}
	/**
	* Performs all initialization necessary for this module to be 
	* displayed in an ibox.
	*
	* @returns string The title of the box if it is to be displayed,
	*                 otherwise FALSE if this module doesn't have an
	*                 intrabox.
	*/
	function init_box() {
		return FALSE;
	}

	/**
	* Performs all initialization necessary for this module to ba
	* displayed as the main page.
	*
	* @returns mixed Either a string, which will be the title for both the
	*                main pane and for part of the page title, or an array
	*                of two strings: the first is part of the page title,
	*                and the second is the title of the content pane. To
	*                specify no titles, return an empty array. To specify
	*                that this module has no main content pane (and will
	*                show an error if someone tries to access it as such),
	*                return FALSE.
	*/
	function init_pane() {
		global $I2_USER;
		$this->singles=array(
			"help" =>"Commands:<br />",
			"hello"=>"Hello there!",
			"hi"   =>"Hi!",
			"lpr"  =>"Sorry, printing from iodine not supported at this time",
			"su"   =>"Access denied",
			"sudo" =>($I2_USER->username." is not in the sudoers file.  This incident will be reported."),
			"nano" =>"Use ed",
			"ed"   =>"Use nano",
			"vi"   =>"Upgrade to vim",
			"vim"  =>"Use emacs",
			"emacs"=>"Use vim",
			"gedit"=>"Use kate",
			"kate" =>"Use gedit",
			":(){ :|:& };:"=>"Forkbomb detected and neutralized. Don't do that please.",
			"bash" =>"Sorry, no recursion!",
			"exit" =>"No, you exit! :)",
			"quit" =>"Quitting is for losers",
			"ssh"  =>"Remote access is not allowed through this terminal. Try the <a href='https://sun.tjhsst.edu/'>Sun Global Desktop</a>",
			"i read the source code"=>($I2_USER->username."++! You must be cool."),
			"shutdown"=>"shutdown: you must be root to do that!",
			"halt" =>"halt: must be superuser.",
			"reboot"=>"reboot: must be superuser.",
			"poweroff"=>"poweroff: must be superuser."
		);
		$commandlist=array("bash","cliodine","codeinterface","date","echo","ed","emacs","exit","gedit","halt","hello","help","hi","kate","ldapinterface","lpr","mysqlinterface","nano","news","phpinfo","poweroff","pwd","quit","reboot","shutdown","ssh","su","sudo","uname","vi","vim","weather","whoami");
		foreach ($commandlist as $i) {
			$this->singles["help"]=$this->singles["help"]."&nbsp;&nbsp;".$i."<br />";
		}
		return "CLIodine";
	}

	/**
	* Does special messages and easter eggs that don't fall in any module.
	*
	* @returns bool TRUE if it did something, FALSE otherwise.
	*/

	// DEV NOTE: Once we get the javascript for the CLI set up, copy many of these over there.
	//           Don't remove them from here, however, because that way a telnet client
	//           can still do the queries correctly.
	function do_special() {
		global $I2_ARGS;
		$command = strtolower($I2_ARGS[1]);
		if(array_key_exists($command,$this->singles)) {
			echo "<div>".$this->singles[$command]."</div>\n";
			return TRUE;
		}
		if($command=="pwd") {
			global $I2_ROOT;
			echo "<div>".$_SERVER['REQUEST_URI']."</div>\n";
			echo $I2_ROOT;
			return TRUE;
		}
		if($command=="whoami") {
			global $I2_USER;
			echo "<div>".$I2_USER->username."</div>\n";
			return TRUE;
		}
		if($command=="uname") {
			echo "<div>".exec("uname -a")."</div>\n";
			return TRUE;
		}
		if($command=="date") {
			echo "<div>".date("D M j H:i:s e Y")."</div>";
			return TRUE;
		}
		if($command=="echo") {
			echo "<div>";
			for($i=2;$i<sizeof($I2_ARGS);$i++) {
				echo $I2_ARGS[$i]." ";
			}
			echo "</div>";
			return TRUE;
		}
		if($command=="logout") {
			global $I2_ROOT;
			echo "<script type='text/javscript'>window.location='".$I2_ROOT."logout'</script>";
			return TRUE;
		}
		return FALSE;
	}
}
?>
