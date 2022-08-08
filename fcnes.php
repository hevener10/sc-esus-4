<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Arquivo do CNES</title>
  <link href="css/form.css" rel="stylesheet" type="text/css">
</head>

<body>
<form method="post" action="upfcnes.php" id="formcb" name="formcb" enctype="multipart/form-data">
  <table width="481" border="0" cellpadding="0" cellspacing="0">
    <!--DWLayoutTable-->
    <tr> 
      <td height="33" colspan="2" valign="top" class="titulo">Arquivo do CNES</td>
    </tr>
    <tr> 
      <td width="108" height="27" valign="top" class="rotulo">Nome&nbsp;&nbsp;</td>
      <td width="373" valign="top" class="valor"> 
	  <p class="padrao">&nbsp;&nbsp;<input type="file" name="arquivo" id="arquivo">&nbsp;&nbsp;</p>
	  </td>
    </tr>
    <tr> 
      <td height="35" colspan="2" valign="top" class="rodape">
	  <button type="submit" form="formcb" value="Submit">Enviar</button>
	  </td>
    </tr>
    <tr> 
      <td height="51" colspan="2" valign="top" class="mensagens"><p id="mensagem"></p></td>
    </tr>
  </table>
</form>
</body>
</html>