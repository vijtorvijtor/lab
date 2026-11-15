<?php
session_start ();
include("config.php");
include("sesionCheck.php");
encabezado();
ini_set('display_errors', 'Off'); 
ini_set('log_errors', 'On'); 
error_reporting(E_ALL | E_STRICT);

$conn = $_SESSION["conn"];
date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL, 'es_MX.UTF-8');
setlocale(LC_TIME, "es_MX.UTF-8","esp");

$sQry="SELECT producto.idProducto AS idProducto, producto AS Producto FROM producto WHERE tipo=1";
$aryTipoProducto=mysqli_query($conn, $sQry); 
$sQry="SELECT  idMarca AS ID,  marca AS Marca FROM marca ORDER BY idMarca";
$aryMarca=mysqli_query($conn, $sQry);
$sQry="SELECT material.idMaterial AS ID, material.material AS Material FROM material";
$aryMaterial=mysqli_query($conn, $sQry); 
$sQry="SELECT tratamiento.idTratamiento AS ID, tratamiento.tratamiento AS Tratamiento FROM tratamiento";
$aryTratamiento=mysqli_query($conn, $sQry); 
$sQry="SELECT vision.idVision AS ID, vision.vision AS Vision FROM vision";
$aryVision=mysqli_query($conn, $sQry); 
$sQry="SELECT serie.idSerie AS ID, serie.serie AS Serie FROM serie";
$arySerie=mysqli_query($conn, $sQry); 

function llenaRadio($cat,$func){
   global $aryMarca, $aryMaterial, $aryTratamiento, $aryVision, $arySerie;
   switch ($cat){
      case "marca": $aryData=$aryMarca; $sValor="ID"; $sTexto="Marca"; break;
      case "material": $aryData=$aryMaterial; $sValor="ID"; $sTexto="Material"; break;
      case "tratamiento": $aryData=$aryTratamiento; $sValor="ID"; $sTexto="Tratamiento"; break;
      case "vision": $aryData=$aryVision; $sValor="ID"; $sTexto="Vision"; break;
      case "serie": $aryData=$arySerie; $sValor="ID"; $sTexto="Serie"; break;
   }   
   mysqli_data_seek($aryData, 0);
   while ($row=$aryData->fetch_assoc()){
      echo "<input type='radio' class='radCompacta' id='id".$row[$sTexto]."'  value='".$row[$sValor]."' name='".$cat."' onchange='".$func."(this)'/><label class='lblCompacta' for='id".$row[$sTexto]."'>".$row[$sTexto]."</label>";
   }   
}
function llenaSelect($cat){
   global $aryTipoProducto;
   switch ($cat){
      case "tipoProducto":
         mysqli_data_seek($aryTipoProducto, 0);
         echo "<option value=''></option>";
         while ($row=$aryTipoProducto->fetch_assoc()){
            echo "<option value='".$row["idProducto"]."'>".$row["Producto"]."</option>";
         }   
        break;  
   }
}
?>
<!DOCTYPE html>
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" >
   <meta name="viewport" content="width=device-width, initial-scale=1" >
   <link rel="stylesheet" href="css/estilos.css?<?php echo time(); ?>" >
   <link rel="stylesheet" href="jquery.mobile/jquery.mobile.min.css" >
   <script src="jquery/jquery.min.js"></script>
   <script src="jquery/jquery-ui.min.js"></script>
   <link href="jquery/jquery-ui.css" rel="stylesheet">
   <link href="jquery/select2.min.css" rel="stylesheet">
   <script src="jquery/select2.min.js"></script>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" >
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
   <title>Laboratorio central -- Inventario</title>
   <style>
   .select2-container .select2-choice {
       height: 200px!important;
       line-height: 200px!important;
       max-height: 200px;
       min-height: 200px !important;
   }
   .tituloSeccion{
      text-align: left;
      font-weight: bold;
      font-size: 1.1em;
      color: darkblue;
   }
   </style>
</head>
<body>
<script type="text/javascript">if(typeof enciendeMenu==='function'){enciendeMenu("encab3");}</script>

<div class="divPartidas" id="divPartidas">
   <label>Producto: </label>
   <select style="width:60%!important;" class="js-example-diacritics selectFiltro" id="idTipoProducto" data-role="none" name="nmTipoProducto" onchange="fCambiaTipoProducto(this)"><?php llenaSelect("tipoProducto")  ?></select><br>
   <div id="divDetalleMica" style="display:none">
      <div class="divPar"><strong>MARCA: </strong><?php llenaRadio("marca","fCambiaMarcaMica")?><br></div>
      <div class="divNon"><strong>MATERIAL: </strong><?php llenaRadio("material","fCambiaMaterial")?><br></div>
      <div class="divPar"><strong>TRATAMIENTO: </strong><?php llenaRadio("tratamiento","fCambiaTratamiento")?><br></div>
      <div class="divNon"><strong>VISION: </strong><?php llenaRadio("vision","fCambiaVision")?><br></div>
      <div class="divPar"><strong>SERIE: </strong><?php llenaRadio("serie","fCambiaSerie")?><br></div>
   </div>
   <!-- Alta de inventario (tabla de graduaciones) -->
   <div class="divGraduacion" id="divGraduacion" style="display:block; text-align:center; margin-top:25px;">
      <strong>Agregar a inventario:</strong>
      <table id="idTblGraduaciones" class="clTblGrad" >
        <tbody>
        <tr>
          <th class="clTheadGrad">Cantidad</th>
          <th class="clTheadGrad">Esfera</th>
          <th class="clTheadGrad">Cilíndro</th>
          <th class="clTheadGrad">Adicción</th>
          <th style="text-align:center" width="10%"><input type="button" value="+" onclick="agregaGrad()"></th>
        </tr>
        <tr>
          <td><input type="number" placeholder="0" min="0" max="50" step="1"></td>
          <td><input class="clInpGrad" type="number"  placeholder="0.00" min="-20" max="20" step="0.25"></td>
          <td>-<input class="clInpGrad" type="number" placeholder="0.00" min="0" max="6" step="0.25"></td>
          <td>+<input class="clInpGrad" type="number" placeholder="0.00" min="0.75" max="3.5" step="0.25"></td>
          <td></td>
        </tr>
        </tbody>
      </table>
      <button id="btnAgregaPartida" onclick="agregaInventario()" style="margin:10px 0;">Cargar nuevo inventario</button>
      <div id="divError" style="display:none;"><p class="pError" id="fraseError"></p></div>
   </div>
</div>

<!-- Consulta rápida del inventario -->
<div id="divTablaInv" style="margin-top:40px;">
   <table id="idTblInventario" name="nmTblInventario" data="dataInventario" class="clTbklInventario" style="display: visible;">
   <tr>
      <th>Marca</th>
      <th>Material</th>
      <th>Tratamiento</th>
      <th>Esfera</th>
      <th>Cilindro</th>
      <th>Adicion</th>
      <th>Vision</th>
      <th>Existencia</th>
   </tr>
   <tbody id='idTBody'></tbody>
   </table>
</div>

<script type="text/javascript">
// ------- Variables y Select2 -------
let nTipoProd=0, nMarca=0, nMaterial=0, nTratamiento=0, nVision=0, nSerie=0;

function ready0(){
   $.fn.select2.defaults.set('matcher', function(params, data){
      if ($.trim(params.term) === '') return data;
      if (typeof data.text === 'undefined') return null;
      data.text=data.text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      var words = params.term.toUpperCase().split(" ");
      for(var i=0;i<words.length;i++){
         if(data.text.toUpperCase().indexOf((words[i]).replace(/[\u0300-\u036f]/g,''))<0)return null;
      }
      return data;
   });
   $('.selectFiltro').select2({placeholder:'Seleciona',allowClear:true,width:"resolve",language:"es"});
}
$(document).ready(ready0);

// ------- Interacción radios/select y consulta/alta -------
function fCambiaTipoProducto(a){
   nTipoProd=a.value;
   if(nTipoProd==1){
      document.getElementById("divDetalleMica").style.display = "block";
   }else{
      document.getElementById("divDetalleMica").style.display = "none";
   }
   $("#idTBody").html('');
}
function fCambiaMarcaMica(a){nMarca=a.value;fLlenaInventario();}
function fCambiaMaterial(a){nMaterial=a.value;fLlenaInventario();}
function fCambiaTratamiento(a){nTratamiento=a.value;fLlenaInventario();}
function fCambiaVision(a){nVision=a.value;fLlenaInventario();}
function fCambiaSerie(a){nSerie=a.value; /* Si quieres filtrar por serie aquí incluir en AJAX */}

function fLlenaInventario(){
   if(!nMarca || !nMaterial || !nTratamiento || !nVision){
      $("#idTBody").html('<tr><td colspan="8" style="color:red;">Selecciona Marca, Material, Tratamiento y Visión</td></tr>');
      return;
   }
   $("#idTBody").html('<tr><td colspan="8">Cargando inventario...</td></tr>');
   $.ajax({
      type: 'POST',
      url: 'fAjax.php',
      dataType: 'json',
      cache: false,
      async: true,
      data: {
         Key: "invGraduaciones",
         nMarca: nMarca,
         nMaterial: nMaterial,
         nTratamiento: nTratamiento,
         nVision: nVision,
         nSerie: nSerie
      },
      success: function(aInventario){
         let content = '';
         if(Array.isArray(aInventario) && aInventario.length==0){
            content = '<tr><td colspan="8" style="color:red;">Sin registros</td></tr>';
         }else{
            aInventario.forEach(item => {
               let sEsf = Number(item.Esfera)>=0 ? "+"+item.Esfera : item.Esfera;
               content += `<tr>
                  <td>${item.Marca}</td>
                  <td>${item.Material}</td>
                  <td>${item.Tratamiento}</td>
                  <td>${sEsf}</td>
                  <td>${item.Cilindro}</td>
                  <td>${item.Adicion}</td>
                  <td>${item.Vision}</td>
                  <td>${item.Inventario}</td>
               </tr>`;
            });
         }
         $('#idTBody').html(content);
      },
      error: function(error){
         $('#idTBody').html('<tr><td colspan="8" style="color:red;">Error al consultar inventario</td></tr>');
         alert("ERROR: ["+JSON.stringify(error)+"]");
      }
   });
}

// ------- Alta/agrega graduación -------
function agregaGrad(){
   let tbl=document.getElementById("idTblGraduaciones").getElementsByTagName('tbody')[0];
   let tr=document.createElement("tr");
   tr.innerHTML = '<td><input class="clInpGrad_" type="number" placeholder="0" min="0" max="50" step="1"></td>'
               + '<td><input class="clInpGrad" type="number" placeholder="0.00" min="-20" max="20" step="0.25"></td>'
               + '<td>-<input class="clInpGrad" type="number" placeholder="0.00" min="0" max="6" step="0.25"></td>'
               + '<td>+<input class="clInpGrad" type="number" placeholder="0.00" min="0.75" max="3.5" step="0.25"></td>'
               + '<td></td>';
   tbl.appendChild(tr);
}

function agregaInventario(){
   let mensajeError='';
   if(!nMarca) mensajeError='Selecciona una marca. ';
   if(!nMaterial) mensajeError+='Selecciona material. ';
   if(!nTratamiento) mensajeError+='Selecciona tratamiento. ';
   if(!nVision) mensajeError+='Selecciona visión. ';
   if(mensajeError){
      document.getElementById("fraseError").innerText = "ERROR: " + mensajeError;
      document.getElementById("divError").style.display = "block";
      return;
   }
   let arrNuevos=[];
   let tbl=document.getElementById("idTblGraduaciones");
   let rows=tbl.getElementsByTagName('tr');
   for(let i=1;i<rows.length;i++){
      let cells=rows[i].getElementsByTagName("input");
      let cantidad=cells[0]?cells[0].value:0;
      let esf=cells[1]?cells[1].value:0;
      let cil=cells[2]?cells[2].value:0;
      let adi=cells[3]?cells[3].value:0;
      arrNuevos.push([cantidad, esf, cil, adi]);
   }
   var arrNuevosJson = JSON.stringify(arrNuevos);

   // DEBUG: muestra en consola lo que vas a enviar
   console.log("DEBUG envía arrNuevos:", arrNuevos);
   console.log("DEBUG JSON:", arrNuevosJson);

   $.ajax({
      type: 'POST',
      url: 'fAjax.php',
      dataType: 'json',
      cache: false,
      async: true,
      data: {
         Key: "nuevoInventario",
         arrNuevos: arrNuevosJson,
         nMarca: nMarca,
         nMaterial: nMaterial,
         nTratamiento: nTratamiento,
         nVision: nVision,
         nSerie: nSerie
      },
success: function(data){
   document.getElementById("fraseError").innerText = data;
   document.getElementById("divError").style.display = "block";
   let tbody = tbl.getElementsByTagName('tbody')[0];
   while(tbody.rows.length > 1) tbody.deleteRow(1);
   fLlenaInventario();
},
      error: function(error){
         document.getElementById("fraseError").innerText = "ERROR AJAX: " + JSON.stringify(error);
         document.getElementById("divError").style.display = "block";
         console.log("ERROR AJAX:", error);
      }
   });
}

</script>
</body>
</html>
