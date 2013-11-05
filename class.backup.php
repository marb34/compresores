<?php

/**
 * @author Marco Ramirez
 * @copyright 2013
 */
require_once("libreria.class.php");
class pm_backup {


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
    $row=mysql_fetch_assoc($res);
    print_r($res);
    print_r(array_intersect($row,$database));
    if(count(array_intersect($database, $row)) == count($database)){
        $resultado=TRUE;
    }
    mysql_close($link);
    return $resultado;
}

/**
 * List of all PM databases
 */
function lista_db(){
    echo "Please complete all information relating to the database server\n";
    echo "servername,username,pass :: ";
    $valor_teclado = trim(fgets(STDIN));
    $host_valu=dividir($valor_teclado);
    $host=$host_valu[0];
    $user=$host_valu[1];
    $pass=$host_valu[2];
    echo "These were the values entered ::".$host." ".$user." ".$pass."\n";
    $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
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
 * @param array $dataname, Name of the database(s)
 * @return string $status
 * 
 * @author Marco Ramirez
 */
function back_db1($host,$user,$pass,$dataname){
    //var_dump($dataname);
        $validador=$this->vali_db($host,$user,$pass,$dataname);
        print_r($validador);
        if($validador){
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
                        echo "Data Base ".$db_name." doesn't exist!\nplease reconnect to the server and review the name of the available databases!\n";
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
        echo "\nPlease, verify if the databases exist!\nYou can use the command listdb";
}

/**
 * Made a backup of your worspace's databases or a single table
 * @param $table Table name,
 * 
 * @return string $status
 * 
 * @author Marco Ramirez
 */
function backup_databases($table=NULL){
    echo "Please complete all information relating to the database server\n";
    echo "servername,username,pass :: ";
    $valor_teclado = trim(fgets(STDIN));
    $host_valu=$this->dividir($valor_teclado);
    $host=$host_valu[0];
    $user=$host_valu[1];
    $pass=$host_valu[2];
    echo "These were the values entered ::".$host." ".$user." ".$pass."\n";
    $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
    if(isset($table)){
        echo $table."\n";
        if(strpos($table,",")){
            echo "hay comas\n";
            $tablas=dividir($table);
            print_r($tablas);
            back_db1($host,$user,$pass,$tablas);
        } 
        else {
            echo "no hay comas\n";
            if ($table==="*"){all_tables($host,$user,$pass);}
            else { $this->back_db1($host,$user,$pass,(array)$table);}
            
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
}
?>