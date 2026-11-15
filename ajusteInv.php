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
	<title>Laboratorio central  --Ajuste Inventario</title>
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
<script type="text/javascript">enciendeMenu("encab5");</script>    
<h2>Ajuste de inventario</h2>

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

$sSelect = "SELECT marca.marca AS Marca, material.material AS Material, tratamiento.tratamiento AS Tratamiento, graduacion.idGrad AS idGrad, graduacion.esf AS Esfera, graduacion.cil AS Cilindro, graduacion.adi AS Adicion, vision.vision AS Vision,  SUM(invMicas.actual) AS Inventario FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento  ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision  GROUP BY idGrad, marca, tratamiento ORDER BY Cilindro, Esfera, ".$field." ".$ordertype;
//$result = $conn->query($sSelect);

$lstCampos=array("Marca","Material", "Tratamiento","Esfera","Cilindro","Adicion","Vision","Inventario");
$lstEncabezados=arraY("Marca","Material","Tratamiento","Esf","Cil","Add","Vision","Inv");
echo "<DIV id='divTabla'>";
tablaAjuste($sSelect,"ajusteInv.php", "graduacion", $ordertype, $sort_arrow, $lstCampos, $lstEncabezados,$field);
echo "</DIV>";

//Querys para obener los valores de los selectores
?>
<br>

<button id="idBtnCargarAjustes" onclick="fLeeAjustes()">Carga ajustes</button>
<div>
   <p id="idInfo" class="clInfo"></p>
</div>
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

//Colorea renglones por marca de micas
const colores = {
    "Deesik": "EA3481",
    "Optiview": "#D9B08C",
    "Seto": "#DCD0D1",
    "PoloStar": "#FFCB9A",
    "Maximo": "#AFBB9A",
    "Julios": "#DFAB3A",    
    "VisionPlus": "#FAA122"    
};
$("table tbody tr").each(function() {
    const marca = $(this).find("td:eq(0)").text().trim();
    if (colores[marca]) {
        $(this).css("background-color", colores[marca]);
    }
});

document.addEventListener("DOMContentLoaded", () => {
  // Agrega clase de desvanecimiento antes de recargar
  document.body.classList.add("fade-out");
  console.log("Antes del setTimeout");
  setTimeout(() => { location.reload();}, 500); // Espera a que termine la animación
  console.log("En espera de que pasen 500 ms");
});



}; //Fin de ready0

/*
function fLeeAjustes(){
    nCuenta=0;
    const tabla = document.getElementById("idTabla");
  const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
  const datos = [];
  for (let i = 0; i < filas.length; i++) {
    const celdas = filas[i].getElementsByTagName("td");
    nTmp=celdas[8].querySelector("input").value;
    nAjuste=(nTmp==='')?0:parseInt(nTmp);
    console.log("valor de ajuste:["+nAjuste+"]");
    if (nAjuste!=0){
        nCuenta++;
        sMarca=celdas[0].textContent;
        sMaterial=celdas[1].textContent;
        sTratamiento=celdas[2].textContent;
        nEsf=Number(celdas[3].textContent);
        nCil=Number(celdas[4].textContent);
        nAdi=Number(celdas[5].textContent);
        sVision=celdas[6].textContent;
        //console.log("Marca:["+sMarca+"], Material:["+sMaterial+"], Tratamiento:["+sTratamiento+"], Esf:["+nCil+"], Cil:["+nCil+"], Adi:["+nAdi+"], Vision:["+sVision+"], Ajuste:["+nAjuste+"]");
        $.ajax({type: 'POST',url:'fAjax.php', dataType:'json',cache: false, async: false, data:{Key:"ajustaInventario", sMarca:sMarca, sMaterial:sMaterial, sTratamiento:sTratamiento, nEsf:nEsf, nCil:nCil,nAdi:nAdi, sVision:sVision, nActual:nAjuste},success: function(data) {
            nActualizado=data;
        },error: function (error) {
            alert("ERROR: ["+JSON.stringify(error)+"]");
         }
        });
        }
    }
    document.getElementById("idInfo").innerText="Registro actualizados"; 
    if (nCuenta>0) {
       document.getElementById("idBtnCargarAjustes").disabled=true;
       alert("Se actualizaron ["+nCuenta+"] registros, ahora se reflejaran los cambios..."); 
       location.reload();
    }

}
*/
function fLeeAjustes(){
    nCuenta = 0;
    nErrores = 0;
    const tabla = document.getElementById("idTabla");
    const filas = tabla.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
    const datos = [];
    
    // Validar que haya ajustes para procesar
    let tieneAjustes = false;
    for (let i = 0; i < filas.length; i++) {
        const celdas = filas[i].getElementsByTagName("td");
        nTmp = celdas[8].querySelector("input").value;
        nAjuste = (nTmp === '') ? 0 : parseInt(nTmp);
        if (nAjuste !== 0) {
            tieneAjustes = true;
            break;
        }
    }
    
    if (!tieneAjustes) {
        alert("No hay ajustes para procesar");
        return;
    }
    
    if (!confirm("¿Está seguro de aplicar los ajustes de inventario? Esta acción no se puede deshacer.")) {
        return;
    }
    
    for (let i = 0; i < filas.length; i++) {
        const celdas = filas[i].getElementsByTagName("td");
        nTmp = celdas[8].querySelector("input").value;
        nAjuste = (nTmp === '') ? 0 : parseInt(nTmp);
        
        if (nAjuste !== 0) {
            nCuenta++;
            sMarca = celdas[0].textContent.trim();
            sMaterial = celdas[1].textContent.trim();
            sTratamiento = celdas[2].textContent.trim();
            nEsf = Number(celdas[3].textContent);
            nCil = Number(celdas[4].textContent);
            nAdi = Number(celdas[5].textContent);
            sVision = celdas[6].textContent.trim();
            
            // Validaciones básicas
            if (isNaN(nAjuste) || isNaN(nEsf) || isNaN(nCil) || isNaN(nAdi)) {
                console.error("Datos numéricos inválidos en fila " + (i+1));
                nErrores++;
                continue;
            }
            
            try {
                $.ajax({
                    type: 'POST',
                    url: 'fAjax.php',
                    dataType: 'json',
                    cache: false,
                    async: false,
                    data: {
                        Key: "ajustaInventario",
                        sMarca: sMarca,
                        sMaterial: sMaterial,
                        sTratamiento: sTratamiento,
                        nEsf: nEsf,
                        nCil: nCil,
                        nAdi: nAdi,
                        sVision: sVision,
                        nActual: nAjuste
                    },
                    success: function(data) {
                        if (data.success) {
                            console.log("Registro actualizado: " + data.message);
                        } else {
                            console.error("Error en fila " + (i+1) + ": " + data.error);
                            nErrores++;
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error AJAX en fila " + (i+1) + ": " + error);
                        nErrores++;
                    }
                });
            } catch (e) {
                console.error("Excepción en fila " + (i+1) + ": " + e.message);
                nErrores++;
            }
        }
    }
    
    let mensaje = "";
    if (nErrores === 0 && nCuenta > 0) {
        mensaje = `Se actualizaron ${nCuenta} registros correctamente. La página se recargará...`;
        document.getElementById("idInfo").innerText = mensaje;
        document.getElementById("idBtnCargarAjustes").disabled = true;
        alert(mensaje);
        setTimeout(() => { location.reload(); }, 1000);
    } else if (nErrores > 0) {
        mensaje = `Se procesaron ${nCuenta} registros con ${nErrores} errores. Revise la consola para más detalles.`;
        document.getElementById("idInfo").innerText = mensaje;
        alert(mensaje);
    }
}

$(document).ready(ready0);
</script>    
</html>
