<?php

class GameAllianceTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new GameAllianceTestSuite( 'GameAlliance Test Cases' );
		
		$suite->addTestSuite( 'GameAllianceFactoryTest' );
		$suite->addTestSuite( 'GameAllianceRegistryTest' );
		$suite->addTestSuite( 'GameAllianceTest' );		
		
		return $suite;
	}
}

class GameAllianceFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	
	public function testLoadAllAlliances( )
	{
		$factory = new GameAlliancePDOFactory( $this->pdo, $this->match );
		$alliances = $factory->createAllProducts( );
		$this->assertArrayHasKey( 'Axis' , $alliances );
		$this->assertArrayHasKey( 'Allies' , $alliances );
		$this->assertArrayHasKey( 'UDSSR' , $alliances );
		$this->assertEquals( 3 , count( $alliances ) );
	}
	
	public function testLoadNoAlliance( )
	{
		$factory = new GameAlliancePDOFactory( $this->pdo, new A3PDOMatchBoard($this->pdo, -1, 1 ) );
		$alliances = $factory->createAllProducts( );
		$this->assertEquals( 0, count( $alliances ) );
	}
	
	/**
	 * @dataProvider failLoadSingleAllianceProvider
	 */
	public function testFailLoadSingleAlliance( $alliance )
	{
		$factory = new GameAlliancePDOFactory( $this->pdo, $this->match );
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
		$factory = new GameAlliancePDOFactory( $this->pdo, $this->match );
		$alliance = $factory->createSingleProduct( $alliance );
		$this->assertType( 'GameAlliance' , $alliance );
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

class GameAllianceRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}

	/**
	 * @dataProvider getValidAllianceProvider
	 */
	public function testGetValidAlliance( $alliance )
	{
		$aAlliance = $this->match->getAlliance( $alliance );
		$this->assertType( 'GameAlliance', $aAlliance );
		
		$sameAlliance = $this->match->getAlliance( $alliance );
		$this->assertType( 'GameAlliance', $sameAlliance );
		
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
		$this->match->getAlliance( $alliance );
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

class GameAllianceTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	
	/**
	 * @dataProvider isNotMemberProvider
	 */
	public function testIsNotMember( $nation )
	{
		$alliance = $this->match->getAlliance( 'Allies' );
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
		$alliance = $this->match->getAlliance( 'UDSSR' );
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
		$alliance = $this->match->getAlliance( 'Allies' );
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