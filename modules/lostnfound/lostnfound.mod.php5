<?php
/**
* Just contains the definition for the class {@link LostNFound}.
* @author The Intranet 2 Development Team <intranet2@tjhsst.edu>
* @copyright 2012 The Intranet 2 Development Team
* @package modules
* @subpackage LostNFound
* @filesource
*/

/**
* The module that keeps track of lost and found items
* @package modules
* @subpackage LostNFound
*/
class LostNFound extends Module {

	/**
	* Template for the specified action
	*/
	private $template;

	/**
	* Template arguments for the specified action
	*/
	private $template_args = [];

	/**
	* A 1-dimensional array of all the items
	*/
	private $items;

	/**
	* A 1-dimensional array containing all of the titles for all news posts.
	*/
	private $summaries = [];

	/**
	* Whether the current user is an Intranet administrator
	*/
	private $is_admin;

	/**
	* Whether the current user is blacklisted from posting lost items
	*/
	private $blacklisted;
	
	/**
	* Sets the global "is_admin" variable
	*/
	private function set_is_admin() {
		global $I2_USER;
		$this->is_admin = $I2_USER->is_group_member('admin_all');
		if ($this->is_admin) {
			d('This user is an Intranet administrator - lost & found alteration privileges have been granted.',7);
		}	
	}
	
	/**
	* Sets the global "blacklisted" variable
	*/
	private function set_blacklisted() {
		global $I2_USER;
		$this->blacklisted = $I2_USER->is_group_member('lostnfound_blacklist');
	}
	
	/**
	* Use the normal look on mobile
	*/
	function init_mobile() {
		return $this->init_pane();
	}

	/**
	* Use the normal look on mobile
	*
	* @param Display $disp The Display object to use for output.
	*/
	function display_mobile($disp) {
		return $this->display_pane($disp);
	}

	/**
	* Returning the command title
	*/
	function init_cli() {
		return "lostnfound";
	}

	/**
	* Handle the lostnfound command.
	*
	*/
	function display_cli() {
		global $I2_ARGS;
		$valid_commands = array("list-lost","list-found");
		if(!isset($I2_ARGS[2]) || !in_array(strtolower($I2_ARGS[2]),$valid_commands) ) {
			return "<div>Usage: lostnfound list-lost<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;lostnfound list-found<br /><br />lostnfound is a command for viewing lost and found items.<br /><br />Commands:<br />&nbsp;&nbsp;&nbsp;list-lost - list all lost items that have not been found<br />&nbsp;&nbsp;&nbsp;list-found - list all unclaimed items that have been found<br /></div>\n";
		}
		switch (strtolower($I2_ARGS[2])) {
			case "list-lost":
				$string= "<div>\n";
					// TODO: print stuff
				$string.="</div>\n";
				return $string;
			case "list-found":
				$string= "<div>\n";
					// TODO: print stuff
				$string.="</div>\n";
			default:
				return "<div>Error: unrecognizable input</div>\n";
		}
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function init_pane() {
		global $I2_SQL,$I2_ARGS,$I2_USER,$I2_ROOT;
		if( ! isset($I2_ARGS[1]) ) {
			$I2_ARGS[1] = '';
		}
		
		if (!isset($this->is_admin)) {
			$this->set_is_admin();
		}
		
		if(!isset($this->blacklisted)) {
			$this->set_blacklisted();
		}
		$this->template_args['blacklisted'] = $this->blacklisted;

		$archive = false;

		switch($I2_ARGS[1]) {
			case 'add':
				$this->template = 'lost_add.tpl';
				// if an item was just submitted
				if(isset($_REQUEST['add_form'])) {
					$title = stripslashes($_REQUEST['title']);
					$text = stripslashes($_REQUEST['text']);

					if(LostItem::create_item($I2_USER, $title, $text)) {
						$this->template_args['added'] = true;
					}
					else {
						$this->template_args['added'] = false;
					}

					return array('Add Lost Item', 'Lost Item Added');
				}
				return array('aaAdd Lost Item', 'Add Lost Item');

			case 'edit':
				//FIXME: make this do something.
				$this->template = 'lost_edit.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of article to edit not specified.');
				}

				try {
					$item = new Lostitem($I2_ARGS[2]);
				} catch(I2Exception $e) {
					throw new I2Exception("Specified item ID {$I2_ARGS[2]} is invalid.");
				}

				if( !$item->editable() ) {
					throw new I2Exception('You do not have permission to edit this item.');
				}

				if( isset($_REQUEST['edit_form']) ) {
					$title = stripslashes($_REQUEST['edit_title']);
					$text = stripslashes($_REQUEST['edit_text']);
					$expire = $_REQUEST['edit_expire'];
					$visible = isset($_REQUEST['edit_visible']) ? 1 : 0;
					$groups = Group::generate($_REQUEST['add_groups']);
					$public = isset($_REQUEST['edit_public']) ? 1 : 0;
					$item->edit($title, $text, $groups,$expire,$visible,$public);
					$item = new Lostitem($I2_ARGS[2]);
					$this->template_args['edited'] = 1;
				}
				
				//$item->title = stripslashes($item->title);
				//$item->text = stripslashes($item->text);
				$item->text = htmlspecialchars_decode($item->text);
				$item->text = preg_replace('/<br\\s*?\/??>/i', "\n", $item->text);
				// To fix highlighting in vim, since it thinks we just closed the tag: <?php
				$this->template_args['item'] = $item;
				return 'Edit Lost Item';
				
			case 'delete':
				$this->template = 'lostnfound_delete.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of item to delete not specified.');
				}
				
				try {
					$item = new LostItem($I2_ARGS[2]);
				} catch(I2Exception $e) {
					throw new I2Exception("Specified item ID {$I2_ARGS[2]} is invalid.");
				}

				if( !$item->editable() ) {
					throw new I2Exception('You do not have permission to delete this item.');
				}

				if( isset($_REQUEST['delete_confirm']) ) {
					$item->delete();
					return 'Lost Item Deleted';
				}
				else {
					$this->template_args['lostitem'] = new LostItem($I2_ARGS[2]);
					return array('Delete Lost Item', 'Confirm Lost Item Delete');
				}
				
			case 'view':
				$this->template = 'lostitem_view.tpl';
				if( !isset($I2_ARGS[2]) ) {
					throw new I2Exception('ID of item to view not specified.');
				}
				$item = new LostItem($I2_ARGS[2]);
				$this->template_args['item'] = $item;
				return "$item->title";
	   		default:
				return self::display_lostnfound();
		}
		//should not happen
		throw new I2Exception('Internal error: sanity check, reached end of init_pane in lostnfound.');
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_pane($disp) {
		$this->template_args['is_admin'] = $this->is_admin;
		$disp->disp($this->template, $this->template_args);
	}
	
	/**
	* Required by the {@link Module} interface.
	*/
	function init_box() {
		if( $this->items === NULL ) {
			global $I2_SQL;
			$this->items = LostItem::get_all_items();
		}
		foreach($this->items as $item) {
			$this->summaries[] = array('title' => $item->title, 'id' => $item->id);
		}
		$num = count($this->summaries);
		if (!isset($this->is_admin)) {
			$this->set_is_admin();
		}
		return 'Lost & Found: '.$num.' item'.($num==1?'':'s').' missing';
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function display_box($disp) {
		$disp->disp('lostnfound_box.tpl',array('summaries'=>$this->summaries,'is_admin'=>$this->is_admin));
	}

	/**
	* Required by the {@link Module} interface.
	*/
	function get_name() {
		return 'Lost & Found';
	}
	function display_lostnfound($title='Lost & Found') {	
		$this->template = 'lostnfound_pane.tpl';
		$I2_ARGS[1] = '';
		
		$this->template_args['items'] = [];

		if( $this->items === NULL) {
			$this->items = LostItem::get_all_items();
		}
		foreach($this->items as $item) {
			$item->title = stripslashes($item->title);
			$this->template_args['items'][] = $item;
		}
		return array('Lost & Found',$title);
	}
}

?>
