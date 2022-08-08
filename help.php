<?php
require_once('session.php');
require_once('sobre.php');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Sobre o sc-SUS</title>
  <link href="css/tab.css" rel="stylesheet" type="text/css">
  <link rel="stylesheet" type="text/css" href="spinner/slick-loader.min.css">
  <script src="spinner/slick-loader.min.js"></script>
</head>
<body>
<script type="text/javascript">
	SlickLoader.enable();
</script>

<table width="903" border="0" cellpadding="0" cellspacing="0">
  <!--DWLayoutTable-->
  <tr> 
    <td width="175" height="30" valign="top" class="dado">Nome</td>
    <td colspan="3" valign="top" class="titulo"><?php echo $sobre['nome'];?></td>
    <td width="263" rowspan="8" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="tabela2">
        <!--DWLayoutTable-->
        <tr> 
          <td width="262" height="30" valign="top" class="dado">V&iacute;deos</td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=D1KqWqM2B1Q">Como instalar o XAMMP?</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=D1KqWqM2B1Q">Como instalar o sc-SUS?</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=u3WqwDfmCxo&t=268s">Como criar usu&aacute;rios?</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=u3WqwDfmCxo">Como trocar a senha do &quot;admin&quot;?</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=D1KqWqM2B1Q&t=416s">Configurando o PHP.ini</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=-SXSB9vevf0">Trocando a porta do Apache</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=49KjZ_MXrWk">PEC e sc-SUS em computadores separados</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="help.php">Como fazer busca ativa?</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><a href="https://www.youtube.com/watch?v=D1KqWqM2B1Q&t=1078s">Como atualizar o sc-SUS?</a></td>
        </tr>
        <tr> 
          <td height="30" valign="top" class="valor"><!--DWLayoutEmptyCell-->&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">Vers&atilde;o</td>
    <td width="236" valign="top" class="valor"><?php echo $sobre['versao'];?></td>
    <td colspan="2" valign="top" class="valor"><?php echo $sobre['alteracao'];?></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">Vers&atilde;o do PEC</td>
    <td colspan="3" valign="top" class="valor"><?php echo $sobre['pec'];?></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">Reposit&oacute;rio de vers&otilde;es</td>
    <td colspan="3" valign="top" class="valor"><a href="<?php echo $sobre['repositorio1'];?>"><?php echo $sobre['repositorio1'];?></a></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">Reposit&oacute;rio alternativo</td>
    <td colspan="3" valign="top" class="valor"><a href="<?php echo $sobre['repositorio2'];?>"><?php echo $sobre['repositorio2'];?></a></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">Youtube Canal</td>
    <td colspan="3" valign="top" class="valor"><a href="<?php echo $sobre['canal'];?>"><?php echo $sobre['canal'];?></a></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">Ferramentas (reposit&oacute;rio)</td>
    <td colspan="3" valign="top" class="valor"><a href="<?php echo $sobre['ferramentas'];?>"><?php echo $sobre['ferramentas'];?></a></td>
  </tr>
  <tr> 
    <td height="120" valign="top" class="dado">Licen&ccedil;a</td>
    <td colspan="3" valign="top" class="valor"><?php echo $sobre['licenca'];?><br> <a href="https://www.apache.org/licenses/LICENSE-2.0"><img src="images/apache.png" width="192" height="94" border="0"></a></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">WhatsApp (grupo)</td>
    <td colspan="2" valign="top" class="valor"><?php echo $sobre['whats'];?></td>
    <td colspan="2" rowspan="3" valign="top" class="valor1"><img src="images/doe1.png" width="405" height="598"></td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="dado">Telegram (grupo)</td>
    <td colspan="2" valign="top" class="valor"><?php echo $sobre['telegram'];?></td>
  </tr>
  <tr> 
    <td height="539" colspan="3" valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0">
        <!--DWLayoutTable-->
        <tr> 
          <td height="30" colspan="3" valign="top" class="dado">Colaboradores</td>
        </tr>
<?php
for ($i=0;$i<count($colaboradores);$i++){
	echo "
        <tr> 
          <td width=\"12\" height=\"30\">&nbsp;</td>
          <td width=\"163\" valign=\"top\" class=\"valor\">".$colaboradores[$i]['funcao']."</td>
          <td width=\"322\" valign=\"top\" class=\"valor\">".$colaboradores[$i]['nome']."</td>
        </tr>
        <tr> 
          <td height=\"30\">&nbsp;</td>
          <td>&nbsp;</td>
          <td valign=\"top\" class=\"valor\">".$colaboradores[$i]['email']."</td>
        </tr>
        <tr> 
          <td height=\"30\"></td>
          <td></td>
          <td valign=\"top\" class=\"valor\">".$colaboradores[$i]['tw']."</td>
        </tr>
	";
}
?>
        <tr> 
          <td height="419"></td>
          <td></td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td height="0"></td>
    <td></td>
    <td width="86"></td>
    <td width="143"></td>
    <td></td>
  </tr>
</table>
<script type="text/javascript">
	SlickLoader.disable();
</script>
</body>
</html>
