<?php
$inputFile = (string) $argv[1];
$inputDir = dirname( $inputFile );

$xmlOriginal = simplexml_load_file($inputFile);

$inputFile = basename( $inputFile );
$tree = array( );

foreach( $xmlOriginal->children( ) as $tableRow )
{
	$tableName = $tableRow->getName( );
	
	$row = array( );
	$columnNames = array( );
	foreach( $tableRow->children( ) as $k => $columnField )
	{
		$columnNames[] = $columnField->getName( );
		$row[] = (string)$columnField;
	} 
	$tree[$tableName]['rows'][] = $row;
	$tree[$tableName]['columns'] = $columnNames;
}

var_dump($tree);

$xmlPHPUnit = new SimpleXMLElement('<dataset></dataset>');

foreach( $tree as $tableName => $table )
{
	$xmlTable = $xmlPHPUnit->addChild('table');
	
	$xmlTable->addAttribute( 'name', $tableName );
	
	foreach( $table['columns'] as $columnName )
	{
		$xmlTable->addChild( 'column', $columnName );
	}
	
	foreach( $table['rows'] as $row )
	{
		$xmlRow = $xmlTable->addChild( 'row' );
		foreach( $row as $field )
		{
			$xmlRow->addChild( 'value', $field );
		}
	}
}

$xmlPHPUnit->asXML( $inputDir . '/phpunit_' . $inputFile );