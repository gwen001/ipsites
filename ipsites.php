<?php

/**
 * I don't believe in license
 * You can do want you want with this program
 * - gwen -
 */

function __autoload( $c ) {
	include( $c.'.php' );
}


// parse command line
{
	if( $_SERVER['argc'] != 2 ) {
		Utils::help();
	}
}
// ---


// init
{
	$ipsites = new IpSites();

	if( !$ipsites->setIp($_SERVER['argv'][1]) ) {
		Utils::help('IP adress not valid!');
	}

	$ipsites->addWrapper( 'AlleDomainNamen' );
	$ipsites->addWrapper( 'UserExp' );

	if( !count($ipsites->getWrappers()) ) {
		Utils::help('No wrapper configured');
	}
}
// ---


// init
{
	$ipsites->run();
}
// ---


exit();

?>
