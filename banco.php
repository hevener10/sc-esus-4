<?php
require_once('session.php');
if (file_exists($_SESSION['filedb'])){
	require_once($_SESSION['filedb']);
} else {
	$dbhost = "localhost";
	$dbport = "5433";
	$dbdb = "esus";
	$dbuser = "postgres";
	$dbpass = "esus";
}
$ex = isset($_GET["ex"]) ? trim($_GET["ex"]) : 0;
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Configuração do banco de dados</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="relatorios.php" id="formdb" name="formdb" onsubmit="return validateForm()">
  <input type="hidden" id="gravardb" name="gravardb" value="1">
  <input type="hidden" id="ex" name="ex" value="<?php echo $ex;?>">
  <table width="481" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Configura&ccedil;&otilde;es do Banco de Dados</td>
    </tr>
    <tr> 
      <td width="108" height="27" valign="top" class="rotulo">Servidor&nbsp;&nbsp;</td>
      <td width="373" valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" name="dbhost" id="dbhost" value="<?php echo $dbhost;?>">&nbsp;&nbsp;localhost</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Porta&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" name="dbport" id="dbport" value="<?php echo $dbport;?>">&nbsp;&nbsp;5433</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Banco de Dados&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" name="dbdb" id="dbdb" value="<?php echo $dbdb;?>">&nbsp;&nbsp;esus</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Usuário&nbsp;&nbsp;</td>
      <td valign="top" class="valor">
	  <p class="padrao">&nbsp;&nbsp;<input type="text" name="dbuser" id="dbuser" value="<?php echo $dbuser;?>">&nbsp;&nbsp;postgres</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Senha&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="password" name="dbpass" id="dbpass" value="<?php echo $dbpass;?>">&nbsp;&nbsp;esus</p>
	  </td>
    </tr>
    <tr> 
      <td height="35" colspan="2" valign="top" class="rodape">
	  <button onclick="window.location.href='relatorios.php'">Cancelar</button> | <button type="submit" form="formdb" value="Submit">Gravar</button>
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

	if (document.forms["formdb"]["dbhost"].value == ''){
		mensagem = mensagem + "Campo 'Servidor' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formdb"]["dbport"].value == ''){
		mensagem = mensagem + "Campo 'Porta' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formdb"]["dbdb"].value == ''){
		mensagem = mensagem + "Campo 'Banco de Dados' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formdb"]["dbuser"].value == ''){
		mensagem = mensagem + "Campo 'Usuário' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formdb"]["dbpass"].value == ''){
		mensagem = mensagem + "Campo 'Senha' é necessário!<br>";
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