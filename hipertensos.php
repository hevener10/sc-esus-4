<?php
require_once('session.php');
require_once('functions.php');
if (file_exists("cfg_rel_h.php")){
	require_once('cfg_rel_h.php');
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
	$m12 = 0;
}
$ex = isset($_GET["ex"]) ? trim($_GET["ex"]) : 0;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Configura&ccedil;&otilde;es do relat&oacute;rio de hipertensos</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="relatorios.php" onsubmit="return validateForm()" id="formh" name="formh" >
  <input type="hidden" id="gravarhi" name="gravarhi" value="1">
  <input type="hidden" id="ex" name="ex" value="<?php echo $ex;?>">
  <table width="587" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Configura&ccedil;&otilde;es do relat&oacute;rio de hipertensos</td>
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
      <td height="27" valign="top" class="rotulo">12 meses de qual data?&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
	    <select id="m12" name="m12">
		<?php
		if ($m12 == 0){
			echo "
			  <option value=\"0\" selected>Data final</option>
			  <option value=\"1\">Data inicial</option>
			";
		} else {
			echo "
			  <option value=\"0\">Data final</option>
			  <option value=\"1\" selected>Data inicial</option>
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
      <td height="35" colspan="2" valign="top" class="rodape"><button onclick="window.location.href='relatorios.php'">Cancelar</button> | <button type="submit" form="formh" value="Submit">Gravar</button></td>
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
	if (!validaData(document.forms["formh"]["dti"].value)){
		mensagem = mensagem + "Campo 'Data inicial' não é uma data válida!<br>";
		submit = false;
		ctrld = 1;
	}
	if (!validaData(document.forms["formh"]["dtf"].value)){
		mensagem = mensagem + "Campo 'Data final' não é uma data válida!<br>";
		submit = false;
		ctrld = 1;
	}
	if (ctrld == 0){
		var dti = parseInt(document.forms["formh"]["dti"].value.substring(6,10) + document.forms["formh"]["dti"].value.substring(3,5) + document.forms["formh"]["dti"].value.substring(0,2));
		var dtf = parseInt(document.forms["formh"]["dtf"].value.substring(6,10) + document.forms["formh"]["dtf"].value.substring(3,5) + document.forms["formh"]["dtf"].value.substring(0,2));
		var gpa = document.forms["formh"]["gpa"].value;
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