<?php

class GameNationTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new GameNationTestSuite( 'GameNation Test Cases' );
		$suite->addTestSuite( 'GameNationFactoryTest' );
		$suite->addTestSuite( 'GameNationRegistryTest' );		
		$suite->addTestSuite( 'BasicGameNationTest' );
		
		return $suite;
	}
}

class GameNationFactoryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	
	public function testLoadAllNations( )
	{
		$factory = new GameNationPDOFactory( $this->pdo, $this->match );
		$nations = $factory->createAllProducts( );
		$this->assertArrayHasKey( 'Russia' , $nations );
		$this->assertArrayHasKey( 'Germany', $nations );
		$this->assertArrayHasKey( 'Japan', $nations );
		$this->assertArrayHasKey( 'USA', $nations );
		$this->assertArrayHasKey( 'Britain', $nations );
		$this->assertArrayHasKey( 'China', $nations );
		$this->assertEquals( 6, count( $nations ) );
	}
	
	public function testLoadNoNations( )
	{
		$factory = new GameNationPDOFactory( $this->pdo, new A3PDOMatchBoard($this->pdo, -1, 1) );
		$nations = $factory->createAllProducts( );
		$this->assertEquals( 0, count( $nations ) );
	}
	
	/**
	 * @dataProvider failLoadSingleNationProvider
	 */
	public function testFailLoadSingleNation( $nation )
	{
		$factory = new GameNationPDOFactory( $this->pdo, $this->match );
		$this->setExpectedException( 'DomainException' );
		$nation = $factory->createSingleProduct( $nation );
	} 
	
	public function failLoadSingleNationProvider( )
	{
		return array(
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
	
	/**
	 * @dataProvider loadSingleNationProvider
	 */
	public function testLoadSingleNation( $nation )
	{
		$factory = new GameNationPDOFactory( $this->pdo, $this->match );
		$nation = $factory->createSingleProduct( $nation );
		$this->assertType( 'GameNation', $nation );
	}
	
	public function loadSingleNationProvider( )
	{
		return array(
			array(
				'Russia',
			),
			array(
				'Germany',
			),
			array(
				'USA',
			),
			array(
				'Britain',
			),
			array(
				'Japan',
			),
			array(
				'China',
			),
		);
	}
}

class GameNationRegistryTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	
	
	/**
	 * @dataProvider invalidGetNationProvider
	 */
	public function testInvalidGetNation( $nation )
	{
		$this->setExpectedException( 'DomainException' );
		$this->match->getNation( $nation );
	}
	
	public function invalidGetNationProvider( )
	{
		return array(
			array(
				'failNation',
			),
			array(
				'',
			),
			array(
				null,
			),
			array(
				123456,
			),
		);
	}
	
	/**
	 * @dataProvider validGetNationProvider
	 */
	public function testValidGetNation( $nation )
	{
		$aNation = $this->match->getNation( $nation );
		$this->assertType( 'GameNation', $aNation );
		
		$sameNation = $this->match->getNation( $nation );
		$this->assertType( 'GameNation', $sameNation );
		
		// the registry should return the same object instance!
		$this->assertTrue( $aNation === $sameNation );
	}
	
	public function validGetNationProvider( )
	{
		return array(
			array(
				'Russia',
			),
			array(
				'Germany',
			),
			array(
				'USA',
			),
			array(
				'Britain',
			),
			array(
				'Japan',
			),
			array(
				'China',
			),
		);
	}
}

class BasicGameNationTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = $this->sharedFixture['match_state'];
	}
	public function testInstanciation( )
	{
		$nation = new GameNation( $this->match, array( GameNation::NAME => 'Test Land', GameNation::ALLIANCES => array( ) ) );
		$this->assertType( 'GameNation', $nation ); 
	}
	
	/**
	 * @dataProvider exceptionIsAllyProvider
	 */
	public function testExceptionIsAlly( $ally )
	{
		$nation = $this->match->getNation( 'Russia' );
		$this->setExpectedException( 'DomainException' );
		$nation->isAllyOf( $ally );
	}
	
	public function exceptionIsAllyProvider( )
	{
		return array(
			array(
				'failNation',
				null,
				'',
				123456,
			)
		);
	}
	
	/**
	 * @dataProvider isAllyProvider
	 */
	public function testIsAlly( $ally )
	{
		$nation = $this->match->getNation( 'Russia' );
		$this->assertTrue( $nation->isAllyOf( $ally ) );
	}
	
	public function isAllyProvider( )
	{
		return array(
			array(
				'Britain',
			  	'USA',
				'China',
			)
		);
	}
	
	/**
	 * @dataProvider isNotAllyProvider
	 */
	public function testIsNotAlly( $ally )
	{
		$nation = $this->match->getNation( 'Russia' );
		$this->assertFalse( $nation->isAllyOf( $ally ) );
	}
	
	public function isNotAllyProvider( )
	{
		return array(
			array(
				'Germany',
				'Japan',
			)
		);
	}
	
	/**
	 * @dataProvider isInAllianceProvider
	 */
	public function testIsInAlliance( $alliance )
	{
		$nation = $this->match->getNation( 'Russia' );
		$this->assertTrue( $nation->isInAlliance( $alliance) );
	}
	
	public function isInAllianceProvider( )
	{
		return array(
			array(
				'Allies',
				'UDSSR',
			)
		);
	}
	/**
	 * @dataProvider isNotInAllianceProvider
	 */
	public function testIsNotInAlliance( $alliance )
	{
		$nation = $this->match->getNation( 'Russia' );
		$this->assertFalse( $nation->isInAlliance( $alliance) );
	}
	
	public function isNotInAllianceProvider( )
	{
		return array(
			array(
				'Axis',
				'failAlliance',
				'',
				null,
				12345,
			)
		);
	}
}