<?php
session_start ();
include("config.php");
include("sesionCheck.php");
include("utils.php");
encabezado();
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
      <link href="css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
   <script src="js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
   <script src="js/utils.js?v=1.1"></script>
   <link href="js/select2.min.css" rel="stylesheet" />
   <script src="js/select2.min.js"></script>
   <title>Óptica ECO -- Nota de venta</title>
</head>


<body >
<script type="text/javascript">enciendeMenu("encab3");</script>  

<script src="js/notav.js"></script>   
<script type="text/javascript">
   function fCambiaId(a){
      console.log("valor de a:("+a+")"+"valor de a.value:("+a.value+")"); 
      $("#idSelNota").val(a).trigger('change');
   }
</script>

<?php
$conn=$_SESSION["conn"];
$dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL, 'es_MX.UTF-8');
setlocale(LC_TIME, "es_MX.UTF-8","esp");
error_reporting(E_ERROR | E_PARSE);
//echo strtotime('next tuesday');
//echo ", El dia jueves es:[". date('W', strtotime('next tuesday'))."]";

$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) &&($_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0' ||  $_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache'); 
//funcion_alert("Refrescada:[".$pageWasRefreshed."]");
//Variables globales
$nIdNV=0;
$sFecha="";
$nIdRx=0;
$sTelefono="";
$sNombre="";
$nArmTipo=0;
$nArmMarca=0;
$sArmDesc="";
$nLenMat=0;
$nLenTra=0;
$nLenVis=0;
$nLenSer=0;
$nOtro=0;
$nTotal=0;
$nResta=0;
$sFAbono="";
$nAbono=0;
$nAcuenta=0;
$sFEntrega="";
$nStatus=99;
$nCtoArm=0;
$nCtoMicas=0;
$nCtoOtros=0;
$sObservaciones="";
$nIdNV=(isset($_GET['idNV']))?$_GET['idNV']:"";
$nIdInv=0;
$nAlmacen=0;
$sTmp="nIdNV:[".$nIdNV."]";

//Si carga la pagina desde index con un idNV especifico
if (isset($_GET['idNV'])){ 
   $nIdNV=$_GET['idNV'];
   llenaCampos($nIdNV);   
}
//Si carga la pagina despues de un submit
if(isset($_POST['submit'])) { //Si idRx 0 o vacio crea un nuevo registro si no lo actualiza

   $nIdNV=$_POST['nmIdNV'];
   llenaCampos($nIdNV);
   colectaDatos();
   calculaCosto(); 

   if ($nIdNV == 0){ //Si es 0 es porque es un registro nuevo
      
      //funcion_alert("Dara de alta el registro");
      //echo("Da de alta un nuevo registro con estos datos: [" . $sNombre ."]" );
      $sVar=" (fecha, nombre, rx, telefono, armTipo, armDesc, armMarca, lenteMat, lenteTra, lenteVis, lenteSer, otro, total, resta, abono, ctoArmNV, ctoMicasNV, ctoOtrosNV,  fechaentrega, estatus, observaciones,idInventario)";
      $sVal=" VALUES('".$sFecha."', '".$sNombre."', ".$nIdRx.", '".$sTelefono."', ".$nArmTipo.", '".$sArmDesc."', ".$nArmMarca.", ".$nLenMat.", ".$nLenTra.",  ".$nLenVis.", ".$nLenSer.", ".$nOtro.",".$nTotal.", ".$nResta.", ".$nAbono.", ".$nCtoArm.", ".$nCtoMicas.", ". $nCtoOtros.", '".$sFEntrega."', ".$nStatus.",'".$sObservaciones."',".$nIdInv.")";
      $sql = " INSERT INTO notav " .$sVar. $sVal ;
      //echo "Inserta registor asi: [".$sql."]";
      if (mysqli_query($conn, $sql)) {
            echo "Registro guardardo!";
            //fAlerta("Dio de alta el registro ");
            $nIdNV = mysqli_insert_id($conn);
            $sFAbono=date('Y-m-d H:i', strtotime($sFAbono));
            //funcion_alert("Primer abono fecha :[".$sFAbono."]");
            if ($nAbono>0){ //Si hay abono agrega nuevo registro a NV
               $sql="INSERT INTO abonos (idNV, fechaAbono, abono) VALUES(".$nIdNV.",'".$sFAbono."', ".$nAbono.")";
               if (mysqli_query($conn, $sql)) {
                  $a=1;
                  //funcion_alert("Abono agregado ok");
               }else{
                  $a=1;
                  //funcion_alert("Fallo agregar el abono");
               }
               if ($nIdInv>0){
                  $sql="UPDATE inventario SET idAlmacen=".$nAlmacen." WHERE idInv=".$nIdInv ;
                  //echo "se cambia el inventario asi en una nota nueva:[".$sql."]<br>";
                  mysqli_query($conn, $sql);                  
               }

            }else{
               $a=1;
               //funcion_alert("No agrega abono porque valor de input:[".$nAbono."]");
            } //if else de si hay abono
            llenaCampos($nIdNV); //LLena los inputs con el mismo campo que se guardo
            echo '<script type="text/javascript">fCambiaId($nIdNV);</script>';


      } else {
         $a=1;
            //echo "Error al insertar el registro: " . $sql . "<br>" . mysqli_error($conn);
      }
      
   } else { //Si el idRx es <>0 lo actualiza porque no es nuevo.
      //funcion_alert("Actualizará el registro:[" . $sFecha . "]");
      //if ($pageWasRefreshed){
      //   exit("No se debe refrescar la pagina");
      //}
      $sIgualdades=trim(" fecha='" . $sFecha . "', ");
      $sIgualdades.="rx=".$nIdRx.", ";          
      $sIgualdades.= "telefono='".$sTelefono."', ";
      $sIgualdades.= "nombre='".$sNombre."', ";
      $sIgualdades.= "armTipo=".$nArmTipo.", ";
      $sIgualdades.= "armDesc='".$sArmDesc."', ";            
      $sIgualdades.= "armMarca=".$nArmMarca.", ";       
      $sIgualdades.= "lenteMat=".$nLenMat.", "; 
      $sIgualdades.= "lenteTra=".$nLenTra.", "; 
      $sIgualdades.= "lenteVis=".$nLenVis.", ";
      $sIgualdades.= "lenteSer=".$nLenSer.", ";
      $sIgualdades.= "otro=".$nOtro.", ";
      $sIgualdades.= "total=".$nTotal.", ";
      $sIgualdades.= "resta=".$nResta.", ";
      $sIgualdades.= "abono=".$nAbono.", ";
      $sIgualdades.= "fechaentrega='".$sFEntrega."', ";
      $sIgualdades.= "ctoArmNV=".$nCtoArm.", ";
      $sIgualdades.= "ctoMicasNV=".$nCtoMicas.", ";
      $sIgualdades.= "ctoOtrosNV=".$nCtoOtros.", ";
      $sIgualdades.= "estatus=".$nStatus.", ";   
      $sIgualdades.= "observaciones='".$sObservaciones."', ";   
      $sIgualdades.= "idInventario=".$nIdInv;   
      $sQry="UPDATE notav SET ".$sIgualdades." WHERE idNV=".$nIdNV;
      //echo "Actualiza asi:[".$sQry."]";      
      if (mysqli_query($conn, $sQry)) {
         $sFAbono=date('Y-m-d H:i', strtotime($sFAbono));
         //echo "se intenta cambiar almacen al:[".$nAlmacen."], inventarioreg:[".$nIdInv."]<BR>";
         if ($nIdInv>0){
            $sql="UPDATE inventario SET idAlmacen=".$nAlmacen." WHERE idInv=".$nIdInv ;
            //echo "se cambia el inventario asi en una nota actualizada:[".$sql."]<br>";
            mysqli_query($conn, $sql);                  
         }

         echo "Registro Actualizado!";
         if ($nAbono<>0){
            $sql="INSERT INTO abonos (idNV, fechaAbono, abono) VALUES(".$nIdNV.",'".$sFAbono."', ".$nAbono.")";
            if (mysqli_query($conn, $sql)) {
               $a=1;
               //funcion_alert("Abono agregado ok");
            }else{
               $a=1;
               //funcion_alert("Fallo agregar el abono");
            }
         }else{
            $a=1;
         } //if else de si hay abono
         llenaCampos($nIdNV);
         //funcion_alert("Actualizo y termino  llenaCampos(".$nId.")");
      } else {
            $a=1;
            //echo "Error: " . $sQql . "<br>" . mysqli_error($conn);
      }
   }
} //Cierra del si-es-submit
function llenaSelect($cat){
   $conn=$_SESSION['conn'];
   $dias = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado"); 
   global $aryArmTipo;
   global $aryArmMarca;
   global $aryLMat;
   global $aryLTra;
   global $aryLSer;
   global $aryLVis;
   global $aryRx;
   global $aryAbn;
   global $nIdNV;
   global $aryStatus;
   global $aryOtro;
   global $aryNota;
   global $aryInventario;
   switch ($cat){
      case "inventario":
         mysqli_data_seek($aryInventario, 0);
         echo "<option value=''></option>";
         while ($row=$aryInventario->fetch_assoc()){
            echo "<option value='".$row["ID"]."'>".$row["CBarras"]."</option>";
         }   
        break;  

      case "nota":
         $sQry="SELECT notav.idNV, notav.nombre, notav.fecha  FROM notav  ORDER BY idNV DESC";
         $aryNota=mysqli_query($conn, $sQry); 
         mysqli_data_seek($aryNota, 0);
         echo "<option value=''></option>";
         while ($row=$aryNota->fetch_assoc()){
            $time = strtotime($row['fecha']);
            $fch =strftime("%a %d/%b/%Y", $time);
            echo "<option value='".$row["idNV"]."'>".$row["idNV"].", ".$row["nombre"].", ".$fch."</option>";
         }     
         break;    
      case "armTipo":
         mysqli_data_seek($aryArmTipo, 0);
         echo "<option value=''></option>";
         while ($row=$aryArmTipo->fetch_assoc()){
            echo "<option value='".$row["idArmTipo"]."'>".$row["nombreArmTipo"]." /".$row["armPrecio"]."</option>";
         }     
         break;    
      case "armMarca":     
         mysqli_data_seek($aryArmMarca, 0);
         echo "<option value=''></option>";
         while ($row=$aryArmMarca->fetch_assoc()){
            echo "<option value='".$row["idArmMarca"]."'>".$row["nombreArmMarca"]."</option>";
         }   
        break;  
      case "lMat":     
         mysqli_data_seek($aryLMat, 0);
         echo "<option value=''></option>";
         while ($row=$aryLMat->fetch_assoc()){
            echo "<option value='".$row["idLMaterial"]."'>".$row["matNombre"]." /".$row["matPrecio"]."</option>";
         }   
        break;    
      case "lTra":     
         mysqli_data_seek($aryLTra, 0);
         echo "<option value=''></option>";
         while ($row=$aryLTra->fetch_assoc()){
            echo "<option value='".$row["idLTratamiento"]."'>".$row["traNombre"]." /".$row["traPrecio"]."</option>";
         }   
        break;    
      case "lVis":     
         mysqli_data_seek($aryLVis, 0);
         echo "<option value=''></option>";
         while ($row=$aryLVis->fetch_assoc()){
            echo "<option value='".$row["idLVision"]."'>".$row["visNombre"]." /".$row["visPrecio"]."</option>";
         }   
        break;    
      case "lSer":     
         mysqli_data_seek($aryLSer, 0);
         echo "<option value=''></option>";
         while ($row=$aryLSer->fetch_assoc()){
            echo "<option value='".$row["idLSerie"]."'>".$row["serNombre"]." /".$row["serPrecio"]."</option>";
         }   
        break;    
      case "rx":     
         mysqli_data_seek($aryRx, 0);
         echo "<option value=''></option>";
         while ($row=$aryRx->fetch_assoc()){
            $time = strtotime($row['fecha']);
            $fch =strftime("%a %d/%b/%Y", $time);
            echo "<option value='".$row["idRx"]."'>".$row["idRx"].", ".$row["nombre"]." " .$row["apellido"].", " .$row["ocupacion"].", " .$fch."</option>";
         }   
        break;    
      case "status":     
         mysqli_data_seek($aryStatus, 0);
         echo "<option value=''></option>";
         while ($row=$aryStatus->fetch_assoc()){
            echo "<option value='".$row["idStatus"]."'>".$row["estado"]."</option>";
         }   
        break;    
      case "otro":     
         mysqli_data_seek($aryOtro, 0);
         echo "<option value=''></option>";
         while ($row=$aryOtro->fetch_assoc()){
            echo "<option value='".$row["idOtros"]."'>".$row["nombre"]." /".$row["precio"]."</option>";
         }   
        break;    
      case "abonos":
         //$sTmp="Llenanodo lista de abonos... nIdNV:[$nIdNV], sizeof(arsyAbn):[".sizeof($aryAbn)."]";
         if (((int)$nIdNV)>0){
            //funcion_alert("Probara si aryAbn tiene datos on seek");
            if(mysqli_data_seek($aryAbn, 0)){
               //$sTmp="seek esTRUE y nIdNV:[$nIdNV]  mayor que 0";
               echo "<option value=''></option>";
               //funcion_alert($sTmp); 
               while ($row=$aryAbn->fetch_assoc()){
                  $time = strtotime($row['fechaAbono']);
                  $fch =strftime("%a %d/%b/%Y", $time);
                  echo "<option value='".$row["idAbono"]."'>$".$row["abono"].", ".$dias[date('w',strtotime($row["fechaAbono"]))]." ".$fch."</option>";

               } //Del While
            } //Delif seek
         }else{
            $aa=1;   
         }
        break;    
   }
}

//Inicia procedimiento principal 
$sQry="SELECT inventario.idInv AS ID, inventario.idAlmacen AS Almacen, concat(inventario.cBarras,', ',catArmMarca.nombreArmMarca,', ',inventario.modelo) AS CBarras FROM inventario INNER JOIN catArmMarca ON inventario.idArmMarca=catArmMarca.idArmMarca ";
$aryInventario=mysqli_query($conn, $sQry); 
$sQry="SELECT *  FROM catArmTipo  ORDER BY nombreArmTipo ";
$aryArmTipo=mysqli_query($conn, $sQry); 
$sQry="SELECT * FROM catArmMarca ORDER BY nombreArmMarca ";
$aryArmMarca=mysqli_query($conn, $sQry);
$sQry="SELECT * FROM catLMaterial ORDER BY matNombre ";
$aryLMat=mysqli_query($conn, $sQry);
$sQry="SELECT * FROM catLTratamiento ORDER BY traNombre ";
$aryLTra=mysqli_query($conn, $sQry);
$sQry="SELECT * FROM catLVision ORDER BY visNombre";
$aryLVis=mysqli_query($conn, $sQry);
$sQry="SELECT * FROM catLSerie ORDER BY serNombre";
$aryLSer=mysqli_query($conn, $sQry);
$sQry="SELECT rx.idRx, paciente.nombre, paciente.apellido, paciente.ocupacion, rx.fecha FROM rx INNER JOIN paciente ON rx.idPaciente=paciente.idPaciente ORDER BY rx.idRx DESC";
$aryRx=mysqli_query($conn, $sQry);
$sQry="SELECT * FROM status ORDER BY idStatus";
$aryStatus=mysqli_query($conn, $sQry);
$sQry="SELECT * FROM otros ORDER BY nombre";
$aryOtro=mysqli_query($conn, $sQry);
$sQry="SELECT * FROM abonos WHERE idNV=".$nIdNV. " ORDER BY fechaAbono ";
$aryAbn=mysqli_query($conn, $sQry);

function llenaCampos($nId){ //Llena los inputs con el registro idRx solicitado
   global  $conn, $dbhost, $dbuser, $dbpass, $dbname, $nIdNV, $nIdRx, $oReg, $sFecha, $sTelefono, $nArmTipo, $sNombre, $nArmMarca, $sArmDesc, $nLenMat, $nLenTra, $nLenVis, $nLenSer,$nOtro, $nTotal, $nAcuenta, $nAbono, $sFAbono, $sFEntrega, $sObservaciones, $nStatus, $nIdInv, $nAlmacen;
   //$conn=mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
   if (mysqli_connect_errno()) {
      die("ERROR al intentar conectar a la base de datos [".$dbhost.", ".$dbname.", ".$dbuser."]: " .mysqli_connect_error())."<br>";
   }else {
      if (!mysqli_set_charset($conn, "utf8")){
         printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($conns));
            exit();
      }  
      $sQry="SELECT * FROM notav WHERE idNV=".$nId.";";
      if ($ret=mysqli_query($conn, $sQry)){
         $oReg = mysqli_fetch_array($ret);
         $nIdNV=$nId;
         $nIdRx=$oReg['rx'];
         $sFecha=date('Y-m-d\TH:i', strtotime($oReg['fecha']));
         $sNombre=$oReg['nombre'];
         //echo "dentro de llenaCampos valode inv:[".$oReg['idInventario']."]";
         $nIdInv=$oReg['idInventario'];
         $nArmTipo=$oReg['armTipo'];
         $nArmMarca=$oReg['armMarca'];
         $sArmDesc=$oReg['armDesc'];
         $nLenMat=$oReg['lenteMat'];
         $nLenTra=$oReg['lenteTra'];
         $nLenVis=$oReg['lenteVis'];
         $nLenSer=$oReg['lenteSer'];
         $nOtro=$oReg['otro'];
         $nTotal=$oReg['total'];
         date_default_timezone_set('America/Mexico_City');
         $dTmp = new DateTime;
         $sFAbono=date_format($dTmp, 'Y-m-d\TH:i');//////////AQUI
         $nAbono=$oReg['abono'];
         //$nAcuenta=$oReg['acuenta'];
         $sFEntrega=$oReg['fechaentrega'];
         //funcion_alert("En llenaCampos() sFEntrega:[".$sFEntrega."]");
         $nStatus=$oReg['estatus'];
         $sObservaciones=$oReg['observaciones'];
      }else{
         $a=1;
         //funcion_alert("No encontro el regstro solicitado");
      }
      $sQry="SELECT SUM(abono) AS abn FROM abonos WHERE  idNV=".$nId.";";
      if ($ret=mysqli_query($conn, $sQry)){
         $rows = mysqli_num_rows($ret);
         if ($rows>0){
            $oReg = mysqli_fetch_array($ret);
            $nAcuenta=is_null($oReg['abn'])?0:$oReg['abn'];
            $nResta=$nTotal - $nAcuenta;
         } else{
            $a=1;
            //funcion_alert("NO Hay abonos asignados a esta nota");
         }
      }  
   }
}

function colectaDatos(){ //Pasa a variables globales el contenido de los inputs
   global $nIdNV, $nIdRx, $sFecha, $sNombre, $sTelefono, $nArmTipo, $sArmDesc, $nArmMarca, $nLenMat, $nLenTra, $nLenVis, $nLenSer, $nOtro, $nTotal, $nResta, $nAbono, $sFAbono, $sFEntrega, $nStatus, $sObservaciones, $nCtoArm, $nCtoMicas, $nIdInv, $nAlmacen;
   $nId=$_POST['nmIdNV'];
   $nIdNV=$nId;
   $sFecha=str_replace("T"," ",$_POST['nmFecha']);
   date_default_timezone_set('America/Mexico_City');
   if ($sFecha==""){
      $date = new DateTime;
      $sFecha=date_format($date, 'Y-m-d H:i');
   }
   $nIdRx=is_numeric($_POST['nmRx'])?$_POST['nmRx']:0;
   //$sTelefono=$_POST['nmTelefono'];
   $nArmTipo=is_numeric($_POST['nmArmTipo'])?$_POST['nmArmTipo']:0;
   //echo "datos despues del submit_ nId:[".$nId."], nIdRx:[".$nIdRx."], sTelefono:[".$sTelefono."], nArmTipo[".$nArmTipo."]";
   $nArmMarca=is_numeric($_POST['nmArmMarca'])?$_POST['nmArmMarca']:0;
   $sNombre=$_POST['nmNombre'];
   $sArmDesc=$_POST['nmArmDesc'];
   $nLenMat=is_numeric($_POST['nmLMat'])?$_POST['nmLMat']:0;
   $nLenTra=is_numeric($_POST['nmLTra'])?$_POST['nmLTra']:0;
   $nLenVis=is_numeric($_POST['nmLVis'])?$_POST['nmLVis']:0;
   $nLenSer=is_numeric($_POST['nmLSer'])?$_POST['nmLSer']:0;
   $nOtro=is_numeric($_POST['nmOtro'])?$_POST['nmOtro']:0;
   $nTotal=is_numeric($_POST['nmTotal'])?$_POST['nmTotal']:0;
   $nResta=is_numeric($_POST['nmResta'])?$_POST['nmResta']:0;
   $sFAbono=str_replace("T"," ",$_POST['nmFAbono']);
   $nAbono=is_numeric($_POST['nmAbono'])?$_POST['nmAbono']:0;
   $nStatus=is_numeric($_POST['nmStatus'])?$_POST['nmStatus']:0;
   $sFEntrega=$_POST['nmFEntrega'];
   $sObservaciones=$_POST['nmObservaciones'];
   $nIdInv=is_numeric($_POST['nmIdInv'])?$_POST['nmIdInv']:0;
   //echo "Dentro de colectaDatos valor de idInv:[".$nIdInv."]<BR>";
   //echo "datos despues del submit_ nId:[".$nId."], nIdRx:[".$nIdRx."], sFecha:[".$sFEntrega."], sObservaciones:[".$sObservaciones."].   ";
   if ($sFEntrega==""){
      $date = date('Y-m-d', strtotime('next Thursday'));
      $sTmp="prox fecha:[".$date."]";
      $sFEntrega=date_format($date, 'Y-m-d H:i');
   }
                  switch ($nStatus){
                  case "1":
                  case "3":
                  case "4":
                  case "8":
                     $nAlmacen=53;
                  break;
                  case "2";
                     $nAlmacen=52;
                     break;
                  case "5";
                     $nAlmacen=1;
                     break;
                  case "6";
                  case "7";
                     $nAlmacen=51;
                     break;
                  case "10";
                     $nAlmacen=54;
                     break;
                  case "11";
                     $nAlmacen=56;
                     break;
               }

}

function calculaCosto(){
global  $dbhost, $dbuser, $dbpass, $dbname,  $oReg, $sFecha,  $nArmTipo, $sNombre, $nLenMat, $nLenTra, $nLenVis, $nLenSer, $nOtro, $nCtoArm, $nCtoOtros, $nCtoMicas;
   $conn=$_SESSION['conn'];
//$conn=mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
//fAlerta("dentro de calculaCosto");
$sQry="SELECT *  FROM catArmTipo  WHERE idArmTipo=".$nArmTipo;
$aryTmp=mysqli_query($conn, $sQry); 
mysqli_data_seek($aryTmp, 0);
while ($row=$aryTmp->fetch_assoc()){
   $nCtoArm=$row["armCosto"];
}
$sQry="SELECT *  FROM catLMaterial  WHERE idLMaterial=".$nLenMat;
$aryTmp=mysqli_query($conn, $sQry); 
mysqli_data_seek($aryTmp, 0);
while ($row=$aryTmp->fetch_assoc()){
   $nCtoMat=$row["matCosto"];
}

$sQry="SELECT *  FROM catLTratamiento  WHERE idLTratamiento=".$nLenTra;
$aryTmp=mysqli_query($conn, $sQry); 
mysqli_data_seek($aryTmp, 0);
while ($row=$aryTmp->fetch_assoc()){
   $nCtoTra=$row["traCosto"];
}

$sQry="SELECT *  FROM catLVision  WHERE idLVision=".$nLenVis;
$aryTmp=mysqli_query($conn, $sQry); 
mysqli_data_seek($aryTmp, 0);
while ($row=$aryTmp->fetch_assoc()){
   $nCtoVis=$row["visCosto"];
}

$sQry="SELECT *  FROM catLSerie  WHERE idLSerie=".$nLenSer;
$aryTmp=mysqli_query($conn, $sQry); 
mysqli_data_seek($aryTmp, 0);
while ($row=$aryTmp->fetch_assoc()){
   $nCtoSer=$row["serCosto"];
}

$sQry="SELECT *  FROM otros  WHERE idOtros=".$nOtro;
$aryTmp=mysqli_query($conn, $sQry); 
mysqli_data_seek($aryTmp, 0);
while ($row=$aryTmp->fetch_assoc()){
   $nCtoOtros=$row["costo"];
}

$nCtoMicas=$nCtoMat+$nCtoTra+$nCtoVis+$nCtoSer;
$sTxt="costoMicas:[".$nCtoMicas."]";
//fAlerta("$sTxt");
}
?>
<form id="idForma"  method="post"  onkeydown="return event.key != 'Enter';"/>
<label>id: </label>
<input type="text" class="ROnly" id="idNV" name="nmIdNV" style="width:12%" value="<?php echo $nIdNV ?>" onchange="fCambiaId(this)" readonly/>

<div id="idDivSelNota">
   <label>Nota: </label>
   <select style="width:70%   !important;" class="js-example-diacritics selectFiltro "  id="idSelNota"  data-role="none" name="nmSelNota" onchange="fCambiaNota(this)" > <?php llenaSelect("nota")  ?></select><br>
</div> 
<div id="idDivSelRX">
   <label>RX: </label>
   <select style="width:70%   !important;" class="selectFiltro"  id="lIdRx"  data-role="none" name="nmRx" onchange="fCambiaRx(this)"> <?php llenaSelect("rx")  ?></select><br>   
</div>
<div  class="general" id="idGeneral">
     
      <label class="labelNV">Nombre: </label>
      <input type="text" class="nombreCapital" id="idNombre" name="nmNombre" style="width:70%" value="<?php echo $sNombre ?>" /><br>
      <label>Fecha: </label>
      <input type="datetime-local" name="nmFecha"  id="idFecha" style="width:60%" value="<?php echo $sFecha ?>"  onchange="fCambiaFecha(this)" /><br>
         <label> Código:</label>
         <select style="width:30%; height:500px  !important;" class="selectFiltro"  id="lIdIdInv"  data-role="none" name="nmIdInv" onchange="fCambiaInventario(this)"> <?php llenaSelect("inventario") ?></select> <br>      
         <div class="clDesc">

         <select style="width:30%; height:500px  !important;" class="selectFiltro"  id="lIdArmMarca"  data-role="none" name="nmArmMarca" onchange="fCambiaArmMarca(this)"> <?php llenaSelect("armMarca")  ?></select>
         <input type="text" id="idADesc" name="nmArmDesc" style="width:65%" value="<?php echo $sArmDesc; ?>"/><br>
         <label class="labelNV">A Tipo: </label>
         <select style="width:50%; height:500px  !important;" class="selectFiltro form-select rounded-0"  id="lIdArmTipo"  data-role="none" name="nmArmTipo" onchange="fCambiaArmTipo(this)" value="<?php echo '6' ?>"> <?php llenaSelect("armTipo")  ?> </select><br>
         <label class="labelNV">L Mat: </label>
         <select style="width:50%; height:500px  !important;" class="selectFiltro"  id="lIdLMat"  data-role="none" name="nmLMat" onchange="fCambiaLMat(this)"> <?php llenaSelect("lMat")  ?></select><br>
         <label class="labelNV">L Tra: </label>
         <select style="width:50%; height:500px  !important;" class="selectFiltro"  id="lIdLTra"  data-role="none" name="nmLTra" onchange="fCambiaLTra(this)"> <?php llenaSelect("lTra")  ?></select><br>
         <label class="labelNV">L Vis: </label>
         <select style="width:50%; height:500px  !important;" class="selectFiltro"  id="lIdLVis"  data-role="none" name="nmLVis" onchange="fCambiaLVis(this)"> <?php llenaSelect("lVis")  ?></select><br>
         <label class="labelNV">L Ser: </label>
         <select style="width:50%; height:500px  !important;" class="selectFiltro"  id="lIdLSer"  data-role="none" name="nmLSer" onchange="fCambiaLSer(this)"> <?php llenaSelect("lSer")  ?></select><br>
         <label class="labelNV">Otro: </label>
         <select style="width:50%; height:500px  !important;" class="selectFiltro"  id="lIdOtro"  data-role="none" name="nmOtro" onchange="fCambiaOtro(this)"> <?php llenaSelect("otro")  ?></select><br>
      </div>
      <div class="clDinero">
         <label>Total: </label>
         <input type="number" id="idTotal" name="nmTotal" style="width:20%" pattern="[0-9]+" step="any" value="<?php echo $nTotal; ?>"/>
          <label>Saldo: </label>
         <input type="number" class="ROnly" id="idResta" name="nmResta" style="width:20%" value="<?php echo $nResta; ?>" readonly/><br>
         <label>A cuenta: </label>
         <input type="number" class="ROnly"  id="idAcuenta" name="nmAcuenta" style="width:20%" value="<?php echo $nAcuenta; ?>"readonly/>
         <select style="width:45% !important;" class="selectFiltro_"  id="lIdAbonos"  data-role="none" name="nmAbonos"> <?php llenaSelect("abonos")  ?></select><br>
         <label>Abono: </label>
         <input type="datetime-local" name="nmFAbono"  id="idFAbono" style="width:35%" value="<?php echo $sFAbono ?>" />
         <input type="number" id="idAbono" name="nmAbono" style="width:20%" step="any" value="<?php echo '0' ?>" /> <br>
        
         <label class="labelNV">Entrega: </label>
         <input type="date" id="idFEntrega" name="nmFEntrega" style="width:30%" value="<?php echo $sFEntrega; ?>"/><br>
         <label >Estatus: </label>
         <select style="width:60%; height:500px  !important;" class="selectFiltro"  id="lIdStatus"  data-role="none" name="nmStatus" onchange="fCambiaStatus(this)"> <?php llenaSelect("status")  ?></select><br>
         <label class="labelNV">Observ: </label>
         <textarea type="text" id="idObservaciones" name="nmObservaciones" style="width:70%;height: 40px" > <?php echo $sObservaciones; ?></textarea><br><br>

      </div>
   
   </div>
      <label type="labelError" id="elmError" ></label>
   </div>
   <div style="display: inline;" align="center">
      <div style="float: left;">
         <input type="submit" id="idBtnAbn"  value="Abn" />
      </div>
      <div style="float: left;">
         <input type="submit" id="idBtnOT"  value="OT" />
      </div>
      <div style="float: right;">
         <input type="submit" style="color: white; background-color: #4caf50; text-decoration: none; border-radius: 4px; display: inline-block; font-size: 100%;" id="submit" value="Guardar" name="submit" class="submit-guardar">
      </div>
   </div>
</form>
<script type="text/javascript">
var nIdxRx=0;
var nIdxNv=0;
var nIdxOt=0;
var nIdxInv=0;
var nPrArm=0;
var nPrMat=0;
var nPrTra=0;
var nPrVis=0;
var nPrSer=0;
var nPrOtr=0;
//alert("Inica script js");
function ready0(){
   //Inicializacion de componentes convierte select a select2

   document.getElementById("idForma").onsubmit = validaForma;
   
   /*
   var defaultMatcher = $.fn.select2.defaults.defaults.matcher;
   function customMatcher(params, data) {
     if (params.term && data.id == "ALL") 
      return null;
     return defaultMatcher(params, data);
   }
   */
   $.fn.select2.defaults.set('matcher', function(params, data) {
      if ($.trim(params.term) === '') {
         return data;
      }
      if (typeof data.text === 'undefined') {
         return null;
      }
      data.text=data.text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
      //console.log("valor["+data.text+"]");
      var words = params.term.toUpperCase().split(" ");
      for (var i = 0; i < words.length; i++) {
         //sTmp=data.text.toUpperCase().indexOf(words[i]).replace(/[\u0300-\u036f]/g, '');
         //if (data.text.toUpperCase().indexOf(words[i]) < 0) {
         if (data.text.toUpperCase().indexOf((words[i]).replace(/[\u0300-\u036f]/g, '')) < 0) {
            return null;
         }
      }
      return data;
   });
   $('.selectFiltro').select2({placeholder: 'Seleciona', allowClear: true, width:"resolve", allowClear: true,language: "es"});
   nTmp='<?php echo "$nIdRx" ?>';
   $("#lIdRx").val(nTmp).trigger('change');
   nTmp='<?php echo "$nIdInv" ?>';
   $("#lIdIdInv").val(nTmp).trigger('change');
   nTmp='<?php echo "$nArmTipo" ?>';
   $("#lIdArmTipo").val(nTmp).trigger('change');
   nTmp='<?php echo "$nArmMarca" ?>';
   $("#lIdArmMarca").val(nTmp).trigger('change');
   nTmp='<?php echo "$nLenMat" ?>';
   $("#lIdLMat").val(nTmp).trigger('change');
   nTmp='<?php echo "$nLenTra" ?>';
   $("#lIdLTra").val(nTmp).trigger('change');
   nTmp='<?php echo "$nLenVis" ?>';
   $("#lIdLVis").val(nTmp).trigger('change');
   nTmp='<?php echo "$nLenSer" ?>';
   $("#lIdLSer").val(nTmp).trigger('change');
   nTmp='<?php echo "$nOtro" ?>';
   $("#lIdOtro").val(nTmp).trigger('change');
   nTmp='<?php echo "$nStatus" ?>';
   $("#lIdStatus").val(nTmp).trigger('change');
   nID=Number($("#idNV").val());
   //alert("Parte de init0, es nota nueva, se asigna la fecha actual["+nID+"]");

   if (nID==0){
      var now = new Date();
      now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
      $("#idFecha").val(now.toISOString().slice(0,16));
      $("#idFAbono").val(now.toISOString().slice(0,16));
      ProxJueves='<?php echo date("Y-m-d",strtotime("next Thursday"))?>';
     $("#idFEntrega").val(ProxJueves);
     //$("#lIdLMat").val(3);
     $("#lIdLMat").select2("val", "2");
     $("#lIdLTra").select2("val", "2");
     $("#lIdLVis").select2("val", "1");
     $("#lIdLSer").select2("val", "1");
     //console.log("n. nota:["+"<?php echo $nIdNV ?>"+"]");
      $("#idSelNota").val(nID).trigger('change');
     } else{
      $("#idSelNota").val(nID).trigger('change');
         //alert("La nota no es nueva, valor de nota es:[]");
   }
   sFin="ot.php?idNV="+"<?php echo $nIdNV ?>";
   $("#idNV").val()
   //document.getElementById("idBtnOT").formAction = "ot.php?idNV="+"<?php echo $nIdNV ?>";
   $('#idTotal').keyup(function() {
      var nTot = Number(this.value) ;
      nAbn=Number($('#idAbono').val());
      nAcu=Number($('#idAcuenta').val());
      //console.log("Total:["+nTot+"], Abono:["+nAbn+"], Acuenta:["+nAcu+"]");
      nRest=nTot - (nAcu+nAbn);
      $('#idResta').val(nRest);
      //console.log("resta:["+nRest+"]");
   });

   $('#idAbono').keyup(function() {
      var nAbn = Number(this.value) ;
      nTot=Number($('#idTotal').val());
      nAcu=Number($('#idAcuenta').val());
      //console.log("Total:["+nTot+"], Abono:["+nAbn+"], Acuenta:["+nAcu+"]");
      nRest=nTot - (nAcu+nAbn);
      $('#idResta').val(nRest);
      //console.log("resta:["+nRest+"]");
   });

   $('#idResta').val(Number($('#idTotal').val())-Number($('#idAcuenta').val()));
 $("#idSelNota").val("<?php echo $nIdNV ?>").trigger('change');
//   alert("armtipo:["+nArmTipo+"]");

}; //Fin de ready0

function validaForma(laForma) {
   with(laForma) {
      // return false would prevent default submission
      estaOK=isNotEmpty(idNombre, "Nombre vacio!", elmError);
      
   }
    if (estaOK==true)
   {
      return (true);
   }
   else
   {
      return (false);
   }
}

function postValidate(isValid, errMsg, errElm, inputElm) {
   if (!isValid) {
      // Show errMsg on errElm, if provided.
      if (errElm !== undefined && errElm !== null
            && errMsg !== undefined && errMsg !== null) {
         errElm.innerHTML = errMsg;
      }
      // Set focus on Input Element for correcting error, if provided.
      if (inputElm !== undefined && inputElm !== null) {
         inputElm.classList.add("errorBox");  // Add class for styling
         inputElm.focus();
      }
   } else {
      // Clear previous error message on errElm, if provided.
      if (errElm !== undefined && errElm !== null) {
         errElm.innerHTML = "";
      }
      if (inputElm !== undefined && inputElm !== null) {
         inputElm.classList.remove("errorBox");
      }
   }
}

function isNotEmpty(inputElm, errMsg, errElm) {
   var isValid = (inputElm.value.trim() !== "");
   postValidate(isValid, errMsg, errElm, inputElm);
   
   return isValid;
}

function fCambiaRx(a){
   aTmp=$('#lIdRx').find(':selected').text().split(",");
   //alert("aTmp["+aTmp[0]+"]");
   if (Number(document.getElementById("idNV").value)==0 && Number(aTmp[0])>0){
      
      document.getElementById("idNombre").value= aTmp[1];
   }
}
function fCambiaNota(a){
   idNV=Number(a.value);
   //console.log("fCambiaNota(),cambio de nota =>["+idNV+"]");
   if (idNV>0){
      //sTmp=$("#idSelNota option:selected").text();
      //aTmp=sTmp.split(",");
      $.ajax({type: 'POST',url:'fAjax.php', dataType:'json',cache: false, async: false, data:{Key:"regNV", idNV:idNV},success: function(data) {
           ret0=data;
           },error: function (error) {
            alert("ERROR: ["+JSON.stringify(error)+"]");
        }
      });
      document.getElementById("idNombre").value=ret0[0].nombre;
      document.getElementById("idNV").value=ret0[0].idNV;
      document.getElementById("lIdRx").value=ret0[0].rx;
      document.getElementById("idFecha").value=ret0[0].fecha;
      document.getElementById("lIdIdInv").value=ret0[0].idInventario;
      document.getElementById("lIdArmMarca").value=ret0[0].armMarca;
      document.getElementById("lIdArmTipo").value=ret0[0].armTipo;
      document.getElementById("idADesc").value=ret0[0].armDesc;
      document.getElementById("lIdLMat").value=ret0[0].lenteMat;
      document.getElementById("lIdLTra").value=ret0[0].lenteTra;
      document.getElementById("lIdLVis").value=ret0[0].lenteVis;
      document.getElementById("lIdLSer").value=ret0[0].lenteSer;
      document.getElementById("lIdOtro").value=ret0[0].otro;
      document.getElementById("lIdStatus").value=ret0[0].estatus;
      document.getElementById("idObservaciones").value=ret0[0].observacionesNV;
      //console.log("valor del fiel total de la tabla antes del change:["+ret0[0].total+"]");

      $('#lIdRx').trigger("change");
      $('#lIdArmMarca').trigger("change");
      $('#lIdIdInv').trigger("change");
      $('#lIdArmTipo').trigger("change");
      $('#idADesc').trigger("change");
      $('#lIdLMat').trigger("change");
      $('#lIdLTra').trigger("change");
      $('#lIdLVis').trigger("change");
      $('#lIdLSer').trigger("change");
      $('#lIdOtro').trigger("change");
      $('#lIdStatus').trigger("change");

      //console.log("valor del field total de la tabla al final del change:["+ret0[0].total+"]");

      document.getElementById("idTotal").value=Number(ret0[0].total);

      document.getElementById("idBtnOT").formAction = "ot.php?idNV="+$("#idNV").val();
      document.getElementById("idBtnAbn").formAction = "abonosresumen.php";

      //console.log("ot.php?idNV="+idNV);
      

      $.ajax({type: 'POST',url:'fAjax.php', dataType:'json',cache: false, async: false, data:{Key:"regAbonos", idNV:idNV},success: function(dat) {
           data=dat;
           }, error: function (error) {
            alert("ERROR: ["+JSON.stringify(error)+"]");
        }
      });
      //console.log("JSON array: "+data[0]);
      $('#lIdAbonos').empty();
      var options = $('#lIdAbonos');
      nAporte=0
      $.each(data, function() {
         options.append($("<option />").val(this.abono).text(this.texto));
         nAporte+=Number(this.abono);
         //console.log("this.abono:["+this.abono+"],Numvber:["+Number(this.abono)+"]");
      });
      
      $('#lIdAbonos').trigger("change");
      document.getElementById("idAcuenta").value=nAporte;
      document.getElementById("idResta").value=document.getElementById("idTotal").value-nAporte;
      //console.log("valor de inv en reg:["+ret[0].idInventario+"],"+"valor de inv en input:["+document.getElementById("lIdIdInv").value+"]");
      //console.log("Valor de input despues de .change:["+document.getElementById("lIdIdInv").value+"]");
   } //Si el valor solicitado de notaV es >0
         

}//Fin CambiaNota

function fCambiaInventario(a){
   idInv=Number(a.value);
   //console.log("Entro a fCambiaInventario(),cambio de inventario =>["+idInv+"], el total es:["+document.getElementById("idTotal").value+"]");
   if (idInv>0){
      //sTmp=$("#idSelNota option:selected").text();
      //aTmp=sTmp.split(",");
      $.ajax({type: 'POST',url:'fAjax.php', dataType:'json',cache: false, async: false, data:{Key:"regInv", idInv:idInv},success: function(data) {
           ret=data;
           },error: function (error) {
            alert("ERROR: ["+JSON.stringify(error)+"]");
        }
      });
      //console.log("regreso de buscar un inventaqrio");
      //console.log(ret);
      document.getElementById("lIdArmMarca").value=ret[0].idArmMarca;
      document.getElementById("lIdArmTipo").value=ret[0].idArmTipo;
      document.getElementById("idADesc").value=ret[0].descripcion+", "+ret[0].modelo;
      $('#lIdArmMarca').trigger("change");
      $('#lIdArmTipo').trigger("change");

      
      //console.log("ot.php?idNV="+idNV);
   }


 }//Fin fCambiaBarras

function fCambiaStatus(){
   $a=1;
}
function precioTotal(){
   nPrTotal=nPrArm+nPrMat+nPrTra+nPrVis+nPrSer+nPrOtr;
   document.getElementById("idTotal").value= nPrTotal;
  // alert("tot:["+nPrTotal+"], nPrArm:["+nPrArm+"]");
}
function fCambiaArmTipo(a){
   aTmp=$('#lIdArmTipo').find(':selected').text().split("/");
   nPrArm=aTmp.length>1? Number(aTmp[1]):0;
   //alert("cambio atmp:["+aTmp[1]+"], tamaño del arreglo["+aTmp.length+"],nPrArm:["+nPrArm+"]");
   precioTotal();
}
function fCambiaLMat(a){
   aTmp=$('#lIdLMat').find(':selected').text().split("/");
   nPrMat=aTmp.length>1? Number(aTmp[1]):0;
   precioTotal();
}
function fCambiaLTra(a){
   aTmp=$('#lIdLTra').find(':selected').text().split("/");
   nPrTra=aTmp.length>1? Number(aTmp[1]):0;
   precioTotal();
}
function fCambiaLSer(a){
   aTmp=$('#lIdLSer').find(':selected').text().split("/");
   nPrSer=aTmp.length>1? Number(aTmp[1]):0;
   precioTotal();
}
function fCambiaLVis(a){
   aTmp=$('#lIdLVis').find(':selected').text().split("/");
   nPrVis=aTmp.length>1? Number(aTmp[1]):0;
   precioTotal();
}
function fCambiaOtro(a){
   aTmp=$('#lIdOtro').find(':selected').text().split("/");
   nPrOtr=aTmp.length>1? Number(aTmp[1]):0;
   precioTotal();
}

function fCambiaOt(a){
   sSeleccion=$('#lIdOt').find(':selected').val();
   document.getElementById("idIrRx").formAction = "ot.php?idrx="+sSeleccion;
}

function fCambiaArmMarca(){
   a=1;
}
function fCambiaFecha(a){
   console.log("la fecha es:["+a.value+"]");
   document.getElementById("idFAbono").value=a.value;
}
//window.onbeforeunload = function() { a=1 }

$(document).ready(ready0);
</script>
</body>
</html>