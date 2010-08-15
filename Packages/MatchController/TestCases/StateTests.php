<?php

require_once $basedir . '/../A3O/include/classes/MatchController/A3/A3Games/AAR/AARBuilder.php';

class StateTestSuite extends PHPUnit_Framework_TestSuite
{
	public static function suite( )
	{
		$suite = new StateTestSuite( 'State Test Cases' );		
		$suite->addTestSuite( 'StateTest' );
		return $suite;
	}
}

class StateTest extends PHPUnit_Framework_TestCase
{
	protected $pdo;
	protected $match;
	
	public function setUp( )
	{
		$this->match = $this->sharedFixture['match_state'];
		$this->pdo = $this->sharedFixture['pdo'];
	}
	
	public function testRemoveCasualtiesStates( )
	{
		$start = new A3RemoveCasualties( '', $this->match );
		$next = new A3RemoveCasualties( '', $this->match );
		$start->setUp( $next );
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		$state = $state->doAction( new Action( null, 'random', array( ) ) );
		$this->assertSame($state, $start);
		$state = $state->doAction( new Action( null, 'select', array( ) ) );
		$this->assertSame($state, $next);
	}
	
	public function testPressRetreat( )
	{
		$start = new A3PressRetreat( '', $this->match );
		$press = new A3PressRetreat( '', $this->match );
		$conclude = new A3PressRetreat( '', $this->match );
		$start->setUp( $conclude, $press );
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		$state = $state->doAction( new Action( null, 'random', array( ) ) );
		$this->assertSame($state, $start);
		$state = $state->doAction( new Action( null, 'press', array( ) ) );
		$this->assertSame($state, $press);
		
		$state = $start;
		$state = $state->doAction( new Action( null, 'retreat', array( ) ) );
		$this->assertSame($state, $conclude);
	}
	
	public function testOpeningFire( )
	{
		$start = new A3OpeningFire( '', $this->match );
		$remove = new A3RemoveCasualties( '', $this->match );

		$start->setUp( $remove );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $remove);
	}
	
	public function testConcludeCombat( )
	{
		$start = new A3ConcludeCombat( '', $this->match );
		$next = new A3ConcludeCombat( '', $this->match );

		$start->setUp( $next );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'random', array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'endcombat', array( )));
		$this->assertSame($state, $next);
	}
	
	public function testResearch( )
	{
		$start = new A3Research( '', $this->match );
		$next = new A3Research( '', $this->match );

		$start->setUp( $next );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'random', array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'endresearch', array( )));
		$this->assertSame($state, $next);
		
		$state = $start;
		$state = $state->doAction(  new Action( null, 'buydice', array( )));
		$this->assertSame($state, $next);
	}
	
	public function testReinforcements( )
	{
		$start = new A3Reinforcements( '', $this->match );
		$next = new A3Reinforcements( '', $this->match );

		$start->setUp( $next );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'random', array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'buy', array( )));
		$this->assertSame($state, $start);
		
		$state = $start;
		$state = $state->doAction(  new Action( null, 'endreinforcements', array( )));
		$this->assertSame($state, $next);
	}
	
	public function testCombatMovement( )
	{
		$start = new A3CombatMovement( '', $this->match );
		$next = new A3CombatMovement( '', $this->match );

		$start->setUp( $next );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'random', array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, A3CombatMovement::MOVE_PIECES, array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, A3CombatMovement::UNDO_MOVE, array( )));
		$this->assertSame($state, $start);
		
		$state = $start;
		$state = $state->doAction(  new Action( null, A3CombatMovement::END_COMBAT_MOVEMENT, array( )));
		$this->assertSame($state, $next);
	}
	
	public function testCombat( )
	{
		$start = new A3Combat( '', $this->match );
		$next = new A3Combat( '', $this->match );
		$conduct = new A3Combat( '', $this->match );

		$start->setUp( $next, $conduct );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'random', array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, A3Combat::CONDUCT_COMBAT, array( )));
		$this->assertSame($state, $conduct);
		
		$state = $start;
		$state = $state->doAction(  new Action( null, A3Combat::CONDUCT_ALL, array( )));
		$this->assertSame($state, $next);
	}
	
	public function testNonCombatMovement( )
	{
		$start = new A3NonCombatMovement( '', $this->match );
		$next = new A3NonCombatMovement( '', $this->match );

		$start->setUp( $next );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'random', array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, A3NonCombatMovement::MOVE_PIECES, array( )));
		$this->assertSame($state, $start);
		
		$state = $start;
		$state = $state->doAction(  new Action( null, A3NonCombatMovement::END_NONCOMBAT_MOVEMENT, array( )));
		$this->assertSame($state, $next);
	}
	
	public function testMobilize( )
	{
		$start = new A3Mobilize( '', $this->match );
		$next = new A3Mobilize( '', $this->match );

		$start->setUp( $next );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, 'random', array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, A3Mobilize::PLACE_PIECES, array( )));
		$this->assertSame($state, $start);
		
		$state = $state->doAction(  new Action( null, A3Mobilize::PLACE_PIECES, array( )));
		$this->assertSame($state, $next);
	}
	
	public function testCollectIncome( )
	{
		$start = new A3CollectIncome( '', $this->match );
		$next = new A3Mobilize( '', $this->match );

		$start->setUp( $next );
		
		$state = $start->doEnter( );
		$this->assertSame($state, $next);
	}
	
	public function testTurnSequence( )
	{
		$turnSequence = new TurnSequence( '', array( 'Russia', 'Germany' ) );
		$combatMovement = new A3CombatMovement('', $this->match);
		$combat = new A3Combat('', $this->match);
		$nonCombatMovement = new A3NonCombatMovement('', $this->match);
		
		$turnSequence->setUp( $combatMovement  ,$combatMovement );
		$combatMovement->setUp($combat);
		$combat->setUp($nonCombatMovement, $combat);
		$nonCombatMovement->setUp( $turnSequence );
		
		$currenState = $turnSequence->doEnter( );
		$this->assertSame($combatMovement, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3CombatMovement::MOVE_PIECES, null ) );
		$this->assertSame($combatMovement, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3CombatMovement::UNDO_MOVE, null ) );
		$this->assertSame($combatMovement, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3CombatMovement::MOVE_PIECES, null ) );
		$this->assertSame($combatMovement, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3CombatMovement::END_COMBAT_MOVEMENT, null ) );
		$this->assertSame($combat, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3Combat::CONDUCT_COMBAT, null )  );
		$this->assertSame($combat, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3Combat::CONDUCT_COMBAT, null )  );
		$this->assertSame($combat, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3Combat::CONDUCT_COMBAT, null )  );
		$this->assertSame($combat, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3Combat::CONDUCT_COMBAT, null )  );
		$this->assertSame($combat, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3Combat::CONDUCT_COMBAT, null )  );
		$this->assertSame($nonCombatMovement, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3NonCombatMovement::MOVE_PIECES, null )  );
		$this->assertSame($nonCombatMovement, $currenState);
		$currenState = $currenState->doAction( new Action( null, A3NonCombatMovement::END_NONCOMBAT_MOVEMENT, null )  );
		// player switch should occur
		$this->assertSame($combatMovement, $currenState);
	}
	
	public function testConductCombatBuilder()
	{
		$builder = new AARConductCombatBuilder( $this->match , true );
		$builder->createNewConductCombatMachine( );
		$builder->buildOpeningFire( 'Opening Fire' );
		$builder->buildOpeningFireRemoveCasualties( 'Opening Fire Remove Casualties' );
		$builder->buildAttackersFire( 'Attackers Fire' );
		$builder->buildDefendersFire( 'Defenders Fire' );
		$builder->buildCombatRemoveCasualties( 'Remove Casualties' );
		$builder->buildPressRetreat( 'Press or Retreat' );
		$builder->buildConcludeCombat( 'Conclude Combat' );
		
		$statemachine = $builder->getConductCombatMachine( new A3GameOver('Game Over', $this->match ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Opening Fire', $statename);
		$statemachine = $statemachine->doEnter( );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Press or Retreat', $statename);
		$statemachine = $statemachine->doAction( new Action(null, 'pressattack', array( ) ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Press or Retreat', $statename);
		$statemachine = $statemachine->doAction( new Action(null, 'retreat', array( ) ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Conclude Combat', $statename);
		$statename = 'Attackers Fire';
		$statemachine = $builder->getStateSavedIn( $statename );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Attackers Fire', $statename );
	}
	
	public function testConductCombatFactory( )
	{
		$factory = new A3ConductCombatBuildDirector( );
		$factory->setConductCombatBuilder( new AARConductCombatBuilder( $this->match, true ) );
		$statemachine = $factory->createStateMachine( new A3GameOver( 'Game Over', $this->match ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Opening Fire', $statename);
		$statemachine = $statemachine->doEnter( );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Press or Retreat', $statename);
		$statemachine = $statemachine->doAction( new Action(null, A3PressRetreat::PRESS_ATTACK, array( ) ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Press or Retreat', $statename);
		$statemachine = $statemachine->doAction( new Action(null, A3PressRetreat::RETREAT, array( ) ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Conclude Combat', $statename);
		$statename = 'Attackers Fire';
		$statemachine = $factory->getStateSavedIn( $statename );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Attackers Fire', $statename );
	}
	
	public function testTurnPhasesBuilder( )
	{
		$factory = new A3ConductCombatBuildDirector( );
		$factory->setConductCombatBuilder( new AARConductCombatBuilder( $this->match, true ) );
		$builder = new AARTurnPhasesBuilder( $this->match, $factory );
		$builder->createNewTurnPhasesMachine( );
		$builder->buildResearch( 'Research' );
		$builder->buildReinforcements( 'Reinforcements' );
		$builder->buildCombatMovement( 'Combat Movement' );
		$builder->buildCombat( 'Combat' );
		$builder->buildNonCombatMovement( 'Non-Combat Movement' );
		$builder->buildMobilize( 'Mobilize' );
		$builder->buildCollectIncome( 'Collect Income' );
		$statemachine = $builder->getTurnPhasesMachine( new A3GameOver( 'Game Over', $this->match ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Research', $statename);
		$statemachine = $statemachine->doEnter( );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Research', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Research::END_RESEARCH, array( )));
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Reinforcements', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Reinforcements::END_REINFORCEMENTS, array( ) ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3CombatMovement::MOVE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3CombatMovement::END_COMBAT_MOVEMENT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Combat::CONDUCT_COMBAT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Press or Retreat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3PressRetreat::RETREAT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Conclude Combat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3ConcludeCombat::END_COMBAT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Combat::CONDUCT_ALL, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Non-Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3NonCombatMovement::MOVE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Non-Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3NonCombatMovement::END_NONCOMBAT_MOVEMENT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Mobilize', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Mobilize::PLACE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Mobilize', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Mobilize::PLACE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Game Over', $statename);
	}
	
	public function testTurnPhasesFactory()
	{
		$combatFactory = new A3ConductCombatBuildDirector( );
		$combatFactory->setConductCombatBuilder( new AARConductCombatBuilder( $this->match, true ) );
		$builder = new AARTurnPhasesBuilder( $this->match, $combatFactory );
		$factory = new A3TurnPhasesBuildDirector( );
		$factory->setTurnPhaseBuilder( $builder );
		$statemachine = $factory->createStateMachine( new A3GameOver('Game Over', $this->match ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Research', $statename);
		$statemachine = $statemachine->doEnter( );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Research', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Research::END_RESEARCH, array( )));
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Reinforcements', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Reinforcements::END_REINFORCEMENTS, array( ) ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3CombatMovement::MOVE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3CombatMovement::END_COMBAT_MOVEMENT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Combat::CONDUCT_COMBAT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Press or Retreat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3PressRetreat::RETREAT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Conclude Combat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3ConcludeCombat::END_COMBAT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Combat', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Combat::CONDUCT_ALL, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Non-Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3NonCombatMovement::MOVE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Non-Combat Movement', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3NonCombatMovement::END_NONCOMBAT_MOVEMENT, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Mobilize', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Mobilize::PLACE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Mobilize', $statename);
		$statemachine = $statemachine->doAction( new Action( null, A3Mobilize::PLACE_PIECES, null ) );
		$statemachine->saveTo( &$statename );
		$this->assertEquals( 'Game Over', $statename);
	}
	
	public function testMatchMachineBuilder( )
	{
		$combatFactory = new A3ConductCombatBuildDirector( );
		$combatFactory->setConductCombatBuilder( new AARConductCombatBuilder( $this->match, true ) );
		$builder = new AARTurnPhasesBuilder( $this->match, $combatFactory );
		$factory = new A3TurnPhasesBuildDirector( );
		$factory->setTurnPhaseBuilder( $builder );
		
		$builder = new AARMatchMachineBuilder( $this->match, new A3BidMachineBuildDirector( ), $factory );
		$builder->createNewMatchMachine( );
		$builder->buildSetup( 'Setup' );
		$builder->buildGameOver( 'Game Over' );
		$statemachine = $builder->getMatchMachine(  );
	}
}