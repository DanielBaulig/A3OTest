<?php

class AARMatchPlayerTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new AARMatchPlayerTestSuite( 'AARMatchPlayer Test Cases' );		
		$suite->addTestSuite( 'AARMatchPlayerTest' );
		return $suite;
	}
}

class AARMatchPlayerTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	
	public function setUp( )
	{
		$this->pdo = $this->sharedFixture['pdo'];
		$this->match = new AARPDOMatchBoard($this->sharedFixture['pdo'], BasicGameTypeFactoryTest::TEST_GAME_ID, BasicMatchZoneFactoryTest::TEST_MATCH_ID );
	}
	
	public function testTechs( )
	{
		
		$player = $this->match->getPlayer( 'Russia' );
		
		$this->assertFalse( $player->hasLongRangeAircraftResearched( ) );
		$this->assertFalse( $player->hasHeavyBombersResearched( ) );
		$this->assertFalse( $player->hasSuperSubsResearched( ) );
		$this->assertFalse( $player->hasRocketsResearched( ) );
		$this->assertFalse( $player->hasCombinedBombardmentResearched( ) );
		$this->assertFalse( $player->hasJetFightersResearched( ) );
		
		$player->researchLongRangeAircraft( );
		$player->researchHeavyBombers( );
		$player->researchSuperSubs( );
		$player->researchRockets( );
		$player->researchCombinedBombardment( );
		$player->researchJetFighters( );
		
		$this->assertTrue( $player->hasLongRangeAircraftResearched( ) );
		$this->assertTrue( $player->hasHeavyBombersResearched( ) );
		$this->assertTrue( $player->hasSuperSubsResearched( ) );
		$this->assertTrue( $player->hasRocketsResearched( ) );
		$this->assertTrue( $player->hasCombinedBombardmentResearched( ) );
		$this->assertTrue( $player->hasJetFightersResearched( ) );
	}
}