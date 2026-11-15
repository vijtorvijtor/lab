<?php
@session_start ();
global $conn;
$_SESSION['dirBase']="/lab/";
$_SESSION['dBHost']="localhost";
$_SESSION['dBName']="u947744031_lab";
$_SESSION['dBUser']="u947744031_lab";
$_SESSION['sBPass']="1Abejash(*@";
$conn = mysqli_connect($_SESSION['dBHost'], $_SESSION['dBUser'], $_SESSION['sBPass'],$_SESSION['dBName']);
$conn->set_charset("utf8");
$_SESSION['conn']=$conn;

ini_set('date.timezone',"Etc/GMT+6");
date_default_timezone_set('Etc/GMT+6');
setlocale(LC_ALL, 'es_MX.UTF-8');

function encabezado(){
echo "<title>Laboratorio Óptico Central</title>";
echo "<p id='lineaSuperior' style='font-family:SF-Pro; text-align:left;'>";
echo " <a href='index.php'><b>Inicio </b></a>";
echo "<span style='float:right;'> UN:[".$_SESSION['idUN']."] Usuario: [".$_SESSION['user']."] <a href='logout.php'><b> &nbsp;Cerrar sesión [X]</b></a>";
echo "</span> <br></p> ";
echo "<div id='divMenu'> ";
   echo '<table id="idTablaMenu" >';
   echo '<tr>';
   echo '<th > <a id="encab1" class="textoMenu" href="index.php">Nota</a> </th>';
   echo '<th > <a id="encab3" class="textoMenu" href="inventario.php">Inventario</a>  </th>';
   echo '<th > <a id="encab4" class="textoMenu" href="existencias.php">Existencias</a></th>';
   echo '<th > <a id="encab5" class="textoMenu" href="ajusteInv.php">Ajuste Inv</a></th>';

   //echo '<th > <a id="encab5" class="textoMenu" href="inventario.php">Inv</a></th>';
   echo '</tr></table>';
echo "</div>";
echo"<p id='idLogo' style='text-align:center;margin-bottom: 0px;margin-top:0px '><img style='text-align:center;margin-top: 0px;margin-top:0px ' src='img/LogoLAB2.png' width='10%'></p>";

} // Cierra function encabezado

function encabezadoInv(){
echo "<title>Ópt ECO</title>";
echo "<p id='lineaSuperior' style='font-family:SF-Pro; text-align:left;'>";
echo " <a href='index.php'><b>Inicio </b></a>";
echo "<span style='float:right;'> UN:[".$_SESSION['idUN']."], Usuario: [".$_SESSION['user']."] <a href='logout.php'><b> &nbsp;Cerrar sesión [X]</b></a>";
echo "</span> <br></p> ";
echo "<div id='divMenu'> ";
   echo '<table id="idTablaMenu" >';
   echo '<tr>';
   echo '<th > <a id="encab1" class="textoMenu" href="invarmazones.php"> Inventario</a> </th>';
   echo '<th > <a id="encab2" class="textoMenu" href="conteo.php">Conteo</a>  </th>';
   echo '<th > <a id="encab3" class="textoMenu" href="catalogosInventario.php">Catalogos</a> </th>';
   echo '<th > <a id="encab4" class="textoMenu" href="logout.php">Salir</a></th>';
   echo '</tr></table>';
echo "</div>";
echo"<p id='idLogo' style='text-align:center;margin-bottom: 0px;margin-top:0px '><img style='text-align:center;margin-top: 0px;margin-top:0px ' src='img/LogoOE.png' width='10%'></p>";

} // Cierra function encabezadoInv



function tabla0($query, $arcPhp, $tbl, $orden, $flecha, $lstCampos, $lstEncabezados,$fOrdenado){
$conn=$_SESSION['conn'];
//echo "qry:[".$query."]";
echo "<input id='filtro' type='text' placeholder='Filtro'> <input id='btnFiltro' type='button' value='Filtar' onclick='filtraGrad()'>";
echo "<div id='idEspacioTabla'>";
$head1='<th id="tEncabezado" style="text-align:left"><a href="'.$arcPhp.'?sort=';
$result =mysqli_query($conn, $query);
if(mysqli_num_rows($result) > 0){
    $i=0;
    echo '<table id="idTabla" class="tablaCatalogo table-striped" width="95%"">';
    echo '<thead class="clTheadGrad">';
    echo '<tr id="renglon" >';
    foreach ($lstCampos as $campo){
        $head2=$campo.'&order='.$orden.'">'.$lstEncabezados[$i];
        echo $head1.$head2;
        if($fOrdenado == $campo) { echo $flecha; }
        echo '</a></th>';
        $i++;
    }
     echo '</thead>';
    //echo '</tr>';
      while($row = mysqli_fetch_assoc($result)){
          echo '<tr>';
          foreach($lstCampos as $campo){
          echo '<td style="text-align:left">'.str_replace("\n","\\n",$row[$campo]).'</td>';
          }
       echo '</tr>';
      }
    echo '</table>';
    echo '</div>';
}else {
    $a=1;
    //echo "NO hay registros: ".$sSelect. ", [".mysqli_num_rows($result)."]";
    echo "NO hay registros [".$query."]";
}
}

function tablaAjuste($query, $arcPhp, $tbl, $orden, $flecha, $lstCampos, $lstEncabezados,$fOrdenado){
$conn=$_SESSION['conn'];
//echo "qry:[".$query."]";
echo "<div class='fijo' id='divEspaciotabla'><input id='filtro' type='text' placeholder='Filtro'> <input id='btnFiltro' type='button' value='Filtar' onclick='filtraGrad()'></div>";
echo "<div class='fijo' id='idEspacioTabla'>";
$head1='<th id="tEncabezado" style="text-align:left"><a href="'.$arcPhp.'?sort=';
$result =mysqli_query($conn, $query);
if(mysqli_num_rows($result) > 0){
    $i=0;
    echo '<table id="idTabla" class="tablaCatalogo clTbklInventario  table-striped" width="95%"">';
    echo '<thead class="clTheadGrad">';
    echo '<tr id="renglon" >';
    foreach ($lstCampos as $campo){
        $head2=$campo.'&order='.$orden.'">'.$lstEncabezados[$i];
        echo $head1.$head2;
        if($fOrdenado == $campo) { echo $flecha; }
        echo '</a></th id="tEncabezado" >';
        $i++;
    }

     echo '<th>Ajuste</th></thead>';
    //echo '</tr>';
      while($row = mysqli_fetch_assoc($result)){
          echo '<tr>';
          foreach($lstCampos as $campo){
            echo '<td style="text-align:left">'.str_replace("\n","\\n",$row[$campo]).'</td>';
          }
          echo '<td style="text-align:left"><input type="number" class="clAjuste" name="nmAjuste" min="-99" max="99" style="width: 90%;" placeholder="0" ></td>';

       echo '</tr>';
      }
    echo '</table>';
    echo '</div></div>';
}else {
    $a=1;
    //echo "NO hay registros: ".$sSelect. ", [".mysqli_num_rows($result)."]";
    echo "NO hay registros [".$query."]";
}
}


?>
