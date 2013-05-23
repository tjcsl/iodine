<?php
/**
* Just contains the definition for the {@link Aphorisms}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2005-2006 The Intranet 2 Development Team
* @package modules
* @subpackage Aphorisms
* @filesource
*/

/**
* A {@link Module} to display Aphorisms.
* @package core
* @subpackage Module
*/
class Aphorisms extends Module {

		  private $aphorism = '';
		  private $updated = FALSE;
		  private $template = 'aphorisms_pane.tpl';
		  private $template_args = [];
	
	/**
	* Displays all of a module's main content.
	*
	* @param Display $disp The Display object to use for output.
	* @abstract
	*/
	function display_pane($disp) {
			  $disp->smarty_assign('aphorism',$this->aphorism);
			  $disp->smarty_assign('updated',$this->updated);
			  $disp->disp($this->template,$this->template_args);
	}
	
	/**
	* Gets the module's name.
	*
	* @returns string The name of the module.
	* @abstract
	*/
	function get_name() {
			  return 'Aphorisms';
	  }

	function ec($str) {
		return str_replace('"','“',$str);
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
	* @abstract
	*/
	function init_pane() {
		global $I2_USER,$I2_SQL,$I2_ARGS;
		$uidnumber = FALSE;
		$admin = $I2_USER->is_group_member('admin_aphorisms');
		$this->template_args['username'] = $I2_USER->name;
		$this->template_args['admin_aphorisms'] = $admin;
		if (isset($I2_ARGS[1])) {
			if ($I2_ARGS[1] == 'choose') {
				$this->template = 'choose.tpl';
				$this->template_args['search_destination'] = 'aphorisms/searched/';
				$this->template_args['first_year'] = User::get_gradyear(12);
				return 'Find a Student';
			} else if ($I2_ARGS[1] == 'searched') {
				$this->template = 'choose.tpl';
				$this->template_args['results_destination'] = 'aphorisms/';
				$this->template_args['return_destination'] = 'aphorisms/choose/';
				$this->template_args['info'] = Search::get_results();
				$this->template_args['first_year'] = User::get_gradyear(12);
				return 'Search Results';
			} else if($I2_ARGS[1] == 'data') {
				if(!$admin) {
					redirect('aphorisms');
					return;
				}
				$this->template = 'data.tpl';
				$data = $I2_SQL->query('SELECT * FROM aphorisms ORDER BY uid')->fetch_all_arrays(Result::ASSOC);
				$this->template_args['data'] = [];
				$this->template_args['users'] = [];
				foreach($data as $row) {
					$this->template_args['users'][] = new User($row['uid']);
					$this->template_args['data'][$row['uid']] = $row;
				}
				usort($this->template_args['users'], array('User', 'name_cmp'));
			} else if ($I2_ARGS[1] == 'csv') {
				if(!$admin) {
					redirect('aphorisms');
					return;
				}
				$in_data = $I2_SQL->query('SELECT * FROM aphorisms ORDER BY uid')->fetch_all_arrays(Result::ASSOC);
				$data = [];
				$users = [];
				foreach($in_data as $row) {
					$users[] = new User($row['uid']);
					$data[$row['uid']] = $row;
				}
				usort($users, array('User', 'name_cmp'));
				header('Pragma: ');
				header('Content-type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename="aphorisms.csv"');
				Display::stop_display();
				echo "\"Student\",\"College\",\"College Plans\",\"National Merit Semi-finalist\",\"National Merit Finalist\",\"National Achievement\",\"Hispanic Achievement\",\"First Honor\",\"Second Honor\",\"Third Honor\",\"Aphorism\"\n";
				foreach($users as $user) {
					$uid = $user->uid;
					echo "\"".$this->ec($user->name)."\",\"".$this->ec($data[$uid]['college'])."\",\"".$this->ec($data[$uid]['collegeplans'])."\",\"" . ($data[$uid]['nationalmeritsemifinalist'] ? 'Yes' : 'No') . "\",\"" . ($data[$uid]['nationalmeritfinalist'] ? 'Yes' : 'No') . "\",\"" . ($data[$uid]['nationalachievement'] ? 'Yes' : 'No') . "\",\"" . ($data[$uid]['hispanicachievement'] ? 'Yes' : 'No') . "\",\"".$this->ec($data[$uid]['honor1'])."\",\"".$this->ec($data[$uid]['honor2'])."\",\"".$this->ec($data[$uid]['honor3'])."\",\"".$this->ec($data[$uid]['aphorism'])."\"\n";
				}
			} else {
				$uidnumber = $I2_ARGS[1];
				$user = new User($uidnumber);
				$this->template_args['username'] = $user->name;
			}
		}
		if (!$uidnumber) {
			$uidnumber = $I2_USER->uid;
		}				  
		if ($uidnumber != $I2_USER->uid && !$admin) {
				  throw new I2Exception('You are not authorized to edit this student\'s aphorisms.');
		}
		if ($I2_USER->grade != 12) {
				  throw new I2Exception('User is not a senior!');
		}
		if (isset($I2_ARGS[1]) && $I2_ARGS[1] == 'edit') {
		}
		if (isset($_REQUEST['posting'])) {
		   if (strlen(preg_replace("\n| |\t|\r\n","",$_REQUEST['aphorism'])) >= 205) {
				throw new I2Exception('Your aphorism may not be longer than 200 characters, excluding spaces!');
			}
		/*	$I2_SQL->query('REPLACE INTO aphorisms SET uid=%d,college=%s,nationalmeritsemifinalist=%d,nationalmeritfinalist=%d,
					  nationalachievement=%d,hispanicachievement=%d,honor1=%s,honor2=%s,honor3=%s,aphorism=%s',$uidnumber,
					  $_REQUEST['college'],isset($_REQUEST['nationalmeritsemifinalist'])?1:0,
					  isset($_REQUEST['nationalmeritfinalist'])?1:0,isset($_REQUEST['nationalachievement'])?1:0,isset($_REQUEST['hispanicachievement'])?1:0,
					  $_REQUEST['honor1'],$_REQUEST['honor2'],$_REQUEST['honor3'],$_REQUEST['aphorism']
			);*/
			$I2_SQL->query('REPLACE INTO aphorisms SET uid=%d,college=%s,honor1=%s,honor2=%s,honor3=%s,aphorism=%s',$uidnumber,
					  $_REQUEST['college'],$_REQUEST['honor1'],$_REQUEST['honor2'],$_REQUEST['honor3'],strtr($_REQUEST['aphorism'], array('|' => ''))
			);
			$this->updated = TRUE;
		}
		$this->aphorism = $I2_SQL->query('SELECT * FROM aphorisms WHERE uid=%d',$uidnumber)->fetch_array(Result::ASSOC);
		return 'Aphorisms';
	}
}
?>
