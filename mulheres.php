<?php
require_once('session.php');
require_once('functions.php');
if (file_exists("cfg_rel_m.php")){
	require_once('cfg_rel_m.php');
} else {
	$dti = date('Ymd');
	$dtf = datasomadias(date('Ymd'),30);
	$ultimo_q = qfechado();
	$ano_q = substr($ultimo_q,3,4);
	$per_q = substr($ultimo_q,0,2);
	if ($per_q == 'Q1'){
		$dti = $ano_q.'0101';
		$dtf = $ano_q.'0430';
	}
	if ($per_q == 'Q2'){
		$dti = $ano_q.'0501';
		$dtf = $ano_q.'0831';
	}
	if ($per_q == 'Q3'){
		$dti = $ano_q.'0901';
		$dtf = $ano_q.'1231';
	}
	$gpa = 0;
	$cfa = 0;
	$paginacao = 0;
	$grupo = 'ine';
	$ordem = 'N';
	$mcabecalho = 1;
	$dt3anos = 'U';
	$ridade = 0;
	$proceds = '0204030188';
	$idin = 0;
	$idfi = 1;
	$apvac = 0;
	$tbusca = 12;
}

$lridade = array();
$lridade[0][0]  = 0;  $lridade[0][1]  = "de 25 até 64";
$lridade[1][0]  = 1;  $lridade[1][1]  = "de 25 até 30";
$lridade[2][0]  = 2;  $lridade[2][1]  = "de 31 até 40";
$lridade[3][0]  = 3;  $lridade[3][1]  = "de 41 até 50";
$lridade[4][0]  = 4;  $lridade[4][1]  = "de 51 até 60";
$lridade[5][0]  = 5;  $lridade[5][1]  = "de 61 até 64";
$lridade[6][0]  = 6;  $lridade[6][1]  = "de 25 até 40";
$lridade[7][0]  = 7;  $lridade[7][1]  = "de 40 até 60";
$lridade[8][0]  = 8;  $lridade[8][1]  = "de 50 até 64";
$lridade[9][0]  = 9;  $lridade[9][1]  = "de 25 até 35";
$lridade[10][0] = 25; $lridade[10][1] = "25";
$lridade[11][0] = 26; $lridade[11][1] = "26";
$lridade[12][0] = 27; $lridade[12][1] = "27";
$lridade[13][0] = 28; $lridade[13][1] = "28";
$lridade[14][0] = 29; $lridade[14][1] = "29";
$lridade[15][0] = 30; $lridade[15][1] = "30";
$lridade[16][0] = 31; $lridade[16][1] = "31";
$lridade[17][0] = 32; $lridade[17][1] = "32";
$lridade[18][0] = 33; $lridade[18][1] = "33";
$lridade[19][0] = 34; $lridade[19][1] = "34";
$lridade[20][0] = 35; $lridade[20][1] = "35";
$lridade[21][0] = 36; $lridade[21][1] = "36";
$lridade[22][0] = 37; $lridade[22][1] = "37";
$lridade[23][0] = 38; $lridade[23][1] = "38";
$lridade[24][0] = 39; $lridade[24][1] = "39";
$lridade[25][0] = 40; $lridade[25][1] = "40";
$lridade[26][0] = 41; $lridade[26][1] = "41";
$lridade[27][0] = 42; $lridade[27][1] = "42";
$lridade[28][0] = 43; $lridade[28][1] = "43";
$lridade[29][0] = 44; $lridade[29][1] = "44";
$lridade[30][0] = 45; $lridade[30][1] = "45";
$lridade[31][0] = 46; $lridade[31][1] = "46";
$lridade[32][0] = 47; $lridade[32][1] = "47";
$lridade[33][0] = 48; $lridade[33][1] = "48";
$lridade[34][0] = 49; $lridade[34][1] = "49";
$lridade[35][0] = 50; $lridade[35][1] = "50";
$lridade[36][0] = 51; $lridade[36][1] = "51";
$lridade[37][0] = 52; $lridade[37][1] = "52";
$lridade[38][0] = 53; $lridade[38][1] = "53";
$lridade[39][0] = 54; $lridade[39][1] = "54";
$lridade[40][0] = 55; $lridade[40][1] = "55";
$lridade[41][0] = 56; $lridade[41][1] = "56";
$lridade[42][0] = 57; $lridade[42][1] = "57";
$lridade[43][0] = 58; $lridade[43][1] = "58";
$lridade[44][0] = 59; $lridade[44][1] = "59";
$lridade[45][0] = 60; $lridade[45][1] = "60";
$lridade[46][0] = 61; $lridade[46][1] = "61";
$lridade[47][0] = 62; $lridade[47][1] = "62";
$lridade[48][0] = 63; $lridade[48][1] = "63";
$lridade[49][0] = 64; $lridade[49][1] = "64";

$ex = isset($_GET["ex"]) ? trim($_GET["ex"]) : 0;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Configura&ccedil;&otilde;es do relat&oacute;rio de mulheres</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="relatorios.php" onsubmit="return validateForm()" id="formm" name="formm" >
  <input type="hidden" id="gravarmu" name="gravarmu" value="1">
  <input type="hidden" id="ex" name="ex" value="<?php echo $ex;?>">
  <table width="587" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Configura&ccedil;&otilde;es do relat&oacute;rio de mulheres</td>
    </tr>
    <tr> 
      <td width="314" height="27" valign="top" class="rotulo">Data Inicial&nbsp;&nbsp;</td>
      <td width="273" valign="top" class="valor">&nbsp;&nbsp;<input type="text" name="dti" id="dti" value="<?php echo dtshow($dti);?>"> 
      </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Data Final&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;<input type="text" name="dtf" id="dtf" value="<?php echo dtshow($dtf);?>"> </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Qual per&iacute;odo considerar para busca?&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
	    <select id="gpa" name="gpa">
		<?php
		if ($gpa == 0){
			echo "
			  <option value=\"0\" selected>Dada inicial e Data final informada</option>
			  <option value=\"1\">A partir de hoje (busca ativa)</option>
			";
		} else {
			echo "
			  <option value=\"0\">Dada inicial e Data final informada</option>
			  <option value=\"1\" selected>A partir de hoje (busca ativa)</option>
			";
		}
		?>
        </select> 
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Considerar fora de área&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
	    <select id="cfa" name="cfa">
		<?php
		if ($cfa == 0){
			echo "
			  <option value=\"0\" selected>Sim</option>
			  <option value=\"1\">Não</option>
			";
		} else {
			echo "
			  <option value=\"0\">Sim</option>
			  <option value=\"1\" selected>Não</option>
			";
		}
		?>
        </select> 
	  </td>
    </tr>
	
    <tr> 
      <td height="27" valign="top" class="rotulo">3 anos a contar ...&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
		<select id="dt3anos" name="dt3anos">
		<?php
		if ($dt3anos == 'U'){
			echo "
			  <option value=\"U\" selected>... da última data da análise</option>
			  <option value=\"P\">... da primeira data da análise</option>
			  <option value=\"A\">... do aniversário do analisado</option>
			";
		} else {
			if ($dt3anos == 'P'){
				echo "
				  <option value=\"U\">... da última data da análise</option>
				  <option value=\"P\" selected>... da primeira data da análise</option>
				  <option value=\"A\">... do aniversário do analisado</option>
				";
			} else {
				if ($dt3anos == 'A'){
					echo "
					  <option value=\"U\">... da última data da análise</option>
					  <option value=\"P\">... da primeira data da análise</option>
					  <option value=\"A\" selected>... do aniversário do analisado</option>
					";
				} else {
					echo "
					  <option value=\"U\" selected>... da última data da análise</option>
					  <option value=\"P\">... da primeira data da análise</option>
					  <option value=\"A\">... do aniversário do analisado</option>
					";
				}
			}
		}
		?>
		</select>
	  </td>
    </tr>
	
    <tr> 
      <td height="27" valign="top" class="rotulo">Idade&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
	    <select id="ridade" name="ridade">
		<?php
		for ($i=0;$i<count($lridade);$i++){
			if ($lridade[$i][0] == $ridade){
				echo "<option value=\"".$lridade[$i][0]."\" selected>".$lridade[$i][1]."</option>";
			} else {
				echo "<option value=\"".$lridade[$i][0]."\">".$lridade[$i][1]."</option>";
			}
		}
		?>
        </select> 
	  </td>
    </tr>
    <tr> 
      <td width="314" height="27" valign="top" class="rotulo">(*) Tempo de busca (meses)&nbsp;&nbsp;</td>
      <td width="273" valign="top" class="valor">&nbsp;&nbsp;<input type="text" name="tbusca" id="tbusca" value="<?php echo $tbusca;?>"> 
      </td>
    </tr>
    <tr> 
      <td width="314" height="27" valign="top" class="rotulo">(*) Idade inicial&nbsp;&nbsp;</td>
      <td width="273" valign="top" class="valor">&nbsp;&nbsp;<input type="text" name="idin" id="idin" value="<?php echo $idin;?>"> 
      </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">(*) Idade Final&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;<input type="text" name="idfi" id="idfi" value="<?php echo $idfi;?>"> </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">(*) Procedimentos&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;<input type="text" name="proceds" id="proceds" value="<?php echo $proceds;?>"> </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">(*) Apenas com procedimento&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
	    <select id="apvac" name="apvac">
		<?php
		if ($apvac == 0){
			echo "
			  <option value=\"0\" selected>Sim</option>
			  <option value=\"1\">Não</option>
			";
		} else {
			echo "
			  <option value=\"0\">Sim</option>
			  <option value=\"1\" selected>Não</option>
			";
		}
		?>
        </select> 
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Paginação&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
		<select id="paginacao" name="paginacao">
		<?php
		if ($paginacao == 0){
			echo "
			  <option value=\"0\" selected>Sem paginação</option>
			  <option value=\"8\">8</option>
			  <option value=\"100\">100</option>
			  <option value=\"500\">500</option>
			  <option value=\"1000\">1000</option>
			";
		} else {
			if ($paginacao == 8){
				echo "
				  <option value=\"0\">Sem paginação</option>
				  <option value=\"8\" selected>8</option>
				  <option value=\"100\">100</option>
				  <option value=\"500\">500</option>
				  <option value=\"1000\">1000</option>
				";
			} else {
				if ($paginacao == 100){
					echo "
					  <option value=\"0\">Sem paginação</option>
					  <option value=\"8\">8</option>
					  <option value=\"100\" selected>100</option>
					  <option value=\"500\">500</option>
					  <option value=\"1000\">1000</option>
					";
				} else {
					if ($paginacao == 500){
						echo "
						  <option value=\"0\">Sem paginação</option>
						  <option value=\"8\">8</option>
						  <option value=\"100\">100</option>
						  <option value=\"500\" selected>500</option>
						  <option value=\"1000\">1000</option>
						";
					} else {
						if ($paginacao == 1000){
							echo "
							  <option value=\"0\">Sem paginação</option>
							  <option value=\"8\">8</option>
							  <option value=\"100\">100</option>
							  <option value=\"500\">500</option>
							  <option value=\"1000\" selected>1000</option>
							";
						} else {
							echo "
							  <option value=\"0\" selected>Sem paginação</option>
							  <option value=\"8\">8</option>
							  <option value=\"100\">100</option>
							  <option value=\"500\">500</option>
							  <option value=\"1000\">1000</option>
							";
						}
					}
				}
			}
		}
		?>
		</select>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Agrupar por&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
		<select id="grupo" name="grupo">
		<?php
		if ($grupo == 'ine'){
			echo "
			  <option value=\"ine\" selected>Equipe</option>
			  <option value=\"cnes\">Unidade</option>
			  <option value=\"cind_micro_area\">Micro-Área</option>
			  <option value=\"SG\">Sem grupo</option>
			";
		} else {
			if ($grupo == 'cnes'){
				echo "
				  <option value=\"ine\">Equipe</option>
				  <option value=\"cnes\" selected>Unidade</option>
				  <option value=\"cind_micro_area\">Micro-Área</option>
				  <option value=\"SG\">Sem grupo</option>
				";
			} else {
				if ($grupo == 'cind_micro_area'){
					echo "
					  <option value=\"ine\">Equipe</option>
					  <option value=\"cnes\">Unidade</option>
					  <option value=\"cind_micro_area\" selected>Micro-Área</option>
					  <option value=\"SG\">Sem grupo</option>
					";
				} else {
					if ($grupo == 'SG'){
						echo "
						  <option value=\"ine\">Equipe</option>
						  <option value=\"cnes\">Unidade</option>
						  <option value=\"cind_micro_area\">Micro-Área</option>
						  <option value=\"SG\" selected>Sem grupo</option>
						";
					} else {
						echo "
						  <option value=\"ine\" selected>Equipe</option>
						  <option value=\"cnes\">Unidade</option>
						  <option value=\"cind_micro_area\">Micro-Área</option>
						  <option value=\"SG\">Sem grupo</option>
						";
					}
				}
			}
		}
		?>
		</select>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Ordenar por&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
		<select id="ordem" name="ordem">
		<?php
		if ($ordem == 'N'){
			echo "
			  <option value=\"N\" selected>Nome</option>
			  <option value=\"C\">CPF</option>
			  <option value=\"S\">CNS</option>
			  <option value=\"DC\">Idade decrescente</option>
			  <option value=\"DD\">Idade crescente</option>
			";
		} else {
			if ($ordem == 'C'){
				echo "
				  <option value=\"N\">Nome</option>
				  <option value=\"C\" selected>CPF</option>
				  <option value=\"S\">CNS</option>
				  <option value=\"DC\">Idade decrescente</option>
				  <option value=\"DD\">Idade crescente</option>
				";
			} else {
				if ($ordem == 'S'){
					echo "
					  <option value=\"N\">Nome</option>
					  <option value=\"C\">CPF</option>
					  <option value=\"S\" selected>CNS</option>
					  <option value=\"DC\">Idade decrescente</option>
					  <option value=\"DD\">Idade crescente</option>
					";
				} else {
					if ($ordem == 'DC'){
						echo "
						  <option value=\"N\">Nome</option>
						  <option value=\"C\">CPF</option>
						  <option value=\"S\">CNS</option>
						  <option value=\"DC\" selected>Idade decrescente</option>
						  <option value=\"DD\">Idade crescente</option>
						";
					} else {
						if ($ordem == 'DD'){
							echo "
							  <option value=\"N\">Nome</option>
							  <option value=\"C\">CPF</option>
							  <option value=\"S\">CNS</option>
							  <option value=\"DC\">Idade decrescente</option>
							  <option value=\"DD\" selected>Idade crescente</option>
							";
						} else {
							echo "
							  <option value=\"N\" selected>Nome</option>
							  <option value=\"C\">CPF</option>
							  <option value=\"S\">CNS</option>
							  <option value=\"DC\">Idade decrescente</option>
							  <option value=\"DD\">Idade crescente</option>
							";
						}
					}
				}
			}
		}
		?>
		</select>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Mostrar cabeçalho&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
		<select id="mcabecalho" name="mcabecalho">
		<?php
		if ($mcabecalho == 1){
			echo "
			  <option value=\"1\" selected>Sim</option>
			  <option value=\"0\">Não</option>
			";
		} else {
			echo "
			  <option value=\"1\">Sim</option>
			  <option value=\"0\" selected>Não</option>
			";
		}
		?>
		</select>
		</td>
    </tr>
    <tr> 
      <td height="61" valign="top" class="fecha-borda1"><!--DWLayoutEmptyCell-->&nbsp;</td>
    </tr>
    <tr> 
      <td height="35" colspan="2" valign="top" class="rodape"><button onclick="window.location.href='relatorios.php'">Cancelar</button> | <button type="submit" form="formm" value="Submit">Gravar</button></td>
    </tr>
    <tr> 
      <td height="51" colspan="2" valign="top" class="mensagens"><p id="mensagem"></p></td>
    </tr>
  </table>
</form>
</body>
</html>

<script>
function validaData(dt) {
	var retorno = true;
	var d_dti = parseInt(dt.substring(0,2));
	var m_dti = parseInt(dt.substring(3,5));
	var a_dti = parseInt(dt.substring(6,10));
	var barras = dt.substring(2,3) + dt.substring(5,6);
	if (barras != '//'){
		retorno = false;
	}
	if (isNaN(dt.substring(0,2))){
		retorno = false;
	}
	if (isNaN(dt.substring(3,5))){
		retorno = false;
	}
	if (isNaN(dt.substring(6,10))){
		retorno = false;
	}
	if (retorno){
		if (a_dti < 1990 || a_dti > 2050){
			retorno = false;
		}
		if (m_dti < 1 || m_dti > 12){
			retorno = false;
		}
		if (retorno){
			if (m_dti == 1 || m_dti == 3 || m_dti == 5 || m_dti == 7 || m_dti == 8 || m_dti == 10 || m_dti == 12){
				if (d_dti < 1 || d_dti > 31){
					retorno = false;
				}
			} else {
				if (m_dti == 2){
					if (d_dti < 1 || d_dti > 28){
						retorno = false;
					}
				} else {
					if (d_dti < 1 || d_dti > 30){
						retorno = false;
					}
				}
			}
		}
	}
	return retorno;
}
function validateForm() {
	var mensagem = "";
	var submit = true;

	var ctrld = 0;
	if (!validaData(document.forms["formm"]["dti"].value)){
		mensagem = mensagem + "Campo 'Data inicial' não é uma data válida!<br>";
		submit = false;
		ctrld = 1;
	}
	if (!validaData(document.forms["formm"]["dtf"].value)){
		mensagem = mensagem + "Campo 'Data final' não é uma data válida!<br>";
		submit = false;
		ctrld = 1;
	}
	if (ctrld == 0){
		var dti = parseInt(document.forms["formm"]["dti"].value.substring(6,10) + document.forms["formm"]["dti"].value.substring(3,5) + document.forms["formm"]["dti"].value.substring(0,2));
		var dtf = parseInt(document.forms["formm"]["dtf"].value.substring(6,10) + document.forms["formm"]["dtf"].value.substring(3,5) + document.forms["formm"]["dtf"].value.substring(0,2));
		var gpa = document.forms["formm"]["gpa"].value;
		if (gpa == 0){
			var ctrlp = 0;
			if (isNaN(dti)){
				mensagem = mensagem + "Campo 'Data inicial' é necessário!<br>";
				ctrlp = 1;
				submit = false;
			}
			if (isNaN(dtf)){
				mensagem = mensagem + "Campo 'Data final' é necessário!<br>";
				ctrlp = 1;
				submit = false;
			}
			if (ctrlp == 0){
				if (dti > dtf){
					mensagem = mensagem + "A data final precisa ser maior ou igual a data inicial!<br>";
					submit = false;
				}
			}
		}
	}
  
	if (!submit){
		document.getElementById("mensagem").innerHTML = mensagem;
		return false;
	}
  
}
</script>