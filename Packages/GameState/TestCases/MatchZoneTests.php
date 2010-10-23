<?php

class MatchZoneTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new MatchZoneTestSuite( 'MatchZone Test Cases' );		
		$suite->addTestSuite( 'BasicMatchZoneFactoryTest' );
		$suite->addTestSuite( 'BasicMatchZoneRegistryTest' );
		$suite->addTestSuite( 'BasicMatchZoneTest' );
		$suite->addTestSuite( 'MatchZoneStorerTests' );
		
		return $suite;
	}
}

class BasicMatchZoneFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];	
		$this->match = $this->sharedFixture['match_state'];
	}
	
	const TEST_MATCH_ID = 1;
	
	public function testInstanciation( )
	{
		$factory = new MatchZonePDOFactory( $this->pdo, $this->match );
		$this->assertType( 'MatchZonePDOFactory', $factory );
		return $factory;
	}
	
	/**
	 * @depends testInstanciation
	 */
	public function testProduction( MatchZonePDOFactory $factory )
	{
		$zone = $factory->createSingleProduct( 'Archangel' );
		$this->assertType( 'MatchZone', $zone );
		$zone = $factory->createSingleProduct( 'Belarus' );
		$this->assertType( 'MatchZone', $zone );
		$this->setExpectedException( 'Exception' );
		$zone = $factory->createSingleProduct( 'Fail' );
	}
}

class BasicMatchZoneRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];	
		$this->match = $this->sharedFixture['match_state'];
	}

	/**
	 * @dataProvider exceptionGetZoneProvider
	 */
	public function testExceptionGetZone( $zone )
	{
		$this->setExpectedException( 'DomainException' );
		$this->match->getZone( $zone );
	}
	
	public function exceptionGetZoneProvider( )
	{
		return array(
			array(
				'failZone'
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
	 * @dataProvider getZoneProvider
	 */
	public function testGetZone( $zone )
	{
		$zone = $this->match->getZone( $zone );
		$this->assertType( 'MatchZone', $zone );
		
	}
	
	public function getZoneProvider( )
	{
		return array(
			array(
				'Archangel'
			),
			array(
				'Belarus'
			),
			array(
				'SZ Water'
			),
		);
	}
}

class BasicMatchZoneTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];	
		$this->match = $this->sharedFixture['match_state'];
	}
	/**
	 * @dataProvider instanciationProvider
	 */
	public function testInstanciation( array $data )
	{
		$zone = new MatchZone( $this->match, $data );
		$this->assertType( 'MatchZone', $zone );
	}
	
	/**
	 * @dataProvider countPiecesProvider
	 */
	public function testCountPieces( $expected, $nation, $type )
	{
		$zone = $this->match->getZone( 'Archangel' );
		
		$this->assertEquals( $expected, $zone->countPieces( $nation, $type ) );
	}
	
	public function countPiecesProvider( )
	{
		return array(
			array(
				5, 'Russia', 'infantry',
			),
			array(
				2, 'Russia', 'tank',
			),
			array(
				0, '', 'tank',
			),
			array(
				0, 'Russia', '',
			),
			array(
				0, null, 'tank',
			),
			array(
				0, 'Russia', null,
			),
			array(
				0, 'failNation', 'tank',
			),
			array(
				0, 'Russia', 'failType',
			),
		);
	}
	
	public function instanciationProvider( )
	{
		return array(
			array ( 
				array ( 
					MatchZone::NAME => '',
					MatchZone::OWNER => '',
					MatchZone::CONNECTIONS => array( ),
					MatchZone::PIECES => array( ),
					MatchZone::OPTIONS => array( ),
				),
			),	
			array ( 
				array ( 
					MatchZone::NAME => 'Archangel',
					MatchZone::OWNER => 'Russia',
					MatchZone::CONNECTIONS => array( 'Belarus', 'SZ Water' ),
					MatchZone::PIECES => array( 'Russia' => array( 'infantry' => array( 1 => 5 ) ) ),
					MatchZone::OPTIONS => array( 'production' => 2 ),
				),
			),	
		);
	}
}

class MatchZoneStorerTests extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	protected $test_db;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->test_db = $this->sharedFixture['test_db'];
		$this->match = $this->sharedFixture['match_state'];
	}
	
	public function tearDown( )
	{
		// reset database once more
		$this->test_db->onSetUp( );
	}
	
	public function testStreamStorer(   )
	{
		$this->test_db->onSetUp( );

		$zone = new MatchZone( $this->match, array(			 	
				 	MatchZone::NAME => 'Archangel', 
				 	MatchZone::OWNER=> 'Russia', 
					MatchZone::PIECES=>array( 'Russia' => array( 'infantry'=>5, 'tank'=>2, 'fighter'=>1 ) ),
		));
		$stream = fopen('php://temp', 'w+');
		$storer = new MatchZoneStreamStorer($stream, true);
		$storer->store($zone);
		fseek($stream, 0);
		$s = fgets($stream);
		$this->assertEquals('"Archangel":{"name":"Archangel","owner":"Russia","pieces":{"Russia":{"infantry":5,"tank":2,"fighter":1}}}', $s);
	}
	
	/**
	 * @dataProvider storeZoneProvider
	 */
	public function testStoreZone( array $zoneData, $expectedZones, $expectedPieces )
	{
		$this->test_db->onSetUp( );
		
		$player = new MatchZone( $this->match, $zoneData );
		$storer = new MatchZonePDOStorer($this->pdo, $this->match->getMatchId( ) );

		$xml_postStoreZones = new PHPUnit_Extensions_Database_DataSet_XmlDataSet( BASEDIR . '/_database/' . $expectedZones );
		$xml_postStorePieces = new PHPUnit_Extensions_Database_DataSet_XmlDataSet( BASEDIR . '/_database/' . $expectedPieces );
		
		$storer->store( $player );
		
		PHPUnit_Extensions_Database_TestCase::assertTablesEqual( $xml_postStoreZones->getTable('a3o_zones'), $this->test_db->getConnection()->createDataset()->getTable('a3o_zones') );
		PHPUnit_Extensions_Database_TestCase::assertTablesEqual( $xml_postStorePieces->getTable('a3o_pieces'), $this->test_db->getConnection()->createDataset()->getTable('a3o_pieces') );
	}
	
	public function storeZoneProvider( )
	{
		return array(
			 array( 
			 	array(			 	
				 	MatchZone::NAME => 'Archangel', 
				 	MatchZone::OWNER=> 'Russia', 
					MatchZone::PIECES=>array( 'Russia' => array( 'infantry'=>5, 'tank'=>2, 'fighter'=>1 ) ),
			 	),
			 	'phpunit_a3o.xml',// a3o_zones
			 	'phpunit_a3o.xml',// a3o_pieces
			 ), 
			 array( 
			 	array(			 	
				 	MatchZone::NAME => 'Archangel', 
				 	MatchZone::OWNER=> 'Russia', 
					MatchZone::PIECES=>array( 'Russia' => array( 'infantry'=>0, 'tank'=>0, 'fighter'=>0 ) ),
			 	),
			 	'phpunit_a3o.xml',// a3o_zonesd
			 	'phpunit_a3o_pieces_zero.xml',// a3o_pieces
			 ), 
			 array( 
			 	array(			 	
				 	MatchZone::NAME => 'Archangel', 
				 	MatchZone::OWNER=> 'Russia', 
					MatchZone::PIECES=>array( 'Russia' => array( 'infantry'=>2, 'tank'=>4, 'fighter'=>10 ) ),
			 	),
			 	'phpunit_a3o.xml',// a3o_zones
			 	'phpunit_a3o_pieces_mixed.xml',// a3o_pieces
			 ), 
			 array( 
			 	array(			 	
				 	MatchZone::NAME => 'Archangel', 
				 	MatchZone::OWNER=> 'Russia', 
					MatchZone::PIECES=>array( 'Russia' => array( 'infantry'=>5, 'tank'=>2, 'fighter'=>1, 'factory'=>1 ) ),
			 	),
			 	'phpunit_a3o.xml',// a3o_zones
			 	'phpunit_a3o_pieces_with_factory.xml',// a3o_pieces
			 ),
			 array( 
			 	array(			 	
				 	MatchZone::NAME => 'Archangel', 
				 	MatchZone::OWNER=> 'Russia', 
					MatchZone::PIECES=>array( 
						'Russia' => array( 'infantry'=>5, 'tank'=>2, 'fighter'=>1), 
						'Germany'=> array('infantry'=>4, 'tank'=> 4, 'fighter'=>3) 
					),
			 	),
			 	'phpunit_a3o.xml',// a3o_zones
			 	'phpunit_a3o_pieces_with_german_army.xml',// a3o_pieces
			 ),
			 array( 
			 	array(			 	
				 	MatchZone::NAME => 'Archangel', 
				 	MatchZone::OWNER=> 'Germany', 
					MatchZone::PIECES=>array( 
						'Russia' => array( 'infantry'=>0, 'tank'=>0, 'fighter'=>0), 
						'Germany'=> array('infantry'=>0, 'tank'=> 1, 'fighter'=>2) 
					),
			 	),
			 	'phpunit_a3o_zones_conquered.xml',// a3o_zones
			 	'phpunit_a3o_pieces_conquered.xml',// a3o_pieces
			 ),
		);
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