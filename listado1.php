<link href="css/estilo.css" rel="stylesheet" />
<link href="filetree/jqueryFileTree.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery-validate.js"></script>
<!--
<script type="text/javascript" src="filetree/jqueryFileTree.js"></script>
-->
<script type="text/javascript" src="filetree/jqueryFileTree2.js"></script>
<script type="text/javascript">
var navInfo = window.navigator.appVersion.toLowerCase(); var so = 'Sistema Operativo';

function retornarSO() { 
    if(navInfo.indexOf('win') != -1) {
        so = 'Windows'; 
        } 
        else if(navInfo.indexOf('linux') != -1) { 
            so = 'Linux'; 
            } else if(navInfo.indexOf('mac') != -1) {
                so = 'Macintosh'; 
                } 
return so 
}

function openFile(file){
    var sist=retornarSO(); 
    if (sist=="Windows"){ 
        $("#root-n").load("unidades.php",function(){
        document.getElementById("nomarch").value=file;
        });
        
    }
    else {
        $("#roota").css("display","none");
        document.getElementById("roota").value="";
        document.getElementById("nomarch").value=file;
    } 
    $("#demo3").css("display","none");    
}

$(document).ready(function() {
    $("#boton").click( function() {
        $("#content").css("display","none");
        $("#demo3").css("display","block");
        $('#demo3').fileTree({ 
            root: '/',
            script: 'filetree/connectors/jqueryFileTree.php' 
        }, 
        function(file) {
            openFile(file);
        },
        function(dir){
            //alert (dir.attr("rel"));
            if($("input[type=checkbox]").is(':checked')) {  
                var archivo1=$("input[type=checkbox]:checked").attr("value");  
                //alert("El folder seleccionado se encuantra en :"+archivo1);
                openFile(archivo1);
            } else {  
                //alert("No está activado");  
            }  
        });
    });


        $("#form1").validate({
        rules: {
            local: { required: true, minlength: 2},
            user: { required:true, minlength: 2},
            pass: { required:true,minlength: 5, maxlength: 15},
            nomarch: { required:true, minlength: 2}
        },
        messages: {
            local: "Debe introducir nombre de su host.",            
            user : "Debe introducir un nombre de usuario válido.",
            pass : "Introduzca una contraseña de acceso a base de datos.",
            nomarch : "Este campo es obligatorio.",
        },
        submitHandler: function(form){
            document.getElementById("valor2").value=document.getElementById("roota").value;
            var datos=$("#form1").serialize();
            var sist=retornarSO();
            $("#content").css("display","block");
            $('#content').html('<div class="loader1"><img src="ajax-loader.gif"/></div>');
            if (sist=="Windows"){
                $.ajax({
                    type: "GET",
                    url: "comprimir.php",
                    data: datos,
                    success: function(data) {
                    //Cargamos finalmente el contenido deseado
                        $('#content').fadeIn(1000).html(data);
                    }
                });
            }
            if (sist=="Linux"){
                $.ajax({
                    type: "GET",
                    url: "comprimir2.php",
                    data: datos,
                    success: function(data) {
                    //Cargamos finalmente el contenido deseado
                        $('#content').fadeIn(1000).html(data);
                    }
                });
            }
        }

  }); 
});

</script>