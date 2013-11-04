<script type="text/javascript">
$(document).ready(function(){
    $("#demo3").css("display","none");
});
</script>
<?php
$no_unidad=$_GET["valor2"];
$no_nomarch=$_GET["nomarch"];
$no_local=$_GET["local"];
$no_user=$_GET["user"];
$no_pass=$_GET["pass"];
$nombre_alt=$no_unidad.$no_nomarch; 
if(file_exists($nombre_alt) and is_dir($nombre_alt)){
    $parentPath=$nombre_alt;
}
else
{
    $pathInfo = pathinfo($no_nomarch);
    $parentPath = $no_unidad.$pathInfo['dirname'];
}
if(file_exists($parentPath)){
    echo "<br/>Backup succesful!!!!";
}
else {
    echo "<h1>Please, verify the correct path, or logical unit</h1>";
    exit();
}

include "libreria.class.php";


$link = mysql_connect("$no_local","$no_user","$no_pass") or die("Can't Connect to the Data Base...");
$res = mysql_query('SHOW DATABASES');

while ($row = mysql_fetch_assoc($res)) {
    if (substr($row["Database"],0,3)==="wf_" or substr($row["Database"],0,3)==="rp_" or substr($row["Database"],0,3)==="rb_" )
    {
        $archivo22=$row["Database"];
        $backupDatabase = new Backup_Database("$no_local","$no_user","$no_pass", $archivo22);
        $status = $backupDatabase->backupTables("*") ? 'OK' : 'KO';
    }
}

mysql_close($link); 

/**
 * Instanciar Backup_Database e iniciar backup
 */

echo "<br /><br /><br />Backup result: ".$status;
$nuevo= new zip_folder;
$nuevo->zipDir($parentPath,"archivos/salida2.zip");
$nuevo->zipDir("./dbbackup","archivos/salida2.zip");

echo "<br /><br /><br />Backup compression: ".$status;
?>
