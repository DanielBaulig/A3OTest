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
	
	/**
	 * @dataProvider exceptionIsAllyProvider
	 */
	public function testExceptionIsAlly( $ally )
	{
		$nation = A3GameNationRegistry::getNation( 'Russia' );
		$this->setExpectedException( 'DomainException' );
		$nation->isAllyOf( $ally );
	}
	
	public function exceptionIsAllyProvider( )
	{
		return array(
			array(
				'failNation',
				null,
				'',
				123456,
			)
		);
	}
	
	/**
	 * @dataProvider isAllyProvider
	 */
	public function testIsAlly( $ally )
	{
		$nation = A3GameNationRegistry::getNation( 'Russia' );
		$this->assertTrue( $nation->isAllyOf( $ally ) );
	}
	
	public function isAllyProvider( )
	{
		return array(
			array(
				'Britain',
			//  'USA',
			//	'China',
			)
		);
	}
	
	/**
	 * @dataProvider isNotAllyProvider
	 */
	public function testIsNotAlly( $ally )
	{
		$nation = A3GameNationRegistry::getNation( 'Russia' );
		$this->assertFalse( $nation->isAllyOf( $ally ) );
	}
	
	public function isNotAllyProvider( )
	{
		return array(
			array(
				'Germany',
				'Japan',
			)
		);
	}
	
	/**
	 * @dataProvider isInAllianceProvider
	 */
	public function testIsInAlliance( $alliance )
	{
		$nation = A3GameNationRegistry::getNation( 'Russia' );
		$this->assertTrue( $nation->isInAlliance( $alliance) );
	}
	
	public function isInAllianceProvider( )
	{
		return array(
			array(
				'Allies',
				'UDSSR',
			)
		);
	}
	/**
	 * @dataProvider isNotInAllianceProvider
	 */
	public function testIsNotInAlliance( $alliance )
	{
		$nation = A3GameNationRegistry::getNation( 'Russia' );
		$this->assertFalse( $nation->isInAlliance( $alliance) );
	}
	
	public function isNotInAllianceProvider( )
	{
		return array(
			array(
				'Axis',
				'failAlliance',
				'',
				null,
				12345,
			)
		);
	}
}