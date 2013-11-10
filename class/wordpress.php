<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//		File:
//			wordpress.php
//		Description:
//			This is a generic class for dealing with various Wordpress plugin tasks.
//		Actions:
//			1) get/set/update Wordpress options
//			2) serve posts and post related items
//			3) handle Wordpress errors
//		Date:
//			Created April 21st, 2009 for Wordpress
//		Version:
//			2.0
//		Copyright:
//			Copyright (c) 2011 Matthew Praetzel.
//		License:
//			This software is licensed under the terms of the GNU Lesser General Public License v3
//			as published by the Free Software Foundation. You should have received a copy of of
//			the GNU Lesser General Public License along with this software. In the event that you
//			have not, please visit: http://www.gnu.org/licenses/gpl-3.0.txt
//
////////////////////////////////////////////////////////////////////////////////////////////////////

/****************************************Commence Script*******************************************/

//                                *******************************                                 //
//________________________________** WORDPRESS                 **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
if(!class_exists('ternWP')) {
//
class ternWP {

	var $errors = array();

//                                *******************************                                 //
//________________________________** OPTIONS                   **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
	function getOption($n,$d='',$v=false) {
		$o = get_option($n);
		if(!isset($o) and !empty($d)) {
			add_option($n,$d);
		}
		elseif(isset($o) and (empty($o) or $v) and !empty($d)) {
			update_option($n,$d);
		}
		elseif(isset($o) and !empty($d)) {
			foreach($d as $k => $v) {
				if(!isset($o[$k])) {
					$o[$k] = $v;
				}
			}
			update_option($n,$o);
		}
		return get_option($n);
	}
	function updateOption($n,$d,$w) {
		global $tern_wp_msg;
		$o = $this->getOption($n,$d);
		if(wp_verify_nonce($_REQUEST['_wpnonce'],$w) and $_REQUEST['action'] == 'update') {
			$f = new parseForm('post','_wp_http_referer,_wpnonce,action,submit,page,page_id');
			foreach($o as $k => $v) {
				if(!isset($f->a[$k])) {
					$f->a[$k] = $v;
				}
			}
			return $this->getOption($n,$f->a,true);
			$tern_wp_msg = empty($tern_wp_msg) ? 'You have successfully updated your settings.' : $tern_wp_msg;
		}
		else {
			return $this->getOption($n,$d);
		}
	}
//                                *******************************                                 //
//________________________________** POSTS                     **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
	function postByName($n) {
		global $wpdb;
		return $wpdb->get_var("select ID from $wpdb->posts where post_name='$n'");
	}
//                                *******************************                                 //
//________________________________** ERRORS                    **_________________________________//
//////////////////////////////////**                           **///////////////////////////////////
//                                **                           **                                 //
//                                *******************************                                 //
	function addError($e) {
		$this->errors[] = $e;
	}
	function renderErrors() {
		global $notice;
		foreach($this->errors as $v) {
			$notice .= empty($notice) ? $v : '<br />'.$v;
		}
	}

}
$getWP = new ternWP;
//
}
	
/****************************************Terminate Script******************************************/
?>
