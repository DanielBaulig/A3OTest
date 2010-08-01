<?php 

class MatchPlayerTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new MatchPlayerTestSuite( 'MatchPlayer Test Cases' );		
		$suite->addTestSuite( 'MatchPlayerFactoryTests' );
		$suite->addTestSuite( 'MatchPlayerRegistryTests' );
		$suite->addTestSuite( 'MatchPlayerTests' );
		
		return $suite;
	}
}

class MatchPlayerFactoryTests extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	/**
	 * @dataProvider exceptionCreateElementProvider
	 */
	public function testExceptionCreateElement( $nation )
	{
		$this->setExpectedException( 'DomainException' );
		$factory = new MatchPlayerPDOFactory( $this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID );
		$factory->createSingleProduct( $nation );
	}
	
	public function exceptionCreateElementProvider( )
	{
		return array(
			array(
				'failNation',
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
	 * @dataProvider createElementProvider
	 */
	public function testCreateElement( $nation )
	{
		$factory = new MatchPlayerPDOFactory( $this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID );
		$player = $factory->createSingleProduct( $nation );
		$this->assertType( 'MatchPlayer', $player );
	}
	
	public function createElementProvider( )
	{
		return array(
			array(
				'Russia',
			)
		);
	}
	
	public function testCreateAllElements( )
	{
		$factory = new MatchPlayerPDOFactory( $this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID );
		$players = $factory->createAllProducts( );
		$this->assertArrayHasKey( 'Russia' , $players );
		$this->assertType( 'MatchPlayer' , $players['Russia'] );
		$this->assertEquals( 1, count( $players ) );
	}
}

class MatchPlayerRegistryTests extends PHPUnit_Framework_TestCase
{
	protected $pdo;	
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	/**
	 * @dataProvider getPlayerProvider
	 */
	public function testGetPlayer( $nation )
	{
		$player = MatchPlayerRegistry::getPlayer( $nation );
		$this->assertType( 'MatchPlayer', $player );
	}
	
	public function getPlayerProvider( )
	{
		return array(
			array(
				'Russia',
			)
		);
	}
}

class MatchPlayerTests extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	/**
	 * @dataProvider isUserProvider
	 */
	public function testIsUser( $nation, $user )
	{
		$player = MatchPlayerRegistry::getPlayer( $nation );
		
		$this->assertTrue( $player->isUser( $user ) );
	}
	
	public function isUserProvider( )
	{
		return array(
			array(
				'Russia', 1,
			),
		);
	}
}