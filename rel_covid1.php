<?php
require_once('session.php');
if (file_exists($_SESSION['filedb'])){
	require_once($_SESSION['filedb']);
} else {
	header('location:banco.php?ex=rel_covid1');
}
if (file_exists("cfg_rel_v.php")){
	require_once('cfg_rel_v.php');
} else {
	header('location:vacinacao.php?ex=rel_covid1');
}

require_once('connect.php');
require_once('functions.php');
require_once('sobre.php');
require_once('dados.php');

// +++++++++++++++++++++++++++++++++++++++++++++
$file = "csv/covid_T_01.csv";
if (file_exists($file)){unlink($file);}
$FT = fopen($file,'w');
// +++++++++++++++++++++++++++++++++++++++++++++
$texto = "Seq;CPF;CNS;Nome;Idade;Dose;Imuno;Lote;Fabricante;DtAplicacao;CNSProf;NomeProf;ImunoNome;GrupoAt;RegAnt\r\n";
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
$num = isset($_GET["num"]) ? trim($_GET["num"]) : 0;
$cdes = isset($_GET["cdes"]) ? trim($_GET["cdes"]) : 0;
$esq_pag = "";
if ($paginacao > 0){
	$esq_pag = "LIMIT ".$paginacao." OFFSET ".$pini;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// .......................................................
$imunos = isset($_GET["ims"]) ? trim($_GET["ims"]) : "85,86,87,88";
$vtitulo = isset($_GET["vt"]) ? trim($_GET["vt"]) : "COVID-19";
$dti = isset($_GET["dti"]) ? trim($_GET["dti"]) : 20200101;
$dtf = isset($_GET["dtf"]) ? trim($_GET["dtf"]) : date('Ymd');
$idade_inicial = isset($_GET["idi"]) ? trim($_GET["idi"]) : 0;
$idade_final = isset($_GET["idf"]) ? trim($_GET["idf"]) : 150;
// .......................................................

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

$vacinados = "
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
		text '# USUARIO NÃO ENCONTRADO #' as no_cidadao,
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
  <title>Relatório de ".$vtitulo." Transparência</title>
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
	<table width=\"1088\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"cabeca-tabela\">
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
CREATE TEMPORARY TABLE tmp_vacinados (
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
	
$num_vacinados = 0;
$run_s1 = pg_query($cdb,$vacinados);
$num_vacinados = pg_num_rows($run_s1);
if ($num_vacinados > 0){
	while ($vacinado = pg_fetch_array($run_s1)){
		$CNS = '0';
		$CPF = '0';
		if (strlen(trim($vacinado['nu_cns'])) == 15){
			$CNS = trim($vacinado['nu_cns']);
		} else {
			if (strlen(trim($vacinado['nu_cns_ci'])) == 15){
				$CNS = trim($vacinado['nu_cns_ci']);
			}
		}
		if (strlen(trim($vacinado['nu_cpf_cidadao'])) == 11){
			$CPF = trim($vacinado['nu_cpf_cidadao']);
		} else {
			if (strlen(trim($vacinado['nu_cpf_cidadao_ci'])) == 11){
				$CPF = trim($vacinado['nu_cpf_cidadao_ci']);
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
			$data_nascimento = dataint($vacinado['dt_nascimento']);
			$data_falecimento = "0";
			if (strlen(trim($vacinado['dt_obito'])) > 5){
				$data_falecimento = dataint($vacinado['dt_obito']);
			} else {
				if (strlen(trim($vacinado['dt_obito_ci'])) > 5){
					$data_falecimento = dataint($vacinado['dt_obito_ci']);
				}
			}
			$micro_area = $vacinado['nu_micro_area'];
			if (strlen($micro_area) == 1){
				$micro_area = '0'.$micro_area;
			}
			if (strlen($micro_area) == 0){
				$micro_area = '00';
			}
			$micro_area_c = $vacinado['nu_micro_area_c'];
			if (strlen($micro_area_c) == 1){
				$micro_area_c = '0'.$micro_area_c;
			}
			if (strlen($micro_area_c) == 0){
				$micro_area_c = '00';
			}
			$cns_resp = '0';
			$cpf_resp = '0';
			if (strlen($vacinado['nu_cns_responsavel']) == 15){
				$cns_resp = $vacinado['nu_cns_responsavel'];
			} else {
				$cns_resp = $vacinado['nu_cns_responsavel_c'];
			}
			if (strlen($vacinado['nu_cpf_responsavel']) == 11){
				$cpf_resp = $vacinado['nu_cpf_responsavel'];
			} else {
				$cpf_resp = $vacinado['nu_cpf_responsavel_c'];
			}

			/*
			$cidadao_nome = "# USUARIO NÃO ENCONTRADO #";
			if (strlen(trim($vacinado['no_cidadao'])) > 0){
				$cidadao_nome = trim($vacinado['no_cidadao']);
			}
			*/
			$cidadao_mae = "# SEM NOME DA MAE #";
			if (strlen(trim($vacinado['no_mae'])) > 0){
				$cidadao_mae = trim($vacinado['no_mae']);
			}
			
			$cidadao_nome = htmlspecialchars(trim($vacinado['no_cidadao']), ENT_QUOTES);
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
			$ine = $vacinado['nu_ine'];
			if ($ine == '-'){
				$ine = '0000000000';
			}
			
			$inserir = "
			INSERT INTO tmp_vacinados(
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
				".$vacinado['st_ativo'].",
				".$data_falecimento.",
				'".$cidadao_nome."',
				'".$cidadao_mae."',
				".$vacinado['st_faleceu'].",
				'".$vacinado['no_sexo']."',
				'".$cns_resp."',
				'".$cpf_resp."',
				".$vacinado['st_gestante'].",
				".$vacinado['st_hipertensao_arterial'].",
				".$vacinado['st_diabete'].",
				'".$vacinado['ds_sexo']."',
				'".$micro_area."',
				".$vacinado['st_ficha_inativa'].",
				'".$vacinado['nu_cnes']."',
				'".$vacinado['no_unidade_saude']."',
				'".$ine."',
				'".$vacinado['no_equipe']."'
			)";
			
			$gravar = false;
			if ($super_filtro){
				if ($cpbusca == 'U'){
					$vlbusca = zesq($vlbusca,7);
					if ($vacinado['nu_cnes'] == $vlbusca){
						$gravar = true;
					}
				}
				if ($cpbusca == 'E'){
					$vlbusca = zesq($vlbusca,10);
					if ($vacinado['nu_ine'] == $vlbusca){
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
				if ($vacinado['st_faleceu'] == 0){
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

// ===========================================================================================================
$rel_cabecalho_2 = "
<table width=\"1088\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"relatorio-tabela\">
  <!--DWLayoutTable-->
  <tr> 
	<td width=\"440\" height=\"32\" valign=\"top\" class=\"relatorio-titulo\">Vacina ".$vtitulo."</td>
	<td width=\"392\" valign=\"top\" class=\"relatorio-periodo\">Todos os vacinados [".$imunos."]</td>
	<td width=\"177\" valign=\"top\" class=\"relatorio-pagina\">(transparência)</td>
  </tr>
</table>
";
$linhas = $linhas + 2;
echo $rel_cabecalho_2;
// ===========================================================================================================

// ===========================================================================================================
$rel_dados_inicio = "
<table width=\"1088\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
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

$vacinados_final = "
SELECT
	*
FROM
	tmp_vacinados
ORDER BY ".$ordenar;
//++++++++++++++++++++++++

$num_vacinados_final = 0;
$run_mf = pg_query($cdb,$vacinados_final);
$num_vacinados_final = pg_num_rows($run_mf);
$conta_geral = $pini;
$conta_geral_desconto = $cdes;
$numerador_ind5 = $num;

$Hsubtitulo = "
<table width=\"1088\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
  <tr> 
	<td width=\"55\" height=\"20\" valign=\"top\" class=\"lista-cabeca\">Seq.</td>
	<td width=\"104\" valign=\"top\" class=\"lista-cabeca\">CPF</td>
	<td width=\"126\" valign=\"top\" class=\"lista-cabeca\">CNS</td>
	<td width=\"311\" valign=\"top\" class=\"lista-cabeca\">Nome</td>
	<td width=\"54\" valign=\"top\" class=\"lista-cabeca\">Idade</td>
	<td width=\"36\" valign=\"top\" class=\"lista-cabeca\">Dose</td>
	<td width=\"215\" valign=\"top\" class=\"lista-cabeca\">Imuno</td>
	<td width=\"100\" valign=\"top\" class=\"lista-cabeca\">Aplica&ccedil;&atilde;o</td>
	<td width=\"87\" valign=\"top\" class=\"lista-cabeca\">Aplicador</td>
  </tr>
";
echo $Hsubtitulo;

if ($num_vacinados_final > 0){
	while ($vacinado_final = pg_fetch_array($run_mf)){
		$duplicados_vacinas = array();
		$conta_duplicados_vacinas = 0;
		
		$rCNS = trim($vacinado_final['cns']);
		$rCPF = trim($vacinado_final['cpf']);
		
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

		$campo_busca = "nu_cns = '000000000000000'";
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

		$vacinas = array();
		$cont_vacinas = 0;

		$imunos_ar = explode(",", $imunos);
		$dose3 = false;
		$imunobiologicos = "";
		foreach ($imunos_ar as $ims) {
			$imunobiologicos .= "'%|".trim($ims)."|%',";
		}
		$imunobiologicos = substr($imunobiologicos,0,-1);
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		$select_tb1 = "
			SELECT 
				t5.co_seq_fat_vacinacao,
				t5.ds_filtro_dose_imunobiologico AS dose,
				t5.ds_filtro_imunobiologico AS imuno,
				t5.ds_filtro_lote AS lote,
				t5.ds_filtro_fabricante AS fabricante,
				t5.co_dim_tempo,
				t5.nu_cbo,
				t5.nu_cnes,
				t5.nu_ine,
				t5.nu_cns,
				t5.no_profissional
			FROM
			(
				SELECT
					t4.*,
					tb_dim_profissional.nu_cns,
					tb_dim_profissional.no_profissional
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
									co_seq_fat_vacinacao,
									ds_filtro_dose_imunobiologico, 
									ds_filtro_imunobiologico,
									co_dim_unidade_saude,
									co_dim_equipe,
									co_dim_profissional, 
									co_dim_cbo, 
									co_dim_tempo, 
									ds_filtro_lote, 
									ds_filtro_fabricante
								FROM 
									tb_fat_vacinacao 
								WHERE 
									".$campo_busca." AND
									ds_filtro_imunobiologico LIKE ANY (array[".$imunobiologicos."])
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
					tb_dim_profissional
				ON tb_dim_profissional.co_seq_dim_profissional = t4.co_dim_profissional
			) AS t5
			ORDER BY co_dim_tempo
		";
		$run_st1 = pg_query($cdb,$select_tb1);
		if (pg_num_rows($run_st1) > 0){
			while ($ex1 = pg_fetch_array($run_st1)){
				$ex_imuno_ar = explode("|", $ex1['imuno']);
				$ex_dose_ar = explode("|", $ex1['dose']);
				$ex_lote_ar = explode("|", $ex1['lote']);
				$ex_fabricante_ar = explode("|", $ex1['fabricante']);
				foreach ($imunos_ar as $ims) {
					for($m=0;$m<count($ex_imuno_ar);$m++){
						if (trim($ims) == $ex_imuno_ar[$m]){
							$junta_vacinas = $ex1['co_dim_tempo'].$ex1['nu_cbo'].$ex1['nu_cnes'].$ex1['nu_ine'].$ex_imuno_ar[$m].$ex_dose_ar[$m].$ex_lote_ar[$m].$ex_fabricante_ar[$m];
							if (!in_array($junta_vacinas, $duplicados_vacinas)) {
								$duplicados_vacinas[$conta_duplicados_vacinas] = $junta_vacinas;
								$conta_duplicados_vacinas++;
								if ($ex_dose_ar[$m] == 3){
									$dose3 = true;
								}
								$sql_imu = "
									SELECT
										co_seq_dim_imunobiologico,
										sg_imunobiologico, 
										no_imunobiologico
									FROM
										tb_dim_imunobiologico
									WHERE
										nu_identificador = '".$ex_imuno_ar[$m]."'
									LIMIT 1
								";
								$run_im = pg_query($cdb,$sql_imu);
								$vac_grupo = '';
								$vac_regant = 0;
								if (pg_num_rows($run_im) > 0){
									$vacinas[$cont_vacinas]['imsg'] = pg_fetch_result($run_im,0,'sg_imunobiologico');
									$vacinas[$cont_vacinas]['imnome'] = pg_fetch_result($run_im,0,'no_imunobiologico');
									/*
									$dtvac = "											
										SELECT 
											t1.st_registro_anterior,
											tb_dim_grupo_atendimento.ds_grupo_atendimento
										FROM
										(
											SELECT 
												st_registro_anterior,
												co_dim_grupo_atendimento
											FROM 
												tb_fat_vacinacao_vacina
											WHERE 
												co_fat_vacinacao = ".$ex1['co_seq_fat_vacinacao']." AND
												co_dim_tempo = ".$ex1['co_dim_tempo']." AND
												co_dim_imunobiologico = ".pg_fetch_result($run_im,0,'co_seq_dim_imunobiologico')."
										) AS t1
										LEFT JOIN
											tb_dim_grupo_atendimento
										ON tb_dim_grupo_atendimento.co_seq_dim_grupo_atendimento = t1.co_dim_grupo_atendimento
									";
									$run_dtva = pg_query($cdb,$dtvac);
									if (pg_num_rows($run_dtva) > 0){
										$vac_grupo = pg_fetch_result($run_dtva,0,'ds_grupo_atendimento');
										$vac_regant = pg_fetch_result($run_dtva,0,'st_registro_anterior');
									}
									*/
								} else {
									$vacinas[$cont_vacinas]['imsg'] = "NE";
									$vacinas[$cont_vacinas]['imnome'] = "NE";
								}
								$vacinas[$cont_vacinas]['data'] = $ex1['co_dim_tempo'];
								$vacinas[$cont_vacinas]['cbo'] = $ex1['nu_cbo'];
								$vacinas[$cont_vacinas]['cnes'] = $ex1['nu_cnes'];
								$vacinas[$cont_vacinas]['ine'] = $ex1['nu_ine'];
								$vacinas[$cont_vacinas]['imuno'] = $ex_imuno_ar[$m];
								$vacinas[$cont_vacinas]['dose'] = $ex_dose_ar[$m];
								$vacinas[$cont_vacinas]['lote'] = $ex_lote_ar[$m];
								$vacinas[$cont_vacinas]['grupo'] = $vac_grupo;
								$vacinas[$cont_vacinas]['regant'] = $vac_regant;
								$vacinas[$cont_vacinas]['fabricante'] = $ex_fabricante_ar[$m];
								$vacinas[$cont_vacinas]['nu_cns'] = $ex1['nu_cns'];
								$vacinas[$cont_vacinas]['no_profissional'] = $ex1['no_profissional'];
								$cont_vacinas++;
							}
						}
					}
				}
			}
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------

		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************

		
		$idade = idadeint($vacinado_final['data_nascimento'],$dtf);


		// ===========================================================================================================
		$rel_dados_sub_tabela = "";
		
		if (count($vacinas) > 0){
			usort($vacinas, 'cmp');
			for ($i=0;$i<count($vacinas);$i++){
				$conta_geral++;
				$zebra = "";
				if (($conta_geral%2) == 0){
					$zebra = "class=\"linha-zebrada\"";
				}
				$rel_dados_sub_tabela .= "
					<tr ".$zebra."> 
						<td height=\"18\" valign=\"top\" class=\"lista-dado1-centro\">".$conta_geral."</td>
						<td valign=\"top\" class=\"lista-dado1-centro\">".mcpf($rCPF)."</td>
						<td valign=\"top\" class=\"lista-dado1-centro\">".mcns($rCNS)."</td>
						<td valign=\"top\" class=\"lista-dado1-esquerdo\">".$vacinado_final['cidadao_nome']."</td>
						<td valign=\"top\" class=\"lista-dado1-centro\">".$idade."</td>
						<td valign=\"top\" class=\"lista-dado1-centro\">".$vacinas[$i]['dose']."</td>
						<td valign=\"top\" class=\"lista-dado1-centro\">".$vacinas[$i]['imsg']."<br>".$vacinas[$i]['lote']." / ".$vacinas[$i]['fabricante']."</td>
						<td valign=\"top\" class=\"lista-dado1-centro\">".dtshow($vacinas[$i]['data'])."</td>
						<td valign=\"top\" class=\"lista-dado1-centro\">".mcns($vacinas[$i]['nu_cns'])."&nbsp;</td>
					</tr>
				";
				$texto = zesq($conta_geral,5).";".mcpf($rCPF).";".mcns($rCNS).";".str_replace("Ã","A",$vacinado_final['cidadao_nome']).";".$idade.";".$vacinas[$i]['dose'].";[".$vacinas[$i]['imsg']."] ".$vacinas[$i]['imsg'].";".$vacinas[$i]['lote'].";".$vacinas[$i]['fabricante'].";".dtshow($vacinas[$i]['data']).";".mcns($vacinas[$i]['nu_cns']).";".$vacinas[$i]['no_profissional'].";".$vacinas[$i]['imnome'].";".$vacinas[$i]['grupo'].";".$vacinas[$i]['regant']."\r\n";
				fwrite($FT, $texto);
			}
		}
		echo $rel_dados_sub_tabela;
	}
}

// ===========================================================================================================
$rel_dados_final = "
</table>
";
echo $rel_dados_final;
// ===========================================================================================================

// -----------------------------------------------------------------------------------------------------------
$link_pag = "";
if ($paginacao > 0){
	$pini = $pini + $num_vacinados_final;
	$num = $numerador_ind5;
	$cdes = $conta_geral_desconto;
	if ($num_vacinados_final > 0){
		$link_pag = "<a href=\"rel_covid1.php?pini=".$pini."&num=".$num."&cdes=".$cdes."\"><img src=\"images/pagina.png\"></a>";
	}
}
// -----------------------------------------------------------------------------------------------------------

$show_desconto = "";
if ($conta_geral_desconto > 0){
	$show_desconto = " (descontar ".$conta_geral_desconto." vacinado(es) por conta de inconsistências)";
}

$total_geral = $conta_geral;
if ($paginacao > 0){
	$total_geral = $conta_gravacao;
}
// ===========================================================================================================
$rel_rodape = "
<table width=\"1088\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"rodape-tabela\">
  <!--DWLayoutTable-->
  <tr> 
    <td width=\"1088\" height=\"25\" valign=\"top\" class=\"rodape-texto\">
	".$sobre['nome']." | 
	".$sobre['versao']." | 
	".date('d/m/Y')." | 
	Total de ".$total_geral." vacinados | 
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
