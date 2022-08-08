<?php
require_once('session.php');
require_once('functions.php');

$rf = isset($_GET["rf"]) ? trim($_GET["rf"]) : '0';

$rel_titulo = "";
$rel_action = "";

if ($rf == '0'){
	header('location:relatorios.php');
} else {
	if ($rf == 'G'){
		if (file_exists("cfg_rel_g.php")){
			require_once('cfg_rel_g.php');
		} else {
			header('location:gestantes.php');
		}
		$rel_titulo = "Gestantes";
		$rel_action = "rel_gestantes.php";
	}
	if ($rf == 'M'){
		if (file_exists("cfg_rel_m.php")){
			require_once('cfg_rel_m.php');
		} else {
			header('location:mulheres.php');
		}
		$rel_titulo = "Mulheres";
		$rel_action = "rel_mulheres.php";
	}
	if ($rf == 'H'){
		if (file_exists("cfg_rel_h.php")){
			require_once('cfg_rel_h.php');
		} else {
			header('location:hipertensos.php');
		}
		$rel_titulo = "Hipertensos";
		$rel_action = "rel_hipertensos.php";
	}
	if ($rf == 'D'){
		if (file_exists("cfg_rel_d.php")){
			require_once('cfg_rel_d.php');
		} else {
			header('location:diabeticos.php');
		}
		$rel_titulo = "Diabéticos";
		$rel_action = "rel_diabeticos.php";
	}
	if ($rf == 'C'){
		if (file_exists("cfg_rel_v.php")){
			require_once('cfg_rel_v.php');
		} else {
			header('location:vacinacao.php');
		}
		$rel_titulo = "Crianças";
		$rel_action = "rel_criancas.php";
	}
	if ($rf == 'V'){
		if (file_exists("cfg_rel_v.php")){
			require_once('cfg_rel_v.php');
		} else {
			header('location:vacinacao.php');
		}
		$rel_titulo = "Vacinas";
		$rel_action = "rel_vacinas.php";
	}
}


?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Filtrar resultado [<?php echo $rel_titulo;?>]</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="<?php echo $rel_action;?>" onsubmit="return validateForm()" id="formf" name="formf">
  <input type="hidden" id="mfiltro" name="mfiltro" value="1">
  <table width="587" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Filtrar resultado [<?php echo $rel_titulo;?>]</td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Buscar em&nbsp;&nbsp;</td>
      <td valign="top" class="valor">&nbsp;&nbsp;
		<select id="cpbusca" name="cpbusca">
		<?php
		if ($cpbusca == 'U'){
			echo "
			  <option value=\"U\" selected>Unidade (digite o CNES ou 0 para SEM UNIDADE)</option>
			  <option value=\"E\">Equipe (digite o INE ou 0 para SEM EQUIPE)</option>
			  <option value=\"M\">Micro-Área (Número ou FA ou 0 para SEM MA)</option>
			  <option value=\"C\">CNS (apenas números)</option>
			  <option value=\"P\">CPF (apenas números)</option>
			";
		} else {
			if ($cpbusca == 'E'){
				echo "
				  <option value=\"U\">Unidade (digite o CNES ou 0 para SEM UNIDADE)</option>
				  <option value=\"E\" selected>Equipe (digite o INE ou 0 para SEM EQUIPE)</option>
				  <option value=\"M\">Micro-Área (Número ou FA ou 0 para SEM MA)</option>
				  <option value=\"C\">CNS (apenas números)</option>
				  <option value=\"P\">CPF (apenas números)</option>
				";
			} else {
				if ($cpbusca == 'M'){
					echo "
					  <option value=\"U\">Unidade (digite o CNES ou 0 para SEM UNIDADE)</option>
					  <option value=\"E\">Equipe (digite o INE ou 0 para SEM EQUIPE)</option>
					  <option value=\"M\" selected>Micro-Área (Número ou FA ou 0 para SEM MA)</option>
					  <option value=\"C\">CNS (apenas números)</option>
					  <option value=\"P\">CPF (apenas números)</option>
					";
				} else {
					if ($cpbusca == 'C'){
						echo "
						  <option value=\"U\">Unidade (digite o CNES ou 0 para SEM UNIDADE)</option>
						  <option value=\"E\">Equipe (digite o INE ou 0 para SEM EQUIPE)</option>
						  <option value=\"M\">Micro-Área (Número ou FA ou 0 para SEM MA)</option>
						  <option value=\"C\" selected>CNS (apenas números)</option>
						  <option value=\"P\">CPF (apenas números)</option>
						";
					} else {
						if ($cpbusca == 'P'){
							echo "
							  <option value=\"U\">Unidade (digite o CNES ou 0 para SEM UNIDADE)</option>
							  <option value=\"E\">Equipe (digite o INE ou 0 para SEM EQUIPE)</option>
							  <option value=\"M\">Micro-Área (Número ou FA ou 0 para SEM MA)</option>
							  <option value=\"C\">CNS (apenas números)</option>
							  <option value=\"P\" selected>CPF (apenas números)</option>
							";
						} else {
							echo "
							  <option value=\"U\" selected>Unidade (digite o CNES ou 0 para SEM UNIDADE)</option>
							  <option value=\"E\">Equipe (digite o INE ou 0 para SEM EQUIPE)</option>
							  <option value=\"M\">Micro-Área (Número ou FA ou 0 para SEM MA)</option>
							  <option value=\"C\">CNS (apenas números)</option>
							  <option value=\"P\">CPF (apenas números)</option>
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
      <td width="314" height="27" valign="top" class="rotulo">Valor&nbsp;&nbsp;</td>
      <td width="273" valign="top" class="valor">&nbsp;&nbsp;<input type="text" name="vlbusca" id="vlbusca"> 
      </td>
    </tr>
    <tr> 
      <td height="61" valign="top" class="fecha-borda1"><!--DWLayoutEmptyCell-->&nbsp;</td>
    </tr>
    <tr> 
      <td height="35" colspan="2" valign="top" class="rodape"><button type="submit" form="formf" value="Submit">Gerar</button></td>
    </tr>
    <tr> 
      <td height="51" colspan="2" valign="top" class="mensagens"><p id="mensagem"></p></td>
    </tr>
  </table>
</form>
</body>
</html>

<script>
function validateForm() {
	var mensagem = "";
	var submit = true;
	var nvalor = document.forms["formf"]["vlbusca"].value;
	
	if (document.forms["formf"]["vlbusca"].value == ''){
		mensagem = mensagem + "Campo 'Valor' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formf"]["cpbusca"].value == 'U' || document.forms["formf"]["cpbusca"].value == 'E'){
		if (isNaN(nvalor)){
			mensagem = mensagem + "Campo 'Buscar em' necessita ser número!<br>";
			submit = false;
		}
	}
	if (document.forms["formf"]["cpbusca"].value == 'C'){
		if (document.forms["formf"]["vlbusca"].value.length != 15){
			mensagem = mensagem + "CNS necessita ter 15 dígitos numéricos!<br>";
			submit = false;
		} else {
			if (isNaN(nvalor)){
				mensagem = mensagem + "CNS necessita ser totalmente numérico!<br>";
				submit = false;
			}
		}
	}
	if (document.forms["formf"]["cpbusca"].value == 'P'){
		if (document.forms["formf"]["vlbusca"].value.length != 11){
			mensagem = mensagem + "CPF necessita ter 11 dígitos numéricos!<br>";
			submit = false;
		} else {
			if (isNaN(nvalor)){
				mensagem = mensagem + "CPF necessita ser totalmente numérico!<br>";
				submit = false;
			}
		}
	}

	if (!submit){
		document.getElementById("mensagem").innerHTML = mensagem;
		return false;
	}
  
}
</script>