<!DOCTYPE html>
<html>
<head>
<?php
include "listado1.php";
//echo $_SERVER["DOCUMENT_ROOT"];
?>
</head>

<body>
<div id="primero">
<form name="form1" id="form1" enctype="multiform/form-data" method="get">
    <table>
        <tr><td>Localhost :</td><td><input name="local" type="text" value="localhost" id="local" required=""/></td></tr>
        <tr><td>Nombre de usuario Mysql :</td><td><input name="user" type="text" value="root" id="user" required=""/></td></tr>
        <tr><td>Contraseña Mysql :</td><td><input name="pass" type="password" id="pass" required=""/></td></tr>
    </table>
        <label for="nomarch"><h3>Ruta de Acceso a ProcessMaker : </h3></label>
        <div id="root-n"></div>
        <input id="nomarch" name="nomarch" type="text" size="80" placeholder="Por Favor! busque el directorio donde se encuentra processmaker.bat"/>
        <input type="hidden" id="valor2" name="valor2" />
        <p class="peque"><strong>* Nota: </strong> si utiliza windows por favor recuerde añadir la unidad donde se encuentra ProcessMaker</p>
        <input type="button" value="browse" id="boton"/> <br />
    <input type="submit" />
</form>
</div>
<div id="demo3" style="display: none;" ></div>
<div id="content" style="display: none;"></div>
</body>
</html>