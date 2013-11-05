<?php

/**
 * @author Marco Ramirez
 * @copyright 2013
 */

date_default_timezone_set('America/La_Paz');
$flag=0;
global $argv;
require_once("class.backup.php");
$pm_back=new pm_backup;
$cantidad=count($argv);
for($i=1;$cantidad>$i;$i++){
     //echo $argv[$i]."\r\n"; 
     if (isset($argv[1])){
            switch($argv){
            case $argv[$i]=="databases":
                $flag=1;
                echo "This is for backup a database or all\n";
                echo count($argv)."\n";
                if(count($argv)>2){$valor2=$argv[$i+1];
                    if($valor2!=="workspace" && $valor2!=="backup" && $valor2!==" "){
                        echo "-----".$valor2."-----\n";
                    }
                }
                else {die('Please use the next parameters "*" or database name "DB1,DB2,DB3"'."\n");}
                $status=$pm_back->backup_databases($valor2);
                break;
            case $argv[$i]=="workspace":
                $flag=1;
                echo "This is for workspace\n";
                echo "Done!!!";
                break;
            case $argv[$i]=="backup":
                $flag=1;
                echo "this is for a complete backup\n";
                $pm_back->backup_all();
                echo "I hope everything is good!?!?!?!\n";
                break;
            case $argv[$i]=="listdb":
                $flag=1;
                echo "this is for listdb\n";
                $pm_back->lista_db();
                break;
            case $argv[$i]=="otro":
                $flag=1;
                echo "this is for otro\n";
                echo "Gracias por participar, Siga intentando\n Thank you for participating, keep trying";
                break;
            default:
                $flag = 0;
                break;
            }     
    }
}
if (!isset($argv[1]) && $flag == 0 ){
    echo "puede elegir entre las siguientes opciones\nYou can Choose between the next options:\n";
    echo "backup\n";
    echo "workspace\n";
    echo "databases\n";
    echo "listdb\n";
    echo "otro\n";
}

?>
