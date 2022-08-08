<?php
require_once('session.php');
if (file_exists("dados.php")){
	require_once('dados.php');
} else {
	$cbnome  = "Secretaria Municipal de Saúde de Teste";
	$cbend1  = "Avenida Sete de Setembro, número 29387 - Sala 2";
	$cbend2  = "Bairro Matarazzo Caprinio";
	$cbend3  = "CEP 98732-980";
	$cbend4  = "São Matheus do Oeste Mineiro - MG";
	$cbcont1  = "+55 (47) 23432-9384 | +55 (47) 12384-3234";
	$cbcont2  = "contatosaude@saomatheus.gov.br | www.saomatheuspref.gov.br";
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Configuração do cabeçalho</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="relatorios.php" id="formcb" name="formcb" onsubmit="return validateForm()">
  <input type="hidden" id="gravarcb" name="gravarcb" value="1">
  <table width="481" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Configura&ccedil;&otilde;es do Cabeçalho</td>
    </tr>
    <tr> 
      <td width="108" height="27" valign="top" class="rotulo">Nome&nbsp;&nbsp;</td>
      <td width="373" valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" size="60" name="cbnome" id="cbnome" value="<?php echo $cbnome;?>">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Endereço linha 1&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" size="60" name="cbend1" id="cbend1" value="<?php echo $cbend1;?>">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Endereço linha 2&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" size="60" name="cbend2" id="cbend2" value="<?php echo $cbend2;?>">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Endereço linha 3&nbsp;&nbsp;</td>
      <td valign="top" class="valor">
	  <p class="padrao">&nbsp;&nbsp;<input type="text" size="60" name="cbend3" id="cbend3" value="<?php echo $cbend3;?>">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Endereço linha 4&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" size="60" name="cbend4" id="cbend4" value="<?php echo $cbend4;?>">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Contato linha 1&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" size="60" name="cbcont1" id="cbcont1" value="<?php echo $cbcont1;?>">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Contato linha 2&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" size="60" name="cbcont2" id="cbcont2" value="<?php echo $cbcont2;?>">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="35" colspan="2" valign="top" class="rodape">
	  <button onclick="window.location.href='relatorios.php'">Cancelar</button> | <button type="submit" form="formcb" value="Submit">Gravar</button>
	  </td>
    </tr>
    <tr> 
      <td height="51" colspan="2" valign="top" class="mensagens"><p id="mensagem"></p></td>
    </tr>
  </table>
</form>
<script>
function validateForm() {
	var mensagem = "";
	var submit = true;

	if (document.forms["formcb"]["cbnome"].value == ''){
		mensagem = mensagem + "Campo 'Nome' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formcb"]["cbend1"].value == ''){
		mensagem = mensagem + "Campo 'Endereço linha 1' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formcb"]["cbend2"].value == ''){
		mensagem = mensagem + "Campo 'Endereço linha 2' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formcb"]["cbend3"].value == ''){
		mensagem = mensagem + "Campo 'Endereço linha 3' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formcb"]["cbend4"].value == ''){
		mensagem = mensagem + "Campo 'Endereço linha 4' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formcb"]["cbcont1"].value == ''){
		mensagem = mensagem + "Campo 'Contato linha 1' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formcb"]["cbcont2"].value == ''){
		mensagem = mensagem + "Campo 'Contato linha 2' é necessário!<br>";
		submit = false;
	}

  
	if (!submit){
		document.getElementById("mensagem").innerHTML = mensagem;
		return false;
	}
  
}
</script>
</body>
</html>