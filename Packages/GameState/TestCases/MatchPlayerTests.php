<?php 

class MatchPlayerTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new MatchPlayerTestSuite( 'MatchPlayer Test Cases' );		
		$suite->addTestSuite( 'MatchPlayerFactoryTests' );
		$suite->addTestSuite( 'MatchPlayerRegistryTests' );
		$suite->addTestSuite( 'MatchPlayerTests' );
		$suite->addTestSuite( 'MatchPlayerStorerTests' );
		
		
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

class MatchPlayerStorerTests extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	protected $test_db;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->test_db = $this->sharedFixture['test_db'];
	}
	
	public function tearDown( )
	{
		// reset database once more
		$this->test_db->onSetUp( );
	}
	
	/**
	 * @dataProvider storePlayerProvider
	 */
	public function testStorePlayer( array $playerData, $expectedTemplate )
	{
		$this->test_db->onSetUp( );
		
		$player = new MatchPlayer( $playerData );
		$storer = new MatchPlayerPDOStorer($this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID );

		$xml_postStore = new PHPUnit_Extensions_Database_DataSet_XmlDataSet( BASEDIR . '/_database/' . $expectedTemplate );
		
		$storer->store( $player );
		
		PHPUnit_Extensions_Database_TestCase::assertTablesEqual( $xml_postStore->getTable('a3o_players'), $this->test_db->getConnection()->createDataset()->getTable('a3o_players') );
	}
	
	public function storePlayerProvider( )
	{
		return array(
			 array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'Russia', 
				 	MatchPlayer::USER=> 1, 
				 	MatchPlayer::OPTIONS=> array()
			 	),
			 	'phpunit_a3o.xml' 
			 ), 
			 array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'USA', 
				 	MatchPlayer::USER=> 2, 
				 	MatchPlayer::OPTIONS=> array()
			 	),
			 	'phpunit_a3o_players_withUSA.xml' 
			 ), 
			 array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'Germany', 
				 	MatchPlayer::USER=> 5, 
				 	MatchPlayer::OPTIONS=> array()
			 	),
			 	'phpunit_a3o_players_withGermany.xml' 
			 ),
			 array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'Russia', 
				 	MatchPlayer::USER=> 4, 
				 	MatchPlayer::OPTIONS=> array()
			 	),
			 	'phpunit_a3o_players_changedRussia.xml' 
			 ),
		);
	}
	
	/**
	 * @dataProvider storePlayerOptionProvider
	 */
	public function testStorePlayerOption( array $playerData, $expectedTemplate )
	{
		$this->test_db->onSetUp( );
		
		$player = new MatchPlayer( $playerData );
		$storer = new MatchPlayerPDOStorer($this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID );

		$xml_postStore = new PHPUnit_Extensions_Database_DataSet_XmlDataSet( BASEDIR . '/_database/' . $expectedTemplate );
		
		$storer->store( $player );
		
		PHPUnit_Extensions_Database_TestCase::assertTablesEqual( $xml_postStore->getTable('a3o_playeroptions'), $this->test_db->getConnection()->createDataset()->getTable('a3o_playeroptions') );
	}
	
	public function storePlayerOptionProvider( )
	{
		return array(
			array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'Russia', 
				 	MatchPlayer::USER=> 1, 
				 	MatchPlayer::OPTIONS=> array( 'UselessOption' => 0 )
			 	),
			 	'phpunit_a3o.xml' 
			 ), 
			array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'Russia', 
				 	MatchPlayer::USER=> 1, 
				 	MatchPlayer::OPTIONS=> array( 'Technology' => 1 )
			 	),
			 	'phpunit_a3o_playeroptions_withPlayer1Tech.xml' 
			 ), 
			 array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'Russia', 
				 	MatchPlayer::USER=> 1, 
				 	MatchPlayer::OPTIONS=> array( 'UselessOption' => 1 )
			 	),
			 	'phpunit_a3o_playeroptions_withPlayer1UselessTrue.xml' 
			 ), 
			 array( 
			 	array(			 	
				 	MatchPlayer::NATION => 'Russia', 
				 	MatchPlayer::USER=> 1, 
				 	MatchPlayer::OPTIONS=> array( 'CoolOption' => 'This is so cool!' )
			 	),
			 	'phpunit_a3o_playeroptions_withPlayer1Cool.xml' 
			 ), 
		);
	}
	
	public function testClearOptions( )
	{
		$this->test_db->onSetUp( );
		
		$player = new MatchPlayer( 
			array(			 	
				 	MatchPlayer::NATION => 'Russia', 
				 	MatchPlayer::USER=> 1, 
				 	MatchPlayer::OPTIONS=> array( 'Technology' => 1 )
			) 
		);
		$storer = new MatchPlayerPDOStorer($this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID );

		$xml_postStore = new PHPUnit_Extensions_Database_DataSet_XmlDataSet( BASEDIR . '/_database/phpunit_a3o_playeroptions_cleared.xml' );

		$storer->store( $player );
		$storer->clearMatchPlayerOptions( );
		
		PHPUnit_Extensions_Database_TestCase::assertTablesEqual( $xml_postStore->getTable('a3o_playeroptions'), $this->test_db->getConnection()->createDataset()->getTable('a3o_playeroptions') );
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