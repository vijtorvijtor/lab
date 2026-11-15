<?php
session_start ();
$dias = array("Dom","Lun","Mar","Mie","Jue","Vie","Sab");
date_default_timezone_set('America/Mexico_City');
setlocale(LC_ALL, 'es_MX.UTF-8');
setlocale(LC_TIME, "es_MX.UTF-8","esp");

$Key=$_POST['Key'];
$conn = mysqli_connect($_SESSION['dBHost'], $_SESSION['dBUser'], $_SESSION['sBPass'],$_SESSION['dBName']);
$conn->set_charset("utf8");

// FUNCIÓN AUXILIAR PARA PROCESAR INVMICAS
function procesarInvMicas($conn, $partida, $nNota, $is_existing, $partida_id) {
    $aCodigo = explode(",", $partida['codigo']);
    
    if (count($aCodigo) >= 11) {
        $nGrad = trim($aCodigo[10]);
        $nMarca = trim($aCodigo[5]);
        $nMaterial = trim($aCodigo[6]);
        $nTratamiento = trim($aCodigo[7]);
        $nVision = trim($aCodigo[8]);
        $nActual = ($partida['cantidad'] * -1);
        $sFecha = date('Y-m-d H:i:s');

        if ($is_existing) {
            // UPDATE de registro existente en invMicas
            $query_inv = "UPDATE invMicas SET 
                fecha = ?, actual = ? 
                WHERE idNota = ? AND marca = ? AND material = ? AND tratamiento = ? AND graduacion = ? AND vision = ?";
            
            $stmt_inv = mysqli_prepare($conn, $query_inv);
            mysqli_stmt_bind_param($stmt_inv, "siiiiisi", 
                $sFecha, 
                $nActual,
                $nNota,
                $nMarca,
                $nMaterial,
                $nTratamiento,
                $nGrad,
                $nVision
            );
        } else {
            // INSERT de nuevo registro en invMicas
            $query_inv = "INSERT INTO invMicas (fecha, marca, material, tratamiento, graduacion, vision, actual, idNota) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt_inv = mysqli_prepare($conn, $query_inv);
            mysqli_stmt_bind_param($stmt_inv, "siiiisdi", 
                $sFecha, 
                $nMarca, 
                $nMaterial, 
                $nTratamiento, 
                $nGrad, 
                $nVision, 
                $nActual, 
                $nNota
            );
        }
        
        if (!mysqli_stmt_execute($stmt_inv)) {
            throw new Exception("Error al procesar invMicas: " . mysqli_error($conn));
        }
        mysqli_stmt_close($stmt_inv);
    }
}


switch ($Key) {

case 'nuevaNota'://arreglo con tipos de armazones
    mysqli_autocommit($conn, false);
    try {
        $nNota = $_POST['idNota'];
        $nCliente = $_POST['idCliente'];
        $nTotal = $_POST['nTotal'];
        $sComentarios = mysqli_real_escape_string($conn, $_POST['sComentarios']);
        $sFecha = date('Y-m-d H:i:s');
        $nUser = $_SESSION['idUN'];
        
        //Se busca no. de optica de acuerdo al idCliente
        $query = "SELECT optica FROM cliente WHERE idCliente = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $nCliente);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Error al buscar óptica: " . mysqli_error($conn));
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $row2 = mysqli_fetch_array($result);
        $nOptica = $row2['optica'];
        mysqli_stmt_close($stmt);

        if ($nNota == 0) {
            //Se inserta el registro como nueva nota
            $query = "INSERT INTO nota (fecha, optica, cliente, estado, comentarios, user, total) VALUES (?, ?, ?, 1, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "siisid", $sFecha, $nOptica, $nCliente, $sComentarios, $nUser, $nTotal);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al insertar nota: " . mysqli_error($conn));
            }
            
            $nNota = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
        } else {
            //Elimina la nota y sus partidas asi como las entradas del inventario para crearlas nuevamente
            $query = "DELETE FROM partida WHERE nota = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $nNota);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al eliminar partidas: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt);

            $query = "DELETE FROM invMicas WHERE idNota = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $nNota);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error al eliminar invMicas: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt);
        }
        
        mysqli_commit($conn);
        echo json_encode($nNota);
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Error en nuevaNota: " . $e->getMessage());
        echo json_encode(0);
    } finally {
        mysqli_autocommit($conn, true);
    }
    break;

case 'nuevaPartida'://arreglo con tipos de armazones
    $nNota=$_POST['idNota'];
    $nCantidad=$_POST['nCantidad'];
    $nProducto=$_POST['nProducto'];
    $sDescripcion="'".$_POST['sDescripcion']."'";
    $nUnitario=$_POST['nUnitario'];
    $nDescuento=$_POST['nDescuento'];
    $nMonto=$_POST['nTotal'];
    $sFecha="'".date('Y-m-d H:i:s')."'";
    $sCodigo=$_POST['sCodigo'];
    //Se inserta el registro como nueva partida
    if ($nProducto==1){ //Si es mica descuenta del inventario
        $aCodigo=explode(",", $sCodigo);
        $nGrad=substr($aCodigo[10],0,-1);
        $nMarca=$aCodigo[5];
        $nMaterial=$aCodigo[6];
        $nTratamiento=$aCodigo[7];
        $nVision=$aCodigo[8];
        $nActual=$aCodigo[1]*(-1);
        //echo "aCodigo(9):[".$aCodigo[9]."], codigo:[".$sCodigo."], codGraduacion:[".$nGrad."]";
        $query="INSERT INTO invMicas (fecha, marca, material, tratamiento, graduacion, vision, actual, idNota) VALUES($sFecha, $nMarca, $nMaterial, $nTratamiento, $nGrad, $nVision, $nActual, $nNota)";
        //echo $query;
        $result = mysqli_query($conn, $query) or die(mysql_error());

    }
    $query="INSERT INTO partida (nota,cantidad,producto,descripcion,unitario,descuento,monto,codigo) VALUES ($nNota,$nCantidad,$nProducto,$sDescripcion,$nUnitario,$nDescuento,$nMonto,$sCodigo)";
    //echo $query;
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $lastId=mysqli_insert_id($conn);  
    //echo $query;
    echo json_encode($lastId);
break;

case 'guardarPartidasCompletas':
    mysqli_autocommit($conn, false);
    $error = false;
    $resultados = [];

    try {
        $nNota = $_POST['idNota'];
        $partidas = json_decode($_POST['partidas'], true);

        // 1. PRIMERO: Obtener IDs de partidas existentes para esta nota
        $existing_partida_ids = [];
        $query_existing = "SELECT idPartida FROM partida WHERE nota = ?";
        $stmt_existing = mysqli_prepare($conn, $query_existing);
        
        if ($stmt_existing) {
            mysqli_stmt_bind_param($stmt_existing, "i", $nNota);
            mysqli_stmt_execute($stmt_existing);
            $result = mysqli_stmt_get_result($stmt_existing);
            while ($row = mysqli_fetch_assoc($result)) {
                $existing_partida_ids[] = $row['idPartida'];
            }
            mysqli_stmt_close($stmt_existing);
        }

        // 2. SEGUNDO: Procesar cada partida con UPSERT
        if (!empty($partidas)) {
            $used_ids = [];
            
            foreach ($partidas as $index => $partida) {
                // Determinar si es una partida existente o nueva
                $is_existing = isset($existing_partida_ids[$index]);
                $partida_id = $is_existing ? $existing_partida_ids[$index] : null;
                
                if ($is_existing && $partida_id) {
                    // UPDATE de partida existente
                    $query_partida = "UPDATE partida SET 
                        cantidad = ?, producto = ?, descripcion = ?, 
                        unitario = ?, descuento = ?, monto = ?, codigo = ? 
                        WHERE idPartida = ? AND nota = ?";
                    
                    $stmt_partida = mysqli_prepare($conn, $query_partida);
                    mysqli_stmt_bind_param($stmt_partida, "iisdidsii", 
                        $partida['cantidad'],
                        $partida['producto'], 
                        $partida['descripcion'],
                        $partida['unitario'],
                        $partida['descuento'],
                        $partida['total'],
                        $partida['codigo'],
                        $partida_id,
                        $nNota
                    );
                    
                    if (!mysqli_stmt_execute($stmt_partida)) {
                        throw new Exception("Error al actualizar partida: " . mysqli_error($conn));
                    }
                    
                    $resultados[] = $partida_id;
                    $used_ids[] = $partida_id;
                    
                } else {
                    // INSERT de nueva partida
                    $query_partida = "INSERT INTO partida (nota, cantidad, producto, descripcion, unitario, descuento, monto, codigo) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt_partida = mysqli_prepare($conn, $query_partida);
                    mysqli_stmt_bind_param($stmt_partida, "iiisdids", 
                        $nNota,
                        $partida['cantidad'],
                        $partida['producto'],
                        $partida['descripcion'],
                        $partida['unitario'],
                        $partida['descuento'],
                        $partida['total'],
                        $partida['codigo']
                    );
                    
                    if (!mysqli_stmt_execute($stmt_partida)) {
                        throw new Exception("Error al insertar partida: " . mysqli_error($conn));
                    }
                    
                    $lastId = mysqli_insert_id($conn);
                    $resultados[] = $lastId;
                    $used_ids[] = $lastId;
                }
                
                mysqli_stmt_close($stmt_partida);

                // 3. Procesar invMicas para producto tipo 1 - CORRECCIÓN AQUÍ
                if ($partida['producto'] == 1) {
                    procesarInvMicas($conn, $partida, $nNota, $is_existing, $partida_id); // SIN $this->
                }
            }

            // 4. Eliminar partidas que ya no están en la nueva lista
            if (!empty($existing_partida_ids)) {
                $ids_to_delete = array_diff($existing_partida_ids, $used_ids);
                if (!empty($ids_to_delete)) {
                    $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
                    $query_delete = "DELETE FROM partida WHERE idPartida IN ($placeholders) AND nota = ?";
                    
                    $stmt_delete = mysqli_prepare($conn, $query_delete);
                    if ($stmt_delete) {
                        // Bind parameters: primero los IDs, luego la nota
                        $types = str_repeat('i', count($ids_to_delete)) . 'i';
                        $params = array_merge($ids_to_delete, [$nNota]);
                        mysqli_stmt_bind_param($stmt_delete, $types, ...$params);
                        
                        if (!mysqli_stmt_execute($stmt_delete)) {
                            throw new Exception("Error al eliminar partidas obsoletas: " . mysqli_error($conn));
                        }
                        mysqli_stmt_close($stmt_delete);
                    }
                }
            }
        } else {
            // Si no hay partidas, eliminar todas las existentes
            $query_delete_all = "DELETE FROM partida WHERE nota = ?";
            $stmt_delete_all = mysqli_prepare($conn, $query_delete_all);
            if ($stmt_delete_all) {
                mysqli_stmt_bind_param($stmt_delete_all, "i", $nNota);
                mysqli_stmt_execute($stmt_delete_all);
                mysqli_stmt_close($stmt_delete_all);
            }
        }

        mysqli_commit($conn);
        echo json_encode(['success' => true, 'ids' => $resultados]);

    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log("Error en guardarPartidasCompletas: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
    } finally {
        mysqli_autocommit($conn, true);
    }
    break;

case 'notas':
    $sQry="SELECT nota.optica AS idOptica, cliente.nombre AS Nombre, cliente.apellido AS Apellido, optica.nombre as Optica, nota.idNota as idNota, nota.fecha AS fechaNota FROM nota INNER JOIN optica ON nota.optica=optica.idOptica INNER JOIN cliente ON nota.cliente=cliente.idCliente ORDER BY idNota DESC";
    $aryNota=mysqli_query($conn, $sQry); 
    $aTmp=array();
    while ($row=$aryNota->fetch_assoc()){
            $time = strtotime($row['fechaNota']);
            $fch =strftime("%a %d/%b/%Y %T", $time);
            $sText=sprintf("%06d",$row["idNota"]).", ".$row["Nombre"]." ".$row["Apellido"].", ".$fch;
           // echo "<option value='".$row["idNota"]."'>".$sText."</option> ";
            $aTmp[]=array("val"=>$row["idNota"],"OptText"=>$sText);
        }
    echo json_encode($aTmp);
break;

case 'getPartidas':
    $idNota=$_POST['idNota'];
    $query="SELECT partida.*, nota.cliente FROM partida INNER JOIN nota ON partida.nota=nota.idNota WHERE partida.nota=".$idNota." ORDER BY idPartida";  
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("idPartida"=>$row2['idPartida'], "nota"=>$row2['nota'],"cantidad"=>$row2['cantidad'],"producto"=>$row2['producto'],"descripcion"=>$row2['descripcion'],"unitario"=>$row2['unitario'],"descuento"=>$row2['descuento'],"total"=>$row2['monto'],"codigo"=>$row2['codigo'],"cliente"=>$row2['cliente']);
    }
    echo json_encode($aTmp);    
break;

case 'getPrecio'://arreglo con tipos de armazones
    $nIdProducto=$_POST['idProducto'];
    $query="SELECT precio FROM producto WHERE idProducto=".$nIdProducto;
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $row2 = mysqli_fetch_array($result);
    $nPrecio=$row2['precio'];
    echo json_encode($nPrecio);
break;


case 'nuevoAbono':
    $idNota=$_POST['idNota'];
    $nAbono=$_POST['nAbono'];
    $sFecha="'".date('Y-m-d H:i:s')."'";
    $query="INSERT INTO abono (nota, fecha, abono) VALUES($idNota, $sFecha, $nAbono) ";
    $result = mysqli_query($conn, $query) or die(mysql_error());
    echo json_encode($query);
break;

case 'getAbonos':
    $idNota=$_POST['idNota'];
    $query="SELECT abono, fecha  FROM abono WHERE nota=".$idNota;
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("fecha"=>$row2['fecha'], "abono"=>$row2['abono']);
    }
    echo json_encode($aTmp);
break;

case 'material'://arreglo con tipos de materiales
  
    $query="SELECT * FROM catLMaterial ORDER BY matPrecio";  
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("id"=>$row2['idLMaterial'], "nbe"=>$row2['matNombre'],"precio"=>number_format($row2['matPrecio']),"costo"=>$row2['matCosto']);
    }
    echo json_encode($aTmp);
break;

case 'tratamiento'://arreglo con tipos de tratamientos
    $query="SELECT * FROM catLTratamiento ORDER BY traPrecio";  
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("id"=>$row2['idLTratamiento'], "nbe"=>$row2['traNombre'],"precio"=>number_format($row2['traPrecio']),"costo"=>$row2['traCosto']);
    }
    echo json_encode($aTmp);
break;

case 'vision'://arreglo con tipos de tratamientos
    $query="SELECT * FROM catLVision ORDER BY visPrecio";  
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("id"=>$row2['idLVision'], "nbe"=>$row2['visNombre'],"precio"=>number_format($row2['visPrecio']),"costo"=>$row2['visCosto']);
    }
    echo json_encode($aTmp);
break;

case 'serie'://arreglo con tipos de tratamientos
    $query="SELECT * FROM catLSerie ORDER BY serPrecio";  
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("id"=>$row2['idLSerie'], "nbe"=>$row2['serNombre'],"precio"=>number_format($row2['serPrecio']),"costo"=>$row2['serCosto']);
    }
    echo json_encode($aTmp);
break;

case 'otro'://arreglo con tipos de tratamientos
    $query="SELECT * FROM otros ORDER BY nombre";  
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("id"=>$row2['idOtros'], "nbe"=>$row2['nombre'],"precio"=>number_format($row2['precio']),"costo"=>$row2['costo']);
    }
    echo json_encode($aTmp);
break;

case 'filtraRadios':
    $query=$_POST['sQry'];
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("Nombre"=>$row2['Nombre']);
    }
    echo json_encode($aTmp);    
break;

case 'regInv':
    $idInv=$_POST['idInv'];
    $query="SELECT * FROM inventario WHERE idInv=".$idInv. " ORDER BY idInv "; 
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        #$time = strtotime($row2["fechaAbono"]);
        #$fcha = strftime("%a %d/%b/%Y",$time);
        #$tx="$".$row2['abono'].", ".$fcha;
        $aTmp[] = array("idInv"=>$row2['idInv'],"idArmMarca"=>$row2['idArmMarca'],"idArmTipo"=>$row2['idArmTipo'],"idGenero"=>$row2['idGenero'],"idAlmacen"=>$row2['idAlmacen'],"modelo"=>$row2['modelo'],"descripcion"=>$row2['descripcion'],"fechaEntrada"=>$row2['fechaEntrada'],"fechaBaja"=>$row2['fechaBaja'],"costo"=>$row2['costo'],"costoDescuento"=>$row2['costoDescuento']);
    }

    //echo json_encode($query);
    echo json_encode($aTmp);
break;

case 'regConteo':
    $idConteo=$_POST['idConteo'];
    $query="SELECT * FROM conteo WHERE idConteo=".$idConteo. " ORDER BY idConteo "; 
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("idConteo"=>$row2['idConteo'],"fecha"=>$row2['fecha'],"carga"=>$row2['carga'],"faltantes"=>$row2['faltantes'],"noEscaneados"=>$row2['noEscaneados']);
    }

    //echo json_encode($query);
    echo json_encode($aTmp);
break;

case 'invGraduaciones':
    $nMarca=$_POST['nMarca'];
    $nMaterial=$_POST['nMaterial'];
    $nTratamiento=$_POST['nTratamiento'];
    $nVision=$_POST['nVision'];
    
    $tmpWhere="";
    //WHERE invMicas.marca=3 AND invMicas.material=1 AND invMicas.tratamiento=2;
    $tmpWhere.=($nMarca>0)?" invMicas.marca=".$nMarca :" invMicas.marca>0 ";
    $tmpWhere.=($nMaterial>0)?" AND invMicas.material=".$nMaterial :" AND invMicas.material>0 ";
    $tmpWhere.=($nTratamiento>0)?" AND invMicas.tratamiento=".$nTratamiento :" AND invMicas.tratamiento>0";
    $tmpWhere.=($nVision>0)?" AND invMicas.vision=".$nVision :" AND invMicas.vision>0";
    $query="SELECT marca.marca AS Marca, material.material AS Material, tratamiento.tratamiento AS Tratamiento, graduacion.idGrad AS idGrad, graduacion.esf AS Esfera, graduacion.cil AS Cilindro, graduacion.adi AS Adicion, vision.vision AS Vision, SUM(invMicas.actual) AS Inventario, invMicas.tope AS invTope FROM invMicas INNER JOIN marca ON marca.idMarca=invMicas.marca INNER JOIN material ON material.idMaterial=invMicas.Material INNER JOIN tratamiento  ON tratamiento.idTratamiento=invMicas.tratamiento INNER JOIN graduacion ON invMicas.graduacion=graduacion.idGrad INNER JOIN vision ON vision.idVision=invMicas.vision WHERE $tmpWhere GROUP BY idGrad, marca, tratamiento ORDER BY Marca, Material,  Tratamiento, Esfera, Cilindro, Adicion";
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("Marca"=>$row2['Marca'],"Material"=>$row2['Material'],"Tratamiento"=>$row2['Tratamiento'],"Esfera"=>$row2['Esfera'],"Cilindro"=>$row2['Cilindro'],"Adicion"=>$row2['Adicion'],"Vision"=>$row2['Vision'],"Inventario"=>$row2['Inventario']);
    }

    //echo json_encode($query);
    echo json_encode($aTmp);
break;

case 'VerificaInventario':
    $nTratamiento=$_POST['nTratamiento'];
    $nVision=$_POST['nVision'];
    $nEsf=$_POST['nEsf'];
    $nCil=$_POST['nCil'];
    $nAdi=$_POST['nAdi'];
    $nMarca=$_POST['nMarca'];
    $nMaterial=$_POST['nMaterial'];

    //WHERE invMicas.marca=3 AND invMicas.material=1 AND invMicas.tratamiento=2;
    $query="SELECT SUM(invMicas.actual) AS Inventario, graduacion AS Graduacion FROM invMicas WHERE marca=".$nMarca." AND material=".$nMaterial." AND tratamiento=".$nTratamiento." AND graduacion=(SELECT idGrad FROM graduacion WHERE esf=".$nEsf." AND cil=".$nCil." AND adi=".$nAdi.")";
       // echo $query;
    
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $aTmp=array();
    while ($row2 = mysqli_fetch_array($result)){
        $aTmp[] = array("Inventario"=>$row2['Inventario'],"Graduacion"=>$row2['Graduacion']);
    }

    //echo json_encode($query);
    echo json_encode($aTmp);
break;

case 'nuevoInventario':
    $arrInv = $_POST['arrNuevos'];
    $nMarca = $_POST['nMarca'];
    $nMaterial = $_POST['nMaterial'];
    $nTratamiento = $_POST['nTratamiento'];
    $nVision = $_POST['nVision'];
    $inv = json_decode($arrInv, false);
    $nRegs = sizeof($inv);
    $nInsertados = 0;

    for ($i = 0; $i < $nRegs; $i++) {
        // Convierte los vacíos en cero
        $nCant = isset($inv[$i][0]) && $inv[$i][0] !== '' ? floatval($inv[$i][0]) : 0;
        $nEsf  = isset($inv[$i][1]) && $inv[$i][1] !== '' ? floatval($inv[$i][1]) : 0;
        $nCil  = isset($inv[$i][2]) && $inv[$i][2] !== '' ? -abs(floatval($inv[$i][2])) : 0;
        $nAdi  = isset($inv[$i][3]) && $inv[$i][3] !== '' ? floatval($inv[$i][3]) : 0;

        // Solo procede si la cantidad es mayor que 0
        if ($nCant <= 0) continue;

        $query = "SELECT idGrad FROM graduacion WHERE esf=$nEsf AND cil=$nCil AND adi=$nAdi";
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
        if (mysqli_num_rows($result) == 0 ){
            $query = "INSERT INTO graduacion (esf, cil, adi) VALUES ($nEsf,$nCil,$nAdi)";
            $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
            $nGrad = mysqli_insert_id($conn);
        } else {
            $row2 = mysqli_fetch_array($result);
            $nGrad = $row2['idGrad'];
        }
        $sFecha = "'".date('Y-m-d H:i:s')."'";
        $query = "INSERT INTO invMicas (fecha, marca, material, tratamiento, graduacion, vision, tope, actual) VALUES($sFecha,$nMarca,$nMaterial,$nTratamiento,$nGrad,$nVision,1,$nCant)";
        $result = mysqli_query($conn, $query) or die(mysqli_error($conn));
        if ($result) $nInsertados++;
    }

    if ($nInsertados > 0) {
        $sRet = "Inventario registrado correctamente. Partidas agregadas: $nInsertados";
    } else {
        $sRet = "No se agregó ningún registro: la cantidad debe ser mayor a cero en cada partida.";
    }
    echo json_encode($sRet);
    break;







/*
case 'ajustaInventario':
    $sMarca=$_POST['sMarca'];
    $sMaterial=$_POST['sMaterial'];
    $sTratamiento=$_POST['sTratamiento'];
    $sVision=$_POST['sVision'];
    $nActual=$_POST['nActual'];
    $nEsf=$_POST['nEsf'];
    $nCil=$_POST['nCil'];
    $nAdi=$_POST['nAdi'];
    $sFecha= "'".date('Y-m-d H:i:s')."'";
    $query="INSERT INTO invMicas (fecha, marca, material, tratamiento, graduacion,vision,actual) VALUES ($sFecha, (SELECT idMarca FROM marca WHERE marca = '".$sMarca."'), (SELECT idMaterial FROM material WHERE material = '".$sMaterial."'), (SELECT idTratamiento FROM tratamiento WHERE tratamiento = '".$sTratamiento."'), (SELECT idGrad FROM graduacion WHERE esf=".$nEsf." AND cil=".$nCil." AND adi=".$nAdi." ), (SELECT idVision FROM vision WHERE vision = '".$sVision."'),  ".$nActual.")";
    //echo "[".$query."]";
    $result = mysqli_query($conn, $query) or die(mysql_error());
    $sRet=($result)?"Registros guardados correctamente":"Error al guardar el inventario";
    echo json_encode($sRet);
break;
*/
case 'ajustaInventario':
    // Inicializar respuesta
    $response = ['success' => false, 'message' => '', 'error' => ''];
    
    try {
        // Validar datos requeridos
        $required_fields = ['sMarca', 'sMaterial', 'sTratamiento', 'sVision', 'nActual', 'nEsf', 'nCil', 'nAdi'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || $_POST[$field] === '') {
                throw new Exception("Campo requerido faltante: $field");
            }
        }
        
        // Sanitizar y validar datos
        $sMarca = mysqli_real_escape_string($conn, trim($_POST['sMarca']));
        $sMaterial = mysqli_real_escape_string($conn, trim($_POST['sMaterial']));
        $sTratamiento = mysqli_real_escape_string($conn, trim($_POST['sTratamiento']));
        $sVision = mysqli_real_escape_string($conn, trim($_POST['sVision']));
        $nActual = intval($_POST['nActual']);
        $nEsf = floatval($_POST['nEsf']);
        $nCil = floatval($_POST['nCil']);
        $nAdi = floatval($_POST['nAdi']);
        
        // Validar valores numéricos
        if ($nActual == 0) {
            throw new Exception("El ajuste no puede ser cero");
        }
        
        // Iniciar transacción
        mysqli_begin_transaction($conn);
        
        // Primero verificar que existen todos los IDs referenciados
        $checks = [
            'marca' => "SELECT idMarca FROM marca WHERE marca = '$sMarca'",
            'material' => "SELECT idMaterial FROM material WHERE material = '$sMaterial'",
            'tratamiento' => "SELECT idTratamiento FROM tratamiento WHERE tratamiento = '$sTratamiento'",
            'vision' => "SELECT idVision FROM vision WHERE vision = '$sVision'",
            'graduacion' => "SELECT idGrad FROM graduacion WHERE esf = $nEsf AND cil = $nCil AND adi = $nAdi"
        ];
        
        $ids = [];
        foreach ($checks as $key => $query) {
            $result = mysqli_query($conn, $query);
            if (!$result || mysqli_num_rows($result) === 0) {
                throw new Exception("No se encontró el registro en $key: " . mysqli_error($conn));
            }
            $row = mysqli_fetch_assoc($result);
            $ids[$key] = $row[array_keys($row)[0]];
            mysqli_free_result($result);
        }
        
        $sFecha = "'" . date('Y-m-d H:i:s') . "'";
        
        // Preparar consulta con parámetros seguros
        $query = "INSERT INTO invMicas (fecha, marca, material, tratamiento, graduacion, vision, actual) 
                 VALUES ($sFecha, {$ids['marca']}, {$ids['material']}, {$ids['tratamiento']}, 
                         {$ids['graduacion']}, {$ids['vision']}, $nActual)";
        
        // Ejecutar inserción
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception("Error en inserción: " . mysqli_error($conn));
        }
        
        // Confirmar transacción
        mysqli_commit($conn);
        
        $response['success'] = true;
        $response['message'] = "Ajuste registrado correctamente";
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($conn);
        
        $response['success'] = false;
        $response['error'] = $e->getMessage();
        $response['message'] = "Error al guardar el ajuste";
        
        // Log del error (opcional)
        error_log("Error en ajusteInventario: " . $e->getMessage());
    }
    
    echo json_encode($response);
    break;
}
?>
