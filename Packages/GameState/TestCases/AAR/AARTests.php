<?php

class AARTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new AARTestSuite( 'AAR Test Cases' );		
		$suite->addTestSuite( 'AARTest' );
		return $suite;
	}
}

class AARTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = new AARPDOMatchBoard($this->sharedFixture['pdo'], BasicGameTypeFactoryTest::TEST_GAME_ID, BasicMatchZoneFactoryTest::TEST_MATCH_ID );
	}
	
	/**
	 * @dataProvider applyModificationProvider
	 */
	public function testApplyModification( $obj, $mod, $exp )
	{
		$this->assertEquals( $exp, AARPDOMatchBoard::applyModification( $obj, $mod) );
	}
	
	public function applyModificationProvider( )
	{
		return array(
			array(
				array(
					GameType::NAME => 'fighter',
					GameType::OPTIONS => array(
						'air' => '1',
						'attack' => '3',
						'defense' => '4',
						'movement' => 4,
					),					
				),
				array(
					GameType::NAME => 'jet_',
					GameType::OPTIONS => array(
						'defense' => +1,
						'dodgeaa' => '1',
					),					
				),
				array(
					GameType::NAME => 'jet_fighter',
					GameType::OPTIONS => array(
						'air' => '1',
						'attack' => '3',
						'defense' => '5',
						'movement' => 4,
						'dodgeaa' => '1'
					),					
				),
			),
		);
	}
	
	// due to that AAR components are highly interdependant because
	// of the teched types mechanic I can only test their new features
	// all together
	public function testTechedTypes( )
	{
		$russia = $this->match->getPlayer( 'Russia' );
		$sameRussia = $this->match->getPlayer( 'Russia' );
		$this->assertSame($russia, $sameRussia);

		$russia->researchJetFighters( );
		$this->assertTrue( $russia->hasJetFightersResearched( ) );
		$this->assertTrue( $sameRussia->hasJetFightersResearched( ) );
		
		$archangel = $this->match->getZone( 'Archangel' );
		$this->assertEquals( 0, $archangel->countPieces( 'Russia', 'fighter' ) );
		$this->assertEquals( 1, $archangel->countPieces( 'Russia', 'jet_fighter' ) );
		
		$russia->researchLongRangeAircraft( );
		$archangel = $this->match->getZone( 'Archangel', true );
		
		$this->assertEquals( 0, $archangel->countPieces( 'Russia', 'fighter' ) );
		$this->assertEquals( 0, $archangel->countPieces( 'Russia', 'jet_fighter' ) );
		$this->assertEquals( 1, $archangel->countPieces( 'Russia', 'longrange_jet_fighter' ) );
	}
}