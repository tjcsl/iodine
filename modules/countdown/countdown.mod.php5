<?php
Class Countdown implements Module
{
	//you know what? I'm not even going to bother to phpdoc all the blank methods. sue me, derek.

	function display_pane($i){ return false;}
	function init_pane() { return false; }
	function init_cli() {return false;}
	function init_mobile() {return false;}
	function display_mobile($i) {return false;}
	function display_cli($disp) {return false;}//I'm actually gonna do this one later
	
	/**
	* We don't really support this yet, but make it look like we do.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function api($disp) {
		return false;
	}
	/**
	* We don't really support this yet, but make it look like we do.
	*/
	function api_build_dtd() {
		return false;
	}

	function get_name() {return "countdown";}

	function init_box()
	{
		return "COUNTDOWN!";
	}
	function display_box($disp)
	{
		$grad=i2config_get('gradtime',1308094200,'countdown'); //unix timestamp for graduation date.
		$diff=$grad-time();
		if($diff<0) $negative=1;
			else $negative=0;
		$years=floor($diff/31556926);
		$diff=$diff%31556926;
		$days=floor($diff/86400);
		$diff=$diff%86400;
		$hours=floor($diff/3600);
		$diff=$diff%3600;
		$mins=floor($diff/60);
		$secs=$diff%60;

		$underclass=0;
		$user = new User();
		$class=$user->grade;
		if($class!=12) $underclass=1;

		$template_args['years']=$years;
		$template_args['days']=$days;
		$template_args['hours']=$hours;
		$template_args['mins']=$mins;
		$template_args['secs']=$secs;
		$template_args['underclass']=$underclass;
		$template_args['negative']=$negative;
		$disp->disp('countdown.tpl',$template_args);
	}
}
?>
