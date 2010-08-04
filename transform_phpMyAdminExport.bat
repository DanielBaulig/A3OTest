set PHP_EXECUTEABLE=C:\xampplite\php\php.exe
for /f %%a IN ('dir /b .\_database\a3o*.xml') do ( call %PHP_EXECUTEABLE% transform_phpMyAdminExport.php .\_database\%%a )