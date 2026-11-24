<?php
$conn = new mysqli("localhost", "u947744031_lab", "1Abejash(*@", "u947744031_lab");
$res = $conn->query("DESCRIBE partidas");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
?>