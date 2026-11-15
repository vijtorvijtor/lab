<?php
session_start ();
include("config.php");
include("sesionCheck.php");
encabezado();
ini_set('display_errors', 'Off'); 
ini_set('log_errors', 'On'); 
error_reporting(E_ALL | E_STRICT);

$conn=$_SESSION["conn"];
date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL, 'es_MX.UTF-8');
setlocale(LC_TIME, "es_MX.UTF-8","esp");
$nIdNota=0;

if (isset($_GET['idNota'])){ 
   $nIdNota=$_GET['idNota'];
   llenaCampos($nIdNota);   
}
if(isset($_POST['submit'])) {
   $nIdNV=$_POST['nmIdNV'];
   llenaCampos($nIdNV);
   colectaDatos();
   calculaCosto(); 
   if ($nIdNV == 0){
      $a=0;
   }
}
function llenaRadio($cat,$func){
   global $aryMarca, $aryMaterial, $aryTratamiento, $aryVision, $arySerie;
   switch ($cat){
      case "marca":
         $aryData=$aryMarca; $sValor="ID"; $sTexto="Marca"; break;
      case "material":
         $aryData=$aryMaterial; $sValor="ID"; $sTexto="Material"; break;
      case "tratamiento":
         $aryData=$aryTratamiento; $sValor="ID"; $sTexto="Tratamiento"; break;
      case "vision":
         $aryData=$aryVision; $sValor="ID"; $sTexto="Vision"; break;
      case "serie":
         $aryData=$arySerie; $sValor="ID"; $sTexto="Serie"; break;
   }   
   mysqli_data_seek($aryData, 0);
   while ($row=$aryData->fetch_assoc()){
      echo "<input type='radio' class='radCompacta' id='id".$row[$sTexto]."'  value='".$row[$sValor]."'  name='nm".$cat."' onChange='".$func."(this)'/><label class='lblCompacta' for='id".$row[$sTexto]."' >".$row[$sTexto]." </label>" ;
   }   
}
function llenaSelect($cat){
   global $aryNota, $aryCliente, $aryTipoProducto;
   $sText="";
   switch ($cat){
      case "nota":
         mysqli_data_seek($aryNota, 0);
         echo "<option value=''></option>";
         while ($row=$aryNota->fetch_assoc()){
            $time = strtotime($row['fechaNota']);
            $fch =strftime("%a %d/%b/%Y %T", $time);
            $sText=sprintf("%06d",$row["idNota"]).", ".$row["Nombre"]." ".$row["Apellido"].", ".$fch;
            echo "<option value='".$row["idNota"]."'>".$sText."</option> ";
         }   
        break;  
      case "cliente":
         mysqli_data_seek($aryCliente, 0);
         echo "<option value=''></option>";
         while ($row=$aryCliente->fetch_assoc()){
            $sText=sprintf("%03d",$row["idCliente"]).", ".$row["Optica"].", ".$row["Nombre"]." ".$row["Apellido"].", ".$row["Tel1"];
            echo "<option value='".$row["idCliente"]."'>".$sText."</option>";
         }   
        break;  
      case "tipoProducto":
         mysqli_data_seek($aryTipoProducto, 0);
         echo "<option value=''></option>";
         while ($row=$aryTipoProducto->fetch_assoc()){
            $sText=$row["Producto"];
            echo "<option value='".$row["idProducto"]."'>".$sText."</option>";
         }   
        break;  
     }
}
$sQry="SELECT nota.optica AS idOptica, cliente.nombre AS Nombre, cliente.apellido AS Apellido, optica.nombre as Optica, nota.idNota as idNota, nota.fecha AS fechaNota FROM nota INNER JOIN optica ON nota.optica=optica.idOptica INNER JOIN cliente ON nota.cliente=cliente.idCliente ORDER BY idNota DESC";
$aryNota=mysqli_query($conn, $sQry); 

$sQry="SELECT cliente.idCliente AS idCliente, optica.nombre AS Optica, cliente.nombre AS Nombre, cliente.apellido AS Apellido, cliente.tel1 AS Tel1 FROM cliente INNER JOIN optica ON cliente.optica=optica.idOptica";
$aryCliente=mysqli_query($conn, $sQry); 
$sQry="SELECT producto.idProducto AS idProducto, producto AS Producto FROM producto";
$aryTipoProducto=mysqli_query($conn, $sQry); 
$sQry="SELECT idMarca AS ID, marca AS Marca FROM marca ORDER BY idMarca";
$aryMarca=mysqli_query($conn, $sQry);
$sQry="SELECT material.idMaterial AS ID, material.material AS Material FROM material";
$aryMaterial=mysqli_query($conn, $sQry); 
$sQry="SELECT tratamiento.idTratamiento AS ID, tratamiento.tratamiento AS Tratamiento FROM tratamiento";
$aryTratamiento=mysqli_query($conn, $sQry); 
$sQry="SELECT vision.idVision AS ID, vision.vision AS Vision FROM vision";
$aryVision=mysqli_query($conn, $sQry); 
$sQry="SELECT serie.idSerie AS ID, serie.serie AS Serie FROM serie";
$arySerie=mysqli_query($conn, $sQry); 
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
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
   <title>Laboratorio central -- Inicio</title>
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
<script type="text/javascript">if(typeof enciendeMenu === 'function'){enciendeMenu("encab1");}</script>
<datalist id="idListaDescuentos">
   <option value="0" label="0"></option>
   <option value="5" label="5"></option>
   <option value="10" label="10"></option>
   <option value="15" label="15"></option>
   <option value="20" label="20"></option>
   <option value="25" label="25"></option>
   <option value="30" label="30"></option>
</datalist>
<div id="idDivSelNota">
   <label>ID: </label>
   <input type="text" class="ROnly" id="idNV" name="nmIdNV" style="width:12%" value="<?php echo $nIdNota ?>" readonly/>
   <label>Nota: </label>
   <select style="width:70% !important;" class="js-example-diacritics selectFiltro" id="idSelNota" data-role="none" name="nmSelNota" onchange="fCambiaNota(this)"><?php llenaSelect("nota")  ?></select><br>
</div>
<div class="divCliente" id="divCliente">
<br>
<label>Cliente: </label>
   <select style="width:70% !important;" class="js-example-diacritics selectFiltro" id="idSelCliente" data-role="none" name="nmSelCliente" onchange="fCambiaCliente(this)"><?php llenaSelect("cliente")  ?></select><br>
</div>
<div class="divPartidas" id="divPartidas">
   <label>Producto: </label>
   <select style="width:60% !important;" class="js-example-diacritics selectFiltro" id="idTipoProducto" data-role="none" name="nmTipoProducto" onchange="fCambiaTipoProducto(this)"><?php llenaSelect("tipoProducto")  ?></select><br>
   <div id="divDetalleMica" style="display: none">
      <div class="divPar"><strong>MARCA: </strong><?php llenaRadio("marca","fCambiaMarcaMica")?><br> </div>
      <div class="divNon"><strong>MATERIAL: </strong><?php llenaRadio("material","fCambiaMaterial")  ?><br> </div>
      <div class="divPar"><strong>TRATAMIENTO: </strong><?php llenaRadio("tratamiento","fCambiaTratamiento") ?><br> </div>
      <div class="divNon"><strong>VISON: </strong><?php llenaRadio("vision","fCambiaVision")  ?><br> </div>
      <div class="divPar"><strong>SERIE: </strong><?php llenaRadio("serie","fCambiaSerie")  ?><br> </div>
      <div class="divGrad"><strong>Graduación:<BR> 
         Esf:<input class="clInpGradNota" id="idGradEsf" type="number" placeholder="0.00" min="-20" max="20" step="0.25" onkeyup="fVerificaValoresGrad(this, 'Esf')">
         Cil: -<input class="clInpGradNota" id="idGradCil" type="number" placeholder="0.00" min="0" max="6" step="0.25" onkeyup="fVerificaValoresGrad(this, 'Cil')">
         Adi: +<input class="clInpGradNota" id="idGradAdi" type="number" placeholder="0.00" min="0.75" max="3.5" step="0.25" onkeyup="fVerificaValoresGrad(this, 'Adi')">
      </div>
   </div>
   <br>Cantidad<input type="number" id="idCantidad" name="nmCantidad" onchange="fCambiaSubtotal()" pattern="[0-9]" step="any"/>
   Unitario: <input type="number" id="idUnitario" name="nmUnitario" onchange="fCambiaSubtotal()" step="any" value="<?php echo '0' ?>" /><br>
   Desc: <input type="number" id="idDescuento" name="nmDescuento" onchange="fCambiaDescuento(this)" value="0" step="5" /> %
   Subtotal: <input type="number" class="ROnly" id="idSubTotal" name="nmTotal" step="any" readonly value="<?php echo '0' ?>" /><br>
   <button id="btnAgregaPartida" onclick="agregaPartida()" disabled>Agregar</button>
</div>
<div id="divError" style="display: none">
   <p class="pError" id="fraseError"></p>
</div>
<div id="divTabla">
   <table id="idTablaPartidas" name="nmTablaPartidas" data="dataPartidas" class="clPartidas" style="display: visible;">
      <tr>
         <th text-align: right>Cant</th>
         <th>Descripción</th>
         <th text-align: right>Unitario</th>
         <th text-align: right>Desc%</th>
         <th text-align: right>Total</th>
         <th text-align: center>Elim</th>
         <th class="colOculta" text-align: left>Code</th>
      </tr>
      <tbody id='idTBody'></tbody>
   </table>
</div>
<div class="divTotal_" id="divTotal"><p class="tituloSeccion_" id="idGranTotal">Gran total:</p></div>
<div id="idGuardar">
   <button id="btnGuardar" onclick="fGuardaNota()" disabled>Guardar</button>
</div>
<div class="divAbonos" id="divAbonos">
   <p class="tituloSeccion">Abonos:<select style="width:70% !important;" class="js-example-diacritics selectFiltro" id="idSelAbonos" data-role="none" name="nmSelAbonos" readonly> </select></p>
   <p class="tituloSeccion">Pago: $<input type="number" class="ROnly" id="idAbono" name="nmAbono" onkeyup="fCambiaAbono(this)" pattern="[0-9]" step="any" disabled />
   <button id="idBtnAbono" onclick="fAbonar()">Pago</button></p>
   <p id="idResta" class="tituloSeccion"> Resta: </p>
</div>
<div><p id="idInfo" class="clInfo"></p></div>
<button id="idTestBtn" onclick="fTestBtn()">Prueba</button>
<input type="text" id="idDebug" name="nmDebug">

<script type="text/javascript">
// -------------------- VARIABLES GLOBALES --------------------
const diasSemana = ["Dom","Lun","Mar","Mie","Jue","Sab","Dom"];
let nErrores=1, nRenCorreccion=0, nIdNota=0, renExtra=0, nGranTotal=0, nSumaAbonos=0, horaTmpJS="";
let nTipoProd=0, nMarca=0, nMaterial=0, nTratamiento=0, nVision=0, nSerie=0, nEsf=0, nCil=0, nAdi=0, nGrad=0;
let sMarca="", sMaterial="", sTratamiento="", sVision="";

// -------------------- INICIALIZACION SELECT2 + EVENTOS --------------------
function ready0(){
   $.fn.select2.defaults.set('matcher', function(params, data) {
      if ($.trim(params.term) === '') return data;
      if (typeof data.text === 'undefined') return null;
      data.text=data.text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      var words = params.term.toUpperCase().split(" ");
      for (var i = 0; i < words.length; i++) {
         if (data.text.toUpperCase().indexOf((words[i]).replace(/[\u0300-\u036f]/g, '')) < 0) { return null; }
      }
      return data;
   });
   $('.selectFiltro').select2({placeholder: 'Seleciona', allowClear: true, width:"resolve", language: "es"});
   $('#idTablaPartidas tbody').on('click', 'td', function () {
      ren = $(this).closest("tr").index()+nRenCorreccion;
      col = $(this).closest("td").index();
      if (col==5 ){
         tbl = document.getElementById("idTablaPartidas");
         msg = tbl.rows[ren].cells[0].innerHTML + " " + tbl.rows[ren].cells[1].innerHTML;
         if (window.confirm("Se eliminará esta partida:\n"+msg)){
            tbl.deleteRow(ren);
         }
         sumaTotal();
      }
   });
}
$(document).ready(ready0);

// -------------------- FUNCIONES PRINCIPALES DE EVENTO --------------------
function fCambiaNota(a){
   idNota=Number(a.value);
   nIdNota=idNota;
   document.getElementById("idNV").value=nIdNota;
   $('#idTBody').html('<tr><td colspan="7">Cargando...</td></tr>');
   $.ajax({
      type: 'POST',
      url:'fAjax.php',
      dataType:'json',
      cache: false,
      async: true,
      data:{Key:"getPartidas", idNota: idNota},
      success: function(aPartidas){
         var content = '';
         aPartidas.forEach(item => {
            content += "<tr><td>" + item.cantidad + "</td><td>" + item.descripcion + "</td><td>" + item.unitario + "</td><td>"+item.descuento +"</td><td>"+item.total+"</td><td>X</td><td>"+item.codigo+"</td></tr>"
         });
         $('#idTBody').html(content);
         if(aPartidas.length > 0) {
            $('#idSelCliente').val(aPartidas[0].cliente).trigger('change');
            document.getElementById("idSelCliente").disabled = true;
         }
         document.getElementById("idAbono").disabled= false;
         document.getElementById("idAbono").classList.remove("ROnly");
         sumaTotal();
         fCambiaAbono();
         cargarAbonos(idNota);
      },
      error: function(error){
         alert("ERROR cargando partidas: " + JSON.stringify(error));
         $('#idTBody').html('<tr><td colspan="7">Error al cargar partidas</td></tr>');
      }
   });
}
function cargarAbonos(idNota){
   $.ajax({
      type:'POST',
      url:'fAjax.php',
      dataType:'json',
      cache: false,
      async: true,
      data:{Key:"getAbonos", idNota:idNota},
      success: function(aAbonos){
         var selAbonos = document.getElementById('idSelAbonos');
         selAbonos.innerHTML = "";
         nSumaAbonos = 0;
         aAbonos.forEach(item => {
            fTmp = new Date(item.fecha);
            sDia = diasSemana[fTmp.getDay()];
            nSumaAbonos += Number(item.abono);
            let content = "$" + item.abono + "; "+sDia+" "+item.fecha;
            selAbonos.add(new Option(content));
         });
         selAbonos.add(new Option("Abono total: $"+nSumaAbonos));
         $('#idSelAbonos option:last-child').attr('selected','selected');
      },
      error: function(error){
         alert("ERROR cargando abonos: " + JSON.stringify(error));
      }
   });
}
function fCambiaCliente(a){
   document.getElementById("idSelNota").disabled = true;
}
function fCambiaTipoProducto(a){
   fEnciendeRadios();
   nTipoProd = a.value;
   $.ajax({type: 'POST',url:'fAjax.php',dataType:'json',cache: false,async: true,data:{Key:"getPrecio", idProducto:nTipoProd},success: function(data){
      nPrecio=data;
      document.getElementById("idUnitario").value=nPrecio;
      if (nTipoProd==1){
         document.getElementById("divDetalleMica").style.display = "block";
      } else {
         document.getElementById("divDetalleMica").style.display = "none";
      }
      document.getElementById("idCantidad").value=0;
      document.getElementById("btnAgregaPartida").disabled=false;
      document.getElementById("divError").style.display = "none";
      $("input[type=radio][name=marca]").prop('checked', false);
      $("input[type=radio][name=material]").prop('checked', false);
      $("input[type=radio][name=tratamiento]").prop('checked', false);
      $("input[type=radio][name=vision]").prop('checked', false);
      $("input[type=radio][name=serie]").prop('checked', false);
   }});
}
function fCambiaMarcaMica(a){ nMarca=a.value; fFiltraRadios(); }
function fCambiaMaterial(a){ nMaterial=a.value; fFiltraRadios(); }
function fCambiaTratamiento(a){ nTratamiento=a.value; fFiltraRadios(); }
function fCambiaSerie(a){ nSerie=a.value; fFiltraRadios(); }
function fCambiaVision(a){ nVision=a.value; fFiltraRadios(); }
function fApagaRadios(sNbe){
   lTmp=document.querySelectorAll('input[name="'+sNbe+'"]');
   for(i of lTmp){
      i.style.display="none";
      i.nextSibling.style.display="none";
   }
}
function fEnciendeRadios(){
   const lGpo=["nmmarca","nmmaterial","nmtratamiento","nmvision"];
   for(let sNbeOpt of lGpo){
      lTmp=document.querySelectorAll('input[name="'+sNbeOpt+'"]');
      for(i of lTmp){
         i.style.display="inline";
         i.nextSibling.style.display="inline";
      }
   }
}

function fFiltraRadios(){
   // Siempre enciende todos los radios ANTES de filtrar
   fEnciendeRadios();
   sMarca = nMarca > 0 ? " =" + nMarca : " ";
   sMaterial = nMaterial > 0 ? " =" + nMaterial : " ";
   sTratamiento = nTratamiento > 0 ? " =" + nTratamiento : " ";
   sVision = nVision > 0 ? " =" + nVision : " ";
   sWhere = " WHERE marca.idMarca" + sMarca + " AND material.idMaterial" + sMaterial + " AND tratamiento.idTratamiento" + sTratamiento + " AND vision.idVision" + sVision;

   var filtros = [
      { sQry: "SELECT DISTINCT marca.marca AS Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmmarca", val: nMarca },
      { sQry: "SELECT DISTINCT material.material AS Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmmaterial", val: nMaterial },
      { sQry: "SELECT DISTINCT tratamiento.tratamiento AS Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmtratamiento", val: nTratamiento },
      { sQry: "SELECT DISTINCT vision.vision as Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmvision", val: nVision }
   ];

   filtros.forEach(function(f) {
      fRadiosSelectivos(f.sQry, f.gpo, f.val);
   });
}

// Solo oculta y muestra radios que sí cumplen el filtro.
// SI EL FILTRO NO REGRESA NADA, VUELVE A MOSTRAR TODOS.
function fRadiosSelectivos(sQuery, sGpo, nValorRadio){
   var lTmp = document.querySelectorAll('input[name="'+sGpo+'"]');
   $.ajax({
      type: 'POST',
      url: 'fAjax.php',
      dataType: 'json',
      cache: false,
      async: true,
      data: { Key:"filtraRadios", sQry:sQuery },
      success: function(aRet) {
         // Primero OCULTA TODAS las opciones del grupo
         for(let i of lTmp){
            i.style.display = "none";
            i.nextSibling.style.display = "none";
         }
         // Si hay resultados del filtro, muestra solo esos
         if(aRet.length > 0) {
            aRet.forEach(item => {
               var sNombre = "id"+item.Nombre;
               for(let i of lTmp){
                  if(i.id == sNombre || i.value == nValorRadio){
                     i.style.display = "inline";
                     i.nextSibling.style.display = "inline";
                  }
               }
            });
         } else {
            // Si no hay resultados, vuelve a mostrar todos los radios y labels
            for(let i of lTmp){
               i.style.display = "inline";
               i.nextSibling.style.display = "inline";
            }
         }
      },
      error: function(error) {
         // Si hay error, muestra todos los radios
         for(let i of lTmp){
            i.style.display = "inline";
            i.nextSibling.style.display = "inline";
         }
      }
   });
}


function fFiltraRadios(){
   // Siempre enciende todos los radios ANTES de filtrar
   fEnciendeRadios();
   sMarca = nMarca > 0 ? " =" + nMarca : " ";
   sMaterial = nMaterial > 0 ? " =" + nMaterial : " ";
   sTratamiento = nTratamiento > 0 ? " =" + nTratamiento : " ";
   sVision = nVision > 0 ? " =" + nVision : " ";
   sWhere = " WHERE marca.idMarca" + sMarca + " AND material.idMaterial" + sMaterial + " AND tratamiento.idTratamiento" + sTratamiento + " AND vision.idVision" + sVision;

   var filtros = [
      { sQry: "SELECT DISTINCT marca.marca AS Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmmarca", val: nMarca },
      { sQry: "SELECT DISTINCT material.material AS Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmmaterial", val: nMaterial },
      { sQry: "SELECT DISTINCT tratamiento.tratamiento AS Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmtratamiento", val: nTratamiento },
      { sQry: "SELECT DISTINCT vision.vision as Nombre FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision " + sWhere, gpo: "nmvision", val: nVision }
   ];

   filtros.forEach(function(f) {
      fRadiosSelectivos(f.sQry, f.gpo, f.val);
   });
}


function fCambiaDescuento(a){
   nVal=a.value;
   if (nVal<0 || nVal>50){
      window.confirm("Porcentaje de descuento inválido");
      a.value=0;
   }
   fCambiaSubtotal();
}


function revisaPartida(){
   console.log("Inicio de revisaPartida");
   nErrores=0;
   mensajeError="";
   var nCantidad = Number(document.getElementById("idCantidad").value);
   var nUnitario = Number(document.getElementById("idUnitario").value);
   var nEsf=Number(document.getElementById("idGradEsf").value);
   var nCil=Math.abs(Number(document.getElementById("idGradCil").value))*(-1);
   var nAdi=Math.abs(Number(document.getElementById("idGradAdi").value));

   if (nTipoProd == 1) {
      $.ajax({
         type: 'POST',
         url: 'fAjax.php',
         dataType: 'json',
         cache: false,
         async: true,
         data: {
            Key: "VerificaInventario",
            nEsf: nEsf,
            nCil: nCil,
            nAdi: nAdi,
            nMarca: nMarca,
            nMaterial: nMaterial,
            nTratamiento: nTratamiento,
            nVision: nVision
         },
         success: function(aRet){
            var nInv = 0;
            var nGrad = 0;
            aRet.forEach(function(item){
               nInv = Number(item.Inventario);
               nGrad = Number(item.Graduacion);
            });

            mensajeError = "";
            if (nInv < nCantidad){
               mensajeError += "Inventario insuficiente, solo se tienen ("+nInv+") piezas";
               nErrores = 1;
            }
            mensajeError += nMarca<1?"Verifique la marca ":"";
            mensajeError += nMaterial<1?"- Verifique el material ":"";
            mensajeError += nTratamiento<1?"- Verifique el tratamiento ":"";
            mensajeError += nSerie<1?"- Verifique la serie ":"";
            mensajeError += nVision<1?"- Verifique el tipo de visión ":"";

            if (nCantidad<=0 || nUnitario<=0){
               mensajeError += " -- Cantidad o precio unitario--";
               nErrores=2;
            } 

            // Renderiza error o éxito
            if (mensajeError.length>0){
               document.getElementById("fraseError").innerText="ERROR: "+mensajeError;
               document.getElementById("divError").style.display = "block";
            }else{
               document.getElementById("fraseError").innerText="TODO OK";
               document.getElementById("divError").style.display = "none";
               nErrores=0;
            }
         },
         error: function(error){
            alert("ERROR rev. inventario: " + JSON.stringify(error));
         }
      });
   } else {
      // Si no es tipo mica, solo validaciones generales
      if (nCantidad<=0 || nUnitario<=0){
         mensajeError = " -- Cantidad o precio unitario--";
         nErrores = 2;
      }
      if (mensajeError.length>0){
         document.getElementById("fraseError").innerText="ERROR: "+mensajeError;
         document.getElementById("divError").style.display = "block";
      }else{
         document.getElementById("fraseError").innerText="TODO OK";
         document.getElementById("divError").style.display = "none";
         nErrores = 0;
      }
   }
}

function agregaPartida(){
   revisaPartida();
   nEsf=Number(document.getElementById("idGradEsf").value);
   sEsf=(nEsf>=0)?"+"+nEsf.toFixed(2):nEsf.toFixed(2);
   nCil=Math.abs(Number(document.getElementById("idGradCil").value))*(-1);
   sCil=nCil.toFixed(2);
   nAdi=Math.abs(Number(document.getElementById("idGradAdi").value));
   sAdi=nAdi>0?"/"+nAdi.toFixed(2):"/";
   sGrad=sEsf+"/"+sCil+sAdi;
   if (!nErrores){
      tbl = document.getElementById("idTablaPartidas");
      ren = tbl.insertRow(-1);
      nCantidad = ren.insertCell(0);
      sDescr = ren.insertCell(1);
      nUnitario = ren.insertCell(2);
      nDescuento = ren.insertCell(3);
      nTotal = ren.insertCell(4);
      sDel=ren.insertCell(5);
      sCodigo=ren.insertCell(6);
      nCantidad.innerText = Number(document.getElementById("idCantidad").value);
      nUnitario.innerText = Number(document.getElementById("idUnitario").value);
      nDescuento.innerText= Number(document.getElementById("idDescuento").value)
      nTotal.innerText = (Number(nCantidad.innerText)*Number(nUnitario.innerText)*(1-(Number(nDescuento.innerText)/100))).toFixed(2);
      sDel.innerText="X";
      sTmp="";
      sCode=nTipoProd+","+nCantidad.innerText+","+nUnitario.innerText+","+nDescuento.innerText+","+nTotal.innerText;
      if (nTipoProd==1){
         sTmp="Lente "
         sTmp+=document.querySelector("input[type='radio'][name='nmmarca']:checked").nextSibling.innerHTML.trim()+", ";
         sTmp+=document.querySelector("input[type='radio'][name='nmmaterial']:checked").nextSibling.innerHTML.trim()+", ";
         sTmp+=document.querySelector("input[type='radio'][name='nmtratamiento']:checked").nextSibling.innerHTML.trim()+", ";
         sTmp+=document.querySelector("input[type='radio'][name='nmvision']:checked").nextSibling.innerHTML.trim()+", ";
         sTmp+=document.querySelector("input[type='radio'][name='nmserie']:checked").nextSibling.innerHTML.trim()+", ";
         sTmp += sGrad;
         sCode += ","+document.querySelector('input[name="nmmarca"]:checked').value+",";
         sCode += document.querySelector('input[name="nmmaterial"]:checked').value+",";
         sCode += document.querySelector('input[name="nmtratamiento"]:checked').value+",";
         sCode += document.querySelector('input[name="nmvision"]:checked').value+",";
         sCode += document.querySelector('input[name="nmserie"]:checked').value+",";
         sCode += nGrad;
      }else {
         sTmp=$('#idTipoProducto option:selected').text();
      }
      sDescr.innerText =sTmp;
      sCodigo.innerText=sCode;
      document.getElementById("idCantidad").value=0;
      document.getElementById("idTipoProducto").value=0;
      document.getElementById("btnAgregaPartida").disabled=true;
      document.getElementById("idDescuento").value=0;
      document.getElementById("idUnitario").value=0;
      sumaTotal();
      document.getElementById("btnGuardar").disabled=false;
   }
}
function fCambiaAbono(){
   var nAbono=Number(document.getElementById("idAbono").value)||0;
   var nTmp= nGranTotal - nAbono - nSumaAbonos;
   document.getElementById("idResta").innerText="Resta: " + Number(nTmp.toFixed(2)).toLocaleString('en-US', { style: 'currency', currency: 'USD' });
   document.getElementById("btnGuardar").disabled=nAbono==0? true:false; 
}
function fCambiaSubtotal(){
   var nFactor=1-(document.getElementById("idDescuento").value /100);
   var nTmp=document.getElementById("idCantidad").value * document.getElementById("idUnitario").value * nFactor;
   document.getElementById("idSubTotal").value=nTmp.toFixed(2);
   document.getElementById("btnGuardar").disabled=(nTmp==0);
}
function sumaTotal(){
   let rows = document.getElementById("idTablaPartidas").getElementsByTagName("tr");
   let sum = 0;
   for (let i = 1; i < rows.length; i++) {
      sum += parseFloat(rows[i].children[4].textContent || "0");
   }
   nGranTotal = sum;
   fCambiaAbono();
   document.getElementById("idGranTotal").innerText="Gran total: "+Number(sum.toFixed(2)).toLocaleString('en-US', { style: 'currency', currency: 'USD' });
}

function fGuardaNota() {
    const nCliente = document.getElementById("idSelCliente").value;
    const tbl = document.getElementById("idTablaPartidas");
    const nGranTotal = parseFloat(document.getElementById("idGranTotal").innerText.replace(/[^0-9.-]+/g,""));
    const nNota = parseInt(document.getElementById("idNV").value);
    
    // Validación básica
    if (!nCliente) {
        alert("Seleccione un cliente");
        return;
    }

    // Deshabilitar y mostrar estado
    const btnGuardar = document.getElementById("btnGuardar");
    const infoDiv = document.getElementById("idInfo");
    btnGuardar.disabled = true;
    infoDiv.innerHTML = "⌛ Guardando...";

    // Preparar partidas
    const partidas = [];
    for (let i = 1; i < tbl.rows.length; i++) {
        let row = tbl.rows[i];
        const partida = {
            cantidad: parseInt(row.cells[0].innerHTML),
            descripcion: row.cells[1].innerHTML.trim(),
            unitario: parseFloat(row.cells[2].innerHTML),
            descuento: parseFloat(row.cells[3].innerHTML),
            total: parseFloat(row.cells[4].innerHTML),
            codigo: row.cells[6].innerHTML.trim()
        };
        partida.producto = parseInt(partida.codigo.split(',')[0]);
        partidas.push(partida);
    }

    // Usar Promise para mejor manejo de async/await
    guardarNotaCompleta(nNota, nCliente, nGranTotal, partidas, infoDiv, btnGuardar);
}

async function guardarNotaCompleta(nNota, nCliente, nGranTotal, partidas, infoDiv, btnGuardar) {
    try {
        // 1. Crear/Actualizar nota
        const notaData = await $.ajax({
            type: 'POST',
            url: 'fAjax.php',
            dataType: 'json',
            data: {
                Key: "nuevaNota",
                idNota: nNota,
                idCliente: nCliente,
                nTotal: nGranTotal,
                sComentarios: ""
            }
        });

        const nIdNota = notaData;

        // 2. Guardar partidas
        const partidasResult = await $.ajax({
            type: 'POST',
            url: 'fAjax.php',
            dataType: 'json',
            data: {
                Key: "guardarPartidasCompletas",
                idNota: nIdNota,
                partidas: JSON.stringify(partidas)
            }
        });

        if (partidasResult.success) {
            infoDiv.innerHTML = nNota === 0 ? 
                `✅ Nueva nota #${nIdNota} guardada (${partidas.length} partidas)` : 
                `✅ Nota #${nIdNota} actualizada (${partidas.length} partidas)`;
            
            document.getElementById("idAbono").disabled = false;
            if (nNota === 0) {
                document.getElementById("idNV").value = nIdNota;
            }
        } else {
            throw new Error(partidasResult.message);
        }

    } catch (error) {
        console.error("Error:", error);
        infoDiv.innerHTML = `❌ Error al guardar: ${error.responseJSON?.message || error.statusText || 'Error de conexión'}`;
        btnGuardar.disabled = false; // Rehabilitar en caso de error
    }
}




function fAbonar(){
   var nAbono=Number(document.getElementById("idAbono").value)||0;
   if (nAbono!=0){
      $.ajax({
         type: 'POST',
         url:'fAjax.php',
         dataType:'json',
         cache: false,
         async: true,
         data:{Key:"nuevoAbono", nAbono:nAbono, idNota:nIdNota},
         success: function(data){
            document.getElementById("idInfo").innerHTML="Pago agregado ($"+nAbono+")";
            document.getElementById("idAbono").value="";
            cargarAbonos(nIdNota);
            sumaTotal();
         },
         error: function(error){
            alert("ERROR: ["+JSON.stringify(error)+"]");
         }
      });
   }
}
function fVerificaValoresGrad(a,tipo){
   var nValor=a.value, nErrVal=0;
   switch (tipo){
      case "Esf":
         if (nValor<-20 || nValor>20 || !(Number.isInteger(nValor*4))) nErrVal=1;
         break;
      case "Cil":
         nValor=Math.abs(nValor)*(-1);
         if (nValor>0 || nValor<-6 || !(Number.isInteger(nValor*4))) nErrVal=1;
         break;
      case "Adi":
         if (nValor<0 || nValor>3.5 || !(Number.isInteger(nValor*4))) nErrVal=1;
         break;
   }
   if (nErrVal>0){
      document.getElementById("fraseError").innerText="ERROR en graduación";
      document.getElementById("divError").style.display = "block";
      document.getElementById("btnAgregaPartida").disabled = true;
   } else{
      document.getElementById("fraseError").innerText="";
      document.getElementById("divError").style.display = "none";
      document.getElementById("btnAgregaPartida").disabled = false;
   }
}
function fTestBtn(){ document.getElementById("idInfo").innerHTML="Prueba de FONT"; }

</script>
</body>
</html>
