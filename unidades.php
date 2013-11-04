<?php

/**
 * @author Marco Ramirez
 * @copyright 2013
 */
$def_logical=substr($_SERVER["DOCUMENT_ROOT"],0,2);
$variable=json_encode(shell_exec("wmic logicaldisk get caption,description,drivetype,providername,volumename"));
$separado=explode('\r\n',$variable);
echo "<select id='roota' class='unidades' requiered=''>";
echo "  <option value='$def_logical'>$def_logical</option>";
foreach($separado as $uni){
    $xyz=substr($uni,9,5);
    if ($xyz=="Local"){
        $nu_uni=substr($uni,0,2);
        echo "
        <option value='$nu_uni'>$nu_uni</option>
        ";
    }
}
echo "</select>";
?>