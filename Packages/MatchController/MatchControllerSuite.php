<?php
require_once 'PHPUnit/Framework.php';
require_once $basedir . '/../A3O/include/classes/MatchController/MatchController.php';

class MatchControllerSuite  extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new MatchControllerSuite( 'GameState Test Suite' );

		$files = array(
			'StateTests.php',
		);

		
		foreach( $files as $file )
		{
			include_once dirname(__FILE__) . '/TestCases/' . $file;
		}
		
		$suites = array(
			StateTestSuite::suite( ),
		);
		
		foreach( $suites as $aSuite )
		{
			$suite->addTest( $aSuite );
		}
		return $suite;
	}
	
	public function setUp( )
	{
		
	}
}