<?php

class GameTypeTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new GameTypeTestSuite( 'MatchZone Test Cases' );		
		$suite->addTestSuite( 'BasicGameTypeFactoryTest' );
		$suite->addTestSuite( 'BasicGameTypeRegistryTest' );
		$suite->addTestSuite( 'BasicGameTypeTest' );
		return $suite;
	}
}

class BasicGameTypeFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	const TEST_GAME_ID = 1;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	
	public function testInstanciation( )
	{
		$factory = new GameTypePDOFactory( $this->pdo, $this->match );
		$this->assertType( 'GameTypePDOFactory', $factory );
		return $factory;
	}
	
	/**
	 * @depends testInstanciation 
	 */
	public function testCreateSingleProduct( GameTypePDOFactory $factory )
	{
		$type = $factory->createSingleProduct( 'infantry' );
		$this->assertType( 'GameType', $type );
	}
	
	/**
	 * @depends testInstanciation
	 */
	public function testCreateAllProducts( GameTypePDOFactory $factory )
	{
		$types = $factory->createAllProducts( );
		$this->assertType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $types );
		$this->assertGreaterThan( 0, count($types) );
		$this->assertType( 'GameType', $types['infantry'] );
	}
}

class BasicGameTypeRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	public function testGetType( ){
		$type = $this->match->getType( 'infantry' );
		$this->assertType( 'GameType', $type );
	}
}

class BasicGameTypeTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	//TODO: Add some meaningful tests!
	public function testNothing( )
	{
		$this->assertTrue(true);
	}
}
