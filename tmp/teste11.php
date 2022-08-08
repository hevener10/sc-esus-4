<?php
$duplicados = array();
$conta_duplicados = 0;

$aCPF = array();
$aCNS = array();

$aCNS[0] = 0;
$aCPF[0] = 27738224800;

$aCNS[1] = 677123234543234;
$aCPF[1] = 0;

$aCNS[2] = 123098123098234;
$aCPF[2] = 27738224800;

$aCNS[3] = 677123234543234;
$aCPF[3] = 36985214785;


for ($i=0;$i<count($aCPF);$i++){
			$CNS = $aCNS[$i];
			$CPF = $aCPF[$i];
			//*******************************************************************************************
			$duplicado = false;
			if (strlen($CNS) == 15){
				if (false !== $key = array_search($CNS, array_column($duplicados, 'cns'))) {
					$duplicado = true;
					if (strlen($CPF) == 11){
						if (strlen($duplicados[$key]['cpf']) != 11){
							$duplicados[$key]['cpf'] = $CPF;
						}
					}
				}
			}
			if (strlen($CPF) == 11){
				if (false !== $key = array_search($CPF, array_column($duplicados, 'cpf'))) {
					$duplicado = true;
					if (strlen($CNS) == 15){
						if (strlen($duplicados[$key]['cns']) != 15){
							$duplicados[$key]['cns'] = $CNS;
						}
					}
				}
			}
			if (!$duplicado){
				$duplicados[$conta_duplicados]['cns'] = $CNS;
				$duplicados[$conta_duplicados]['cpf'] = $CPF;
				$conta_duplicados++;
			}
			//*******************************************************************************************
			
	echo $i." - ".$CNS." - ".$CPF." [".$duplicado."]<br><br>";	
	echo "<br>-------------------------------------------------<br>";
	print_r ($duplicados);
	echo "<br>-------------------------------------------------<br><br><br>";
}

?>