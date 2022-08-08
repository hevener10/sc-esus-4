<?php
require_once('session.php');
if (file_exists($_SESSION['filedb'])){
	require_once($_SESSION['filedb']);
} else {
	header('location:banco.php?ex=rel_duplicados');
}
if (file_exists("cfg_rel_du.php")){
	require_once('cfg_rel_du.php');
} else {
	header('location:duplicados.php?ex=rel_duplicados');
}

require_once('connect.php');
require_once('functions.php');
require_once('sobre.php');
require_once('dados.php');

// +++++++++++++++++++++++++++++++++++++++++++++
$file = "csv/duplicados_T_01.csv";
if (file_exists($file)){unlink($file);}
$FT = fopen($file,'w');
// +++++++++++++++++++++++++++++++++++++++++++++
$texto = "Seq;CPF;CNS;Nome;DtNascimento;Mae;Pai;Idade;MarcGestante;MarcHipertensa;MarcDiabetica;SeqCid;DtAtuali;Unificado;CNES;INE;MA\r\n";
fwrite($FT, $texto);

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$super_filtro = false;
$mfiltro = isset($_POST["mfiltro"]) ? trim($_POST["mfiltro"]) : 0;
if ($mfiltro > 0){
	$paginacao = 0;
	$vlbusca = isset($_POST["vlbusca"]) ? trim($_POST["vlbusca"]) : 0;
	$cpbusca = isset($_POST["cpbusca"]) ? trim($_POST["cpbusca"]) : 0;
	if ($cpbusca != 0){
		$super_filtro = true;
	}
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$pini = isset($_GET["pini"]) ? trim($_GET["pini"]) : 0;
$num1 = isset($_GET["num1"]) ? trim($_GET["num1"]) : 0;
$cdes = isset($_GET["cdes"]) ? trim($_GET["cdes"]) : 0;
$esq_pag = "";
if ($paginacao > 0){
	$esq_pag = "LIMIT ".$paginacao." OFFSET ".$pini;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// ---------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------
$duplicados = "
	SELECT 
		a.nu_cns,
		a.nu_cpf as nu_cpf_cidadao,
		a.no_cidadao,
		a.dt_nascimento,
		a.no_mae,
		a.no_pai,
		a.co_seq_cidadao,
		a.dt_atualizado,
		a.st_unificado
	FROM
		tb_cidadao AS a 
	INNER JOIN (
		SELECT 
			no_cidadao, 
			dt_nascimento, 
			count(*) 
		FROM 
			tb_cidadao 
		WHERE
			st_faleceu = 0 AND
			st_ativo = 1 AND
			st_unificado = 0
		GROUP BY 
			no_cidadao, 
			dt_nascimento 
		HAVING count(*) > 1
	) AS b 
	ON 
		a.no_cidadao = b.no_cidadao AND a.dt_nascimento = b.dt_nascimento
	ORDER BY a.no_cidadao
".$esq_pag;

// ===========================================================================================================
$linhas = 0;
$rel_pagina_inicio = "
<!DOCTYPE html>
<html>
<head>
  <meta charset=\"UTF-8\">
  <link rel=\"sortcut icon\" href=\"images/favicon.ico\" type=\"image/x-icon\" />
  <title>Relatório de duplicados</title>
  <link href=\"css/rel.css\" rel=\"stylesheet\" type=\"text/css\">
  <link rel=\"stylesheet\" type=\"text/css\" href=\"spinner/slick-loader.min.css\">
  <script src=\"spinner/slick-loader.min.js\"></script>
</head>
<body>
<script type=\"text/javascript\">
	SlickLoader.enable();
</script>
";
echo $rel_pagina_inicio;
// ===========================================================================================================

if ($mcabecalho == 1){
	// ===========================================================================================================
	$rel_cabecalho_1 = "
	<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"cabeca-tabela\">
	  <!--DWLayoutTable-->
	  <tr> 
		<td width=\"150\" rowspan=\"3\" valign=\"top\" class=\"logo\"><img src=\"images/logo_cidade.jpg\" width=\"145\" height=\"145\"></td>
		<td height=\"42\" colspan=\"3\" valign=\"top\" class=\"cabeca-titulo\">".$cbnome."</td>
	  </tr>
	  <tr> 
		<td width=\"18\" height=\"70\" valign=\"top\" class=\"cabeca-borda-baixo\"><!--DWLayoutEmptyCell-->&nbsp;</td>
		<td colspan=\"2\" valign=\"top\" class=\"cabeca-endereco\"> <p> 
				".$cbend1."<br>
				".$cbend2."<br>
				".$cbend3."<br>
				".$cbend4."
		</p></td>
	  </tr>
	  <tr> 
		<td height=\"38\">&nbsp;</td>
		<td width=\"641\" valign=\"top\" class=\"cabeca-fone\">
			".$cbcont1."<br>
			".$cbcont2."
		</td>
		<td width=\"200\" valign=\"top\" class=\"cabeca-pagina\"><!--DWLayoutEmptyCell-->&nbsp;</td>
		</tr>
	</table>
	";
	$linhas = $linhas + 8;
	echo $rel_cabecalho_1;
	// ===========================================================================================================
}

$conta_gravacao = 0;
$tabela_temp = "
CREATE TEMPORARY TABLE tmp_duplicados (
	sequencia serial PRIMARY KEY,
	cns varchar(15),
	cpf varchar(11),
	data_nascimento bigint,
	cidadao_nome varchar(500),
	cidadao_mae varchar(500),
	cidadao_pai varchar(500),
	seq_cid bigint,
	data_atuali bigint,
	unificado int,
	cind_gestante int,
	cind_hipertenso int,
	cind_diabetico int,
	cind_sexo varchar(24),
	cind_micro_area varchar(5),
	cns_resp varchar(15),
	cpf_resp varchar(11),
	cind_inativo int,
	cnes varchar(20),
	nome_unidade varchar(500),
	ine varchar(20),
	nome_equipe varchar(255)
)
";

$run_tt = pg_query($cdb,$tabela_temp);
	
$num_duplicados = 0;
$run_s1 = pg_query($cdb,$duplicados);
$num_duplicados = pg_num_rows($run_s1);
if ($num_duplicados > 0){
	while ($duplicado = pg_fetch_array($run_s1)){
		$CNS = '0';
		$CPF = '0';
		$campo_busca = "";
		$campo_ordem = "";
		$busca_cns = false;
		$busca_cpf = false;
		$busca = false;
		if (strlen(trim($duplicado['nu_cns'])) == 15){
			$busca_cns = true;
			$CNS = trim($duplicado['nu_cns']);
		}
		if (strlen(trim($duplicado['nu_cpf_cidadao'])) == 11){
			$busca_cpf = true;
			$CPF = trim($duplicado['nu_cpf_cidadao']);
		}
		if ($busca_cns && $busca_cpf){
			$campo_busca = "(nu_cns = '".$CNS."' OR nu_cpf_cidadao = '".$CPF."')";
			$campo_ordem = "nu_cns, nu_cpf_cidadao";
			$busca = true;
		} else {
			if ($busca_cns){
				$campo_busca = "nu_cns = '".$CNS."'";
				$campo_ordem = "nu_cpf_cidadao DESC";
				$busca = true;
			}
			if ($busca_cpf){
				$campo_busca = "nu_cpf_cidadao = '".$CPF."'";
				$campo_ordem = "nu_cns DESC";
				$busca = true;
			}
		}
		if ($busca){
			$cindividual = "
				SELECT
					TRIM(FROM t3.nu_cns) as nu_cns,
					TRIM(FROM t3.nu_cpf_cidadao) as nu_cpf_cidadao,
					t3.dt_obito,
					t3.nu_micro_area,
					t3.nu_cns_responsavel,
					t3.nu_cpf_responsavel,
					t3.st_ficha_inativa,
					t3.nu_ine,
					t3.no_equipe,
					t3.nu_cnes,
					t3.no_unidade_saude,
					t3.st_gestante,
					t3.st_hipertensao_arterial,
					t3.st_diabete,
					upper(tb_dim_sexo.ds_sexo) AS ds_sexo,
					int '0' as teste1,
					text '0' as teste2
				FROM
				(
					SELECT
						t2.*,
						tb_dim_unidade_saude.nu_cnes,
						tb_dim_unidade_saude.no_unidade_saude
					FROM
					(
						SELECT 
							t1.*,
							tb_dim_equipe.nu_ine,
							tb_dim_equipe.no_equipe
						FROM
						(
							SELECT
								CASE WHEN nu_cns IS NULL THEN '0'
									ELSE nu_cns END
									nu_cns,
								CASE WHEN nu_cpf_cidadao IS NULL THEN '0'
									ELSE nu_cpf_cidadao END
									nu_cpf_cidadao,
								co_dim_unidade_saude,
								co_dim_equipe,
								co_dim_sexo,
								dt_obito,
								CASE WHEN nu_cns_responsavel IS NULL THEN '0'
									ELSE TRIM(FROM nu_cns_responsavel) END
									nu_cns_responsavel,
								CASE WHEN nu_cpf_responsavel IS NULL THEN '0'
									ELSE TRIM(FROM nu_cpf_responsavel) END
									nu_cpf_responsavel,
								CASE WHEN nu_micro_area IS NULL THEN '00'
									ELSE nu_micro_area END
									nu_micro_area,
								st_ficha_inativa,
								CASE WHEN st_gestante IS NULL THEN '0'
									ELSE st_gestante END
									st_gestante,
								CASE WHEN st_hipertensao_arterial IS NULL THEN '0'
									ELSE st_hipertensao_arterial END
									st_hipertensao_arterial,
								CASE WHEN st_diabete IS NULL THEN '0'
									ELSE st_diabete END
									st_diabete
							FROM 
								tb_fat_cad_individual 
							WHERE 
								".$campo_busca."
							ORDER BY co_dim_tempo DESC
							LIMIT 1
						) AS t1
						LEFT JOIN
							tb_dim_equipe
						ON tb_dim_equipe.co_seq_dim_equipe = t1.co_dim_equipe
					) AS t2
					LEFT JOIN
						tb_dim_unidade_saude
					ON tb_dim_unidade_saude.co_seq_dim_unidade_saude = t2.co_dim_unidade_saude
				) AS t3
				LEFT JOIN
					tb_dim_sexo
				ON tb_dim_sexo.co_seq_dim_sexo = t3.co_dim_sexo
			";
			$cind_cpf = '0';
			$cind_cns = '0';
			$cind_gestante = 0;
			$cind_hipertenso = 0;
			$cind_diabetico = 0;
			$cind_obito = NULL;
			$cind_cns_res = '0';
			$cind_cpf_res = '0';
			$cind_micro_area = '00';
			$cind_inativo = 0;
			$cnes = "0000000";
			$nome_unidade = "SEM UNIDADE SAUDE";
			$ine = "0000000000";
			$nome_equipe = "SEM EQUIPE";
			$nome_sexo = "FEMININO";
			$num_cind = 0;
			$run_s3 = pg_query($cdb,$cindividual);
			$num_cind = pg_num_rows($run_s3);
			if ($num_cind > 0){
				$cind_cpf = trim(pg_fetch_result($run_s3,0,'nu_cpf_cidadao'));
				$cind_cns = trim(pg_fetch_result($run_s3,0,'nu_cns'));
				$cind_gestante = pg_fetch_result($run_s3,0,'st_gestante');
				$cind_hipertenso = pg_fetch_result($run_s3,0,'st_hipertensao_arterial');
				$cind_diabetico = pg_fetch_result($run_s3,0,'st_diabete');
				$cind_obito = pg_fetch_result($run_s3,0,'dt_obito');
				$cind_cns_res = trim(pg_fetch_result($run_s3,0,'nu_cns_responsavel'));
				$cind_cpf_res =trim( pg_fetch_result($run_s3,0,'nu_cpf_responsavel'));
				$cind_micro_area = pg_fetch_result($run_s3,0,'nu_micro_area');
				if (strlen($cind_micro_area) == 1){
					$cind_micro_area = '0'.$cind_micro_area;
				}
				if (strlen($cind_micro_area) == 0 || $cind_micro_area == NULL){
					$cind_micro_area = '00';
				}
				$cind_inativo = pg_fetch_result($run_s3,0,'st_ficha_inativa');
				$cnes = pg_fetch_result($run_s3,0,'nu_cnes');
				$nome_unidade = pg_fetch_result($run_s3,0,'no_unidade_saude');
				$ine = pg_fetch_result($run_s3,0,'nu_ine');
				$nome_equipe = pg_fetch_result($run_s3,0,'no_equipe');
				$nome_sexo = pg_fetch_result($run_s3,0,'ds_sexo');
				if (!$busca_cns){
					if (strlen($cind_cns) == 15){
						$CNS = trim($cind_cns);
						$busca_cns = true;
					}
				}
				if (!$busca_cpf){
					if (strlen($cind_cpf) == 11){
						$CPF = $cind_cpf;
						$busca_cpf = true;
					}
				}
			}

			$data_nascimento = dataint($duplicado['dt_nascimento']); // ou dataint($cidadao_nascimento)
			$cidadao_nome = htmlspecialchars($duplicado['no_cidadao'], ENT_QUOTES);
			$cidadao_mae = htmlspecialchars($duplicado['no_mae'], ENT_QUOTES);
			$cidadao_pai = htmlspecialchars($duplicado['no_pai'], ENT_QUOTES);
			if ($cind_micro_area == '-' || $cind_micro_area == '--' || $cind_micro_area == '0-'){
				$cind_micro_area = '00';
			}
			if ($ine == '-'){
				$ine = '0000000000';
			}
			
			if (strlen($duplicado['dt_atualizado']) > 6){
				$data_atuali = dataint(substr($duplicado['dt_atualizado'],0,10));
			}
			
			$inserir = "
			INSERT INTO tmp_duplicados(
				cns,
				cpf,
				data_nascimento,
				cidadao_nome,
				cidadao_mae,
				cidadao_pai,
				seq_cid,
				data_atuali,
				unificado,
				cind_gestante,
				cind_hipertenso,
				cind_diabetico,
				cind_sexo,
				cind_micro_area,
				cns_resp,
				cpf_resp,
				cind_inativo,
				cnes,
				nome_unidade,
				ine,
				nome_equipe
			) VALUES (
				'".$CNS."',
				'".$CPF."',
				".$data_nascimento.",
				'".$cidadao_nome."',
				'".$cidadao_mae."',
				'".$cidadao_pai."',
				".$duplicado['co_seq_cidadao'].",
				".$data_atuali.",
				".$duplicado['st_unificado'].",
				".$cind_gestante.",
				".$cind_hipertenso.",
				".$cind_diabetico.",
				'".$nome_sexo."',
				'".$cind_micro_area."',
				'".$cind_cns_res."',
				'".$cind_cpf_res."',
				".$cind_inativo.",
				'".$cnes."',
				'".$nome_unidade."',
				'".$ine."',
				'".$nome_equipe."'
			)";
			
			$gravar = true;
			if ($_SESSION['ine'] != 0){
				if ($ine != $_SESSION['ine']){
					$gravar = false;
				}
			}
			if ($_SESSION['cnes'] != 0){
				if ($cnes != $_SESSION['cnes']){
					$gravar = false;
				}
			}
			if ($gravar){
				$run_ins = pg_query($cdb,$inserir);
				$conta_gravacao++;
			}
		}
	}
}


// ===========================================================================================================
$rel_cabecalho_2 = "
<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"relatorio-tabela\">
  <!--DWLayoutTable-->
  <tr> 
    <td width=\"440\" height=\"32\" valign=\"top\" class=\"relatorio-titulo\">Relatório de Duplicados</td>
    <td width=\"392\" valign=\"top\" class=\"relatorio-periodo\">Duplicados ativos</td>
    <td width=\"177\" valign=\"top\" class=\"relatorio-pagina\"><a href=\"duplicados.php?ex=rel_duplicados\"><img src=\"images/config.png\" width=\"24\" height=\"24\" border=\"0\"></a></td>
  </tr>
</table>
";
$linhas = $linhas + 2;
echo $rel_cabecalho_2;
// ===========================================================================================================

// ===========================================================================================================
$rel_dados_inicio = "
<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <!--DWLayoutTable-->
";
echo $rel_dados_inicio;
// ===========================================================================================================

$ordenar = "cidadao_nome";
$show_ordem = "Nome";
if ($ordem == 'C'){
	$ordenar = "cpf";
	$show_ordem = "CPF";
} else {
	if ($ordem == 'S'){
		$ordenar = "cns";
		$show_ordem = "CNS";
	}
}
//++++++++++++++++++++++++
$quebra = "";
$duplicados_final = "
SELECT
	*
FROM
	tmp_duplicados
ORDER BY ".$ordenar;
//++++++++++++++++++++++++

$num_duplicados_final = 0;
$run_gf = pg_query($cdb,$duplicados_final);
$num_duplicados_final = pg_num_rows($run_gf);
$conta_geral = $pini;
$conta_geral_desconto = $cdes;
$numerador_ind1 = $num1;

if ($num_duplicados_final > 0){
	while ($duplicado_final = pg_fetch_array($run_gf)){
		$conta_geral++;
		
		$rCNS = trim($duplicado_final['cns']);
		$rCPF = trim($duplicado_final['cpf']);
		
		$show_gp_inverso = "Unidade";	
		$show_gp_id_inverso = $duplicado_final['cnes'];	
		$show_gp_nm_inverso = $duplicado_final['nome_unidade']." (".$duplicado_final['ine'].")";
		$show_gp_id2_inverso = $duplicado_final['cind_micro_area'];
		$show_gp_nm2_inverso = "Micro-&Aacute;rea";
		// ===========================================================================================================
		$rel_dados_grupo = "
		  <tr> 
			<td width=\"23\" height=\"20\"></td>
			<td width=\"55\" valign=\"top\" class=\"lista-cabeca\">Seq.</td>
			<td width=\"113\" valign=\"top\" class=\"lista-cabeca\">CPF</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-cabeca\">CNS</td>
			<td colspan=\"8\" valign=\"top\" class=\"lista-cabeca\">Nome</td>
			<td width=\"109\" valign=\"top\" class=\"lista-cabeca\">Dta. Nascimento</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-cabeca\">Observações</td>
		  </tr>
		";
		$linhas = $linhas + 1;
		echo $rel_dados_grupo;
		// ===========================================================================================================
		
		$marcado_gestante = "NÃO";
		$marcado_hipertenso = "NÃO";
		$marcado_diabetico = "NÃO";
		if ($duplicado_final['cind_gestante'] == 1){
			$marcado_gestante = "SIM";
		}
		if ($duplicado_final['cind_hipertenso'] == 1){
			$marcado_hipertenso = "SIM";
		}
		if ($duplicado_final['cind_diabetico'] == 1){
			$marcado_diabetico = "SIM";
		}
		
		$estilo_inco = "lista-status";
		$inconsistencias = "";
		$doc_valido = 0;
		if (strlen($rCNS) == 15){
			if (!validaCNS($rCNS)){
				$inconsistencias .= "CNS considerado inválido.<br>";
				$doc_valido++;
			}
		}
		if (strlen($rCPF) == 11){
			if (!validaCPF($rCPF)){
				$inconsistencias .= "CPF considerado inválido.<br>";
				$doc_valido++;
			}
		}
		if ($doc_valido >= 2){
			$inconsistencias .= "-<br>";
		}
		if ($rCNS == '0' && $rCPF == '0'){
			$inconsistencias .= "Sem CNS e sem CPF.<br>-<br>";
		}
		if ($duplicado_final['cind_inativo'] != 0){
			$inconsistencias .= "Cadastro está como inativo (Cad. Ind.).<br>";
		}
		if (strlen($inconsistencias) > 0){
			$estilo_inco = "lista-status-Vermelho";
		}

		$idade = idadeint($duplicado_final['data_nascimento']);
		
		
		// ===========================================================================================================
		$rel_dados_unitario = "
		  <tr> 
			<td height=\"18\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-BB\">".zesq($conta_geral,5)."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".mcpf($rCPF)."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado1-centro-B\">".mcns($rCNS)."</td>
			<td colspan=\"8\" valign=\"top\" class=\"lista-dado1-esquerdo-B\">".$duplicado_final['cidadao_nome']."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".dtshow($duplicado_final['data_nascimento'])."</td>
			<td colspan=\"2\" rowspan=\"6\" valign=\"top\" class=\"".$estilo_inco."\">
			<p>
				".$inconsistencias."
			</p></td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\"><!--DWLayoutEmptyCell-->&nbsp;</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Nome da m&atilde;e</td>
			<td colspan=\"9\" valign=\"top\" class=\"lista-dado2-centro-B\">".$duplicado_final['cidadao_mae']."</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$duplicado_final['seq_cid']."</td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td valign=\"top\"><!--DWLayoutEmptyCell-->&nbsp;</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Nome do pai</td>
			<td colspan=\"9\" valign=\"top\" class=\"lista-dado2-centro-B\">".$duplicado_final['cidadao_pai']."</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\"></td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">".$show_gp_inverso."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_id_inverso."</td>
			<td colspan=\"8\" valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_nm_inverso."</td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Declarada gestante</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_gestante."</td>
			<td colspan=\"3\" valign=\"top\" class=\"lista-sub-dados-B\">Idade</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$idade."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">".$show_gp_nm2_inverso."</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_id2_inverso."</td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Declarada Diab&eacute;tica</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_diabetico."</td>
			<td colspan=\"3\" valign=\"top\" class=\"lista-sub-dados-B\">Declarada Hipertensa</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_hipertenso."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">Dt. Atualização</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".dtshow($duplicado_final['data_atuali'])."</td>
		  </tr>";
		$linhas = $linhas + 13;
		echo $rel_dados_unitario;

		$texto = zesq($conta_geral,5).";".mcpf($rCPF).";".mcns($rCNS).";".str_replace("Ã","A",$duplicado_final['cidadao_nome']).";".dtshow($duplicado_final['data_nascimento']).";".str_replace("Ã","A",$duplicado_final['cidadao_mae']).";".str_replace("Ã","A",$duplicado_final['cidadao_pai']).";".$idade.";".str_replace("Ã","A",$marcado_gestante).";".str_replace("Ã","A",$marcado_hipertenso).";".str_replace("Ã","A",$marcado_diabetico).";".$duplicado_final['seq_cid'].";".dtshow($duplicado_final['data_atuali']).";".$duplicado_final['unificado'].";".$duplicado_final['cnes'].";".$duplicado_final['ine'].";".$duplicado_final['cind_micro_area']."\r\n";
		fwrite($FT, $texto);

	}
}

// ===========================================================================================================

$rel_dados_final = "
  <tr> 
    <td height=\"1\"></td>
    <td></td>
    <td></td>
    <td></td>
    <td width=\"64\"></td>
    <td width=\"19\"></td>
    <td width=\"62\"></td>
    <td></td>
    <td width=\"95\"></td>
    <td width=\"92\"></td>
    <td></td>
    <td></td>
    <td width=\"41\"></td>
    <td></td>
    <td width=\"45\"></td>
    <td></td>
  </tr>
</table>
";
echo $rel_dados_final;
// ===========================================================================================================

// -----------------------------------------------------------------------------------------------------------
$link_pag = "";
if ($paginacao > 0){
	$pini = $pini + $num_duplicados_final;
	if ($num_duplicados_final > 0){
		$link_pag = "<a href=\"rel_duplicados.php?pini=".$pini."\"><img src=\"images/pagina.png\"></a>";
	}
}
// -----------------------------------------------------------------------------------------------------------

$total_geral = $conta_geral;
if ($paginacao > 0){
	$total_geral = $conta_gravacao;
}
// ===========================================================================================================
$rel_rodape = "
<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"rodape-tabela\">
  <!--DWLayoutTable-->
  <tr> 
    <td width=\"1009\" height=\"25\" valign=\"top\" class=\"rodape-texto\">
	".$sobre['nome']." | 
	".$sobre['versao']." | 
	".date('d/m/Y')." | 
	Total de ".$total_geral." duplicados | 
	Ordenado por ".$show_ordem." | ".$dbdb." |
	".$link_pag."
	</td>
  </tr>
</table>
";
$linhas = $linhas + 1;
echo $rel_rodape;
// ===========================================================================================================

echo "
<script type=\"text/javascript\">
	SlickLoader.disable();
</script>
";

// ===========================================================================================================
$rel_pagina_final = "
</body>
</html>
";
echo $rel_pagina_final;
// ===========================================================================================================

fclose($FT);


?>
