<?php
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once  'PHPUnit/Extensions/Database/DataSet/XmlDataSet.php';
define( 'BASEDIR', dirname(__FILE__) );
require_once BASEDIR . '/Packages/GameState/GameStateSuite.php';
require_once BASEDIR . '/Packages/MatchController/MatchControllerSuite.php';

class A3OTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new A3OTestSuite( 'Complete A3O TestSuite' );
		$suite->addTest( GameStateSuite::suite( ) );
		$suite->addTest( MatchControllerSuite::suite( ) );
		
		return $suite;
	}
	
	protected function setUp( )
	{
		echo 'Running A3O Unit Tests.' . "\n";
		echo 'Setting up database connection... ';
		$sql_host = 'localhost';
		$sql_username = 'root';
		$sql_password = '';
		$sql_database = 'a3o';
		try
		{
			$pdo = new PDO('mysql:host=' . $sql_host . ';dbname='. $sql_database, $sql_username, $sql_password );
		}
		catch (PDOException $e)
		{
			die($e->getMessage());
		}
		if ( ! $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) || $pdo->getAttribute(PDO::ATTR_ERRMODE) != PDO::ERRMODE_EXCEPTION)
		{
			throw new Exception('Error Mode change failed!');
		}
		
		$test_db = new PHPUnit_Extensions_Database_DefaultTester( new PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection ( $pdo, 'a3o' ) );
		$test_db->setSetUpOperation( PHPUnit_Extensions_Database_Operation_Factory::CLEAN_INSERT( true ) );
		$test_db->setTearDownOperation( PHPUnit_Extensions_Database_Operation_Factory::NONE( ) );
		$xml = new PHPUnit_Extensions_Database_DataSet_XmlDataSet( dirname(__FILE__) . '/_database/phpunit_a3o.xml' );
		$test_db->setDataSet( $xml );

		// make sure the database is intialized with the default values (important for registries!)
		$test_db->onSetUp( );
		
		$this->sharedFixture['test_db'] = $test_db;
		$this->sharedFixture['pdo'] = $pdo;
		
		echo "established.\n";
		
		$this->sharedFixture['match_state'] = new A3PDOMatchBoard( $pdo, BasicGameTypeFactoryTest::TEST_GAME_ID, BasicMatchZoneFactoryTest::TEST_MATCH_ID );
	}
	
	protected function tearDown( )
	{
		echo "\nDone.\n";
	}
}