<?php
require_once 'PHPUnit/Framework.php';
require_once $basedir . '/../A3O/include/classes/MatchState/A3/A3MatchState.php';

class GameStateSuite  extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new GameStateSuite( 'GameState Test Suite' );

		$files = array(
			'GameNationTests.php',
			'MatchZoneTests.php',
			'GameTypeTests.php',
			'GameAllianceTests.php',
			'A3/A3MatchZoneTests.php',
			'A3/A3GameTypeTests.php',
			'MatchPlayerTests.php',
		);

		
		foreach( $files as $file )
		{
			include_once dirname(__FILE__) . '/TestCases/' . $file;
		}
		
		$suites = array(
			GameNationTestSuite::suite( ),
			MatchZoneTestSuite::suite( ),
			GameTypeTestSuite::suite( ),
			GameAllianceTestSuite::suite( ),
			A3MatchZoneTestSuite::suite(),
			A3GameTypeTestSuite::suite(),
			MatchPlayerTestSuite::suite(),
			
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