<?php
require_once 'PHPUnit/Framework.php';
require_once $basedir . '/../A3O/include/classes/GameState/A3GameState.php';

class GameStateSuite  extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new GameStateSuite( 'GameState Test Suite' );

		$files = array(
			'A3GameNationTests.php',
			'A3MatchZoneTests.php',
			'A3GameTypeTests.php',
		);

		
		foreach( $files as $file )
		{
			include_once dirname(__FILE__) . '/TestCases/' . $file;
		}
		
		$suites = array(
			A3GameNationTestSuite::suite( ),
			A3MatchZoneTestSuite::suite( ),
			A3GameTypeTestSuite::suite( ),
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