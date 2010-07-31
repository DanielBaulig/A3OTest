<?php
require_once 'PHPUnit/Framework.php';

class A3MatchZoneTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new A3MatchZoneTestSuite( 'A3MatchZone Test Cases' );		
		$suite->addTestSuite( 'BasicA3MatchZoneFactoryTest' );
		$suite->addTestSuite( 'BasicA3MatchZoneRegistryTest' );
		$suite->addTestSuite( 'BasicA3MatchZoneTest' );
		return $suite;
	}
}

class BasicA3MatchZoneFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];	
	}
	
	const TEST_MATCH_ID = 1;
	
	public function testInstanciation( )
	{
		$factory = new A3MatchZonePDOFactory( $this->pdo, self::TEST_MATCH_ID );
		$this->assertType( 'A3MatchZonePDOFactory', $factory );
		return $factory;
	}
	
	/**
	 * @depends testInstanciation
	 */
	public function testProduction( A3MatchZonePDOFactory $factory )
	{
		$zone = $factory->createSingleProduct( 'Archangel' );
		$this->assertType( 'A3MatchZone', $zone );
		$zone = $factory->createSingleProduct( 'Belarus' );
		$this->assertType( 'A3MatchZone', $zone );
		$this->setExpectedException( 'Exception' );
		$zone = $factory->createSingleProduct( 'Fail' );
	}
}

class BasicA3MatchZoneRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];	
	}
	
	public function testInitialization( )
	{
		
	}
}

class BasicA3MatchZoneTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];	
	}
	/**
	 * @dataProvider instanciationProvider
	 */
	public function testInstanciation( array $data )
	{
		$zone = new A3MatchZone( $data );
		$this->assertType( 'A3MatchZone', $zone );
	}
	
	/**
	 * @dataProvider countPiecesProvider
	 */
	public function testCountPieces( $expected, $nation, $type, $remainingMovement = null )
	{
		$zone = A3MatchZoneRegistry::getZone( 'Archangel' );
		
		$this->assertEquals( $expected, $zone->countPieces( $nation, $type, $remainingMovement ) );
		
		/*$this->assertEquals( 5, $zone->countPieces( 'Russia', 'infantry' ) );
		$this->assertEquals( 5, $zone->countPieces( 'Russia', 'infantry', 1 ) );
		$this->assertEquals( 2, $zone->countPieces( 'Russia', 'tank', 1 ) );
		$this->assertEquals( 0, $zone->countPieces( 'Russia', 'infantry', 10 ) );
		
		// these following calls look like they should provoke DomainExceptions
		// however, countPieces will never try to instanciate type or nation 
		// objects because it does not need them to determine the number of those
		// units within the zone.
		// this (and canMove, bc it depends entire on count pieces) is the only 
		// place in the API where invalid keys can be used without provoking 
		// DomainExceptions. I wonder if I should change this just for the sake 
		// of consistency - even if this would mean a tiny performance loss.
		$this->assertEquals( 0, $zone->countPieces( '', 'infantry', 1 ) );
		$this->assertEquals( 0, $zone->countPieces( '', '', 1 ) );
		$this->assertEquals( 0, $zone->countPieces( 'Russia', '', 1 ) );
		$this->assertEquals( 0, $zone->countPieces( '', '' ) );
		$this->assertEquals( 0, $zone->countPieces( NULL, NULL ) );
		$this->assertEquals( 0, $zone->countPieces( 123, 456, 789 ) );*/
	}
	
	public function countPiecesProvider( )
	{
		return array(
			array(
				5, 'Russia', 'infantry',
			),
			array(
				5, 'Russia', 'infantry', 1,
			),
			array(
				2, 'Russia', 'tank',
			),
			array(
				2, 'Russia', 'tank', 1
			),
			array(
				2, 'Russia', 'tank', 2
			),
			array(
				0, 'Russia', 'infantry', 2,
			),
			array(
				0, 'Russia', 'tank', 3,
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
			array(
				0, null, null, null,
			),
		);
	}
	
	/**
	 * @dataProvider cantMoveProvider
	 */
	public function testCantMove( $count, $nation, $type, $distance )
	{
		$zone = A3MatchZoneRegistry::getZone( 'Archangel' );
		$this->assertFalse( $zone->canMovePieces( $count, $nation, $type, $distance ) );
	}
		
	public function cantMoveProvider( )
	{
		return array(
			/*
			 * That these values never get -1 should be assured when they are first touched!
			 * They are a serious security threat and could be used to exploit game mechanics!
			array(
				-1, 'Russia', 'infantry', 0,
			),
			array(
				0, 'Russia', 'infantry', -1,
			),*/
			array(
				6, 'Russia', 'infantry', 1,
			),
			array(
				1, 'Russia', 'infantry', 2,
			),
			array(
				1, 'Russia', 'tank', 3,
			),
			array(
				3, 'Russia', 'tank', 1,
			),
			array(
				1, 'failNation', 'infantry', 1,
			),
			array(
				1, 'Russia', 'failType', 1,
			),
			array(
				null, null, null, null,
			),
			array(
				1, null, 'infantry', 1,
			),
			array(
				1, 'Russia', null, 1,
			),			
		);
	}
	
	/**
	 * @dataProvider canMoveProvider
	 */
	public function testCanMove( $count, $nation, $type, $distance )
	{
		$zone = A3MatchZoneRegistry::getZone( 'Archangel' );
		$this->assertTrue( $zone->canMovePieces( $count, $nation, $type, $distance ) );
		
		/*$this->assertTrue( $zone->canMovePieces( 5, 'Russia', 'infantry', 1 ) );
		$this->assertTrue( $zone->canMovePieces( 0, 'Russia', 'infantry', 1 ) );
		$this->assertFalse( $zone->canMovePieces( 0, 'Russia', 'infantry', 10 ) );
		$this->assertTrue( $zone->canMovePieces( 3, 'Russia', 'infantry', 1 ) );
		$this->assertTrue( $zone->canMovePieces( 5, 'Russia', 'infantry', 0 ) );
		$this->assertTrue( $zone->canMovePieces( 0, 'Russia', 'infantry', 0 ) );
		$this->assertTrue( $zone->canMovePieces( 2, 'Russia', 'tank', 2 ) );
		$this->assertFalse( $zone->canMovePieces( 2, 'Russia', 'tank', 3 ) );
		$this->assertFalse( $zone->canMovePieces( 3, 'Russia', 'tank', 2 ) );
		
		// see testCountPieces why these dont throw DomainExceptions
		$this->assertFalse( $zone->canMovePieces( 1, 'Russia', 'failUnit', 1 ) );
		$this->assertFalse( $zone->canMovePieces( 1, 'failNation', 'infantry', 1 ) );
		$this->assertFalse( $zone->canMovePieces( 1, null, null, 1 ) );
		$this->assertFalse( $zone->canMovePieces( null, null, null, null ) );
		$this->assertFalse( $zone->canMovePieces( 0, 'Russia', null, 0 ) );*/
	}
	
	public function canMoveProvider( )
	{
		return array(
			array(
				0, 'Russia', 'infantry', 0,
			),
			array(
				1, 'Russia', 'infantry', 0,
			),
			array(
				0, 'Russia', 'infantry', 1,
			),
			array(
				1, 'Russia', 'infantry', 1,
			),			
			array(
				5, 'Russia', 'infantry', 1,
			),
			array(
				0, 'Russia', 'tank', 0,
			),
			array(
				1, 'Russia', 'tank', 1,
			),
			array(
				1, 'Russia', 'tank', 2,
			),
			array(
				2, 'Russia', 'tank', 2,
			),
			array(
				null, 'Russia', 'infantry', 1,
			),
			array(
				1, 'Russia', 'infantry', null,
			),
		);
	}
	
	/**
	 * @dataProvider exceptionPathProvider
	 */
	public function testExceptionPath( array $path )
	{
		$zone = A3MatchZoneRegistry::getZone( 'Archangel' );
		$this->setExpectedException('DomainException');
		$zone->isValidPath( $path );
	}	
	/**
	 * @dataProvider invalidPathProvider
	 */
	public function testInvalidPaths( array $path )
	{
		$zone = A3MatchZoneRegistry::getZone( 'Archangel' );
		$this->assertFalse( $zone->isValidPath( $path ) );
	}	
	/**
	 * @dataProvider validPathProvider
	 */
	public function testValidPath( array $path )
	{
		$zone = A3MatchZoneRegistry::getZone( 'Archangel' );
		$this->assertTrue( $zone->isValidPath( $path ) );
	}
	
	public function validPathProvider( ){
		return array(
			array(
				array(
					'Belarus',
				),
				array(
					'Belarus', 
					'Archangel',
				),
				array(
					'Belarus', 
					'Archangel',
					'Belarus',
				),
			)
		);
	}
	
	public function invalidPathProvider( )
	{
		return array(
			array(
				array(
					'Archangel',
				),
				array(
					'SZ Water',
				),
				array(
					'Belarus', 'Belarus'
				),
				array(
					'Belarus', 'SZ Water'
				),
			)
		);
	}
	
	public function exceptionPathProvider( )
	{
		return array(
			array(
				array(
					null,
				),
				array(
					'',
				),
				array(
					'failZone',
				),
				array(
					'Belarus', null,
				),
				array(
					'Belarus', 'failZone',
				),
				array(
					'Belarus', 'Archangel', null,
				),
				array(
					'Belarus', 'Archangel', 'failZone',
				),
				array(
					'Belarus', 'Archangel', 2,
				),
			)
		);
	}
	
	public function instanciationProvider( )
	{
		return array(
			array ( 
				array ( 
					A3MatchZone::NAME => '',
					A3MatchZone::OWNER => '',
					A3MatchZone::CONNECTIONS => array( ),
					A3MatchZone::PIECES => array( ),
					A3MatchZone::OPTIONS => array( ),
				),
			),	
			array ( 
				array ( 
					A3MatchZone::NAME => 'Archangel',
					A3MatchZone::OWNER => 'Russia',
					A3MatchZone::CONNECTIONS => array( 'Belarus', 'SZ Water' ),
					A3MatchZone::PIECES => array( 'Russia' => array( 'infantry' => array( 1 => 5 ) ) ),
					A3MatchZone::OPTIONS => array( 'production' => 2 ),
				),
			),			
		);
	}
}
