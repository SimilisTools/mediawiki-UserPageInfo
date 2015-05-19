<?php
/*
 * 2011-2015
 * Provides user email or real name information, only to be shown in user pages
 *
*/

class UserPageInfo {
	
	/**
	 * @param $parser Parser
	 * @param $frame PPFrame
	 * @param $args array
	 * @return string
	 */

	public function userpageinfo ( &$parser, $frame, $args ) {

		global $wgUPAllowedGroups;
		global $wgUser;

		$parser->disableCache();
		$title = $parser->getTitle();

		$user = $wgUser;
		if ( count( $args ) > 0 ) {
			$param = trim( $frame->expand( $args[0] ) );
		} else {
			$param = 'email'; // Email default
		}

		// Can be filtered at the parser level, current user group and page, only user ns and avoid supages
		$cur_ns = $title->getNamespace();
		$cur_gps = $user->getEffectiveGroups();
		
		$ingroup = false;
		
		foreach ($cur_gps as $cur_gp) {
			if (in_array($cur_gp, $wgUPAllowedGroups[$param])) {
				$ingroup = true;
				break;
			}
		}

		if (!$ingroup) {
			return(false);
		}
		
		// Check if in User Namespace
		if ($cur_ns != NS_USER) {
			return(false);
		}
		
		//Now do
		return($this->userget($title, $param));
		
	}

	
	private function userget($userpage, $param) {

		if ($userpage->isSubpage()) {
			return("");
		}

		$username = $userpage->getBaseText();
		
		if ($param == 'email') { 
			return($this->getUserEmail($username));
		}
		elseif ($param == 'groups') {
			return($this->getUserGroups($username));
		}
		else {
			return($this->getUserRealName($username));
		}

	}	

	//Function for getting user email of that profile
	private function getUserEmail($username) {
		
		$user = User::newFromName($username);
		return ($user->getEmail());
	}
	
	//Function for getting user groups of that profile
	private function getUserGroups($username) {

		$user = User::newFromName($username);
		return(implode(",", $user->getGroups()));
	}
	
	//Function for getting user real name of that profile
	private function getUserRealName($username) {

		$user = User::newFromName($username);	
		return ($user->getRealName());
    }

}
