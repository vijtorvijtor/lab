<?php 
session_start ();
include("config.php");
include("sesionCheck.php");
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
  	<link href="jquery/jquery-ui.css" rel="stylesheet" >
  	<link href="jquery/select2.min.css" rel="stylesheet" >
	<script src="jquery/select2.min.js"></script>
    <script type="text/javascript" src="js/utils.js?v=<?= filemtime('js/utils.js') ?>"></script>
	<title>Laboratorio central  --Catálogo de Sets</title>
</head>
<style>
	.select2-container .select2-choice {
    height: 200px!important;
    line-height: 200px!important;
    max-height: 200px;
    min-height: 200px !important;
}
</style>	

<body>
<script type="text/javascript">enciendeMenu("encab4");</script>    
<h2>Existencias</h2>

<?php
///Ejecuta accion de boton nuevo,editar,eliminar


if(isset($_GET['sort'])){
    $field =$_GET['sort'];
    $ordertype = ($_GET['order'] == 'asc')? 'desc' : 'asc';
    $sort_arrow =($ordertype =='asc')?'<img src="img/sort-des.png"/>':'<img src="img/sort-asc.png"/>';
}else{
    $field = ' Esf ';
    $sort_arrow =  '<img src="img/sort-des.png" />';
    $ordertype = ' desc ';
}

$sSelect = "SELECT marca.marca AS Marca, material.material AS Material, tratamiento.tratamiento AS Tratamiento, graduacion.idGrad AS idGrad, graduacion.esf AS Esfera, graduacion.cil AS Cilindro, graduacion.adi AS Adicion, vision.vision AS Vision, SUM(invMicas.actual) AS Inventario, invMicas.tope AS invTope FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento  ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision  GROUP BY idGrad, marca, tratamiento ORDER BY Cilindro, Esfera, ".$field." ".$ordertype;
//$result = $conn->query($sSelect);

$lstCampos=array("Marca","Material", "Tratamiento","Esfera","Cilindro","Adicion","Vision","Inventario","invTope");
$lstEncabezados=arraY("Marca","Material","Tratamiento","Esf","Cil","Add","Vision","Inv","Tope");
echo "<DIV id='divTabla'>";
tabla0($sSelect,"existencias.php", "graduacion", $ordertype, $sort_arrow, $lstCampos, $lstEncabezados,$field);
echo "</DIV>";

//Querys para obener los valores de los selectores
?>
<br>

</body>

<script type="text/javascript">
function ready0(){
propSelect2();

//Llena de valores inputs de acuerdo al renglon seleccionado


for (const td of document.querySelectorAll("tr>td:nth-child(4)")) {
    if (Number(td.textContent)>=0) td.textContent = "+"+Number(td.textContent).toFixed(2);
}
for (const td of document.querySelectorAll("tr>td:nth-child(5)")) {
    if (Number(td.textContent)==0) td.textContent = "-"+Number(td.textContent).toFixed(2);
}
}; //Fin de ready0



$(document).ready(ready0);
</script>    
</html>
