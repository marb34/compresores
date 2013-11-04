<?php

/**
 * @author Marco Ramirez
 * @copyright 2013
 */

/*Inicio de funciones*/

/**
 * Validador of DataBases
 * @param string $host
 * @param string $user
 * @param string $pass
 * @param string $database name
 * 
 * @return Boolean
 */
function vali_db($host,$user,$pass,$database){
    $resultado=FALSE;
    $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
    $res = mysql_query('SHOW DATABASES');
    while ($row = mysql_fetch_assoc($res)) {
        $valorA=array_search($database,$row);
        if ($valorA)
            {
                echo "ENCONTRADO!!! ".$row["Database"]."\n";
                $resultado=true;
            }
    }
    mysql_close($link);
    return $resultado;
}

function lista_db(){
    echo "Please complete all information relating to the database server\n";
    echo "servername,username,pass :: ";
    $valor_teclado = trim(fgets(STDIN));
    $host_valu=dividir($valor_teclado);
    $host=$host_valu[0];
    $user=$host_valu[1];
    $pass=$host_valu[2];
    echo "These were the values entered ::".$host." ".$user." ".$pass."\n";
    $link = mysql_connect("$host","$user","$pass") or die("Can't Connect to the Data Base...");
    $res = mysql_query('SHOW DATABASES');
    while ($row = mysql_fetch_assoc($res)) {
        if (substr($row["Database"],0,3)==="wf_" or substr($row["Database"],0,3)==="rp_" or substr($row["Database"],0,3)==="rb_" )
            {
                echo $row["Database"]."\n";
            }
    }
}

/**
 * Explode all the string separated by comma
 * @param string $variable
 */
function dividir($variable){
    $resultado3=explode(",",$variable);
    return $resultado3;    
}
/**
 * Made a backup of all the existent PM MySQL tables un this computer
 * @param string $host
 * @param string $user
 * @param string $pass
 * @return string $status
 * 
 * @author Marco Ramirez
 */
function all_tables($host,$user,$pass){
        $link = mysql_connect("$host","$user","$pass") or die("Can't Connect to the Data Base...");
        echo "\nprocessing!!\n";
        $res = mysql_query('SHOW DATABASES');
        while ($row = mysql_fetch_assoc($res)) {
            if (substr($row["Database"],0,3)==="wf_" or substr($row["Database"],0,3)==="rp_" or substr($row["Database"],0,3)==="rb_" )
            {
                $archivo22=$row["Database"];
                $backupDatabase = new Backup_Database("$host","$user","$pass", $archivo22);
                $status = $backupDatabase->backupTables("*") ? 'OK' : 'KO';
            }
        }
}

/**
 * Made a backup of PM MySQL tables on this computer
 * @param string $host
 * @param string $user
 * @param string $pass
 * @param string $dataname, Name of the database
 * @return string $status
 * 
 * @author Marco Ramirez
 */
function back_db1($host,$user,$pass,$dataname){
        vali_db($host,$user,$pass,$dataname);
        $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
        var_dump($dataname);
        echo "\nprocessing!!\n";
        if (is_array($dataname)){
            for ($i=0;$i<count($dataname);$i++){
                $db_name=$dataname[$i];
                $status2=new Clase_Backup_Database;
                $valor_retorno=$status2->Backup_Database($host,$user,$pass,$db_name);
                echo "\nValor de retorno de la clase :".$valor_retorno."\n";
                if($valor_retorno){
                    $status = $status2->backupTables("*") ? 'OK' : 'KO';
                }
                else {
                    echo "Data Base ".$db_name."doesn't exist!\nplease reconnect to the server and review the name of the available databases!\n";
                    lista_db();
                }
            }
        //echo "Backup successful $status !!!!!\nverify all the information in dbbackup folder!\n"; 
        }
        else {
            $status2=new Clase_Backup_Database;
            $valor_retorno=$status2->Backup_Database($host,$user,$pass,$dataname);
            echo "\nValor de retorno de la clase :".$valor_retorno."\n";
            if($valor_retorno){
                    $status = $status2->backupTables("*") ? 'OK' : 'KO';
                    echo "Backup successful $status !!!!!\nverify all the information in dbbackup folder!\n";
                }
                else {
                    echo "Data Base ".$dataname."doesn't exist!\nplease reconnect to the server and review the name of the available databases!\n";
                    lista_db();
                }
            
        } 
}

/**
 * Made a backup of your worspace's databases or a single table
 * @param $table Table name,
 * 
 * Return $status
 * 
 * @author Marco Ramirez
 */
function backup_databases($table=NULL){
    echo "Please complete all information relating to the database server\n";
    echo "servername,username,pass :: ";
    $valor_teclado = trim(fgets(STDIN));
    $host_valu=dividir($valor_teclado);
    $host=$host_valu[0];
    $user=$host_valu[1];
    $pass=$host_valu[2];
    echo "These were the values entered ::".$host." ".$user." ".$pass."\n";
    $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
    if(isset($table)){
        echo $table."\n";
        if(strpos($table, ',') === TRUE){
            $posi=strpos($table,","); 
            echo "hay comas\n";
            $tablas=dividir($table);
            print_r($tablas);
            back_db1($host,$user,$pass,$tablas);
        } 
        else {
            echo "no hay comas\n";
            if ($table==="*"){all_tables($host,$user,$pass);}
            else { back_db1($host,$user,$pass,$table);}
            
            }
        $status="Done";
        //echo "Backup successful $status !!!!!\nverify all the information in dbbackup folder!\n";
    }
    return $status;
}

/**
 * Made a backup of your worspace(s)
 * @param $ruta ProcessMaker Path,
 * 
 * @author Marco Ramirez
 */
function workspace_backup(){
    echo "please fill the full path of processmaker (PmPath/opt/processmaker) :: \r\n";
    $valor_teclado1 = trim(fgets(STDIN));
    $ruta="$valor_teclado1/shared/";
    $nuevo= new zip_folder;
    $nuevo->zipDir($ruta,"archivos/salida2.zip");
    if (file_exists("dbbackup")) {
        $nuevo->zipDir("./dbbackup","archivos/salida2.zip");
    }
    return $ruta;
}

function backup_all(){
    backup_databases();
    $n_ruta=workspace_backup();
    if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
        exec("tar -cPzvf archivo.tar.gz $n_ruta");
    }
}

/* Fin de Funciones */


date_default_timezone_set('America/La_Paz');
$flag=0;
global $argv;
require_once("libreria.class.php");
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
                $status=backup_databases($valor2);
                break;
            case $argv[$i]=="workspace":
                $flag=1;
                echo "This is for workspace\n";
                echo "Done!!!";
                break;
            case $argv[$i]=="backup":
                $flag=1;
                echo "this is for a complete backup\n";
                backup_all();
                echo "I hope everything is good!?!?!?!\n";
                break;
            case $argv[$i]=="listdb":
                $flag=1;
                echo "this is for listdb\n";
                lista_db();
                break;
            case $argv[$i]=="otro":
                $flag=1;
                echo "this is for otro\n";
                vali_db("localhost","root","marb180575","wf_test");
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
