<?php

/**
 * @author Marco Ramirez
 * @copyright 2013
 */
require_once("libreria.class.php");
class pm_backup {

    var $host = '';
    var $username = '';
    var $passwd = '';
    var $dbName = '';
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
    $o=0;
    $resultado=0;
    $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
    $res=mysql_list_dbs($link);
    while ($row = mysql_fetch_assoc($res)) {
        $compara=array_intersect($database,$row);
        if (count($compara)>0) $o++;
        if(count($database)==$o){
            $resultado=1;
        }
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
    $host_valu=$this->dividir($valor_teclado);
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
    mysql_close($link);
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
        $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
        echo "\nprocessing!!\n";
        $res = mysql_query('SHOW DATABASES');
        while ($row = mysql_fetch_assoc($res)) {
            if (substr($row["Database"],0,3)==="wf_" or substr($row["Database"],0,3)==="rp_" or substr($row["Database"],0,3)==="rb_" )
            {
                $archivo22=$row["Database"];
                $pm_back1= new Clase_Backup_Database;
                $backupDatabase = $pm_back1->Backup_Database($host,$user,$pass, $archivo22);
                $status = $pm_back1->backupTables("*") ? 'OK' : 'KO';
            }
        }
        mysql_close($link);
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
        $validador=$this->vali_db($host,$user,$pass,$dataname);
        if($validador){
            $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
            echo "\nprocessing!!\n";
            if (count($dataname)>1){
                for ($i=0;$i<count($dataname);$i++){
                    $db_name=$dataname[$i];
                    echo "\n".$db_name;
                    $status2=new Clase_Backup_Database;
                    $valor_retorno=$status2->Backup_Database($host,$user,$pass,$db_name);
                    if($valor_retorno){
                        $status = $status2->backupTables("*") ? 'OK' : 'KO';
                        echo "\nBackup successful $status !!!!!\nverify all the information in dbbackup folder!\n";
                    }
                }
            }
            else {
                $status2=new Clase_Backup_Database;
                $db_name=$dataname[0];
                echo "\n".$db_name;
                $valor_retorno=$status2->Backup_Database($host,$user,$pass,$db_name);
                if($valor_retorno){
                        $status = $status2->backupTables("*") ? 'OK' : 'KO';
                        echo "\nBackup successful $status !!!!!\nverify all the information in dbbackup folder!\n";
                }
            } 
        }
        else { 
            echo "\nOne of the Data Bases doesn't exist!\nplease reconnect to the server and review the name of the available databases!\n";
            echo "-------------------------------------------------------------------------------\n";
            $this->lista_db();
        }        
}

/**
 * Made a backup of your worspace's databases or a single table
 * @param $table Table name,
 * 
 * @return string $status
 * 
 * @author Marco Ramirez
 */
function backup_databases($table){
    echo "Please complete all information relating to the database server\n";
    echo "servername,username,pass :: ";
    $valor_teclado = trim(fgets(STDIN));
    $host_valu=$this->dividir($valor_teclado);
    $host=$host_valu[0];
    $user=$host_valu[1];
    $pass=$host_valu[2];
    $this->host=$host;
    $this->username=$user;
    $this->passwd=$pass;
    $this->dbName=$table;
    echo "These were the values entered ::".$host." ".$user." ".$pass."\n";
    $link = mysql_connect($host,$user,$pass) or die("Can't Connect to the Data Base...");
    if(isset($table)){
        echo $table."\n";
        if(strpos($table,",")){
            $tablas=$this->dividir($table);
            $this->back_db1($host,$user,$pass,$tablas);
        } 
        else {
            if ($table==="*"){$this->all_tables($host,$user,$pass);}
            else { $this->back_db1($host,$user,$pass,(array)$table);}
            
            }
        $status="Done";
    }
    return $status;
}

/**
 * Made a backup of your worspace(s)
 * @param string $workspace, ProcessMaker workspace name,
 * 
 * @return string $ruta, new path.
 * @author Marco Ramirez
 */
function workspace_backup($workspace){
    if($workspace==="*"){
        echo "\nplease fill the full path of processmaker (PmPath/opt/processmaker) :: \r\n";
        $valor_teclado1 = trim(fgets(STDIN));
        $ruta="$valor_teclado1/shared/sites/";
        $nuevo= new zip_folder;
        $nuevo->zipDir($ruta,"archivos/salida2.zip");
        if (file_exists("dbbackup")) {
            $nuevo->zipDir("./dbbackup","archivos/salida2.zip");
        }
    }
    else{
        echo "\nplease fill the full path of processmaker (PmPath/opt/processmaker) :: \r\n";
        $valor_teclado1 = trim(fgets(STDIN));
        $ruta="$valor_teclado1/shared/sites/$workspace/";
        $nuevo= new zip_folder;
        $nuevo->zipDir($ruta,"archivos/salida2.zip");
        if (file_exists("dbbackup")) {
            $nuevo->zipDir("./dbbackup","archivos/salida2.zip");
        }
    }
    return $ruta;
}

function backup_all(){
    $this->backup_databases("*");
    $n_ruta=$this->workspace_backup("*");
    if (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN') {
        exec("tar -cPzvf archivo.tar.gz $n_ruta");
    }
}

/* Fin de Funciones */
}
?>