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
                if(count($argv)>2){$valor2=$argv[$i+1];
                    if($valor2!=="workspace" && $valor2!=="backup" && $valor2!==" "){
                        echo "-----".$valor2."-----\n";
                        $status=$pm_back->backup_databases($valor2);
                    }
                }
                else {die('Please use the next parameters "*" or database name "DB1,DB2,DB3"'."\n");}
                break;
            case $argv[$i]=="workspace":
                $flag=1;
                echo "This is for workspace\nPlease check that you have enough hard disk space to contain all compressed archives.\n";
                echo "Do you? (Y/N):";
                $aceptar = strtoupper(trim(fgets(STDIN)));
                if ($aceptar==="Y"){
                    if(count($argv)>2){$valor2=$argv[$i+1];
                        if($valor2!=="databases" && $valor2!=="backup" && $valor2!==" "){
                            echo "-----".$valor2."-----\n";
                            $status=$pm_back->workspace_backup($valor2);
                        }
                    }
                    else {die('Please use the next parameters "*" or workspace name "workflow,site1,test,etc"'."\n");}
                }
                break;
            case $argv[$i]=="backup":
                $flag=1;
                echo "This is for full backup\nPlease check that you have enough hard disk space to contain all compressed archives.\n";
                echo "Do you? (Y/N):";
                $aceptar = strtoupper(trim(fgets(STDIN)));
                if ($aceptar==="Y") {
                    $pm_back->backup_all();
                    echo "I hope everything is good!?!?!?!\n";
                }
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
    echo "-------------------------------------------------------\n";
    echo "<backup>, for make a complete backup\n";
    echo "<workspace>, for make a specific workspace backup (without databases)\n";
    echo "<databases>, for make a Databases backup\n";
    echo "<listdb>, to see all the PM databases\n";
    echo "<otro>\n";
}

?>
