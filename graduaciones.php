<?php
	$mono=$multi=$i=0;
	#Esferas altas
	/*
	for ($esf=-15; $esf<= -6; $esf+= 0.5){
		echo "Esfera:".$esf."<br>";
		for ($cil=0;$cil<=6;$cil+=1){
			$cont++;
			echo "Esfera: ".$esf."/ Cilindro:".$cil."<br>";
		}
	}
*/
	echo "Esfera, Cilindro, Adicion,<br>";
	#Esferas medias
	for ($esf=-6; $esf<= -4; $esf+= 0.5){
		for ($cil=0;$cil>=-3;$cil-=0.5){
			$mono++;
			$i++;
			echo "$i, $esf, $cil, 0 <br>";
		}
		for ($cil=-4;$cil>=-6;$cil-=1){
			$mono++;
			$i++;
			echo "$i, $esf, $cil, 0 <br>";
		}
	}
	for ($esf=-3.75; $esf<= 3.75; $esf+= 0.25){
		for ($cil=0;$cil>=-3;$cil-=0.25){
			$mono++;
			$i++;
			echo "$i, $esf, $cil, 0 <br>";
		}
		for ($cil=-3.5;$cil>=-6;$cil-=0.5){
			$mono++;
			$i++;
			echo "$i, $esf, $cil, 0<br>";
		}
	}

	for ($esf=4; $esf<= 6; $esf+= 0.5){
		for ($cil=0;$cil>=-6;$cil-=0.5){
			$mono++;
			$i++;
			echo "$i, $esf, $cil, 0 <br>";
		}
	}
	#adiciones
	for ($esf=-4; $esf<= 4; $esf+= 0.25){
		for ($add=0.75;$add<=3;$add+=0.25){
			$multi++;
			$i++;
			echo "$i, $esf, 0, $add <br>";
		}
	}
?>
