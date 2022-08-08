<?php
require_once('session.php');
if (file_exists("cfg_db.php")){
	require_once('cfg_db.php');
} else {
	header('location:banco.php');
}
if (file_exists("cfg_rel_m.php")){
	require_once('cfg_rel_m.php');
} else {
	header('location:mulheres.php');
}

require_once('connect.php');
require_once('functions.php');
require_once('sobre.php');
require_once('dados.php');

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$pini = isset($_GET["pini"]) ? trim($_GET["pini"]) : 0;
$proxima_pag = 0;
if ($pini == 0){
	$proxima_pag = $paginacao + $pini - 1;
} else {
	$proxima_pag = $paginacao + $pini;
}
$esq_pag = "";
$link_pag = "";
if ($paginacao > 0){
	$esq_pag = "LIMIT ".$paginacao." OFFSET ".$pini;
	$link_pag = "<a href=\"rel_mulheres.php?pini=".$proxima_pag."\">Página ></a>";
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$show_tabelas = "";
if ($tb1 == 1) $show_tabelas .= "TB1 ";
if ($tb2 == 1) $show_tabelas .= "TB2 ";
if ($tb3 == 1) $show_tabelas .= "TB3 ";
if ($tb4 == 1) $show_tabelas .= "TB4 ";
if ($tb5 == 1) $show_tabelas .= "TB5 ";
if ($tb6 == 1) $show_tabelas .= "TB6 ";

if ($gpa == 1){
	$dti = datasomadias(date('Ymd'),180,'-');
	$dtf = date('Ymd');
}

if ($ridade >= 0 && $ridade <= 9){
	switch ($ridade) {
		case 0:
			$idade_inicial = 25;
			$idade_final = 64;
			break;
		case 1:
			$idade_inicial = 25;
			$idade_final = 30;
			break;
		case 2:
			$idade_inicial = 31;
			$idade_final = 40;
			break;
		case 3:
			$idade_inicial = 41;
			$idade_final = 50;
			break;
		case 4:
			$idade_inicial = 51;
			$idade_final = 60;
			break;
		case 5:
			$idade_inicial = 61;
			$idade_final = 64;
			break;
		case 6:
			$idade_inicial = 25;
			$idade_final = 40;
			break;
		case 7:
			$idade_inicial = 40;
			$idade_final = 60;
			break;
		case 8:
			$idade_inicial = 50;
			$idade_final = 64;
			break;
		case 9:
			$idade_inicial = 25;
			$idade_final = 35;
			break;
	}
	$idade_inicial++;
	$idade_final++;
} else {
	$idade_inicial = $ridade + 1;
	$idade_final = $ridade + 1;
}



$a_dti = (int) substr($dti,0,4);
$a_dtix = $a_dti;
$m_dti = substr($dti,4,2);
$d_dti = substr($dti,6,2);
$a_dti = $a_dti - $idade_final;
$dti_nas = $a_dti.'-'.$m_dti.'-'.$d_dti;

$a_dtf = (int) substr($dtf,0,4);
$a_dtfx = $a_dtf;
$m_dtf = substr($dtf,4,2);
$d_dtf = substr($dtf,6,2);
$a_dtf = $a_dtf - $idade_inicial;
$dtf_nas = $a_dtf.'-'.$m_dtf.'-'.$d_dtf;

// ---------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------
$duplicados = array();
$conta_duplicados = 0;

$mulheres = "
SELECT DISTINCT ON (nu_cns, nu_cpf_cidadao)
	CASE WHEN nu_cns IS NULL THEN '0'
		ELSE nu_cns END
	nu_cns,
	CASE WHEN nu_cpf IS NULL THEN '0'
		ELSE nu_cpf END
	nu_cpf_cidadao,
	dt_nascimento, 
	no_sexo, 
	CASE WHEN st_faleceu IS NULL THEN '0'
		ELSE st_faleceu END
	st_faleceu,
	no_cidadao,
	no_mae, 
	dt_obito, 
	st_ativo
FROM 
	tb_cidadao 
WHERE 
	no_sexo = 'FEMININO' AND
	(dt_nascimento >= '".$dti_nas."' AND dt_nascimento <= '".$dtf_nas."') 
ORDER BY nu_cns, nu_cpf_cidadao 
".$esq_pag;

// ===========================================================================================================
$linhas = 0;
$rel_pagina_inicio = "
<!DOCTYPE html>
<html>
<head>
  <meta charset=\"UTF-8\">
  <link rel=\"sortcut icon\" href=\"images/favicon.ico\" type=\"image/x-icon\" />
  <title>Relatório de mulheres</title>
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

$periodo_show = "Análise entre ".dtshow($dti)." e ".dtshow($dtf);

// ===========================================================================================================
$rel_cabecalho_2 = "
<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"relatorio-tabela\">
  <!--DWLayoutTable-->
  <tr> 
    <td width=\"440\" height=\"32\" valign=\"top\" class=\"relatorio-titulo\">Relatório de Mulheres</td>
    <td width=\"392\" valign=\"top\" class=\"relatorio-periodo\">".$periodo_show."</td>
    <td width=\"177\" valign=\"top\" class=\"relatorio-pagina\">".$link_pag."</td>
  </tr>
</table>
";
$linhas = $linhas + 2;
echo $rel_cabecalho_2;
// ===========================================================================================================

$tabela_temp = "
CREATE TEMPORARY TABLE tmp_mulheres (
	sequencia serial PRIMARY KEY,
	cns varchar(15),
	cpf varchar(11),
	data_nascimento bigint,
	cidadao_ativo int,
	data_falecimento bigint,
	cidadao_nome varchar(500),
	cidadao_mae varchar(500),
	cidadao_faleceu int,
	cidadao_sexo varchar(24),
	cind_gestante int,
	cind_hipertenso int,
	cind_diabetico int,
	cind_sexo varchar(24),
	cind_micro_area varchar(3),
	cind_inativo int,
	cnes varchar(20),
	nome_unidade varchar(500),
	ine varchar(20),
	nome_equipe varchar(255)
)
";
$run_tt = pg_query($cdb,$tabela_temp);
	
$numerador_ind4 = 0;
	
$num_mulheres = 0;
$run_s1 = pg_query($cdb,$mulheres);
$num_mulheres = pg_num_rows($run_s1);
if ($num_mulheres > 0){
	while ($mulher = pg_fetch_array($run_s1)){
		$CNS = '0';
		$CPF = '0';
		$campo_busca = "";
		$campo_ordem = "";
		$busca_cns = false;
		$busca_cpf = false;
		$busca = false;
		if (strlen(trim($mulher['nu_cns'])) == 15){
			$busca_cns = true;
			$CNS = trim($mulher['nu_cns']);
		}
		if (strlen(trim($mulher['nu_cpf_cidadao'])) == 11){
			$busca_cpf = true;
			$CPF = trim($mulher['nu_cpf_cidadao']);
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
					t3.nu_cns,
					t3.nu_cpf_cidadao,
					t3.dt_obito,
					t3.nu_micro_area,
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
								CASE WHEN nu_micro_area IS NULL THEN '0'
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
			$cind_micro_area = '0';
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
				$cind_micro_area = pg_fetch_result($run_s3,0,'nu_micro_area');
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
			//*******************************************************************************************
			$duplicado = false;
			if (strlen($CNS) == 15){
				if (false !== $key = array_search($CNS, array_column($duplicados, 'cns'))) {
					$duplicado = true;
					if (strlen($CPF) == 11){
						if (strlen($duplicados[$key]['cpf']) != 11){
							$duplicados[$key]['cpf'] = $CPF;
						}
					}
				}
			}
			if (strlen($CPF) == 11){
				if (false !== $key = array_search($CPF, array_column($duplicados, 'cpf'))) {
					$duplicado = true;
					if (strlen($CNS) == 15){
						if (strlen($duplicados[$key]['cns']) != 15){
							$duplicados[$key]['cns'] = $CNS;
						}
					}
				}
			}
			if (!$duplicado){
				$duplicados[$conta_duplicados]['cns'] = $CNS;
				$duplicados[$conta_duplicados]['cpf'] = $CPF;
				$conta_duplicados++;
			}
			//*******************************************************************************************
				
			if (!$duplicado){
				$data_nascimento = dataint($mulher['dt_nascimento']);
				$data_falecimento = "0";
				if (strlen(trim($mulher['dt_obito'])) > 5){
					$data_falecimento = dataint($mulher['dt_obito']);
				} else {
					if (strlen(trim($cind_obito)) > 5){
						$data_falecimento = dataint($cind_obito);
					}
				}

				$inserir = "
				INSERT INTO tmp_mulheres(
					cns,
					cpf,
					data_nascimento,
					cidadao_ativo,
					data_falecimento,
					cidadao_nome,
					cidadao_mae,
					cidadao_faleceu,
					cidadao_sexo,
					cind_gestante,
					cind_hipertenso,
					cind_diabetico,
					cind_sexo,
					cind_micro_area,
					cind_inativo,
					cnes,
					nome_unidade,
					ine,
					nome_equipe
				) VALUES (
					'".$mulher['nu_cns']."',
					'".$mulher['nu_cpf_cidadao']."',
					".$data_nascimento.",
					".$mulher['st_ativo'].",
					".$data_falecimento.",
					'".$mulher['no_cidadao']."',
					'".$mulher['no_mae']."',
					".$mulher['st_faleceu'].",
					'".$mulher['no_sexo']."',
					".$cind_gestante.",
					".$cind_hipertenso.",
					".$cind_diabetico.",
					'".$nome_sexo."',
					'".$cind_micro_area."',
					".$cind_inativo.",
					'".$cnes."',
					'".$nome_unidade."',
					'".$ine."',
					'".$nome_equipe."'
				)";
				
				if ($cfa == 0){
					$run_ins = pg_query($cdb,$inserir);
				} else {
					if ($cind_micro_area != '0' && $cind_micro_area != '00' && $cind_micro_area != 'FA'){
						$run_ins = pg_query($cdb,$inserir);
					}
				}
			}
		}
	}
}

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
	} else {
		if ($ordem == 'DC'){
			$ordenar = "data_nascimento";
			$show_ordem = "Idade decrescente";
		} else {
			if ($ordem == 'DD'){
				$ordenar = "data_nascimento DESC";
				$show_ordem = "Idade crescente";
			}
		}
	}
}
//++++++++++++++++++++++++
$ordena_grupo = $grupo.",";
if ($grupo == "SG"){
	$ordena_grupo = "";
}
$quebra = "";
$conta_geral = $pini;
$conta_geral_desconto = 0;
$gestantes_final = "
SELECT
	*
FROM
	tmp_mulheres
WHERE
	cidadao_faleceu = 0
ORDER BY ".$ordena_grupo." ".$ordenar;
//++++++++++++++++++++++++

$num_mulheres_final = 0;
$run_mf = pg_query($cdb,$gestantes_final);
$num_mulheres_final = pg_num_rows($run_mf);
if ($num_mulheres_final > 0){
	while ($mulher_final = pg_fetch_array($run_mf)){
		/*
		
		$mulher_final['sequencia']
		$mulher_final['cns']
		$mulher_final['cpf']
		$mulher_final['data_nascimento']
		$mulher_final['cidadao_ativo']
		$mulher_final['data_falecimento']
		$mulher_final['cidadao_nome']
		$mulher_final['cidadao_mae']
		$mulher_final['cidadao_faleceu']
		$mulher_final['cidadao_sexo']
		$mulher_final['cind_sexo']
		$mulher_final['cind_micro_area']
		$mulher_final['cind_inativo']
		$mulher_final['cnes']
		$mulher_final['nome_unidade']
		$mulher_final['ine']
		$mulher_final['nome_equipe']
		
		*/
		
		$conta_geral++;
		
		$rCNS = $mulher_final['cns'];
		$rCPF = $mulher_final['cpf'];
		
		if (strlen($rCNS) != 15){
			if (false !== $key = array_search($rCPF, array_column($duplicados, 'cpf'))) {
				if (strlen($duplicados[$key]['cns']) == 15){
					$rCNS = $duplicados[$key]['cns'];
				}
			}
		}
		if (strlen($rCPF) != 11){
			if (false !== $key = array_search($rCNS, array_column($duplicados, 'cns'))) {
				if (strlen($duplicados[$key]['cpf']) == 11){
					$rCPF = $duplicados[$key]['cpf'];
				}
			}
		}
		
		if ($grupo != "SG"){
			$grupo_id = $mulher_final[$grupo];
			if ($quebra != $grupo_id){
				$grupo_nome = "Micro-Área";
				$show_gp_inverso = "Unidade";	
				$show_gp_id_inverso = $mulher_final['cnes'];	
				$show_gp_nm_inverso = $mulher_final['nome_unidade'];
				$show_gp_id2_inverso = $mulher_final['ine'];
				$show_gp_nm2_inverso = "Equipe";
				if ($grupo == "ine"){
					$grupo_nome = $mulher_final['nome_equipe'];	
					$show_gp_id2_inverso = $mulher_final['cind_micro_area'];
					$show_gp_nm2_inverso = "Micro-&Aacute;rea";
				}
				if ($grupo == "cnes"){
					$grupo_nome = $mulher_final['nome_unidade'];
					$show_gp_inverso = "Equipe";	
					$show_gp_id_inverso = $mulher_final['ine'];	
					$show_gp_nm_inverso = $mulher_final['nome_equipe'];
					$show_gp_id2_inverso = $mulher_final['cind_micro_area'];
					$show_gp_nm2_inverso = "Micro-&Aacute;rea";
				}
				// ===========================================================================================================
				$rel_dados_grupo = "
				  <tr> 
					<td height=\"19\" colspan=\"12\" valign=\"top\" class=\"lista-equipe\">[ ".$grupo_id." ] ".$grupo_nome."</td>
					<td width=\"174\" valign=\"top\" class=\"lista-equipe2\">.</td>
				  </tr>
				  <tr> 
					<td width=\"23\" height=\"20\"></td>
					<td width=\"55\" valign=\"top\" class=\"lista-cabeca\">Seq.</td>
					<td width=\"113\" valign=\"top\" class=\"lista-cabeca\">CPF</td>
					<td colspan=\"2\" valign=\"top\" class=\"lista-cabeca\">CNS</td>
					<td colspan=\"5\" valign=\"top\" class=\"lista-cabeca\">Nome</td>
					<td width=\"109\" valign=\"top\" class=\"lista-cabeca\">Dta. Nascimento</td>
					<td colspan=\"2\" valign=\"top\" class=\"lista-cabeca\">Observações</td>
				  </tr>
				";
				$linhas = $linhas + 2;
				echo $rel_dados_grupo;
				// ===========================================================================================================
				$quebra = $grupo_id;
			}
		} else {
			$show_gp_inverso = "Unidade";	
			$show_gp_id_inverso = $mulher_final['cnes'];	
			$show_gp_nm_inverso = $mulher_final['nome_unidade']." (".$mulher_final['ine'].")";
			$show_gp_id2_inverso = $mulher_final['cind_micro_area'];
			$show_gp_nm2_inverso = "Micro-&Aacute;rea";
			// ===========================================================================================================
			$rel_dados_grupo = "
				  <tr> 
					<td height=\"19\" colspan=\"12\" valign=\"top\" class=\"lista-equipe\">.</td>
					<td width=\"174\" valign=\"top\" class=\"lista-equipe2\">.</td>
				  </tr>
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
		}

		$campo_busca = "";
		if (strlen($rCNS) == 15 && strlen($rCPF) == 11){
			$campo_busca = "(nu_cns = '".$rCNS."' OR nu_cpf_cidadao = '".$rCPF."')";
		} else {
			if (strlen($rCNS) == 15){
				$campo_busca = "nu_cns = '".$rCNS."'";
			}
			if (strlen($rCPF) == 11){
				$campo_busca = "nu_cpf_cidadao = '".$rCPF."'";
			}
		}

		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************

		$a_atual = $a_dtfx;
		if ($a_dtix > $a_dtfx){
			$a_atual = $a_dtix;
		}
		$a_atual = $a_atual - 3;
		$dt_3anos = $a_atual.substr($mulher_final['data_nascimento'],4,2).substr($mulher_final['data_nascimento'],6,2);

		$exame_cito = false;
		$dt_ex_cito = 0;
		$tbs_exames_c = '';
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		if ($tb1 == 1){
			$select_tb1_h = "
				SELECT
					*
				FROM
				(
					SELECT
						t1.*,
						tb_dim_cbo.nu_cbo
					FROM
					(
						SELECT 
							co_dim_tempo,
						CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
							ELSE co_dim_cbo_1 END
							co_dim_cbo
						FROM
							tb_fat_atendimento_individual
						WHERE
							".$campo_busca." AND
							(co_dim_tempo >= ".$dt_3anos." AND co_dim_tempo <= ".$dtf.") AND
							ds_filtro_proced_avaliados LIKE ANY (array['%|ABPG010|%','%|0201020033|%'])
						ORDER BY co_dim_tempo DESC
					) AS t1
					LEFT JOIN
						tb_dim_cbo
					ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
				) AS t2
				WHERE
					nu_cbo LIKE ANY (array['2251%', '2252%', '2253%', '2231%', '2235%'])
				LIMIT 1
			";
			if (!$exame_cito){
				$run_st1 = pg_query($cdb,$select_tb1_h);
				if (pg_num_rows($run_st1)){
					$exame_cito = true;
					$dt_ex_cito = pg_fetch_result($run_st1,0,'co_dim_tempo');
					$tbs_exames_c .= 'TB1 ';
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		if ($tb2 == 1){
			$select_tb2_h = "
				SELECT
					*
				FROM
				(
					SELECT
						t1.*,
						tb_dim_cbo.nu_cbo
					FROM
					(
						SELECT 
							co_dim_tempo,
						CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
							ELSE co_dim_cbo_1 END
							co_dim_cbo
						FROM
							tb_fat_atendimento_individual
						WHERE
							".$campo_busca." AND
							(co_dim_tempo >= ".$dt_3anos." AND co_dim_tempo <= ".$dtf.") AND
							ds_filtro_proced_solicitados LIKE ANY (array['%|ABPG010|%','%|0201020033|%'])
						ORDER BY co_dim_tempo DESC
					) AS t1
					LEFT JOIN
						tb_dim_cbo
					ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
				) AS t2
				WHERE
					nu_cbo LIKE ANY (array['2251%', '2252%', '2253%', '2231%', '2235%'])
				LIMIT 1
			";
			if (!$exame_cito){
				$run_st2 = pg_query($cdb,$select_tb2_h);
				if (pg_num_rows($run_st2)){
					$exame_cito = true;
					$dt_ex_cito = pg_fetch_result($run_st2,0,'co_dim_tempo');
					$tbs_exames_c .= 'TB2 ';
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		if ($tb3 == 1){
			$select_tb3_h = "
				SELECT
					*
				FROM
				(
					SELECT
						t1.*,
						tb_dim_cbo.nu_cbo
					FROM
					(
						SELECT 
							co_dim_tempo,
							co_dim_cbo
						FROM
							tb_fat_proced_atend
						WHERE
							".$campo_busca." AND
							(co_dim_tempo >= ".$dt_3anos." AND co_dim_tempo <= ".$dtf.") AND
							ds_filtro_procedimento LIKE ANY (array['%|ABPG010|%','%|0201020033|%'])
						ORDER BY co_dim_tempo DESC
					) AS t1
					LEFT JOIN
						tb_dim_cbo
					ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
				) AS t2
				WHERE
					nu_cbo LIKE ANY (array['2251%', '2252%', '2253%', '2231%', '2235%'])
				LIMIT 1
			";
			if (!$exame_cito){
				$run_st3 = pg_query($cdb,$select_tb3_h);
				if (pg_num_rows($run_st3)){
					$exame_cito = true;
					$dt_ex_cito = pg_fetch_result($run_st3,0,'co_dim_tempo');
					$tbs_exames_c .= 'TB3 ';
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		if ($tb4 == 1){
			$select_tb4_h = "
				SELECT
					*
				FROM
				(
					SELECT
						t1.*,
						tb_dim_cbo.nu_cbo
					FROM
					(
						SELECT 
							co_dim_tempo,
							co_dim_cbo
						FROM 
							tb_fat_proced_atend_proced
						WHERE
							".$campo_busca." AND
							(co_dim_tempo >= ".$dt_3anos." AND co_dim_tempo <= ".$dtf.") AND
							co_dim_procedimento IN
							(
								SELECT 
									co_seq_dim_procedimento
								FROM 
									tb_dim_procedimento
								WHERE
									co_proced IN ('ABPG010','0201020033')
							)
						ORDER BY co_dim_tempo DESC
					) AS t1
					LEFT JOIN
						tb_dim_cbo
					ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
				) AS t2
				WHERE
					nu_cbo LIKE ANY (array['2251%', '2252%', '2253%', '2231%', '2235%'])
				LIMIT 1
			";
			if (!$exame_cito){
				$run_st4 = pg_query($cdb,$select_tb4_h);
				if (pg_num_rows($run_st4)){
					$exame_cito = true;
					$dt_ex_cito = pg_fetch_result($run_st4,0,'co_dim_tempo');
					$tbs_exames_c .= 'TB4 ';
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		if ($tb5 == 1){
			$select_tb5_h = "
				SELECT
					*
				FROM
				(
					SELECT
						t1.*,
						tb_dim_cbo.nu_cbo
					FROM
					(
						SELECT 
							co_dim_tempo,
						CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
							ELSE co_dim_cbo_1 END
							co_dim_cbo
						FROM 
							tb_fat_atd_ind_procedimentos
						WHERE
							".$campo_busca." AND
							(co_dim_tempo >= ".$dt_3anos." AND co_dim_tempo <= ".$dtf.") AND
							co_dim_procedimento_avaliado IN
							(
								SELECT 
									co_seq_dim_procedimento
								FROM 
									tb_dim_procedimento
								WHERE
									co_proced IN ('ABPG010','0201020033')
							)
						ORDER BY co_dim_tempo DESC
					) AS t1
					LEFT JOIN
						tb_dim_cbo
					ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
				) AS t2
				WHERE
					nu_cbo LIKE ANY (array['2251%', '2252%', '2253%', '2231%', '2235%'])
				LIMIT 1
			";
			if (!$exame_cito){
				$run_st5 = pg_query($cdb,$select_tb5_h);
				if (pg_num_rows($run_st5)){
					$exame_cito = true;
					$dt_ex_cito = pg_fetch_result($run_st5,0,'co_dim_tempo');
					$tbs_exames_c .= 'TB5 ';
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		if ($tb6 == 1){
			$select_tb6_h = "
				SELECT
					*
				FROM
				(
					SELECT
						t1.*,
						tb_dim_cbo.nu_cbo
					FROM
					(
						SELECT 
							co_dim_tempo,
						CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
							ELSE co_dim_cbo_1 END
							co_dim_cbo
						FROM 
							tb_fat_atd_ind_procedimentos
						WHERE
							".$campo_busca." AND
							(co_dim_tempo >= ".$dt_3anos." AND co_dim_tempo <= ".$dtf.") AND
							co_dim_procedimento_solicitado IN
							(
								SELECT 
									co_seq_dim_procedimento
								FROM 
									tb_dim_procedimento
								WHERE
									co_proced IN ('ABPG010','0201020033')
							)
						ORDER BY co_dim_tempo DESC
					) AS t1
					LEFT JOIN
						tb_dim_cbo
					ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
				) AS t2
				WHERE
					nu_cbo LIKE ANY (array['2251%', '2252%', '2253%', '2231%', '2235%'])
				LIMIT 1
			";
			if (!$exame_cito){
				$run_st6 = pg_query($cdb,$select_tb6_h);
				if (pg_num_rows($run_st6)){
					$exame_cito = true;
					$dt_ex_cito = pg_fetch_result($run_st6,0,'co_dim_tempo');
					$tbs_exames_c .= 'TB6 ';
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------

		
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************

		$marcado_gestante = "NÃO";
		$marcado_hipertenso = "NÃO";
		$marcado_diabetico = "NÃO";
		if ($mulher_final['cind_gestante'] == 1){
			$marcado_gestante = "SIM";
		}
		if ($mulher_final['cind_hipertenso'] == 1){
			$marcado_hipertenso = "SIM";
		}
		if ($mulher_final['cind_diabetico'] == 1){
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
			$conta_geral_desconto++;
		}
		if ($mulher_final['cind_sexo'] != 'FEMININO'){
			$inconsistencias .= "Cadastro está como do sexo Masculino.<br>";
		}
		$inativo2 = 0;
		if ($mulher_final['cidadao_ativo'] != 1){
			$inconsistencias .= "Cadastro está como inativo (Cidadao).<br>";
			$inativo2++;
		}
		if ($mulher_final['cind_inativo'] != 0){
			$inconsistencias .= "Cadastro está como inativo (Cad. Ind.).<br>";
			$inativo2++;
		}
		if ($inativo2 >= 2){
			$conta_geral_desconto++;
		}
		$faleceu2 = 0;
		if ($mulher_final['cidadao_faleceu'] != 0){
			$inconsistencias .= "Faleceu (Cidadao)<br>";
			$faleceu2++;
		}
		if (strlen($mulher_final['data_falecimento']) > 5){
			$inconsistencias .= "Faleceu em ".dtshow($mulher_final['data_falecimento'])."<br>";
			$faleceu2++;
		}
		if ($faleceu2 >= 2){
			$conta_geral_desconto++;
		}
		if (strlen($inconsistencias) > 0){
			$estilo_inco = "lista-status-Vermelho";
		}
		
		$idade = idadeint($mulher_final['data_nascimento']);
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		$exame_citopatologico = "NÃO";
		$estilo_excito = "indicador-c1-NAO";
		$estilo_excito_indicador = "indicador-titulo-Vermelho-B";
		$show_c_data = "";
		if ($exame_cito){
			$exame_citopatologico = "SIM";
			$estilo_excito = "indicador-c1-SIM";
			$estilo_excito_indicador = "indicador-titulo-Verde-B";
			$show_c_data = dtshow($dt_ex_cito);
			$numerador_ind4++;
		}
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// ===========================================================================================================
		$rel_dados_unitario = "
		  <tr> 
			<td height=\"18\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-BB\">".zesq($conta_geral,5)."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".mcpf($rCPF)."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado1-centro-B\">".mcns($rCNS)."</td>
			<td colspan=\"5\" valign=\"top\" class=\"lista-dado1-esquerdo-B\">".$mulher_final['cidadao_nome']."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".dtshow($mulher_final['data_nascimento'])."</td>
			<td colspan=\"2\" rowspan=\"5\" valign=\"top\" class=\"".$estilo_inco."\"><p>
			".$inconsistencias."
			</p></td>
		  </tr>
		  <tr> 
			<td height=\"19\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\"><!--DWLayoutEmptyCell-->&nbsp;</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Nome da m&atilde;e</td>
			<td colspan=\"7\" valign=\"top\" class=\"lista-dado2-centro-B\">".$mulher_final['cidadao_mae']."</td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">".$show_gp_inverso."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_id_inverso."</td>
			<td colspan=\"5\" valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_nm_inverso."</td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">An&aacute;lise do proced. desde</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".dtshow($dt_3anos)."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">Declarada Diab&eacute;tica</td>
			<td width=\"69\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_diabetico."</td>
			<td width=\"84\" valign=\"top\" class=\"lista-sub-dados-B\">".$show_gp_nm2_inverso."</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_id2_inverso."</td>
		  </tr>
		  <tr> 
			<td height=\"19\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">.</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">.</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">Declarada Hipertensa</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_hipertenso."</td>
			<td valign=\"top\" class=\"lista-sub-dados-B\">Idade</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$idade."</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" rowspan=\"2\" valign=\"top\" class=\"".$estilo_excito_indicador."\">Indicador 4 [ ".$exame_citopatologico." ]</td>
			<td colspan=\"3\" rowspan=\"2\" valign=\"top\" class=\"indicador-c1-B\">Exame citopatol&oacute;gico (25 - 64 anos)</td>
			<td colspan=\"4\" rowspan=\"2\" valign=\"top\" class=\"indicador-c1-centro-X-B\">".$show_tabelas."</td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-c1\">Procedimento em ".$show_c_data."</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-cx-B\">Tabela(s) ".$tbs_exames_c."</td>
		  </tr>
		  ";
		$linhas = $linhas + 13;
		echo $rel_dados_unitario;
	}
}

// ===========================================================================================================
$rel_dados_final = "
  <tr> 
    <td height=\"1\"></td>
    <td></td>
    <td></td>
    <td width=\"86\"></td>
    <td width=\"64\"></td>
    <td width=\"19\"></td>
    <td width=\"106\"></td>
    <td width=\"62\"></td>
    <td></td>
    <td></td>
    <td></td>
    <td width=\"45\"></td>
    <td></td>
  </tr>
</table>
";
echo $rel_dados_final;
// ===========================================================================================================

$show_desconto = "";
if ($conta_geral_desconto > 0){
	$show_desconto = " (descontar ".$conta_geral_desconto." mulher(es) por conta de inconsistências)";
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
	Total de ".$conta_geral." mulheres | 
	Ordenado por ".$show_ordem." |
	".$link_pag."
	</td>
  </tr>
</table>
";
$linhas = $linhas + 1;
echo $rel_rodape;
// ===========================================================================================================

// ===========================================================================================================
if ($paginacao <= 0 && $ridade <= 0){
	$rel_indicadores = "
	</table>
	<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
	  <!--DWLayoutTable-->
	  <tr> 
		<td width=\"214\" height=\"30\">&nbsp;</td>
		<td width=\"260\" valign=\"top\" class=\"resumo-sub-titulo\">Indicador 4</td>
		<td width=\"120\">&nbsp;</td>
	  </tr>
	  <tr> 
		<td height=\"30\" valign=\"top\" class=\"resumo-numerador\">Numerador&nbsp;&nbsp;</td>
		<td valign=\"top\" class=\"resumo-v-numerador\">".$numerador_ind4."</td>
		<td></td>
	  </tr>
	  <tr> 
		<td height=\"30\" valign=\"top\" class=\"resumo-denominador\">Denominador&nbsp;&nbsp;</td>
		<td valign=\"top\" class=\"resumo-v-denominador\">".$conta_geral.$show_desconto."</td>
		<td></td>
	  </tr>
	</table>
	";
	$linhas = $linhas + 1;
	echo $rel_indicadores;
}
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

?>
