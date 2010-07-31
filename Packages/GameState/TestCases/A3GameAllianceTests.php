<?php

class A3GameAllianceTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new A3GameAllianceTestSuite( 'A3GameAlliance Test Cases' );
		
		$suite->addTestSuite( 'A3GameAllianceFactoryTest' );
		$suite->addTestSuite( 'A3GameAllianceRegistryTest' );
		$suite->addTestSuite( 'A3GameAllianceTest' );		
		
		return $suite;
	}
}

class A3GameAllianceFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testLoadAllAlliances( )
	{
		$factory = new A3GameAlliancePDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID );
		$alliances = $factory->createAllProducts( );
		$this->assertArrayHasKey( 'Axis' , $alliances );
		$this->assertArrayHasKey( 'Allies' , $alliances );
		$this->assertArrayHasKey( 'UDSSR' , $alliances );
		$this->assertEquals( 3 , count( $alliances ) );
	}
	
	public function testLoadNoAlliance( )
	{
		$factory = new A3GameAlliancePDOFactory( $this->pdo, -1 );
		$alliances = $factory->createAllProducts( );
		$this->assertEquals( 0, count( $alliances ) );
	}
	
	/**
	 * @dataProvider failLoadSingleAllianceProvider
	 */
	public function testFailLoadSingleAlliance( $alliance )
	{
		$factory = new A3GameAlliancePDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID );
		$this->setExpectedException( 'DomainException' );
		$factory->createSingleProduct( $alliance );
	}
	
	public function failLoadSingleAllianceProvider( )
	{
		return array(
			array(
				'failAlliance',
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
	 * @dataProvider loadSingleAllianceProvider
	 */
	public function testLoadSingleAlliance( $alliance )
	{
		$factory = new A3GameAlliancePDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID );
		$alliance = $factory->createSingleProduct( $alliance );
		$this->assertType( 'A3GameAlliance' , $alliance );
	}
	
	public function loadSingleAllianceProvider( )
	{
		return array(
			array(
				'Axis'
			),
			array(
				'Allies'
			),
			array(
				'UDSSR'
			),
		);
	}
}

class A3GameAllianceRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testReinitializationException( )
	{
		$this->setExpectedException( 'Exception' );
		A3GameAllianceRegistry::initializeRegistry( new A3GameAlliancePDOFactory( $this->pdo, BasicA3GameTypeFactoryTest::TEST_GAME_ID ) );
	}
	
	public function testGetInstance( )
	{
		$this->assertType( 'A3GameAllianceRegistry', A3GameAllianceRegistry::getInstance( ) );
	}
	
	/**
	 * @dataProvider getValidAllianceProvider
	 */
	public function testGetValidAlliance( $alliance )
	{
		$aAlliance = A3GameAllianceRegistry::getAlliance( $alliance );
		$this->assertType( 'A3GameAlliance', $aAlliance );
		
		$sameAlliance = A3GameAllianceRegistry::getAlliance( $alliance );
		$this->assertType( 'A3GameAlliance', $sameAlliance );
		
		$this->assertTrue( $aAlliance === $sameAlliance );
	}
	
	public function getValidAllianceProvider( )
	{
		return array(
			array(
				'UDSSR'	
			),
			array(
				'Axis'
			),
			array(
				'Allies'
			),
		);
	}
	
	/**
	 * @dataProvider getFailAllianceProvider
	 */
	public function testGetFailAlliance( $alliance )
	{
		$this->setExpectedException( 'DomainException' );
		A3GameAllianceRegistry::getAlliance( $alliance );
	}
	
	public function getFailAllianceProvider( )
	{
		return array(
			array(
				'failAlliance',
				'',
				null,
				12345
			),
		);
	}
}

class A3GameAllianceTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	/**
	 * @dataProvider isNotMemberProvider
	 */
	public function testIsNotMember( $nation )
	{
		$alliance = A3GameAllianceRegistry::getAlliance( 'Allies' );
		$this->assertFalse( $alliance->hasMember( $nation ) );
	}
	
	public function isNotMemberProvider( )
	{
		return array(
			array(
				'Germany',
			),
			array(
				'Japan'
			),
			array(
				'failNation'
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
	
	private $eachLoops;
	
	public function testForEachMember( )
	{
		$this->eachLoops = 0;
		$alliance = A3GameAllianceRegistry::getAlliance( 'UDSSR' );
		$alliance->forEachMember( array( $this, 'forEachUDSSR' ) );
		$this->assertEquals( 1, $this->eachLoops );
	}
	
	public function forEachUDSSR( $nation )
	{
		$this->assertEquals( 'Russia' , $nation );
		$this->eachLoops += 1;
	}
	
	/**
	 * @dataProvider hasMemberProvider
	 */
	public function testhasMember( $nation )
	{
		$alliance = A3GameAllianceRegistry::getAlliance( 'Allies' );
		$this->assertTrue( $alliance->hasMember( $nation ) );
	}
	
	public function hasMemberProvider( )
	{
		return array(
			array(
				'China',
			),
			array(
				'Russia'	
			),
			array(
				'Britain'
			),
			array(
				'USA'
			),
		);
	}
}