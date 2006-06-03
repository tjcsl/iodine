<?php
class TabbedPane implements Module {

	/**
	* Wrapped module name 
	*/
	private $mod;

	/**
	* Wrapped module instance
	*/
	private $wrapmodule;

	private $template = 'pane.tpl';

	private $template_args = array();

	private $title;

	public function init_box() {
		return FALSE;
	}

	public function display_box($display) {
		return FALSE;
	}

	public function init_pane() {
		global $I2_USER,$I2_ARGS,$I2_ROOT;
		if (isSet($I2_ARGS[1])) {
			$this->mod = $I2_ARGS[1];
		} else  {
			$this->mod = $I2_USER->startpage;
		}
		$mod = $this->mod;
		$this->wrapmodule = new $mod();
		if (!$this->wrapmodule instanceof Module) {
			throw new I2Exception("$mod is not a valid Module class!");
		}
		// Chop off the 'tabbedpane' at the front of I2_ARGS.
		$I2_ARGS = array_slice($I2_ARGS,1);
		// Make links point to tabbedpane
		$I2_ROOT .= 'tabbedpane/';
		$this->title = $this->wrapmodule->init_pane();
		if (!$this->title) {
			return FALSE;
		}
		if (is_array($this->title)) {
			$this->title = $this->title[1];
		}
		return 'Tab: '.$this->mod;
	}

	public function display_pane($display) {
		$display->set_module_name($this->mod);
		$display->set_buffering(TRUE);
		$display->flush_buffer();
		$this->wrapmodule->display_pane($display);
		$output = $display->get_buffer();
		$display->clear_buffer();
		$display->set_buffering(FALSE);
		$this->template_args['tab_content'] = $output;
		$this->template_args['tab_title'] = $this->title;
		$display->set_module_name('tabbedpane');
		$display->disp($this->template,$this->template_args);
	}

	public function get_name() {
		return 'Nags';
	}

}
?>
