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
		$this->assertEquals('Archangel', $zone->getName( ) );
		$zone = $factory->createSingleProduct( 'Belarus' );
		$this->assertType( 'A3MatchZone', $zone );
		$this->assertEquals('Belarus', $zone->getName( ) );
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
	 * @dataProvider instanciationProvider
	 */
	public function testCountPieces( array $data )
	{
		$zone = new A3MatchZone( $data );
		$this->coverCountPieces( $zone );
	}
	
	protected function coverCountPieces( $zone )
	{
		$zone->countPieces( 'Russia', 'infantry' );
		$zone->countPieces( 'Russia', 'infantry', 1 );
		$zone->countPieces( 'Russia', 'infantry', 10 );
		$zone->countPieces( '', 'infantry', 1 );
		$zone->countPieces( '', '', 1 );
		$zone->countPieces( 'Russia', '', 1 );
		$zone->countPieces( '', '' );
		$zone->countPieces( NULL, NULL );
		$zone->countPieces( 123, 456, 789 );
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
