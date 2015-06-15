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

	public static function process( &$parser, $frame, $args ) {

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

		if (!$ingroup && !( self::isMaintenance() ) ) {
			return(false);
		}
		
		// Check if in User Namespace
		if ($cur_ns != NS_USER) {
			return(false);
		}
		
		//Now do
		return(self::userget($title, $param));
		
	}

	/**
	 * @param $parser Parser
	 * @param $frame PPFrame
	 * @param $args array
	 * @return string
	 */

	public static function check( &$parser, $frame, $args ) {

		global $wgUPAllowedGroups;
		global $wgUser;

		$parser->disableCache();
		$title = $parser->getTitle();

		$check = null;
		$yes = 1;
		$no = 0;

		$user = $wgUser;
		if ( count( $args ) > 0 ) {
			$param = trim( $frame->expand( $args[0] ) );

			if ( array_key_exists( 1, $args ) ) {
				$check = trim( $frame->expand( $args[1] ) );
			}

			if ( array_key_exists( 2, $args ) ) {
				$yes = trim( $frame->expand( $args[2] ) );
			}

			if ( array_key_exists( 3, $args ) ) {
				$no = trim( $frame->expand( $args[3] ) );
			}

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

		if (!$ingroup && !( self::isMaintenance() )) {
			return(false);
		}
		
		// Check if in User Namespace
		if ($cur_ns != NS_USER) {
			return(false);
		}
		
		$userget = self::userget($title, $param);
		
		if ( $param != 'groups' ) {
			if ( $userget == $check ) {
				return $yes;
			}
		} else {
			$usergetArr = explode( ",", $userget );
			// Redo array
			if ( in_array( $check, $usergetArr ) ) {
				return $yes;
			}
		}
		
		//Default no
		return $no;
		
	}
	
	private function userget($userpage, $param) {

		if ($userpage->isSubpage()) {
			return("");
		}

		$username = $userpage->getBaseText();
		
		if ($param == 'email') { 
			return(self::getUserEmail($username));
		}
		elseif ($param == 'groups') {
			return(self::getUserGroups($username));
		}
		else {
			return(self::getUserRealName($username));
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

	private function isMaintenance() {
		if ( is_null( RequestContext::getMain()->getTitle() ) ) {
			return true;
		}

		return false;
	}

}
