<?php
$_UP['pasta'] = 'xml/';
$_UP['tamanho'] = 1024 * 1024 * 2; // 2Mb
$_UP['extensoes'] = array('zip');
$_UP['renomeia'] = true;
$_UP['erros'][0] = 'Não houve erro';
$_UP['erros'][1] = 'O arquivo no upload é maior do que o limite do PHP';
$_UP['erros'][2] = 'O arquivo ultrapassa o limite de tamanho especifiado no HTML';
$_UP['erros'][3] = 'O upload do arquivo foi feito parcialmente';
$_UP['erros'][4] = 'Não foi feito o upload do arquivo';
$msg_final = "";
if ($_FILES['arquivo']['error'] != 0) {
	die("Não foi possível fazer o upload, erro:<br />" . $_UP['erros'][$_FILES['arquivo']['error']]);
	exit;
}
//$extensao = strtolower(end(explode('.', $_FILES['arquivo']['name'])));
$extensao = strtolower(pathinfo($_FILES['arquivo']['name'], PATHINFO_EXTENSION));
if (array_search($extensao, $_UP['extensoes']) === false) {
	$msg_final .= "Por favor, envie arquivos com as seguintes extensões: jpg, png ou gif<br>";
} else if ($_UP['tamanho'] < $_FILES['arquivo']['size']) {
	$msg_final .= "O arquivo enviado é muito grande, envie arquivos de até 2Mb<br>";
} else {
	if ($_UP['renomeia'] == true) {
		//$nome_final = time().'.jpg';
		$nome_final = 'cnes.zip';
	} else {
		$nome_final = $_FILES['arquivo']['name'];
	}
	if (move_uploaded_file($_FILES['arquivo']['tmp_name'], $_UP['pasta'] . $nome_final)) {
		$zip = new ZipArchive;
		if ($zip->open('xml/cnes.zip') === TRUE) {
			$zip->extractTo('xml/');
			$zip->close();
			if (file_exists('xml/cnes.zip')){
				unlink('xml/cnes.zip');
			}
			if (file_exists('xml/cnes.xml')){
				unlink('xml/cnes.xml');
			}
			if ($handle = opendir('xml/')) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						if (substr($entry,0,13) == 'XmlParaESUS21'){
							rename("xml/".$entry,"xml/cnes.xml");
						}
					}
				}
				closedir($handle);
			}
			//header('location:relatorios.php');
			$msg_final = "Envio do arquivo com sucesso, pode fechar a janela";
		} else {
			$msg_final .= "Não foi possível descompactar o arquivo<br>";
		}
	} else {
		$msg_final .= "Não foi possível enviar o arquivo, tente novamente<br>";
	}
}
echo $msg_final;
?>
