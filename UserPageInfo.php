<?php
/**
 * Copyright (C) 2011 Toni Hermoso Pulido <toniher@cau.cat>
 * http://www.cau.cat
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
    echo "Not a valid entry point";
    exit( 1 );
}


//self executing anonymous function to prevent global scope assumptions
call_user_func( function() {

	// At first, only allow to sysop to be checked
	$GLOBALS['wgUPAllowedGroups']['email'] = array('sysop');
	$GLOBALS['wgUPAllowedGroups']['realname'] = array('sysop');
	$GLOBALS['wgUPAllowedGroups']['groups'] = array('sysop');
	
	$GLOBALS['wgExtensionCredits']['parserhook'][] = array(
			'path' => __FILE__,
			'name' => 'UserPageInfo',
			'author' => 'Toni Hermoso',
			'version' => '0.1',
			'url' => 'https://github.com/SimilisTools/mediawiki-UserPageInfo',
			'descriptionmsg' => 'userpageinfo-desc',
	);
	
	$GLOBALS['wgAutoloadClasses']['UserPageInfo'] = __DIR__ . '/UserPageInfo_body.php';
	$GLOBALS['wgMessagesDirs']['UserPageInfo'] = __DIR__ . '/i18n';

	$GLOBALS['wgExtensionMessagesFiles']['UserPageInfo'] = __DIR__ . '/UserPageInfo.i18n.php';
	$GLOBALS['wgExtensionMessagesFiles']['UserPageInfoMagic'] = __DIR__ . '/UserPageInfo.i18n.magic.php';
	
	$GLOBALS['wgHooks']['ParserFirstCallInit'][] = 'wfRegisterUserPageInfo';

} );

function wfRegisterUserPageInfo( $parser ) {

	// Can be filtered at the parser level, current user group and page, only user ns and avoid supges
	$parser->setFunctionHook( 'userpageinfo', 'UserPageInfo::process', Parser::SFH_OBJECT_ARGS );
	return true;
	
}
