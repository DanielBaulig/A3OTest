<?php
require_once 'PHPUnit/Framework.php';

class A3GameNationTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new A3GameNationTestSuite( 'A3GameNation Test Cases' );
		$suite->addTestSuite( 'A3GameNationFactoryTest' );
		$suite->addTestSuite( 'A3GameNationRegistryTest' );		
		$suite->addTestSuite( 'BasicA3GameNationTest' );
		
		return $suite;
	}
}

class A3GameNationFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testLoadAllNations( )
	{
		$factory = new A3GameNationPDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID );
		$nations = $factory->createAllProducts( );
		$this->assertArrayHasKey( 'Russia' , $nations );
		$this->assertArrayHasKey( 'Germany', $nations );
		$this->assertArrayHasKey( 'Japan', $nations );
		$this->assertArrayHasKey( 'USA', $nations );
		$this->assertArrayHasKey( 'Britain', $nations );
		$this->assertArrayHasKey( 'China', $nations );
		$this->assertEquals( 6, count( $nations ) );
	}
	
	public function testLoadNoNations( )
	{
		$factory = new A3GameNationPDOFactory( $this->pdo, -1 );
		$nations = $factory->createAllProducts( );
		$this->assertEquals( 0, count( $nations ) );
	}
	
	/**
	 * @dataProvider failLoadSingleNationProvider
	 */
	public function testFailLoadSingleNation( $nation )
	{
		$factory = new A3GameNationPDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID );
		$this->setExpectedException( 'DomainException' );
		$nation = $factory->createSingleProduct( $nation );
	} 
	
	public function failLoadSingleNationProvider( )
	{
		return array(
			array(
				'failNation'
			),
			array(
				''
			),
			array(
				null
			),
			array(
				12345
			),
		);
	}
	
	/**
	 * @dataProvider loadSingleNationProvider
	 */
	public function testLoadSingleNation( $nation )
	{
		$factory = new A3GameNationPDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID );
		$nation = $factory->createSingleProduct( $nation );
		$this->assertType( 'A3GameNation', $nation );
	}
	
	public function loadSingleNationProvider( )
	{
		return array(
			array(
				'Russia',
			),
			array(
				'Germany',
			),
			array(
				'USA',
			),
			array(
				'Britain',
			),
			array(
				'Japan',
			),
			array(
				'China',
			),
		);
	}
}

class A3GameNationRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testReinitializationException( )
	{
		$this->setExpectedException( 'Exception' );		
		A3GameNationRegistry::initializeRegistry( new A3GameNationPDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID ) );
	}
	
	public function testGetInstance( )
	{
		$this->assertType( 'A3GameNationRegistry', A3GameNationRegistry::getInstance( ) );	
	}
	
	/**
	 * @dataProvider invalidGetNationProvider
	 */
	public function testInvalidGetNation( $nation )
	{
		$this->setExpectedException( 'DomainException' );
		A3GameNationRegistry::getNation( $nation );
	}
	
	public function invalidGetNationProvider( )
	{
		return array(
			array(
				'failNation',
			),
			array(
				'',
			),
			array(
				null,
			),
			array(
				123456,
			),
		);
	}
	
	/**
	 * @dataProvider validGetNationProvider
	 */
	public function testValidGetNation( $nation )
	{
		$aNation = A3GameNationRegistry::getNation( $nation );
		$this->assertType( 'A3GameNation', $aNation );
		
		$sameNation = A3GameNationRegistry::getNation( $nation );
		$this->assertType( 'A3GameNation', $sameNation );
		
		// the registry should return the same object instance!
		$this->assertTrue( $aNation === $sameNation );
	}
	
	public function validGetNationProvider( )
	{
		return array(
			array(
				'Russia',
			),
			array(
				'Germany',
			),
			array(
				'USA',
			),
			array(
				'Britain',
			),
			array(
				'Japan',
			),
			array(
				'China',
			),
		);
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
			  	'USA',
				'China',
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