<?php

/**
 * @author Marco Ramirez
 * @copyright 2013
 */

$hoy=date();
echo $hoy;
echo "
<input type='date' name='fecha' id='fecha'/>
";
?>
<script>
var val_fecha=document.getElementById("fecha").value;
</script>