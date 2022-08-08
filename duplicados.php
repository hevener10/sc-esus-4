<?php
require_once('session.php');
require_once('functions.php');
if (file_exists("cfg_rel_du.php")){
	require_once('cfg_rel_du.php');
} else {
	$gpa = 0;
	$cfa = 0;
	$paginacao = 0;
	$ordem = 'N';
	$mcabecalho = 1;
}
$ex = isset($_GET["ex"]) ? trim($_GET["ex"]) : 0;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Configura&ccedil;&otilde;es do relat&oacute;rio de duplicados</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="relatorios.php" onsubmit="return validateForm()" id="formd" name="formd" >
  <input type="hidden" id="gravardu" name="gravardu" value="1">
  <input type="hidden" id="ex" name="ex" value="<?php echo $ex;?>">
  <table width="587" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Configura&ccedil;&otilde;es do relat&oacute;rio de duplicados</td>
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
      <td height="35" colspan="2" valign="top" class="rodape"><button onclick="window.location.href='relatorios.php'">Cancelar</button> | <button type="submit" form="formd" value="Submit">Gravar</button></td>
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
  
	if (!submit){
		document.getElementById("mensagem").innerHTML = mensagem;
		return false;
	}
  
}
</script>