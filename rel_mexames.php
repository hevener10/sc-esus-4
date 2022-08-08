<?php
require_once('session.php');
if (file_exists($_SESSION['filedb'])){
	require_once($_SESSION['filedb']);
} else {
	header('location:banco.php?ex=rel_mexames');
}
if (file_exists("cfg_rel_m.php")){
	require_once('cfg_rel_m.php');
} else {
	header('location:mulheres.php?ex=rel_mexames');
}

require_once('connect.php');
require_once('functions.php');
require_once('sobre.php');
require_once('dados.php');

// +++++++++++++++++++++++++++++++++++++++++++++
$file = "csv/mexames_T_01.csv";
if (file_exists($file)){unlink($file);}
$FT = fopen($file,'w');
$file = "csv/mexames_P_01.csv";
if (file_exists($file)){unlink($file);}
$FP = fopen($file,'w');
// +++++++++++++++++++++++++++++++++++++++++++++
$texto = "Seq;CPF;CNS;Nome;DtNascimento;Mae;Idade;MarcGestante;MarcHipertensa;MarcDiabetica;CNES;INE;MA\r\n";
fwrite($FT, $texto);
$texto = "Seq;CPF;CNS;DtProced;Tabela;CNSProf;NomeProf;CNES;INE;CBO;Procedimento(A/S)\r\n";
fwrite($FP, $texto);

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
$num = isset($_GET["num"]) ? trim($_GET["num"]) : 0;
$cdes = isset($_GET["cdes"]) ? trim($_GET["cdes"]) : 0;
$esq_pag = "";
if ($paginacao > 0){
	$esq_pag = "LIMIT ".$paginacao." OFFSET ".$pini;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if ($gpa == 1){
	$dti = datasomadias(date('Ymd'),180,'-');
	$dtf = date('Ymd');
}

$idade_inicial = $idin;
$idade_final = $idfi;

$a_dti = (int) substr($dti,0,4);
$a_dtf = (int) substr($dtf,0,4);
$m_dtf = substr($dtf,4,2);
$d_dtf = substr($dtf,6,2);
$a_dtfx = $a_dtf - $idade_final - 1;
$a_dtix = $a_dtf - $idade_inicial;
$dti_nas = $a_dtfx.'-'.$m_dtf.'-'.$d_dtf;
$dtf_nas = $a_dtix.'-'.$m_dtf.'-'.$d_dtf;

// ---------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------
$duplicados = array();
$conta_duplicados = 0;

$mulheres = "
SELECT 
	* 
FROM
(
	SELECT
		TRIM(FROM t4.nu_cns) as nu_cns,
		TRIM(FROM t4.nu_cpf_cidadao) as nu_cpf_cidadao,
		CASE WHEN t5.nu_cns IS NULL THEN '0'
			ELSE TRIM(FROM t5.nu_cns) END
		nu_cns_ci,
		CASE WHEN t5.nu_cpf_cidadao IS NULL THEN '0'
			ELSE TRIM(FROM t5.nu_cpf_cidadao) END
		nu_cpf_cidadao_ci,
		t4.dt_nascimento,
		t4.no_sexo,
		t4.st_faleceu,
		t4.no_cidadao,
		t4.no_mae, 
		t4.dt_obito, 
		t4.nu_cns_responsavel_c, 
		t4.nu_cpf_responsavel_c, 
		t5.nu_cns_responsavel, 
		t5.nu_cpf_responsavel,
		t4.st_ativo,
		t5.dt_obito AS dt_obito_ci,
		CASE WHEN t5.st_ficha_inativa IS NULL THEN '0'
			ELSE t5.st_ficha_inativa END
		st_ficha_inativa,
		t4.nu_micro_area_c,
		CASE WHEN t5.nu_micro_area IS NULL THEN '00'
			ELSE t5.nu_micro_area END
		nu_micro_area,
		CASE WHEN t5.nu_ine IS NULL THEN '0000000000'
			ELSE t5.nu_ine END
		nu_ine,
		CASE WHEN t5.no_equipe IS NULL THEN 'SEM EQUIPE'
			ELSE t5.no_equipe END
		no_equipe,
		CASE WHEN t5.nu_cnes IS NULL THEN '0000000'
			ELSE t5.nu_cnes END
		nu_cnes,
		CASE WHEN t5.no_unidade_saude IS NULL THEN 'SEM UNIDADE'
			ELSE t5.no_unidade_saude END
		no_unidade_saude,
		CASE WHEN t5.st_gestante IS NULL THEN 0
			ELSE t5.st_gestante END
		st_gestante,
		CASE WHEN t5.st_hipertensao_arterial IS NULL THEN 0
			ELSE t5.st_hipertensao_arterial END
		st_hipertensao_arterial,	
		CASE WHEN t5.st_diabete IS NULL THEN 0
			ELSE t5.st_diabete END
		st_diabete,
		CASE WHEN t5.ds_sexo IS NULL THEN 'FEMININO'
			ELSE t5.ds_sexo END
		ds_sexo,
		int '0' as teste1,
		text '0' as teste2
	FROM 
	(
		SELECT DISTINCT ON (no_cidadao, no_mae, dt_nascimento)
			CASE WHEN nu_cns IS NULL THEN '0'
				ELSE TRIM(nu_cns) END
			nu_cns,
			CASE WHEN nu_cpf IS NULL THEN '0'
				ELSE TRIM(nu_cpf) END
			nu_cpf_cidadao,
			dt_nascimento, 
			no_sexo, 
			CASE WHEN st_faleceu IS NULL THEN '0'
				ELSE st_faleceu END
			st_faleceu,
			no_cidadao,
			no_mae, 
			dt_obito, 
			CASE WHEN nu_cns_responsavel IS NULL THEN '0'
				ELSE TRIM(nu_cns_responsavel) END
			nu_cns_responsavel_c,
			CASE WHEN nu_cpf_responsavel IS NULL THEN '0'
				ELSE TRIM(nu_cpf_responsavel) END
			nu_cpf_responsavel_c,
			st_ativo,
			CASE WHEN nu_micro_area IS NULL THEN '00'
				ELSE nu_micro_area END
			nu_micro_area_c
		FROM 
			tb_cidadao 
		WHERE 
			no_sexo = 'FEMININO' AND
			(dt_nascimento >= '".$dti_nas."' AND dt_nascimento <= '".$dtf_nas."')
		ORDER BY no_cidadao, no_mae, dt_nascimento, dt_atualizado DESC
	) AS t4
	LEFT JOIN
	(
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
			t3.nu_cns_responsavel,
			t3.nu_cpf_responsavel
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
					SELECT DISTINCT ON (nu_cns, nu_cpf_cidadao)
						CASE WHEN nu_cns IS NULL THEN '0'
							ELSE TRIM(FROM nu_cns) END
						nu_cns,
						CASE WHEN nu_cpf_cidadao IS NULL THEN '0'
							ELSE TRIM(FROM nu_cpf_cidadao) END
						nu_cpf_cidadao,
						co_dim_unidade_saude,
						co_dim_equipe,
						co_dim_sexo,
						CASE WHEN nu_cns_responsavel IS NULL THEN '0'
							ELSE TRIM(FROM nu_cns_responsavel) END
						nu_cns_responsavel,
						CASE WHEN nu_cpf_responsavel IS NULL THEN '0'
							ELSE TRIM(FROM nu_cpf_responsavel) END
						nu_cpf_responsavel,
						dt_obito,
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
					ORDER BY nu_cns, nu_cpf_cidadao, co_dim_tempo DESC
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
	) AS t5
	ON
	CASE WHEN length(t5.nu_cns) = 15 THEN t5.nu_cns = t4.nu_cns ELSE CASE WHEN length(t5.nu_cpf_cidadao) = 11 THEN t5.nu_cpf_cidadao = t4.nu_cpf_cidadao ELSE false END END
	ORDER BY nu_cns, nu_cpf_cidadao, no_cidadao
) AS tf1
UNION ALL
SELECT
	* 
FROM 
(
	SELECT
		CASE WHEN nu_cns IS NULL THEN '0'
			ELSE TRIM(FROM nu_cns) END
		nu_cns,
		CASE WHEN nu_cpf_cidadao IS NULL THEN '0'
			ELSE TRIM(FROM nu_cpf_cidadao) END
		nu_cpf_cidadao,
		CASE WHEN nu_cns IS NULL THEN '0'
			ELSE TRIM(FROM nu_cns) END
		nu_cns_ci,
		CASE WHEN nu_cpf_cidadao IS NULL THEN '0'
			ELSE TRIM(FROM nu_cpf_cidadao) END
		nu_cpf_cidadao_ci,
		dt_nascimento,
		text 'FEMININO' as no_sexo,
		int '0' as st_faleceu,
		text '# MULHER NÃO ENCONTRADA #' as no_cidadao,
		text '# SEM NOME DA MAE #' as no_mae,
		dt_obito,
		text '00' as nu_cns_responsavel_c,
		text '00' as nu_cpf_responsavel_c,
		CASE WHEN nu_cns_responsavel IS NULL THEN '0'
			ELSE TRIM(FROM nu_cns_responsavel) END
		nu_cns_responsavel,
		CASE WHEN nu_cpf_responsavel IS NULL THEN '0'
			ELSE TRIM(FROM nu_cpf_responsavel) END
		nu_cpf_responsavel,
		int '1' as st_ativo,
		dt_obito as dt_obito_ci,
		CASE WHEN st_ficha_inativa IS NULL THEN '0'
			ELSE st_ficha_inativa END
		st_ficha_inativa,
		text '00' as nu_micro_area_c,
		CASE WHEN nu_micro_area IS NULL THEN '00'
			ELSE nu_micro_area END
		nu_micro_area,
		CASE WHEN nu_ine IS NULL THEN '0000000000'
			ELSE nu_ine END
		nu_ine,
		CASE WHEN no_equipe IS NULL THEN 'SEM EQUIPE'
			ELSE no_equipe END
		no_equipe,
		CASE WHEN nu_cnes IS NULL THEN '0000000'
			ELSE nu_cnes END
		nu_cnes,
		CASE WHEN no_unidade_saude IS NULL THEN 'SEM UNIDADE'
			ELSE no_unidade_saude END
		no_unidade_saude,
		CASE WHEN st_gestante IS NULL THEN '0'
			ELSE st_gestante END
		st_gestante,
		CASE WHEN st_hipertensao_arterial IS NULL THEN '0'
			ELSE st_hipertensao_arterial END
		st_hipertensao_arterial,
		CASE WHEN st_diabete IS NULL THEN '0'
			ELSE st_diabete END
		st_diabete,
		ds_sexo,
		int '0' as teste1,
		text '0' as teste2
	FROM
	(
		SELECT 
			*
		FROM
		(
			SELECT 
				*
			FROM
			(
				SELECT
					* 
				FROM
				(
					SELECT
						t1.*,
						upper(tb_dim_sexo.ds_sexo) AS ds_sexo
					FROM
					(
						SELECT DISTINCT ON (nu_cns, nu_cpf_cidadao)
							*
						FROM 
							tb_fat_cad_individual
						WHERE
							(dt_nascimento >= '".$dti_nas."' AND dt_nascimento <= '".$dtf_nas."')
						ORDER BY nu_cns, nu_cpf_cidadao, co_dim_tempo DESC
					) AS t1
					LEFT JOIN
						tb_dim_sexo
					ON tb_dim_sexo.co_seq_dim_sexo = t1.co_dim_sexo
				) AS t2
				WHERE
					ds_sexo = 'FEMININO'
			) AS t4
			LEFT JOIN
				tb_dim_equipe
			ON tb_dim_equipe.co_seq_dim_equipe = t4.co_dim_equipe
		) AS t5
		LEFT JOIN
			tb_dim_unidade_saude
		ON tb_dim_unidade_saude.co_seq_dim_unidade_saude = t5.co_dim_unidade_saude
	) AS t3
	WHERE NOT EXISTS
	(
		SELECT
			nu_cns, nu_cpf
		FROM
			tb_cidadao
		WHERE
			CASE WHEN length(t3.nu_cns) = 15 THEN t3.nu_cns = tb_cidadao.nu_cns ELSE CASE WHEN length(t3.nu_cpf_cidadao) = 11 THEN t3.nu_cpf_cidadao = tb_cidadao.nu_cpf ELSE false END END
	)
	ORDER BY nu_cns, nu_cpf_cidadao, no_cidadao
) AS tf2
".$esq_pag;

// ===========================================================================================================
$linhas = 0;
$rel_pagina_inicio = "
<!DOCTYPE html>
<html>
<head>
  <meta charset=\"UTF-8\">
  <link rel=\"sortcut icon\" href=\"images/favicon.ico\" type=\"image/x-icon\" />
  <title>Relatório de mulheres com solicitação de mamografia</title>
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
	cns_resp varchar(15),
	cpf_resp varchar(11),
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
	
$num_mulheres = 0;
$run_s1 = pg_query($cdb,$mulheres);
$num_mulheres = pg_num_rows($run_s1);
if ($num_mulheres > 0){
	while ($mulher = pg_fetch_array($run_s1)){
		$CNS = '0';
		$CPF = '0';
		if (strlen(trim($mulher['nu_cns'])) == 15){
			$CNS = trim($mulher['nu_cns']);
		} else {
			if (strlen(trim($mulher['nu_cns_ci'])) == 15){
				$CNS = trim($mulher['nu_cns_ci']);
			}
		}
		if (strlen(trim($mulher['nu_cpf_cidadao'])) == 11){
			$CPF = trim($mulher['nu_cpf_cidadao']);
		} else {
			if (strlen(trim($mulher['nu_cpf_cidadao_ci'])) == 11){
				$CPF = trim($mulher['nu_cpf_cidadao_ci']);
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
				if (strlen(trim($mulher['dt_obito_ci'])) > 5){
					$data_falecimento = dataint($mulher['dt_obito_ci']);
				}
			}
			$micro_area = $mulher['nu_micro_area'];
			if (strlen($micro_area) == 1){
				$micro_area = '0'.$micro_area;
			}
			if (strlen($micro_area) == 0){
				$micro_area = '00';
			}
			$micro_area_c = $mulher['nu_micro_area_c'];
			if (strlen($micro_area_c) == 1){
				$micro_area_c = '0'.$micro_area_c;
			}
			if (strlen($micro_area_c) == 0){
				$micro_area_c = '00';
			}
			$cns_resp = '0';
			$cpf_resp = '0';
			if (strlen($mulher['nu_cns_responsavel']) == 15){
				$cns_resp = $mulher['nu_cns_responsavel'];
			} else {
				$cns_resp = $mulher['nu_cns_responsavel_c'];
			}
			if (strlen($mulher['nu_cpf_responsavel']) == 11){
				$cpf_resp = $mulher['nu_cpf_responsavel'];
			} else {
				$cpf_resp = $mulher['nu_cpf_responsavel_c'];
			}
			
			$cidadao_nome = "# MULHER NÃO ENCONTRADA #";
			$cidadao_mae = "# SEM NOME DA MAE #";
			if (strlen(trim($mulher['no_cidadao'])) > 0){
				$cidadao_nome = trim($mulher['no_cidadao']);
			}
			if (strlen(trim($mulher['no_mae'])) > 0){
				$cidadao_mae = trim($mulher['no_mae']);
			}
			
			$cidadao_nome = htmlspecialchars($cidadao_nome, ENT_QUOTES);
			$cidadao_mae = htmlspecialchars($cidadao_mae, ENT_QUOTES);
			if ($micro_area == '-' || $micro_area == '--' || $micro_area == '0-'){
				$micro_area = '00';
			}
			if ($micro_area_c == '-' || $micro_area_c == '--' || $micro_area_c == '0-'){
				$micro_area_c = '00';
			}
			if ($micro_area == '00'){
				if ($micro_area_c != '00'){
					$micro_area = $micro_area_c;
				}
			}
			$ine = $mulher['nu_ine'];
			if ($ine == '-'){
				$ine = '0000000000';
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
				cns_resp,
				cpf_resp,
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
				'".$CNS."',
				'".$CPF."',
				".$data_nascimento.",
				".$mulher['st_ativo'].",
				".$data_falecimento.",
				'".$cidadao_nome."',
				'".$cidadao_mae."',
				".$mulher['st_faleceu'].",
				'".$mulher['no_sexo']."',
				'".$cns_resp."',
				'".$cpf_resp."',
				".$mulher['st_gestante'].",
				".$mulher['st_hipertensao_arterial'].",
				".$mulher['st_diabete'].",
				'".$mulher['ds_sexo']."',
				'".$micro_area."',
				".$mulher['st_ficha_inativa'].",
				'".$mulher['nu_cnes']."',
				'".$mulher['no_unidade_saude']."',
				'".$ine."',
				'".$mulher['no_equipe']."'
			)";
			
			$gravar = false;
			if ($super_filtro){
				if ($cpbusca == 'U'){
					$vlbusca = zesq($vlbusca,7);
					if ($mulher['nu_cnes'] == $vlbusca){
						$gravar = true;
					}
				}
				if ($cpbusca == 'E'){
					$vlbusca = zesq($vlbusca,10);
					if ($mulher['nu_ine'] == $vlbusca){
						$gravar = true;
					}
				}
				if ($cpbusca == 'M'){
					$vlbusca = strtoupper($vlbusca);
					if (strlen($vlbusca) == 1){
						$vlbusca = '0'.$vlbusca;
					}
					if (strlen($vlbusca) == 0){
						$vlbusca = '00';
					}
					if ($micro_area == $vlbusca){
						$gravar = true;
					}
				}
				if ($cpbusca == 'C'){
					$vlbusca = zesq($vlbusca,15);
					if ($CNS == $vlbusca){
						$gravar = true;
					}
				}
				if ($cpbusca == 'P'){
					$vlbusca = zesq($vlbusca,11);
					if ($CPF == $vlbusca){
						$gravar = true;
					}
				}
			} else {
				$gravar = true;
			}
			
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
				if ($mulher['st_faleceu'] == 0){
					if ($cfa == 0){
						$run_ins = pg_query($cdb,$inserir);
						$conta_gravacao++;
					} else {
						if ($cpbusca != 'M'){
							if ($micro_area != '00' && $micro_area != 'FA'){
								$run_ins = pg_query($cdb,$inserir);
								$conta_gravacao++;
							}
						} else {
							$run_ins = pg_query($cdb,$inserir);
							$conta_gravacao++;
						}
					}
				}
			}
		}
	}
}

$periodo_show = "De ".dtshow($dti)." até ".dtshow($dtf);
// ===========================================================================================================
$rel_cabecalho_2 = "
<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"relatorio-tabela\">
  <!--DWLayoutTable-->
  <tr> 
	<td width=\"440\" height=\"32\" valign=\"top\" class=\"relatorio-titulo\">Relatório de Procedimentos (de ".$idade_inicial." até ".$idade_final." anos)</td>
	<td width=\"392\" valign=\"top\" class=\"relatorio-periodo\">".$periodo_show." [".$proceds."] -".$tbusca." meses</td>
	<td width=\"177\" valign=\"top\" class=\"relatorio-pagina\">".qdata($dti)."</td>
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
$mulheres_final = "
SELECT
	*
FROM
	tmp_mulheres
ORDER BY ".$ordena_grupo." ".$ordenar;
//++++++++++++++++++++++++

$num_mulheres_final = 0;
$run_mf = pg_query($cdb,$mulheres_final);
$num_mulheres_final = pg_num_rows($run_mf);
$conta_geral = $pini;

if ($num_mulheres_final > 0){
	while ($mulher_final = pg_fetch_array($run_mf)){
		$duplicados_exames = array();
		$conta_duplicados_exames = 0;
		
		$conta_geral++;
		
		$rCNS = trim($mulher_final['cns']);
		$rCPF = trim($mulher_final['cpf']);
		
		if (strlen($rCNS) != 15){
			if (false !== $key = array_search($rCPF, array_column($duplicados, 'cpf'))) {
				if (strlen($duplicados[$key]['cns']) == 15){
					$rCNS = trim($duplicados[$key]['cns']);
				}
			}
		}
		if (strlen($rCPF) != 11){
			if (false !== $key = array_search($rCNS, array_column($duplicados, 'cns'))) {
				if (strlen($duplicados[$key]['cpf']) == 11){
					$rCPF = trim($duplicados[$key]['cpf']);
				}
			}
		}
		
		if ($grupo != "SG"){
			$grupo_id = $mulher_final[$grupo];
			
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
			
			if ($quebra != $grupo_id){
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

		$campo_busca = "nu_cns = '000000000000000'";
		$campo_busca_res = "nu_cns_responsavel = '000000000000000'";
		if (strlen($rCNS) == 15 && strlen($rCPF) == 11){
			$campo_busca = "(nu_cns = '".$rCNS."' OR nu_cpf_cidadao = '".$rCPF."')";
			$campo_busca_res = "(nu_cns_responsavel = '".$rCNS."' OR nu_cpf_responsavel = '".$rCPF."')";
		} else {
			if (strlen($rCNS) == 15){
				$campo_busca = "nu_cns = '".$rCNS."'";
				$campo_busca_res = "nu_cns_responsavel = '".$rCNS."'";
			}
			if (strlen($rCPF) == 11){
				$campo_busca = "nu_cpf_cidadao = '".$rCPF."'";
				$campo_busca_res = "nu_cpf_responsavel = '".$rCPF."'";
			}
		}
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		
		$idade = idadeint($mulher_final['data_nascimento'],$dtf);
		if ($dt3anos == 'U'){
			$dt_busca = datasomameses($dtf,$tbusca,'-');
		}
		if ($dt3anos == 'P'){
			$a_atual = $a_dti - $tempo_meses;
			$dt_busca = datasomameses($dti,$tbusca,'-');
		}
		if ($dt3anos == 'A'){
			$a_atual = $a_dtf;
			if ($a_dti > $a_dtf){
				$a_atual = $a_dti;
			}
			$dtaniv = $a_atual.substr($mulher_final['data_nascimento'],4,2).substr($mulher_final['data_nascimento'],6,2);
			$dt_busca = datasomameses($dtaniv,$tbusca,'-');
		}
		
		$exames = array();
		$cont_exames = 0;
		$contro_cbo = "'2251%', '2252%', '2253%', '2231%', '2235%', '3222%'";
		
		$proceds_ar = explode(",", $proceds);
		$dose3 = false;
		$procedimentos = "";
		foreach ($proceds_ar as $ims) {
			$procedimentos .= "'".trim($ims)."',";
		}
		$procedimentos = substr($procedimentos,0,-1);
		
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		$select_tb1 = "
			SELECT
				t6.co_dim_tempo,
				t6.nu_cbo,
				t6.nu_cnes,
				t6.nu_ine,
				t6.avaliado,
				t6.solicitado,
				t6.cns_prof,
				t6.no_profissional,
				text 'tb_fat_atd_ind_procedimentos' as tabela
			FROM
			(
				---------------------------------------------------------
				---------------------------------------------------------
				---------------------------------------------------------
				SELECT
					t7.*,
					tb_dim_profissional.nu_cns AS cns_prof,
					tb_dim_profissional.no_profissional
				FROM
				(
					SELECT
						t5.*,
						tb_dim_procedimento.co_proced AS solicitado
					FROM
					(
						SELECT
							t4.*,
							tb_dim_procedimento.co_proced AS avaliado
						FROM
						(
							SELECT
								t3.*,
								tb_dim_equipe.nu_ine
							FROM
							(
								SELECT
									t2.*,
									tb_dim_unidade_saude.nu_cnes
								FROM
								(
									SELECT
										t1.*,
										tb_dim_cbo.nu_cbo
									FROM
									(
										SELECT
											CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
											ELSE co_dim_cbo_1 END
											co_dim_cbo,
											CASE WHEN co_dim_unidade_saude_1 = 1 THEN co_dim_unidade_saude_2
											ELSE co_dim_unidade_saude_1 END
											co_dim_unidade_saude,
											CASE WHEN co_dim_equipe_1 = 1 THEN co_dim_equipe_2
											ELSE co_dim_equipe_1 END
											co_dim_equipe,
											co_dim_tempo,
											co_dim_procedimento_avaliado,
											co_dim_procedimento_solicitado,
											CASE WHEN co_dim_profissional_1 = 1 THEN co_dim_profissional_2
												ELSE co_dim_profissional_1 END
												co_dim_profissional
										FROM 
											tb_fat_atd_ind_procedimentos
										WHERE
											".$campo_busca." AND
											(co_dim_tempo >= ".$dt_busca." AND co_dim_tempo <= ".$dtf.") AND
											(
												co_dim_procedimento_avaliado IN
												(
													SELECT 
														co_seq_dim_procedimento
													FROM 
														tb_dim_procedimento
													WHERE
														co_proced IN (".$procedimentos.")
												)
												OR
												co_dim_procedimento_solicitado IN
												(
													SELECT 
														co_seq_dim_procedimento
													FROM 
														tb_dim_procedimento
													WHERE
														co_proced IN (".$procedimentos.")
												)	
											)
									) AS t1
									LEFT JOIN
										tb_dim_cbo
									ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
								) AS t2
								LEFT JOIN
									tb_dim_unidade_saude
								ON tb_dim_unidade_saude.co_seq_dim_unidade_saude = t2.co_dim_unidade_saude
							) AS t3
							LEFT JOIN
								tb_dim_equipe
							ON tb_dim_equipe.co_seq_dim_equipe = t3.co_dim_equipe
						) AS t4
						LEFT JOIN
							tb_dim_procedimento
						ON tb_dim_procedimento.co_seq_dim_procedimento = t4.co_dim_procedimento_avaliado
					) AS t5
					LEFT JOIN
						tb_dim_procedimento
					ON tb_dim_procedimento.co_seq_dim_procedimento = t5.co_dim_procedimento_solicitado
				) AS t7
				LEFT JOIN
					tb_dim_profissional
				ON tb_dim_profissional.co_seq_dim_profissional = t7.co_dim_profissional
				---------------------------------------------------------
				---------------------------------------------------------
				---------------------------------------------------------
			) AS t6
			WHERE
				nu_cbo LIKE ANY (array[".$contro_cbo."])
		";
		$run_st1 = pg_query($cdb,$select_tb1);
		if (pg_num_rows($run_st1) > 0){
			while ($ex1 = pg_fetch_array($run_st1)){
				$junta_exames = $ex1['co_dim_tempo'].$ex1['nu_cbo'].$ex1['nu_cnes'].$ex1['nu_ine'].$ex1['avaliado'].$ex1['solicitado'];
				if (!in_array($junta_exames, $duplicados_exames)) {
					$duplicados_exames[$conta_duplicados_exames] = $junta_exames;
					$conta_duplicados_exames++;
					$exames[$cont_exames]['data'] = $ex1['co_dim_tempo'];
					$exames[$cont_exames]['cbo'] = $ex1['nu_cbo'];
					$exames[$cont_exames]['cnes'] = $ex1['nu_cnes'];
					$exames[$cont_exames]['ine'] = $ex1['nu_ine'];
					$exames[$cont_exames]['cns_prof'] = $ex1['cns_prof'];
					$exames[$cont_exames]['no_profissional'] = $ex1['no_profissional'];
					$exames[$cont_exames]['procedimento1'] = $ex1['avaliado'];
					$exames[$cont_exames]['procedimento2'] = $ex1['solicitado'];
					$exames[$cont_exames]['tabela'] = $ex1['tabela'];
					$cont_exames++;
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		$select_tb2 = "
			SELECT
				t6.co_dim_tempo,
				t6.nu_cbo,
				t6.nu_cnes,
				t6.nu_ine,
				t6.procedimento,
				t6.cns_prof,
				t6.no_profissional,
				text 'tb_fat_proced_atend_proced' as tabela
			FROM
			(
				---------------------------------------------------------
				---------------------------------------------------------
				---------------------------------------------------------
				SELECT
					t7.*,
					tb_dim_profissional.nu_cns AS cns_prof,
					tb_dim_profissional.no_profissional
				FROM
				(
					SELECT
						t4.*,
						tb_dim_procedimento.co_proced AS procedimento
					FROM
					(
						SELECT
							t3.*,
							tb_dim_equipe.nu_ine
						FROM
						(
							SELECT
								t2.*,
								tb_dim_unidade_saude.nu_cnes
							FROM
							(
								SELECT
									t1.*,
									tb_dim_cbo.nu_cbo
								FROM
								(
									SELECT
										co_dim_cbo,
										co_dim_unidade_saude,
										co_dim_equipe,
										co_dim_tempo,
										co_dim_procedimento,
										co_dim_profissional
									FROM 
										tb_fat_proced_atend_proced
									WHERE
										".$campo_busca." AND
										(co_dim_tempo >= ".$dt_busca." AND co_dim_tempo <= ".$dtf.") AND
										(
											co_dim_procedimento IN
											(
												SELECT 
													co_seq_dim_procedimento
												FROM 
													tb_dim_procedimento
												WHERE
													co_proced IN (".$procedimentos.")
											)	
										)
								) AS t1
								LEFT JOIN
									tb_dim_cbo
								ON tb_dim_cbo.co_seq_dim_cbo = t1.co_dim_cbo
							) AS t2
							LEFT JOIN
								tb_dim_unidade_saude
							ON tb_dim_unidade_saude.co_seq_dim_unidade_saude = t2.co_dim_unidade_saude
						) AS t3
						LEFT JOIN
							tb_dim_equipe
						ON tb_dim_equipe.co_seq_dim_equipe = t3.co_dim_equipe
					) AS t4
					LEFT JOIN
						tb_dim_procedimento
					ON tb_dim_procedimento.co_seq_dim_procedimento = t4.co_dim_procedimento
				) AS t7
				LEFT JOIN
					tb_dim_profissional
				ON tb_dim_profissional.co_seq_dim_profissional = t7.co_dim_profissional
				---------------------------------------------------------
				---------------------------------------------------------
				---------------------------------------------------------
			) AS t6
			WHERE
				nu_cbo LIKE ANY (array[".$contro_cbo."])
		";
		$run_st2 = pg_query($cdb,$select_tb2);
		if (pg_num_rows($run_st2) > 0){
			while ($ex2 = pg_fetch_array($run_st2)){
				$junta_exames = $ex2['co_dim_tempo'].$ex2['nu_cbo'].$ex2['nu_cnes'].$ex2['nu_ine'].$ex2['procedimento'].'-';
				if (!in_array($junta_exames, $duplicados_exames)) {
					$duplicados_exames[$conta_duplicados_exames] = $junta_exames;
					$conta_duplicados_exames++;
					$exames[$cont_exames]['data'] = $ex2['co_dim_tempo'];
					$exames[$cont_exames]['cbo'] = $ex2['nu_cbo'];
					$exames[$cont_exames]['cnes'] = $ex2['nu_cnes'];
					$exames[$cont_exames]['ine'] = $ex2['nu_ine'];
					$exames[$cont_exames]['cns_prof'] = $ex2['cns_prof'];
					$exames[$cont_exames]['no_profissional'] = $ex2['no_profissional'];
					$exames[$cont_exames]['procedimento1'] = $ex2['procedimento'];
					$exames[$cont_exames]['procedimento2'] = '-';
					$exames[$cont_exames]['tabela'] = $ex2['tabela'];
					$cont_exames++;
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------

		
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		
		$ma_familiar = '00';
		$familia = "
			SELECT 
				nu_cns_responsavel,
				nu_micro_area,
				nu_cpf_responsavel
			FROM
				tb_fat_cad_dom_familia
			WHERE
				".$campo_busca_res."
			ORDER BY co_dim_tempo DESC
			LIMIT 1
		";
		$run_sfm = pg_query($cdb,$familia);
		if (pg_num_rows($run_sfm) <= 0){
			$campo_busca_res = "nu_cns_responsavel = '000000000000000'";
			if (strlen($mulher_final['cns_resp']) == 15 && strlen($mulher_final['cpf_resp']) == 11){
				$campo_busca_res = "(nu_cns_responsavel = '".$mulher_final['cns_resp']."' OR nu_cpf_responsavel = '".$mulher_final['cpf_resp']."')";
			} else {
				if (strlen($mulher_final['cns_resp']) == 15){
					$campo_busca_res = "nu_cns_responsavel = '".$mulher_final['cns_resp']."'";
				}
				if (strlen($mulher_final['cpf_resp']) == 11){
					$campo_busca_res = "nu_cpf_responsavel = '".$mulher_final['cpf_resp']."'";
				}
			}
			$familia = "
				SELECT 
					nu_cns_responsavel,
					nu_micro_area,
					nu_cpf_responsavel
				FROM
					tb_fat_cad_dom_familia
				WHERE
					".$campo_busca_res."
				ORDER BY co_dim_tempo DESC
				LIMIT 1
			";
			$run_sfm2 = pg_query($cdb,$familia);
			if (pg_num_rows($run_sfm2) > 0){
				$ma_familiar = pg_fetch_result($run_sfm2,0,'nu_micro_area');
				if (strlen($ma_familiar) == 0 || $ma_familiar == '0' || $ma_familiar == NULL){
					$ma_familiar = '00';
				}
				if (strlen($ma_familiar) == 1){
					$ma_familiar = '0'.$ma_familiar;
				}
			}
		} else {
			$ma_familiar = pg_fetch_result($run_sfm,0,'nu_micro_area');
			if (strlen($ma_familiar) == 0 || $ma_familiar == '0' || $ma_familiar == NULL){
				$ma_familiar = '00';
			}
			if (strlen($ma_familiar) == 1){
				$ma_familiar = '0'.$ma_familiar;
			}
		}
		if (strlen($show_gp_id2_inverso) == 2){
			$show_gp_id2_inverso = $show_gp_id2_inverso."/".$ma_familiar;
		}
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
			$inconsistencias .= "-<br>";
		}
		if ($rCNS == '0' && $rCPF == '0'){
			$inconsistencias .= "Sem CNS e sem CPF.<br>-<br>";
		}
		if ($ma_familiar == '00' && $mulher_final['cind_micro_area'] == '00'){
			$inconsistencias .= "Mulher sem cadastro familiar.<br>";
		}
		if ($mulher_final['cind_sexo'] != 'FEMININO'){
			$inconsistencias .= "Cadastro está como do sexo Masculino.<br>";
		}
		if (substr($mulher_final['cidadao_nome'],0,1) == '#'){
			$inconsistencias .= "Mulher não localizada em tb_cidadao.<br>";
		}
		$inativo2 = 0;
		if ($mulher_final['cidadao_ativo'] != 1){
			//$inconsistencias .= "Cadastro está como inativo (Cidadao).<br>";
			$inativo2++;
		}
		if ($mulher_final['cind_inativo'] != 0){
			$inconsistencias .= "Cadastro está como inativo (Cad. Ind.).<br>";
			$inativo2++;
		}
		if ($inativo2 >= 2){
			$inconsistencias .= "-<br>";
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
			$inconsistencias .= "-<br>";
		}
		if (strlen($inconsistencias) > 0){
			$estilo_inco = "lista-status-Vermelho";
		}
		
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
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".dtshow($dt_busca)."</td>
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
		  ";
		$linhas = $linhas + 13;
		
		$texto = zesq($conta_geral,5).";".mcpf($rCPF).";".mcns($rCNS).";".str_replace("Ã","A",$mulher_final['cidadao_nome']).";".dtshow($mulher_final['data_nascimento']).";".str_replace("Ã","A",$mulher_final['cidadao_mae']).";".$idade.";".str_replace("Ã","A",$marcado_gestante).";".str_replace("Ã","A",$marcado_hipertenso).";".str_replace("Ã","A",$marcado_diabetico).";".$mulher_final['cnes'].";".$mulher_final['ine'].";".$ma_familiar."/".$mulher_final['cind_micro_area']."\r\n";

		if ($apvac == 0){
			if (count($exames) > 0){
				echo $rel_dados_unitario;
				fwrite($FT, $texto);
			}
		} else {
			echo $rel_dados_unitario;
			fwrite($FT, $texto);
		}

		if (count($exames) > 0){
			usort($exames, 'cmp');
			$rel_dados_sub_tabela = "
			  <tr> 
				<td height=\"54\"></td>
				<td></td>
				<td></td>
				<td width=\"86\">&nbsp;</td>
				<td colspan=\"9\" valign=\"top\">
				<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"procedimentos-tabela\">
					<!--DWLayoutTable-->
					<tr> 
					  <td width=\"36\" height=\"18\"></td>
					  <td width=\"169\" valign=\"top\" class=\"procedimentos-cabeca\">Procedimento (A/S)</td>
					  <td width=\"75\" valign=\"top\" class=\"procedimentos-cabeca\">Data</td>
					  <td width=\"213\" valign=\"top\" class=\"procedimentos-cabeca\">Tabela</td>
					  <td width=\"43\" valign=\"top\" class=\"procedimentos-cabeca\">CBO</td>
					  <td width=\"78\" valign=\"top\" class=\"procedimentos-cabeca\">INE</td>
					  <td width=\"56\" valign=\"top\" class=\"procedimentos-cabeca\">CNES</td>
					  <td width=\"62\" valign=\"top\" class=\"procedimentos-cabeca\">Quant.</td>
					</tr>
			";
			$soma_procedimentos = 0;
			for ($i=0;$i<count($exames);$i++){
				$soma_procedimentos++;
				
				$texto = $soma_procedimentos.";".mcpf($rCPF).";".mcns($rCNS).";".dtshow($exames[$i]['data']).";".$exames[$i]['tabela'].";".$exames[$i]['cns_prof'].";".$exames[$i]['no_profissional'].";".$exames[$i]['cnes'].";".$exames[$i]['ine'].";".$exames[$i]['cbo'].";".$exames[$i]['procedimento1']."/".$exames[$i]['procedimento2']."\r\n";
				fwrite($FP, $texto);
				
				$rel_dados_sub_tabela .= "
						<tr> 
						  <td height=\"18\" valign=\"top\" class=\"procedimentos-dado\">".$soma_procedimentos."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['procedimento1']." / ".$exames[$i]['procedimento2']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".dtshow($exames[$i]['data'])."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['tabela']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['cbo']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['ine']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['cnes']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">1</td>
						</tr>
				";
			}
			$rel_dados_sub_tabela .= "
					<tr> 
					  <td height=\"18\"></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td></td>
					  <td valign=\"top\" class=\"procedimentos-dado\">".$soma_procedimentos."</td>
					</tr>
				  </table>
				  </td>
			  </tr>
			";
			echo $rel_dados_sub_tabela;
		}
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

// -----------------------------------------------------------------------------------------------------------
$link_pag = "";
if ($paginacao > 0){
	$pini = $pini + $num_mulheres_final;
	if ($num_mulheres_final > 0){
		$link_pag = "<a href=\"rel_mulheres.php?pini=".$pini."><img src=\"images/pagina.png\"></a>";
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
	Total de ".$total_geral." mulheres | 
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
fclose($FP);

?>
