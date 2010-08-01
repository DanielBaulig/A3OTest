<?php
require_once 'PHPUnit/Framework.php';

class MatchZoneTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new MatchZoneTestSuite( 'MatchZone Test Cases' );		
		$suite->addTestSuite( 'BasicMatchZoneFactoryTest' );
		$suite->addTestSuite( 'BasicMatchZoneRegistryTest' );
		$suite->addTestSuite( 'BasicMatchZoneTest' );
		return $suite;
	}
}

class BasicMatchZoneFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];	
	}
	
	const TEST_MATCH_ID = 1;
	
	public function testInstanciation( )
	{
		$factory = new MatchZonePDOFactory( $this->pdo, self::TEST_MATCH_ID );
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
	}

	/**
	 * @dataProvider exceptionGetZoneProvider
	 */
	public function testExceptionGetZone( $zone )
	{
		$this->setExpectedException( 'DomainException' );
		MatchZoneRegistry::getZone( $zone );
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
		$zone = MatchZoneRegistry::getZone( $zone );
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
	}
	/**
	 * @dataProvider instanciationProvider
	 */
	public function testInstanciation( array $data )
	{
		$zone = new MatchZone( $data );
		$this->assertType( 'MatchZone', $zone );
	}
	
	/**
	 * @dataProvider countPiecesProvider
	 */
	public function testCountPieces( $expected, $nation, $type )
	{
		$zone = MatchZoneRegistry::getZone( 'Archangel' );
		
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
