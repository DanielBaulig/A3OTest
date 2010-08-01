<?php
require_once 'PHPUnit/Framework.php';

class A3MatchZoneTestSuite extends PHPUnit_Framework_TestSuite
{
	private $gameTypeFactory;
	private $matchZoneFactory;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		
		$this->gameTypeFactory = GameTypeRegistry::getInstance( )->swapFactory( new A3GameTypePDOFactory( $this->pdo, BasicGameTypeFactoryTest::TEST_GAME_ID ) );
		$this->matchZoneFactory = MatchZoneRegistry::getInstance( )->swapFactory( new A3MatchZonePDOFactory( $this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID ) );
	}
	
	public function tearDown()
	{
		GameTypeRegistry::getInstance( )->swapFactory( $this->gameTypeFactory );
		MatchZoneRegistry::getInstance( )->swapFactory( $this->matchZoneFactory );
	}
	
	public static function suite( )
	{
		$suite = new A3MatchZoneTestSuite( 'A3MatchZone Test Cases' );		
	
		$suite->addTestSuite( 'A3MatchZoneRegistryTests' );
		$suite->addTestSuite( 'A3MatchZoneFactoryTests' );
		
		return $suite;
	}
}

class A3MatchZoneFactoryTests extends PHPUnit_Framework_TestCase
{
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testCreateSingle( )
	{
		$factory = new A3MatchZonePDOFactory( $this->pdo, BasicMatchZoneFactoryTest::TEST_MATCH_ID );
		$zone = $factory->createSingleProduct( 'Archangel' );
		$this->assertType( 'A3MatchZone', $zone );
	}
}

class A3MatchZoneRegistryTests extends PHPUnit_Framework_TestCase
{
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	/**
	 * @dataProvider getElementProvider
	 */
	public function testGetElement( $zone )
	{
		$zone = MatchZoneRegistry::getZone( $zone );
		$this->assertType( 'A3MatchZone', $zone );
	}
	
	public function getElementProvider( )
	{
		return array(
			array(
				'Archangel'
			),
			array(
				'SZ Water'
			),
			array(
				'Belarus'
			),
			array(
				'West Russia'
			),
		);
	}
}