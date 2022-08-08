<?php
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Content-Type: text/html;  charset=utf-8",true);
session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
if (!file_exists("cfg_usr.php")){
	$texto = "<?php
	\$sys_log = array();
	\$sys_log[0]['login'] = \"admin\";
	\$sys_log[0]['senha'] = \"123\";
	\$sys_log[0]['perfil'] = \"admin\";
	\$sys_log[0]['cnes'] = 0;
	\$sys_log[0]['ine'] = 0;
	\$sys_log[0]['filedb'] = \"cfg_db.php\";
	?>\r\n";
	$file = "cfg_usr.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
include('sobre.php');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Acesso ao sistema</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
  <link href="css/barra.css" rel="stylesheet" type="text/css">
</head>

<body>

<table width="907" border="0" cellpadding="0" cellspacing="0" class="main-barra-table">
  <!--DWLayoutTable-->
  <tr> 
    <td width="70" rowspan="2" valign="top" class="main-barra-imagem"><img src="images/logo1.png" width="65" height="65"></td>
    <td width="175" height="35" valign="top" class="main-barra-nome"><?php echo $sobre['nome'];?></td>
    <td width="381" rowspan="2" valign="top" class="main-barra-versiculo">&quot;Portanto 
      dele, por Ele e para Ele s&atilde;o todas as coisas. A Ele<br>
      seja a gl&oacute;ria perpetuamente! Am&eacute;m.&quot;<br>
      Romanos 11:36</td>
    <td colspan="2" valign="top" class="main-barra-usuario"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="35" valign="top" class="main-barra-versao"><?php echo $sobre['versao'];?></td>
    <td width="238" valign="top" class="main-barra-doar">Doe para o projeto.<br>
      Doe no PIX, n&atilde;o permita que o projeto termine.</td>
    <td width="37" valign="top" class="main-bairra-icones"><a href="doar.php" target="_blank"><img src="images/money.png" width="24" height="24" border="0"></a></td>
  </tr>
</table>

<table width="907" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr> 
    <td width="481" height="200" valign="top">

	  
<form method="post" action="check.php" id="formlogin" name="formlogin" onsubmit="return validateForm()">
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Acesso ao sistema</td>
    </tr>
    <tr> 
      <td width="108" height="27" valign="top" class="rotulo">Usuário&nbsp;&nbsp;</td>
      <td width="373" valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="text" name="login" id="login">&nbsp;&nbsp;admin</p>
	  </td>
    </tr>
    <tr> 
      <td height="27" valign="top" class="rotulo">Senha&nbsp;&nbsp;</td>
      <td valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="password" name="senha" id="senha">&nbsp;&nbsp;123</p>
	  </td>
    </tr>
    <tr> 
      <td height="35" colspan="2" valign="top" class="rodape">
	  
	  <button type="submit" form="formlogin" value="Submit">Acessar</button>
	  </td>
    </tr>
    <tr> 
      <td height="51" colspan="2" valign="top" class="mensagens"><p id="mensagem"></p></td>
    </tr>
  </table>
</form>
	  
	  
	  
	  
	</td>
    <td width="426" valign="top">
	
	
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr> 
          <td width="426" height="200">&nbsp;</td>
        </tr>
      </table>
	  
	  
	  
	  </td>
  </tr>
</table>







<script>
function validateForm() {
	var mensagem = "";
	var submit = true;

	if (document.forms["formlogin"]["login"].value == ''){
		mensagem = mensagem + "Campo 'Usuário' é necessário!<br>";
		submit = false;
	}
	if (document.forms["formlogin"]["senha"].value == ''){
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