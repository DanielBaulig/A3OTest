<?php
require_once 'PHPUnit/Framework.php';

class GameNationTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new GameNationTestSuite( 'GameNation Test Cases' );
		$suite->addTestSuite( 'GameNationFactoryTest' );
		$suite->addTestSuite( 'GameNationRegistryTest' );		
		$suite->addTestSuite( 'BasicGameNationTest' );
		
		return $suite;
	}
}

class GameNationFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testLoadAllNations( )
	{
		$factory = new GameNationPDOFactory( $this->pdo, BasicGameTypeFactoryTest::TEST_GAME_ID );
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
		$factory = new GameNationPDOFactory( $this->pdo, -1 );
		$nations = $factory->createAllProducts( );
		$this->assertEquals( 0, count( $nations ) );
	}
	
	/**
	 * @dataProvider failLoadSingleNationProvider
	 */
	public function testFailLoadSingleNation( $nation )
	{
		$factory = new GameNationPDOFactory( $this->pdo, BasicGameTypeFactoryTest::TEST_GAME_ID );
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
		$factory = new GameNationPDOFactory( $this->pdo, BasicGameTypeFactoryTest::TEST_GAME_ID );
		$nation = $factory->createSingleProduct( $nation );
		$this->assertType( 'GameNation', $nation );
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

class GameNationRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testReinitializationException( )
	{
		$this->setExpectedException( 'Exception' );		
		GameNationRegistry::initializeRegistry( new GameNationPDOFactory( $this->pdo, BasicGameTypeFactoryTest::TEST_GAME_ID ) );
	}
	
	public function testGetInstance( )
	{
		$this->assertType( 'GameNationRegistry', GameNationRegistry::getInstance( ) );	
	}
	
	/**
	 * @dataProvider invalidGetNationProvider
	 */
	public function testInvalidGetNation( $nation )
	{
		$this->setExpectedException( 'DomainException' );
		GameNationRegistry::getNation( $nation );
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
		$aNation = GameNationRegistry::getNation( $nation );
		$this->assertType( 'GameNation', $aNation );
		
		$sameNation = GameNationRegistry::getNation( $nation );
		$this->assertType( 'GameNation', $sameNation );
		
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

class BasicGameNationTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	public function testInstanciation( )
	{
		$nation = new GameNation( array( GameNation::NAME => 'Test Land', GameNation::ALLIANCES => array( ) ) );
		$this->assertType( 'GameNation', $nation ); 
	}
	
	/**
	 * @dataProvider exceptionIsAllyProvider
	 */
	public function testExceptionIsAlly( $ally )
	{
		$nation = GameNationRegistry::getNation( 'Russia' );
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
		$nation = GameNationRegistry::getNation( 'Russia' );
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
		$nation = GameNationRegistry::getNation( 'Russia' );
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
		$nation = GameNationRegistry::getNation( 'Russia' );
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
		$nation = GameNationRegistry::getNation( 'Russia' );
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