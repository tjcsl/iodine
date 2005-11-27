<?php
/**
* Just contains the definition for the {@link Module} I2SCM.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005 The Intranet 2 Development Team
* @since 1.0
* @package core
* @subpackage Module
* @filesource
*/

/**
* A module to display I2's code repository, ViewCVS style.
* @package core
* @subpackage Module
*/
class I2SCM implements Module {
	/**
	* Path to the main repository.
	*/
	private $root;
	
	/**
	* Object for accessing the repository.
	*/
	private $repo;

	private $template_args = array();

	/**
	* Constructor.
	*/
	public function __construct() {
		$this->root = i2config_get('path','/shared/hg/intranet2/','i2scm');
		$repoclass = i2config_get('driver','HGRepository','i2scm');
		
		try {
			$this->repo = new $repoclass($this->root);
		} catch( I2Exception $e ) {
			// Error will get caught in the instanceof line, no need to handle anything here
		}
		
		if(! $this->repo instanceof Repository) {
			throw new I2Exception("Invalid repository driver $repoclass specified in the Iodine config.");
		}
	}

	/**
	* Displays all of a module's ibox content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_box($disp) {
	}
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_pane($disp) {
		$disp->disp('listing.tpl',$this->template_args);
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	*/
	function get_name() {
		return 'I2SCM';
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
	* Performs all initialization necessary for this module to be
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
		global $I2_ARGS;

		$i2dev = new Group('i2_dev');
		if(!$i2dev->has_member()) {
			return FALSE;
		}

		if(!isset($I2_ARGS[1])) {
			$path = '';
		}
		else {
			$path = implode('/',array_slice($I2_ARGS,1));
		}

		$this->template_args['dirs'] = array();
		$this->template_args['files'] = array();
		foreach($this->repo->list_files($path) as $file) {
			if($this->repo->is_dir($file)) {
				$this->template_args['dirs'][] = $this->repo->summary($file);
			}
			else {
				$this->template_args['files'][] = $this->repo->summary($file);
			}
		}

		return 'Intranet2 SCM Viewer' . ($path?" $path":'');
	}

	/**
	* Returns whether this module functions as an intrabox.
	*
	* @returns boolean True if the module has an intrabox, false if it does not.
	*
	*/
	function is_intrabox() {
		return FALSE;
	}
	
}
?>
