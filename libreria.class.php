<?php
/**
 * @author Marco Ramirez
 * @copyright 2013
 */
 
 
class zip_folder 
{ 
  /** 
   * Add files and sub-directories in a folder to zip file. 
   * @param string $folder 
   * @param ZipArchive $zipFile 
   * @param int $exclusiveLength Number of text to be exclusived from the file path. 
   */ 
  private static function folderToZip($folder, &$zipFile, $exclusiveLength) { 
    $handle = opendir($folder); 
    while (false !== $f = readdir($handle)) { 
      if ($f != '.' && $f != '..') { 
        $filePath = "$folder/$f"; 
        // Remove prefix from file path before add to zip. 
        $localPath = substr($filePath, $exclusiveLength); 
        if (is_file($filePath)) { 
          $zipFile->addFile($filePath, $localPath); 
        } elseif (is_dir($filePath)) { 
          // Add sub-directory. 
          $zipFile->addEmptyDir($localPath); 
          self::folderToZip($filePath, $zipFile, $exclusiveLength); 
        } 
      } 
    } 
    closedir($handle);
  } 

  /** 
   * Zip a folder (include itself). 
   * Usage: 
   *   zip_folder::zipDir('/path/to/sourceDir', '/path/to/out.zip'); 
   * 
   * @param string $sourcePath Path of directory to be zip. 
   * @param string $outZipPath Path of output zip file. 
   */ 
  public static function zipDir($sourcePath, $outZipPath) 
  { 
    $pathInfo = pathInfo($sourcePath); 
    $parentPath = $pathInfo['dirname']; 
    $dirName = $pathInfo['basename']; 
    //die($parentPath);
    if (!file_exists("archivos")) mkdir("archivos");
    $z = new ZipArchive(); 
    $res=$z->open($outZipPath, ZIPARCHIVE::CREATE); 
    if ($res === TRUE) {
        $z->addEmptyDir($dirName); 
        self::folderToZip($sourcePath, $z, strlen("$parentPath/")); 
        $z->close();
    } else {
        echo 'failed, code:' . $res;
    }
    
  } 
} 

class Clase_Backup_Database {
    var $host = '';
    var $username = '';
    var $passwd = '';
    var $dbName = '';
    var $charset = '';
/**
 * @author Marco Ramirez
* Constructor, Return True if the connection was succesful
* @param string $host host of database. 
* @param string $username.
* @param string $passwd password of database server.
* @param string $dbname database name.
* 
* @return boolean $err_value.
*/
    function Backup_Database($host, $username, $passwd, $dbName, $charset = 'utf8')
    {
        $this->host     = $host;
        $this->username = $username;
        $this->passwd   = $passwd;
        $this->dbName   = $dbName;
        $this->charset  = $charset;
 
        $err_value=$this->initializeDatabase();
        return $err_value;
    }
 
    protected function initializeDatabase()
    {
        $conn = mysql_connect($this->host, $this->username, $this->passwd);
        if(mysql_select_db($this->dbName, $conn)){echo "\nOK, All seems fine!";$stat_value=TRUE;} else {echo "\nThe DataBase ".$this->dbName." doesn't exist!";$stat_value=FALSE;}
        if (! mysql_set_charset ($this->charset, $conn))
        {
            mysql_query('SET NAMES '.$this->charset);
        }
        return $stat_value;
    }
 
    /**
     * Backup de toda la Base de Datos o solo unas tablas
     * Backup the hole dataBase or just some tables
     * Usar '*' para toda la base de datos o 'table1 table2 table3...'
     * Use '*' for all the tables or 'table1 table2 table3...'
     * @param string $tables
     */
    public function backupTables($tables = '*')
    {
        try
        {
            /**
            * Tablas a exportar
            */
            if($tables == '*')
            {
                $tables = array();
                $result = mysql_query('SHOW TABLES');
                while($row = mysql_fetch_row($result))
                {
                    $tables[] = $row[0];
                }
            }
            else
            {
                $tables = is_array($tables) ? $tables : explode(',',$tables);
            }
 
            $sql = 'CREATE DATABASE IF NOT EXISTS '.$this->dbName.";\n\n";
            $sql .= 'USE '.$this->dbName.";\n\n";
 
            /**
            * Tablas itinerantes
            */
            foreach($tables as $table)
            {
                //echo "Backing up tabla...".$table;
 
                $result = mysql_query('SELECT * FROM '.$table);
                $numFields = mysql_num_fields($result);
 
                $sql .= 'DROP TABLE IF EXISTS '.$table.';';
                $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
                $sql.= "\n\n".$row2[1].";\n\n";
 
                for ($i = 0; $i < $numFields; $i++)
                {
                    while($row = mysql_fetch_row($result))
                    {
                        $sql .= 'INSERT INTO '.$table.' VALUES(';
                        for($j=0; $j<$numFields; $j++)
                        {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                            if (isset($row[$j]))
                            {
                                $sql .= '"'.$row[$j].'"' ;
                            }
                            else
                            {
                                $sql.= '""';
                            }
 
                            if ($j < ($numFields-1))
                            {
                                $sql .= ',';
                            }
                        }
 
                        $sql.= ");\n";
                    }
                }
 
                $sql.="\n\n\n";
 
                //echo " OK" . "<br />";
            }
        }
        catch (Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
 
        return $this->saveFile($sql);
    }
 
    /**
     * Save the file .sql
     * @param string $sql
     */
    protected function saveFile(&$sql)
    {
        if (!$sql) return false;
 
        try
        {
            if (!file_exists("dbbackup")) mkdir("dbbackup");
            $handle = fopen('dbbackup/db-backup-'.$this->dbName.'-'.date("Ymd").'.sql','w+');
            fwrite($handle, $sql);
            fclose($handle);
        }
        catch (Exception $e)
        {
            var_dump($e->getMessage());
            return false;
        }
 
        return true;
    }
}

?>
