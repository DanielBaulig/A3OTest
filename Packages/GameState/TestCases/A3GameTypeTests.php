<?php
require_once 'PHPUnit/Framework.php';

class A3GameTypeTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new A3MatchZoneTestSuite( 'A3MatchZone Test Cases' );		
		$suite->addTestSuite( 'BasicA3GameTypeFactoryTest' );
		$suite->addTestSuite( 'BasicA3GameTypeRegistryTest' );
		$suite->addTestSuite( 'BasicA3GameTypeTest' );
		return $suite;
	}
}

class BasicA3GameTypeFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	const TEST_GAME_ID = 1;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testInstanciation( )
	{
		$factory = new A3GameTypePDOFactory( $this->pdo, self::TEST_GAME_ID );
		$this->assertType( 'A3GameTypePDOFactory', $factory );
		return $factory;
	}
	
	/**
	 * @depends testInstanciation 
	 */
	public function testCreateSingleProduct( A3GameTypePDOFactory $factory )
	{
		$type = $factory->createSingleProduct( 'infantry' );
		$this->assertType( 'A3GameType', $type );
	}
	
	/**
	 * @depends testInstanciation
	 */
	public function testCreateAllProducts( A3GameTypePDOFactory $factory )
	{
		$types = $factory->createAllProducts( );
		$this->assertType( PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $types );
		$this->assertGreaterThan( 0, count($types) );
		$this->assertType( 'A3GameType', $types['infantry'] );
	}
}

class BasicA3GameTypeRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	public function testInitializationFail( )
	{
		$this->setExpectedException( 'Exception' );
		A3GameTypeRegistry::initializeRegistry( new A3GameTypePDOFactory($this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID ) );
	}
	public function testGetInstance( )
	{
		$registry = A3GameTypeRegistry::getInstance( );
		$this->assertType( 'A3GameTypeRegistry', $registry );
	}
	public function testGetType( ){
		$type = A3GameTypeRegistry::getType( 'infantry' );
		$this->assertType( 'A3GameType', $type );
	}
}

class BasicA3GameTypeTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testOptions( )
	{
		// reset database
		$this->sharedFixture['test_db']->onSetUp( );
		// refresh the registry
		A3GameTypeRegistry::getInstance()->precacheElements( );

		$infantry = A3GameTypeRegistry::getType( 'infantry' );
		$this->assertEquals( 1, $infantry->movement );
		$this->assertEquals( 1, $infantry->attack );
		$this->assertEquals( 2, $infantry->defense );
		$this->assertEquals( 0, $infantry->factory );
		$this->assertEquals( 0, $infantry->invalid );
		$tank = A3GameTypeRegistry::getType( 'tank' );
		$this->assertEquals( 2, $tank->movement );
		$this->assertEquals( 3, $tank->attack );
		$this->assertEquals( 3, $tank->defense );
		$this->assertEquals( 0, $tank->factory );
		$this->assertEquals( 0, $tank->invalid );
		$factory = A3GameTypeRegistry::getType( 'factory' );
		$this->assertEquals( 0, $factory->movement );
		$this->assertEquals( 0, $factory->attack );
		$this->assertEquals( 0, $factory->defense );
		$this->assertEquals( 1, $factory->factory );
		$this->assertEquals( 0, $factory->invalid );
		
		$this->assertFalse( isset($factory->movement) );
		$this->assertFalse( isset($factory->attack) );
		$this->assertTrue( isset($factory->factory) );
		
		$this->sharedFixture['test_db']->onTearDown( );
	}
}
