<?php
require_once('session.php');
require_once('functions.php');

$ex = isset($_POST["ex"]) ? trim($_POST["ex"]) : 0;

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravardb = isset($_POST["gravardb"]) ? trim($_POST["gravardb"]) : 0;
$dbhost = isset($_POST["dbhost"]) ? trim($_POST["dbhost"]) : "localhost";
$dbport = isset($_POST["dbport"]) ? trim($_POST["dbport"]) : "5433";
$dbdb = isset($_POST["dbdb"]) ? trim($_POST["dbdb"]) : "esus";
$dbuser = isset($_POST["dbuser"]) ? trim($_POST["dbuser"]) : "postgres";
$dbpass = isset($_POST["dbpass"]) ? trim($_POST["dbpass"]) : "esus";
if ($gravardb == 1){
	$texto = "<?php
	\$dbhost = \"".$dbhost."\";
	\$dbport = \"".$dbport."\";
	\$dbdb = \"".$dbdb."\";
	\$dbuser = \"".$dbuser."\";
	\$dbpass = \"".$dbpass."\";
	?>\r\n";
	$file = $_SESSION['filedb'];
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravarcb = isset($_POST["gravarcb"]) ? trim($_POST["gravarcb"]) : 0;
$cbnome = isset($_POST["cbnome"]) ? trim($_POST["cbnome"]) : "Secretaria Municipal de Saúde de Teste";
$cbend1 = isset($_POST["cbend1"]) ? trim($_POST["cbend1"]) : "Avenida Sete de Setembro, número 29387 - Sala 2";
$cbend2 = isset($_POST["cbend2"]) ? trim($_POST["cbend2"]) : "Bairro Matarazzo Caprinio";
$cbend3 = isset($_POST["cbend3"]) ? trim($_POST["cbend3"]) : "CEP 98732-980";
$cbend4 = isset($_POST["cbend4"]) ? trim($_POST["cbend4"]) : "São Matheus do Oeste Mineiro - MG";
$cbcont1 = isset($_POST["cbcont1"]) ? trim($_POST["cbcont1"]) : "+55 (47) 23432-9384 | +55 (47) 12384-3234";
$cbcont2 = isset($_POST["cbcont2"]) ? trim($_POST["cbcont2"]) : "contatosaude@saomatheus.gov.br | www.saomatheuspref.gov.br";
if ($gravarcb == 1){
	$texto = "<?php
	\$cbnome = \"".$cbnome."\";
	\$cbend1 = \"".$cbend1."\";
	\$cbend2 = \"".$cbend2."\";
	\$cbend3 = \"".$cbend3."\";
	\$cbend4 = \"".$cbend4."\";
	\$cbcont1 = \"".$cbcont1."\";
	\$cbcont2 = \"".$cbcont2."\";
	?>\r\n";
	$file = "dados.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravargt = isset($_POST["gravargt"]) ? trim($_POST["gravargt"]) : 0;
$dti = isset($_POST["dti"]) ? dataint(trim($_POST["dti"]),'b') : date('Ymd');
$dtf = isset($_POST["dtf"]) ? dataint(trim($_POST["dtf"]),'b') : datasomadias(date('Ymd'),1);
$dpp = isset($_POST["dpp"]) ? trim($_POST["dpp"]) : 294;
$gpa = isset($_POST["gpa"]) ? trim($_POST["gpa"]) : 0;
$dum = isset($_POST["dum"]) ? trim($_POST["dum"]) : 'U';
$paginacao = isset($_POST["paginacao"]) ? trim($_POST["paginacao"]) : 0;
$ordem = isset($_POST["ordem"]) ? trim($_POST["ordem"]) : 'N';
$grupo = isset($_POST["grupo"]) ? trim($_POST["grupo"]) : 'ine';
$mcabecalho = isset($_POST["mcabecalho"]) ? trim($_POST["mcabecalho"]) : 1;
$mconsultas = isset($_POST["mconsultas"]) ? trim($_POST["mconsultas"]) : 1;
$tbodonto = isset($_POST["tbodonto"]) ? trim($_POST["tbodonto"]) : 0;
if ($gravargt == 1){
	$texto = "<?php
	\$dti = ".$dti.";
	\$dtf = ".$dtf.";
	\$dpp = ".$dpp.";
	\$gpa = ".$gpa.";
	\$dum = '".$dum."';
	\$paginacao = ".$paginacao.";
	\$ordem = '".$ordem."';
	\$grupo = '".$grupo."';
	\$mcabecalho = ".$mcabecalho.";
	\$mconsultas = ".$mconsultas.";
	\$tbodonto = ".$tbodonto.";
	?>\r\n";
	$file = "cfg_rel_g.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravarmu = isset($_POST["gravarmu"]) ? trim($_POST["gravarmu"]) : 0;
$dti = isset($_POST["dti"]) ? dataint(trim($_POST["dti"]),'b') : date('Ymd');
$dtf = isset($_POST["dtf"]) ? dataint(trim($_POST["dtf"]),'b') : datasomadias(date('Ymd'),1);
$gpa = isset($_POST["gpa"]) ? trim($_POST["gpa"]) : 0;
$paginacao = isset($_POST["paginacao"]) ? trim($_POST["paginacao"]) : 0;
$ordem = isset($_POST["ordem"]) ? trim($_POST["ordem"]) : 'N';
$grupo = isset($_POST["grupo"]) ? trim($_POST["grupo"]) : 'ine';
$mcabecalho = isset($_POST["mcabecalho"]) ? trim($_POST["mcabecalho"]) : 1;
$ridade = isset($_POST["ridade"]) ? trim($_POST["ridade"]) : 0;
$cfa = isset($_POST["cfa"]) ? trim($_POST["cfa"]) : 0;
$tbusca = isset($_POST["tbusca"]) ? trim($_POST["tbusca"]) : 12;
$apvac = isset($_POST["apvac"]) ? trim($_POST["apvac"]) : 0;
$idin = isset($_POST["idin"]) ? trim($_POST["idin"]) : 0;
$idfi = isset($_POST["idfi"]) ? trim($_POST["idfi"]) : 150;
$proceds = isset($_POST["proceds"]) ? trim($_POST["proceds"]) : '';
$dt3anos = isset($_POST["dt3anos"]) ? trim($_POST["dt3anos"]) : 'U';
if ($gravarmu == 1){
	$texto = "<?php
	\$dti = ".$dti.";
	\$dtf = ".$dtf.";
	\$gpa = ".$gpa.";
	\$tbusca = ".$tbusca.";
	\$apvac = ".$apvac.";
	\$idin = ".$idin.";
	\$idfi = ".$idfi.";
	\$proceds = '".$proceds."';
	\$paginacao = ".$paginacao.";
	\$ordem = '".$ordem."';
	\$grupo = '".$grupo."';
	\$mcabecalho = ".$mcabecalho.";
	\$ridade = ".$ridade.";
	\$cfa = ".$cfa.";
	\$dt3anos = '".$dt3anos."';
	?>\r\n";
	$file = "cfg_rel_m.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravarhi = isset($_POST["gravarhi"]) ? trim($_POST["gravarhi"]) : 0;
$dti = isset($_POST["dti"]) ? dataint(trim($_POST["dti"]),'b') : date('Ymd');
$dtf = isset($_POST["dtf"]) ? dataint(trim($_POST["dtf"]),'b') : datasomadias(date('Ymd'),1);
$gpa = isset($_POST["gpa"]) ? trim($_POST["gpa"]) : 0;
$paginacao = isset($_POST["paginacao"]) ? trim($_POST["paginacao"]) : 0;
$m12 = isset($_POST["m12"]) ? trim($_POST["m12"]) : 0;
$ordem = isset($_POST["ordem"]) ? trim($_POST["ordem"]) : 'N';
$grupo = isset($_POST["grupo"]) ? trim($_POST["grupo"]) : 'ine';
$mcabecalho = isset($_POST["mcabecalho"]) ? trim($_POST["mcabecalho"]) : 1;
$cfa = isset($_POST["cfa"]) ? trim($_POST["cfa"]) : 0;
if ($gravarhi == 1){
	$texto = "<?php
	\$dti = ".$dti.";
	\$dtf = ".$dtf.";
	\$gpa = ".$gpa.";
	\$m12 = ".$m12.";
	\$paginacao = ".$paginacao.";
	\$ordem = '".$ordem."';
	\$grupo = '".$grupo."';
	\$mcabecalho = ".$mcabecalho.";
	\$cfa = ".$cfa.";
	?>\r\n";
	$file = "cfg_rel_h.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravardi = isset($_POST["gravardi"]) ? trim($_POST["gravardi"]) : 0;
$dti = isset($_POST["dti"]) ? dataint(trim($_POST["dti"]),'b') : date('Ymd');
$dtf = isset($_POST["dtf"]) ? dataint(trim($_POST["dtf"]),'b') : datasomadias(date('Ymd'),1);
$gpa = isset($_POST["gpa"]) ? trim($_POST["gpa"]) : 0;
$paginacao = isset($_POST["paginacao"]) ? trim($_POST["paginacao"]) : 0;
$m12 = isset($_POST["m12"]) ? trim($_POST["m12"]) : 0;
$ordem = isset($_POST["ordem"]) ? trim($_POST["ordem"]) : 'N';
$grupo = isset($_POST["grupo"]) ? trim($_POST["grupo"]) : 'ine';
$mcabecalho = isset($_POST["mcabecalho"]) ? trim($_POST["mcabecalho"]) : 1;
$cfa = isset($_POST["cfa"]) ? trim($_POST["cfa"]) : 0;
if ($gravardi == 1){
	$texto = "<?php
	\$dti = ".$dti.";
	\$dtf = ".$dtf.";
	\$gpa = ".$gpa.";
	\$m12 = ".$m12.";
	\$paginacao = ".$paginacao.";
	\$ordem = '".$ordem."';
	\$grupo = '".$grupo."';
	\$mcabecalho = ".$mcabecalho.";
	\$cfa = ".$cfa.";
	?>\r\n";
	$file = "cfg_rel_d.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravarva = isset($_POST["gravarva"]) ? trim($_POST["gravarva"]) : 0;
$dti = isset($_POST["dti"]) ? dataint(trim($_POST["dti"]),'b') : date('Ymd');
$dtf = isset($_POST["dtf"]) ? dataint(trim($_POST["dtf"]),'b') : datasomadias(date('Ymd'),1);
$gpa = isset($_POST["gpa"]) ? trim($_POST["gpa"]) : 0;
$paginacao = isset($_POST["paginacao"]) ? trim($_POST["paginacao"]) : 0;
$ordem = isset($_POST["ordem"]) ? trim($_POST["ordem"]) : 'N';
$grupo = isset($_POST["grupo"]) ? trim($_POST["grupo"]) : 'ine';
$apvac = isset($_POST["apvac"]) ? trim($_POST["apvac"]) : 0;
$idin = isset($_POST["idin"]) ? trim($_POST["idin"]) : 0;
$idfi = isset($_POST["idfi"]) ? trim($_POST["idfi"]) : 1;
$imbios = isset($_POST["imbios"]) ? trim($_POST["imbios"]) : '33';
$mcabecalho = isset($_POST["mcabecalho"]) ? trim($_POST["mcabecalho"]) : 1;
$cfa = isset($_POST["cfa"]) ? trim($_POST["cfa"]) : 0;
if ($gravarva == 1){
	$texto = "<?php
	\$dti = ".$dti.";
	\$dtf = ".$dtf.";
	\$gpa = ".$gpa.";
	\$apvac = ".$apvac.";
	\$paginacao = ".$paginacao.";
	\$idin = ".$idin.";
	\$idfi = ".$idfi.";
	\$imbios = '".$imbios."';
	\$ordem = '".$ordem."';
	\$grupo = '".$grupo."';
	\$mcabecalho = ".$mcabecalho.";
	\$cfa = ".$cfa.";
	?>\r\n";
	$file = "cfg_rel_v.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$gravardu = isset($_POST["gravardu"]) ? trim($_POST["gravardu"]) : 0;
$gpa = isset($_POST["gpa"]) ? trim($_POST["gpa"]) : 0;
$paginacao = isset($_POST["paginacao"]) ? trim($_POST["paginacao"]) : 0;
$ordem = isset($_POST["ordem"]) ? trim($_POST["ordem"]) : 'N';
$mcabecalho = isset($_POST["mcabecalho"]) ? trim($_POST["mcabecalho"]) : 1;
$cfa = isset($_POST["cfa"]) ? trim($_POST["cfa"]) : 0;
if ($gravardu == 1){
	$texto = "<?php
	\$gpa = ".$gpa.";
	\$paginacao = ".$paginacao.";
	\$ordem = '".$ordem."';
	\$mcabecalho = ".$mcabecalho.";
	\$cfa = ".$cfa.";
	?>\r\n";
	$file = "cfg_rel_du.php";
	if (file_exists($file)){unlink($file);}
	$fconfig = fopen($file,'w');
	fwrite($fconfig, $texto);
	fclose($fconfig);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
include('sobre.php');

if ($ex != 0){
	header("location:".$ex.".php");	
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <link rel="sortcut icon" href="images/favicon.ico" type="image/x-icon" />
  <title>Relatórios</title>
  <link href="css/main.css" rel="stylesheet" type="text/css">
  <link href="css/barra.css" rel="stylesheet" type="text/css">
</head>

<body>
<table width="907" border="0" cellpadding="0" cellspacing="0" class="main-barra-table">
  <!--DWLayoutTable-->
  <tr> 
    <td width="69" rowspan="2" valign="top" class="main-barra-imagem"><img src="images/logo1.png" width="65" height="65"></td>
    <td width="174" height="35" valign="top" class="main-barra-nome"><?php echo $sobre['nome'];?></td>
    <td colspan="11" valign="top" class="main-barra-superior"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="203" valign="top" class="main-barra-usuario"><?php echo $_SESSION['login'];?></td>
    <td width="35" valign="top" class="main-bairra-icones"><a href="banco.php"><img src="images/banco.png" width="24" height="24" border="0"></a></td>
    <td width="35" valign="top" class="main-bairra-icones"><a href="exit.php"><img src="images/exit.png" width="24" height="24" border="0"></a></td>
  </tr>
  <tr> 
    <td height="35" valign="top" class="main-barra-versao"><?php echo $sobre['versao'];?></td>
    <td width="35" valign="top" class="main-bairra-icones"><a href="fcnes.php" target="_blank"><img src="images/reportx.png" width="24" height="24" border="0"></a></td>
    <td width="35" valign="top" class="main-bairra-icones"><a href="cabecalho.php"><img src="images/tools.png" width="24" height="24" border="0"></a></td>
    <td width="35" valign="top" class="main-bairra-icones"><a href="help.php" target="_blank"><img src="images/help.png" width="24" height="24" border="0"></a></td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td width="35" valign="top" class="main-bairra-icones"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td colspan="2" valign="top" class="main-barra-doar">Doe para o projeto.<br>Doe no PIX, n&atilde;o permita que o projeto termine.</td>
    <td valign="top" class="main-bairra-icones"><a href="doar.php" target="_blank"><img src="images/money.png" width="24" height="24" border="0"></a></td>
  </tr>
</table>
<table width="907" border="0" cellpadding="0" cellspacing="0" class="main-table">
  <!--DWLayoutTable-->
  <tr> 
    <td height="150" colspan="2" valign="top" class="main-imagem"><img src="images/gestante.png" width="140" height="140"></td>
    <td colspan="2" valign="top" class="main-imagem"><img src="images/mulher.png" width="140" height="140"></td>
    <td colspan="2" valign="top" class="main-imagem"><img src="images/vacina.png" width="140" height="140"></td>
    <td colspan="2" valign="top" class="main-imagem"><img src="images/hipertenso.png" width="140" height="140"></td>
    <td colspan="2" valign="top" class="main-imagem"><img src="images/diabetico.png" width="140" height="140"></td>
    <td colspan="2" valign="top" class="main-imagem-final"><img src="images/outros.png" width="140" height="140"></td>
  </tr>
  <tr> 
    <td width="30" height="30" valign="top" class="main-config-1"><a href="gestantes.php"><img src="images/config.png" width="24" height="24" border="0"></a></td>
    <td width="120" valign="top" class="main-secao">Gestantes</td>
    <td width="30" valign="top" class="main-config-1"><a href="mulheres.php"><img src="images/config.png" width="24" height="24" border="0"></a></td>
    <td width="120" valign="top" class="main-secao">Mulheres</td>
    <td width="30" valign="top" class="main-config-1"><a href="vacinacao.php"><img src="images/config.png" width="24" height="24" border="0"></a></td>
    <td width="127" valign="top" class="main-secao">Vacinas</td>
    <td width="30" valign="top" class="main-config-1"><a href="hipertensos.php"><img src="images/config.png" width="24" height="24" border="0"></a></td>
    <td width="120" valign="top" class="main-secao">Hipertensos</td>
    <td width="30" valign="top" class="main-config-1"><a href="diabeticos.php"><img src="images/config.png" width="24" height="24" border="0"></a></td>
    <td width="120" valign="top" class="main-secao">Diab&eacute;ticos</td>
    <td width="30" valign="top" class="main-config-1"></td>
    <td width="120" valign="top" class="main-secao-final">Outros</td>
  </tr>
  <tr> 
    <td height="29" valign="top" class="main-report"><a href="rel_gestantes.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Indicadores (1, 2 e 3)</td>
    <td valign="top" class="main-report"><a href="rel_mulheres.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Indicador 4</td>
    <td valign="top" class="main-report"><a href="rel_criancas.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Indicador 5</td>
    <td valign="top" class="main-report"><a href="rel_hipertensos.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Indicador 6</td>
    <td valign="top" class="main-report"><a href="rel_diabeticos.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Indicador 7</td>
    <td valign="top" class="main-report"><a href="rel_duplicados.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto-final">Duplicados</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><a href="filtro.php?rf=G" target="_blank"><img src="images/filtro.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Filtro</td>
    <td valign="top" class="main-report"><a href="filtro.php?rf=M" target="_blank"><img src="images/filtro.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Filtro</td>
    <td valign="top" class="main-report"><a href="filtro.php?rf=C" target="_blank"><img src="images/filtro.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">(Ind. 5) Filtro</td>
    <td valign="top" class="main-report"><a href="filtro.php?rf=H" target="_blank"><img src="images/filtro.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Filtro</td>
    <td valign="top" class="main-report"><a href="filtro.php?rf=D" target="_blank"><img src="images/filtro.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Filtro</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=duplicados_T_01&tp=rel_duplicados" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto-final">Duplicados</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><a href="csv.php?rf=gest_T_01&tp=rel_gestantes" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Gestantes</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=mulher_T_01&tp=rel_mulheres" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Mulheres</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=crianca_T_01&tp=rel_criancas" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">(Ind. 5) Crianças</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=hiper_T_01&tp=rel_hipertensos" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Hipertensos</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=diab_T_01&tp=rel_diabeticos" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Diabéticos</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><a href="csv.php?rf=gest_P_01&tp=rel_gestantes" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Procedimentos</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=mulher_P_01&tp=rel_mulheres" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Procedimentos</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=crianca_V_01&tp=rel_criancas" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">(Ind. 5) Vacinas</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=hiper_P_01&tp=rel_hipertensos" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Procedimentos</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=diab_P_01&tp=rel_diabeticos" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Procedimentos</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><a href="csv.php?rf=gest_C_01&tp=rel_gestantes" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Consultas</td>
    <td valign="top" class="main-report"><a href="rel_mexames.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Exames</td>
    <td valign="top" class="main-report"><a href="rel_vacinas.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Vacinas</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=hiper_C_01&tp=rel_hipertensos" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Consultas</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=diab_C_01&tp=rel_diabeticos" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Consultas</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=mexames_T_01&tp=rel_mexames" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Exames (Lista)</td>
    <td valign="top" class="main-report"><a href="filtro.php?rf=V" target="_blank"><img src="images/filtro.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Filtro</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=mexames_P_01&tp=rel_mexames" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Exames (Proced.)</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=vacinado_T_01&tp=rel_vacinas" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Vacinados</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=vacinado_V_01&tp=rel_vacinas" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">Vacinas</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><a href="rel_covid1.php" target="_blank"><img src="images/report.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">COVID-19 (Transparência)</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><a href="csv.php?rf=covid_T_01&tp=rel_covid1" target="_blank"><img src="images/csv2.png" width="24" height="24" border="0"></a></td>
    <td valign="top" class="main-report-texto">COVID-19 (Transparência)</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-report-texto-final"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
  <tr> 
    <td height="30" valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td height="30" valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td height="30" valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td height="30" valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td height="30" valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td height="30" valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
    <td valign="top" class="main-borda-cima"><!--DWLayoutEmptyCell-->&nbsp;</td>
  </tr>
</table>
</body>
</html>