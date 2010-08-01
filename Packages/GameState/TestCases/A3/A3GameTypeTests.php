<?php
require_once 'PHPUnit/Framework.php';

class A3GameTypeTestSuite extends PHPUnit_Framework_TestSuite
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
		$suite = new A3GameTypeTestSuite( 'A3MatchZone Test Cases' );		

		$suite->addTestSuite( 'A3GameTypeFactoryTests' );
		$suite->addTestSuite( 'A3GameTypeTests' );
		
		return $suite;
	}
}

class A3GameTypeFactoryTests extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testCreateProduct( )
	{
		$factory = new A3GameTypePDOFactory( $this->pdo, BasicGameTypeFactoryTest::TEST_GAME_ID );
		$type = $factory->createSingleProduct( 'infantry' );
		$this->assertType( 'A3GameType', $type );
	}
}

class A3GameTypeTests extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	/**
	 * @dataProvider cantTraverseProvider
	 */
	public function testCantTraverse( $type, array $path, $nation, $allowCombat = false )
	{
		//$this->markTestSkipped();
		$type = GameTypeRegistry::getType( $type );
		$this->assertFalse( $type->canTraversePath( $path, $nation, $allowCombat ) );
	}
	
	public function cantTraverseProvider( )
	{
		return array(
			array(
				'infantry', array( 'Archangel', 'Belarus', 'Archangel' ), 'Russia'
			),
			array(
				'tank', array( 'Archangel', 'Belarus', 'Archangel', 'Belarus' ), 'Russia'
			),
			array(
				'tank', array( 'Archangel', 'SZ Water' ), 'Russia'
			),
			array(
				'tank', array( 'Archangel', 'SZ Water', 'Archangel' ), 'Russia'
			),
			array(
				'infantry', array( 'Archangel', 'West Russia' ), 'Russia'
			),
			array(
				'tank', array( 'Archangel', 'West Russia' ), 'Russia'
			),
			array(
				'tank', array( 'Archangel', 'West Russia', 'Archangel' ), 'Russia', true			
			),
			array(
				'fighter', array( 'Archangel', 'West Russia', 'Belarus', 'West Russia' ), 'Russia', false			
			),
		);
	}
	
	/**
	 * @dataProvider canTraverseProvider
	 */
	public function testCanTraverse( $type, array $path, $nation, $allowCombat = false )
	{
		$type = GameTypeRegistry::getType( $type );
		$this->assertTrue( $type->canTraversePath( $path, $nation, $allowCombat ) );
	}
	
	public function canTraverseProvider( )
	{
		return array(
			array(
				'infantry', array( 'Archangel'), 'Russia'
			),
			array(
				'infantry', array( 'Archangel', 'Belarus' ), 'Russia'
			),
			array(
				'tank', array( 'Archangel', 'Belarus' ), 'Russia'
			),
			array(
				'tank', array( 'Archangel', 'Belarus', 'Archangel' ), 'Russia'
			),
			array(
				'fighter', array('Archangel', 'SZ Water'), 'Russia'
			),
			array(
				'fighter', array('Archangel', 'SZ Water', 'Archangel'), 'Russia'
			),
			array(
				'fighter', array('Archangel', 'West Russia', ), 'Russia', true
			),
			array(
				'fighter', array('Archangel', 'West Russia', 'Archangel', 'SZ Water', 'Archangel'), 'Russia'
			),
			array(
				'infantry', array( 'Archangel', 'West Russia' ), 'Russia', true
			),
			array(
				'tank', array( 'Archangel', 'Belarus', 'West Russia' ), 'Russia', true
			),
		);
	}

}