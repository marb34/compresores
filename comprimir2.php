<?php

/**
 * @author Marco Ramirez
 * @copyright 2013
 */
$no_unidad=$_GET["valor2"];
$no_nomarch=$_GET["nomarch"];
$no_local=$_GET["local"];
$no_user=$_GET["user"];
$no_pass=$_GET["pass"];
$pathInfo = pathInfo($no_nomarch);
$parentPath = $pathInfo['dirname']; 
$dirName = $pathInfo['basename'];

include "archive.php";

error_reporting(E_ALL);
 
/**
 * Definir bases de datos
 */
define("DB_USER", $no_user);
define("DB_PASSWORD",$no_pass);
define("DB_HOST", $no_local);
define("TABLES", '*');

$link = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("Can't connect to the Data Base...");
$res = mysql_query('SHOW DATABASES');

while ($row = mysql_fetch_assoc($res)) {
    //echo $row['Database'] . "\n";
    if (substr($row["Database"],0,3)==="wf_" || substr($row["Database"],0,3)==="rp_" || substr($row["Database"],0,3)==="rb_" ){
        $archivo22=$row["Database"];
        $backupDatabase = new Backup_Database(DB_HOST, DB_USER, DB_PASSWORD, $archivo22);
        $status = $backupDatabase->backupTables(TABLES) ? 'OK' : 'KO';
    }
}
mysql_close($link); 

/**
 * Instanciar Backup_Database e iniciar backup
 */

echo "<br /><br /><br />Backup result: ".$status;


//The following example creates a gzipped tar file:
$test = new gzip_file("archivos/bck.tar.gz");
// Set basedir to "../..", which translates to /var/www
// Overwrite /var/www/htdocs/test/test.tgz if it already exists
// Set compression level to 1 (lowest)
$test->set_options(array('basedir' => ".", 'overwrite' => 1, 'level' => 9));
// Add entire htdocs directory and all subdirectories
// Add all php files in htsdocs and its subdirectories
$test->add_files(array("dbbackup", "$parentPath"));
// Exclude all jpg files in htdocs and its subdirectories
//$test->exclude_files("htdocs/*.jpg");
// Create /var/www/htdocs/test/test.tgz
$test->create_archive();
// Check for errors (you can check for errors at any point)
if (count($test->errors) > 0)
	print ("Errors occurred."); // Process errors here

?>