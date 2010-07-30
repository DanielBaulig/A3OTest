<?php
require_once 'PHPUnit/Framework.php';

class A3GameNationTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new A3GameNationTestSuite( 'A3GameNation Test Cases' );		
		$suite->addTestSuite( 'BasicA3GameNationTest' );
		return $suite;
	}
}

class BasicA3GameNationTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	public function testInstanciation( )
	{
		$nation = new A3GameNation( array( A3GameNation::NAME => 'Test Land', A3GameNation::ALLIANCES => array( ) ) );
		$this->assertType( 'A3GameNation', $nation ); 
	}
}