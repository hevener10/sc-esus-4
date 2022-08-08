<?php
require_once('session.php');
if (file_exists($_SESSION['filedb'])){
	require_once($_SESSION['filedb']);
} else {
	header('location:banco.php?ex=rel_hipertensos');
}
if (file_exists("cfg_rel_h.php")){
	require_once('cfg_rel_h.php');
} else {
	header('location:hipertensos.php?ex=rel_hipertensos');
}

require_once('connect.php');
require_once('functions.php');
require_once('sobre.php');
require_once('dados.php');

// +++++++++++++++++++++++++++++++++++++++++++++
$file = "csv/hiper_T_01.csv";
if (file_exists($file)){unlink($file);}
$FT = fopen($file,'w');
$file = "csv/hiper_C_01.csv";
if (file_exists($file)){unlink($file);}
$FC = fopen($file,'w');
$file = "csv/hiper_P_01.csv";
if (file_exists($file)){unlink($file);}
$FP = fopen($file,'w');
// +++++++++++++++++++++++++++++++++++++++++++++
$texto = "Seq;CPF;CNS;Nome;DtNascimento;Mae;Idade;MarcGestante;MarcHipertenso;MarcDiabetico;Indicador6;Sexo;CNES;INE;MA;NumConsS1;NumConsS2;NumProcS1;NumProcS2\r\n";
fwrite($FT, $texto);
$texto = "Seq;CPF;CNS;DtProced;CNES;INE;CBO;Semestre;CID;CIAP;CNSProf;NomeProf;ProfAlerta\r\n";
fwrite($FC, $texto);
$texto = "Seq;CPF;CNS;DtProced;DtCons;Tabela;CNSProf;NomeProf;CNES;INE;CBO;Semestre;Procedimento(A/S);ProfAlerta\r\n";
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



// ---------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------
$duplicados = array();
$conta_duplicados = 0;

$per_12m = datasomameses($dtf,12,'-');
if ($m12 == 1){
	$per_12m = datasomameses($dti,12,'-');
}

$hipertensos = "
SELECT
	*
FROM
(
	SELECT DISTINCT ON (t3.nu_cns, t3.nu_cpf_cidadao)
		t3.nu_cns,
		t3.nu_cpf_cidadao,
		t3.co_dim_tempo,
		t3.dt_nascimento,
		t3.nu_ine,
		t3.no_equipe,
		t3.nu_cnes,
		t3.no_unidade_saude,
		tb_dim_cbo.nu_cbo,
		tb_dim_cbo.no_cbo
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
						ELSE TRIM(FROM nu_cns) END
						nu_cns,
					CASE WHEN nu_cpf_cidadao IS NULL THEN '0'
						ELSE TRIM(FROM nu_cpf_cidadao) END
						nu_cpf_cidadao,
					co_dim_tempo, 
					dt_nascimento,
					CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
						ELSE co_dim_cbo_1 END
						co_dim_cbo,
					CASE WHEN co_dim_unidade_saude_1 = 1 THEN co_dim_unidade_saude_2
						ELSE co_dim_unidade_saude_1 END
						co_dim_unidade_saude,
					CASE WHEN co_dim_equipe_1 = 1 THEN co_dim_equipe_2
						ELSE co_dim_equipe_1 END
						co_dim_equipe
				FROM
					tb_fat_atendimento_individual
				WHERE
					(co_dim_tempo >= ".$per_12m." AND co_dim_tempo <= ".$dtf.") AND
					(
						ds_filtro_ciaps LIKE ANY (
							array[
								'%|K86|%',
								'%|K87|%',
								'%|W81|%',
								'%|ABP005|%'
							]
						) OR
						ds_filtro_cids LIKE ANY (
							array[
								'%|I10|%',
								'%|I11|%',
								'%|I110|%',
								'%|I119|%',
								'%|I12|%',
								'%|I120|%',
								'%|I129|%',
								'%|I13|%',
								'%|I130|%',
								'%|I131|%',
								'%|I132|%',
								'%|I139|%',
								'%|I15|%',
								'%|I150|%',
								'%|I151|%',
								'%|I152|%',
								'%|I158|%',
								'%|I159|%',
								'%|I270|%',
								'%|I272|%',
								'%|O10|%',
								'%|O100|%',
								'%|O101|%',
								'%|O102|%',
								'%|O103|%',
								'%|O104|%',
								'%|O109|%'
							]
						)
					)
				ORDER BY co_dim_tempo
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
		tb_dim_cbo
	ON tb_dim_cbo.co_seq_dim_cbo = t3.co_dim_cbo
	ORDER BY t3.nu_cns, t3.nu_cpf_cidadao, t3.co_dim_tempo DESC
) AS t4
WHERE
	nu_cbo LIKE ANY (array['2251%','2252%','2253%','2231%','2235%'])
".$esq_pag;

// ===========================================================================================================
$linhas = 0;
$rel_pagina_inicio = "
<!DOCTYPE html>
<html>
<head>
  <meta charset=\"UTF-8\">
  <link rel=\"sortcut icon\" href=\"images/favicon.ico\" type=\"image/x-icon\" />
  <title>Relatório de hipertensos</title>
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
CREATE TEMPORARY TABLE tmp_hipertensos (
	sequencia serial PRIMARY KEY,
	cns varchar(15),
	cpf varchar(11),
	data_nascimento bigint,
	g_co_dim_tempo bigint,
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
	cind_micro_area varchar(5),
	cns_resp varchar(15),
	cpf_resp varchar(11),
	cind_inativo int,
	cnes varchar(20),
	nome_unidade varchar(500),
	ine varchar(20),
	nome_equipe varchar(255),
	cds int
)
";
$run_tt = pg_query($cdb,$tabela_temp);

	
$num_hipertensos = 0;
$run_s1 = pg_query($cdb,$hipertensos);
$num_hipertensos = pg_num_rows($run_s1);
if ($num_hipertensos > 0){
	while ($hipertenso = pg_fetch_array($run_s1)){
		$CNS = '0';
		$CPF = '0';
		$campo_busca = "";
		$campo_ordem = "";
		$busca_cns = false;
		$busca_cpf = false;
		$busca = false;
		if (strlen(trim($hipertenso['nu_cns'])) == 15){
			$busca_cns = true;
			$CNS = trim($hipertenso['nu_cns']);
		}
		if (strlen(trim($hipertenso['nu_cpf_cidadao'])) == 11){
			$busca_cpf = true;
			$CPF = trim($hipertenso['nu_cpf_cidadao']);
		}
		if ($busca_cns && $busca_cpf){
			$campo_busca = "(nu_cns = '".$CNS."' OR nu_cpf = '".$CPF."')";
			$campo_ordem = "nu_cns, nu_cpf";
			$busca = true;
		} else {
			if ($busca_cns){
				$campo_busca = "nu_cns = '".$CNS."'";
				$campo_ordem = "nu_cpf DESC";
				$busca = true;
			}
			if ($busca_cpf){
				$campo_busca = "nu_cpf = '".$CPF."'";
				$campo_ordem = "nu_cns DESC";
				$busca = true;
			}
		}
		if ($busca){
			$cidadaos = "
			SELECT
				st_ativo, 
				CASE WHEN nu_cns IS NULL THEN '0'
					ELSE TRIM(FROM nu_cns) END
					nu_cns,
				CASE WHEN nu_cpf IS NULL THEN '0'
					ELSE TRIM(FROM nu_cpf) END
					nu_cpf,
				no_cidadao, 
				dt_nascimento, 
				no_mae, 
				dt_obito, 
				st_faleceu, 
				nu_micro_area, 
				st_fora_area, 
				CASE WHEN nu_cns_responsavel IS NULL THEN '0'
					ELSE TRIM(FROM nu_cns_responsavel) END
					nu_cns_responsavel,
				CASE WHEN nu_cpf_responsavel IS NULL THEN '0'
					ELSE TRIM(FROM nu_cpf_responsavel) END
					nu_cpf_responsavel,
				no_sexo
			FROM
				tb_cidadao
			WHERE
				".$campo_busca."
			ORDER BY
				".$campo_ordem."
			LIMIT 1
			";
			$cidadao_ativo = 1;
			$cidadao_cpf = '0';
			$cidadao_cns = '0';
			$cidadao_nome = "# HIPERTENSO NÃO ENCONTRADO #";
			$cidadao_mae = "# SEM NOME DA MAE #";
			$cidadao_nascimento = $hipertenso['dt_nascimento'];
			$cidadao_obito = NULL;
			$cidadao_faleceu = 0;
			$cidadao_cns_res = '0';
			$cidadao_cpf_res = '0';
			$cidadao_ma = '00';
			$cidadao_sexo = "FEMININO";
			$num_cidadaos = 0;
			$run_s2 = pg_query($cdb,$cidadaos);
			$num_cidadaos = pg_num_rows($run_s2);
			if ($num_cidadaos > 0){
				$cidadao_ativo = pg_fetch_result($run_s2,0,'st_ativo');
				$cidadao_cpf = trim(pg_fetch_result($run_s2,0,'nu_cpf'));
				$cidadao_cns = trim(pg_fetch_result($run_s2,0,'nu_cns'));
				$cidadao_nome = trim(pg_fetch_result($run_s2,0,'no_cidadao'));
				if (strlen(trim(pg_fetch_result($run_s2,0,'no_mae'))) > 0){
					$cidadao_mae = trim(pg_fetch_result($run_s2,0,'no_mae'));
				}
				$cidadao_nascimento = pg_fetch_result($run_s2,0,'dt_nascimento');
				$cidadao_obito = pg_fetch_result($run_s2,0,'dt_obito');
				$cidadao_faleceu = pg_fetch_result($run_s2,0,'st_faleceu');
				$cidadao_cns_res = trim(pg_fetch_result($run_s2,0,'nu_cns_responsavel'));
				$cidadao_cpf_res = trim(pg_fetch_result($run_s2,0,'nu_cpf_responsavel'));
				$cidadao_ma = pg_fetch_result($run_s2,0,'nu_micro_area');
				if (strlen($cidadao_ma) == 0 || $cidadao_ma == '0' || $cidadao_ma == NULL){
					$cidadao_ma = '00';
				}
				if (strlen($cidadao_ma) == 1){
					$cidadao_ma = '0'.$cidadao_ma;
				}
				$cidadao_sexo = pg_fetch_result($run_s2,0,'no_sexo');
				if (!$busca_cns){
					if (strlen($cidadao_cns) == 15){
						$CNS = $cidadao_cns;
						$busca_cns = true;
					}
				}
				if (!$busca_cpf){
					if (strlen($cidadao_cpf) == 11){
						$CPF = $cidadao_cpf;
						$busca_cpf = true;
					}
				}
			}
			if ($busca_cns && $busca_cpf){
				$campo_busca = "(nu_cns = '".$CNS."' OR nu_cpf_cidadao = '".$CPF."')";
			} else {
				if ($busca_cns){
					$campo_busca = "nu_cns = '".$CNS."'";
				}
				if ($busca_cpf){
					$campo_busca = "nu_cpf_cidadao = '".$CPF."'";
				}
			}
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

				if (strlen($cind_micro_area) == 0 || $cind_micro_area == '0' || $cind_micro_area == NULL){
					$cind_micro_area = '00';
				}
				if (strlen($cind_micro_area) == 1){
					$cind_micro_area = '0'.$cind_micro_area;
				}
				if ($cind_micro_area == '00' && $cidadao_ma != '00'){
					$cind_micro_area = $cidadao_ma;
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
			if (strlen($cind_cns_res) < 15){
				$cind_cns_res = $cidadao_cns_res;
			}
			if (strlen($cind_cpf_res) < 11){
				$cind_cpf_res = $cidadao_cpf_res;
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
				$data_nascimento = dataint($hipertenso['dt_nascimento']); // ou dataint($cidadao_nascimento)
				$data_falecimento = "0";
				if (strlen(trim($cidadao_obito)) > 5){
					$data_falecimento = dataint($cidadao_obito);
				} else {
					if (strlen(trim($cind_obito)) > 5){
						$data_falecimento = dataint($cind_obito);
					}
				}
				if ($cind_micro_area == '-' || $cind_micro_area == '--' || $cind_micro_area == '0-'){
					$cind_micro_area = '00';
				}
				if ($ine == '-'){
					$ine = '0000000000';
				}

				//**********************************************************************************
				$nome_cidadao = $cidadao_nome;
				$nome_mae_cidadao = $cidadao_mae;
				$cds = 0;
				//----------------------------------------------
				/*
				if ((substr($nome_cidadao,0,1) == '#' || strlen($nome_cidadao) <= 0) && strlen($CPF) == 11){
					// busca no cds
					$bcds = "
						SELECT DISTINCT ON (no_cidadao, no_mae_cidadao, dt_nascimento)
							no_cidadao,
							--SUBSTRING('testetesteettetesteste', 1, 10) AS teste,
							to_char(dt_nascimento,'YYYY-MM-DD') AS dt_nascimento,
							co_sexo,
							no_mae_cidadao,
							dt_obito,
							nu_micro_area,
							nu_cpf_cidadao,
							nu_cpf_responsavel
						FROM
							tb_cds_cad_individual
						WHERE 
							nu_cpf_cidadao = '".$CPF."'
						ORDER BY no_cidadao, no_mae_cidadao, dt_nascimento, dt_cad_individual DESC
						LIMIT 1
					";
					$run_cds = pg_query($cdb,$bcds);
					if (pg_num_rows($run_cds) > 0){
						$cds = 1;
						$nome_cidadao = pg_fetch_result($run_cds,0,'no_cidadao');
						$nome_mae_cidadao = pg_fetch_result($run_cds,0,'no_mae_cidadao');
					}
				}
				*/
				//----------------------------------------------
				$nome_cidadao = htmlspecialchars(trim($nome_cidadao), ENT_QUOTES);
				$nome_mae_cidadao = htmlspecialchars(trim($nome_mae_cidadao), ENT_QUOTES);
				//**********************************************************************************

				$inserir = "
				INSERT INTO tmp_hipertensos(
					cns,
					cpf,
					data_nascimento,
					g_co_dim_tempo,
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
					cns_resp,
					cpf_resp,
					cind_inativo,
					cnes,
					nome_unidade,
					ine,
					nome_equipe,
					cds
				) VALUES (
					'".$CNS."',
					'".$CPF."',
					".$data_nascimento.",
					".$hipertenso['co_dim_tempo'].",
					".$cidadao_ativo.",
					".$data_falecimento.",
					'".$nome_cidadao."',
					'".$nome_mae_cidadao."',
					".$cidadao_faleceu.",
					'".$cidadao_sexo."',
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
					'".$nome_equipe."',
					".$cds."
				)";
				
				$gravar = false;
				if ($super_filtro){
					if ($cpbusca == 'U'){
						$vlbusca = zesq($vlbusca,7);
						if ($cnes == $vlbusca){
							$gravar = true;
						}
					}
					if ($cpbusca == 'E'){
						$vlbusca = zesq($vlbusca,10);
						if ($ine == $vlbusca){
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
						if ($cind_micro_area == $vlbusca){
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
					if ($cidadao_faleceu == 0){
						if ($cfa == 0){
							$run_ins = pg_query($cdb,$inserir);
							$conta_gravacao++;
						} else {
							if ($cpbusca != 'M'){
								if ($cind_micro_area != '00' && $cind_micro_area != 'FA'){
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
}

// ===========================================================================================================
$rel_cabecalho_2 = "
<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"relatorio-tabela\">
  <!--DWLayoutTable-->
  <tr> 
    <td width=\"440\" height=\"32\" valign=\"top\" class=\"relatorio-titulo\">Relatório de Hipertensos</td>
    <td width=\"392\" valign=\"top\" class=\"relatorio-periodo\">Período entre ".dtshow($dti)." e ".dtshow($dtf)."</td>
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
	}
}
//++++++++++++++++++++++++
$ordena_grupo = $grupo.",";
if ($grupo == "SG"){
	$ordena_grupo = "";
}
$quebra = "";
$hipertensos_final = "
SELECT
	*
FROM
	tmp_hipertensos
ORDER BY ".$ordena_grupo." ".$ordenar;
//++++++++++++++++++++++++

$num_hipertensos_final = 0;
$run_hi = pg_query($cdb,$hipertensos_final);
$num_hipertensos_final = pg_num_rows($run_hi);
$conta_geral = $pini;
$conta_geral_desconto = $cdes;
$numerador_ind6 = $num;

if ($num_hipertensos_final > 0){
	while ($hipertenso_final = pg_fetch_array($run_hi)){
		$duplicados_cons = array();
		$conta_duplicados_cons = 0;
		$duplicados_exames = array();
		$conta_duplicados_exames = 0;
		$conta_geral++;
		
		$rCNS = trim($hipertenso_final['cns']);
		$rCPF = trim($hipertenso_final['cpf']);
		
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
			$grupo_id = $hipertenso_final[$grupo];
			
			$grupo_nome = "Micro-Área";
			$show_gp_inverso = "Unidade";	
			$show_gp_id_inverso = $hipertenso_final['cnes'];	
			$show_gp_nm_inverso = $hipertenso_final['nome_unidade'];
			$show_gp_id2_inverso = $hipertenso_final['ine'];
			$show_gp_nm2_inverso = "Equipe";
			if ($grupo == "ine"){
				$grupo_nome = $hipertenso_final['nome_equipe'];	
				$show_gp_id2_inverso = $hipertenso_final['cind_micro_area'];
				$show_gp_nm2_inverso = "Micro-&Aacute;rea";
			}
			if ($grupo == "cnes"){
				$grupo_nome = $hipertenso_final['nome_unidade'];
				$show_gp_inverso = "Equipe";	
				$show_gp_id_inverso = $hipertenso_final['ine'];	
				$show_gp_nm_inverso = $hipertenso_final['nome_equipe'];
				$show_gp_id2_inverso = $hipertenso_final['cind_micro_area'];
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
			$show_gp_id_inverso = $hipertenso_final['cnes'];	
			$show_gp_nm_inverso = $hipertenso_final['nome_unidade']." (".$hipertenso_final['ine'].")";
			$show_gp_id2_inverso = $hipertenso_final['cind_micro_area'];
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
		$dt_6meses1 = datasomadias($per_12m,1);
		$dt_6meses2 = semestrem($dtf);
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		
		$consultas_ar = array();
		$consultas_ar_data = array();
		$cont_consultas_ar = 0;
		$consultas = "
			SELECT
				*
			FROM
			(
				-------------------------------------------------------
				-------------------------------------------------------
				-------------------------------------------------------
				SELECT
					t5.*,
					tb_dim_profissional.nu_cns AS cns_prof,
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
									co_dim_tempo,
									CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
										ELSE co_dim_cbo_1 END
										co_dim_cbo,
									CASE WHEN co_dim_unidade_saude_1 = 1 THEN co_dim_unidade_saude_2
										ELSE co_dim_unidade_saude_1 END
										co_dim_unidade_saude,
									CASE WHEN co_dim_equipe_1 = 1 THEN co_dim_equipe_2
										ELSE co_dim_equipe_1 END
										co_dim_equipe,
										CASE WHEN co_dim_profissional_1 = 1 THEN co_dim_profissional_2
											ELSE co_dim_profissional_1 END
											co_dim_profissional,
									ds_filtro_ciaps,
									ds_filtro_cids
								FROM
									tb_fat_atendimento_individual
								WHERE
									".$campo_busca." AND
									(co_dim_tempo >= ".$dt_6meses1." AND co_dim_tempo <= ".$dtf.") AND
									(
										ds_filtro_ciaps LIKE ANY (
											array[
												'%|K86|%',
												'%|K87|%',
												'%|W81|%',
												'%|ABP005|%'
											]
										) OR
										ds_filtro_cids LIKE ANY (
											array[
												'%|I10|%',
												'%|I11|%',
												'%|I110|%',
												'%|I119|%',
												'%|I12|%',
												'%|I120|%',
												'%|I129|%',
												'%|I13|%',
												'%|I130|%',
												'%|I131|%',
												'%|I132|%',
												'%|I139|%',
												'%|I15|%',
												'%|I150|%',
												'%|I151|%',
												'%|I152|%',
												'%|I158|%',
												'%|I159|%',
												'%|I270|%',
												'%|I272|%',
												'%|O10|%',
												'%|O100|%',
												'%|O101|%',
												'%|O102|%',
												'%|O103|%',
												'%|O104|%',
												'%|O109|%'
											]
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
				) AS t5
				LEFT JOIN
					tb_dim_profissional
				ON tb_dim_profissional.co_seq_dim_profissional = t5.co_dim_profissional
				-------------------------------------------------------
				-------------------------------------------------------
				-------------------------------------------------------
			) AS t4
			WHERE
				nu_cbo LIKE ANY (array['2251%','2252%','2253%','2231%','2235%'])
			ORDER BY co_dim_tempo
		";
		$conta_consultas_s1 = 0;
		$conta_consultas_s2 = 0;
		$run_con = pg_query($cdb,$consultas);
		if (pg_num_rows($run_con) > 0){
			while ($exc = pg_fetch_array($run_con)){
				$junta_consultas = $exc['co_dim_tempo'].$exc['nu_cbo'];
				if (!in_array($junta_consultas, $duplicados_cons)) {
					$duplicados_cons[$conta_duplicados_cons] = $junta_consultas;
					$conta_duplicados_cons++;
					$consultas_ar[$cont_consultas_ar]['data'] = $exc['co_dim_tempo'];
					$consultas_ar[$cont_consultas_ar]['cbo'] = $exc['nu_cbo'];
					$consultas_ar[$cont_consultas_ar]['cnes'] = $exc['nu_cnes'];
					$consultas_ar[$cont_consultas_ar]['ine'] = $exc['nu_ine'];
					$consultas_ar[$cont_consultas_ar]['ciaps'] = str_replace("|"," ",$exc['ds_filtro_ciaps']);
					$consultas_ar[$cont_consultas_ar]['cids'] = str_replace("|"," ",$exc['ds_filtro_cids']);
					$consultas_ar[$cont_consultas_ar]['semestre'] = '0';
					$consultas_ar[$cont_consultas_ar]['cns_prof'] = $exc['cns_prof'];
					$consultas_ar[$cont_consultas_ar]['no_profissional'] = $exc['no_profissional'];
					if ($exc['co_dim_tempo'] >= $dt_6meses1 && $exc['co_dim_tempo'] < $dt_6meses2){
						$conta_consultas_s1++;
						$consultas_ar[$cont_consultas_ar]['semestre'] = '1';
					}
					if ($exc['co_dim_tempo'] >= $dt_6meses2 && $exc['co_dim_tempo'] <= $dtf){
						$conta_consultas_s2++;
						$consultas_ar[$cont_consultas_ar]['semestre'] = '2';
					}
					$consultas_ar_data[$cont_consultas_ar] = $exc['co_dim_tempo'];
					$cont_consultas_ar++;
				}
			}
		}
		
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		
		$exames = array();
		$cont_exames = 0;
		$contro_cbo = "'2251%', '2252%', '2253%', '2231%', '2235%', '3222%'";
		$procedimentos = "'0301100039','ABPG033'";
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
											(co_dim_tempo >= ".$dt_6meses1." AND co_dim_tempo <= ".$dtf.") AND
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
					$exames[$cont_exames]['procedimento1'] = $ex1['avaliado'];
					$exames[$cont_exames]['procedimento2'] = $ex1['solicitado'];
					$exames[$cont_exames]['tabela'] = $ex1['tabela'];
					$exames[$cont_exames]['cns_prof'] = $ex1['cns_prof'];
					$exames[$cont_exames]['no_profissional'] = $ex1['no_profissional'];
					if ($ex1['co_dim_tempo'] >= $dt_6meses1 && $ex1['co_dim_tempo'] < $dt_6meses2){
						$exames[$cont_exames]['semestre'] = '1';
					}
					if ($ex1['co_dim_tempo'] >= $dt_6meses2 && $ex1['co_dim_tempo'] <= $dtf){
						$exames[$cont_exames]['semestre'] = '2';
					}
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
										(co_dim_tempo >= ".$dt_6meses1." AND co_dim_tempo <= ".$dtf.") AND
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
					$exames[$cont_exames]['procedimento1'] = $ex2['procedimento'];
					$exames[$cont_exames]['procedimento2'] = '-';
					$exames[$cont_exames]['tabela'] = $ex2['tabela'];
					$exames[$cont_exames]['cns_prof'] = $ex2['cns_prof'];
					$exames[$cont_exames]['no_profissional'] = $ex2['no_profissional'];
					if ($ex2['co_dim_tempo'] >= $dt_6meses1 && $ex2['co_dim_tempo'] < $dt_6meses2){
						$exames[$cont_exames]['semestre'] = '1';
					}
					if ($ex2['co_dim_tempo'] >= $dt_6meses2 && $ex2['co_dim_tempo'] <= $dtf){
						$exames[$cont_exames]['semestre'] = '2';
					}
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
			if (strlen($hipertenso_final['cns_resp']) == 15 && strlen($hipertenso_final['cpf_resp']) == 11){
				$campo_busca_res = "(nu_cns_responsavel = '".$hipertenso_final['cns_resp']."' OR nu_cpf_responsavel = '".$hipertenso_final['cpf_resp']."')";
			} else {
				if (strlen($hipertenso_final['cns_resp']) == 15){
					$campo_busca_res = "nu_cns_responsavel = '".$hipertenso_final['cns_resp']."'";
				}
				if (strlen($hipertenso_final['cpf_resp']) == 11){
					$campo_busca_res = "nu_cpf_responsavel = '".$hipertenso_final['cpf_resp']."'";
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
		if ($hipertenso_final['cind_gestante'] == 1){
			$marcado_gestante = "SIM";
		}
		if ($hipertenso_final['cind_hipertenso'] == 1){
			$marcado_hipertenso = "SIM";
		}
		if ($hipertenso_final['cind_diabetico'] == 1){
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
			$conta_geral_desconto++;
		}
		if ($rCNS == '0' && $rCPF == '0'){
			$inconsistencias .= "Sem CNS e sem CPF.<br>-<br>";
			$conta_geral_desconto++;
		}
		if ($ma_familiar == '00' && $hipertenso_final['cind_micro_area'] == '00'){
			$inconsistencias .= " Hipertenso sem cadastro familiar.<br>";
		}

		if (substr($hipertenso_final['cidadao_nome'],0,1) == '#'){
			$inconsistencias .= "Hipertenso não localizada em tb_cidadao.<br>";
		}
		$inativo2 = 0;
		if ($hipertenso_final['cidadao_ativo'] != 1){
			//$inconsistencias .= "Cadastro está como inativo (Cidadao).<br>";
			$inativo2++;
		}
		if ($hipertenso_final['cind_inativo'] != 0){
			$inconsistencias .= "Cadastro está como inativo (Cad. Ind.).<br>";
			$inativo2++;
		}
		if ($inativo2 >= 2){
			$conta_geral_desconto++;
			$inconsistencias .= "-<br>";
		}
		$faleceu2 = 0;
		if ($hipertenso_final['cidadao_faleceu'] != 0){
			$inconsistencias .= "Faleceu (Cidadao)<br>";
			$faleceu2++;
		}
		if (strlen($hipertenso_final['data_falecimento']) > 5){
			$inconsistencias .= "Faleceu em ".dtshow($hipertenso_final['data_falecimento'])."<br>";
			$faleceu2++;
		}
		if ($faleceu2 >= 2){
			$conta_geral_desconto++;
			$inconsistencias .= "-<br>";
		}
		if (strlen($inconsistencias) > 0){
			$estilo_inco = "lista-status-Vermelho";
		}
		
		$idade = idadeint($hipertenso_final['data_nascimento'],$dtf);
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		$exame_rotina = "NÃO";
		$estilo_exrot = "indicador-c1-NAO";
		$estilo_exrot_indicador = "indicador-titulo-Vermelho-B";
		$show_e_data = "";
		$semestre1 = false;
		$semestre2 = false;
		$soma_proc_s1 = 0;
		$soma_proc_s2 = 0;
		if (count($exames) > 0){
			for ($i=0;$i<count($exames);$i++){
				if ($exames[$i]['semestre'] == '1'){
					$soma_proc_s1++;
				} else {
					$soma_proc_s2++;
				}
			}
			$conta_consultas_x = $conta_consultas_s1 + $conta_consultas_s2;
			if ($soma_proc_s1 > 0 && $conta_consultas_x > 0){
				$semestre1 = true;
			}
			if ($soma_proc_s2 > 0 && $conta_consultas_x > 0){
				$semestre2 = true;
			}
			if ($semestre1 && $semestre2){
				$exame_rotina = "SIM";
				$estilo_exrot = "indicador-c1-SIM";
				$estilo_exrot_indicador = "indicador-titulo-Verde-B";
				$numerador_ind6++;
			} else {
				if ($semestre1){
					$estilo_exrot_indicador = "indicador-titulo-Amarelo-B";
				}
				if ($semestre2){
					$estilo_exrot_indicador = "indicador-titulo-Amarelo-B";
				}
			}
		}
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// ===========================================================================================================
		$rel_dados_unitario = "
		  <tr> 
			<td height=\"18\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-BB\">".zesq($conta_geral,5)."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".mcpf($rCPF)."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado1-centro-B\">".mcns($rCNS)."</td>
			<td colspan=\"5\" valign=\"top\" class=\"lista-dado1-esquerdo-B\">".$hipertenso_final['cidadao_nome']."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".dtshow($hipertenso_final['data_nascimento'])."</td>
			<td colspan=\"2\" rowspan=\"5\" valign=\"top\" class=\"".$estilo_inco."\"><p>
			".$inconsistencias."
			</p></td>
		  </tr>
		  <tr> 
			<td height=\"19\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\"><!--DWLayoutEmptyCell-->&nbsp;</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Nome da m&atilde;e</td>
			<td colspan=\"7\" valign=\"top\" class=\"lista-dado2-centro-B\">".$hipertenso_final['cidadao_mae']."</td>
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
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Sexo</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$hipertenso_final['cidadao_sexo']."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">Declarado Diab&eacute;tico</td>
			<td width=\"69\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_diabetico."</td>
			<td width=\"84\" valign=\"top\" class=\"lista-sub-dados-B\">".$show_gp_nm2_inverso."</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_id2_inverso."</td>
		  </tr>
		  <tr> 
			<td height=\"19\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">.</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$hipertenso_final['cds']."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">Declarado Hipertenso</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_hipertenso."</td>
			<td valign=\"top\" class=\"lista-sub-dados-B\">Idade</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$idade."</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" rowspan=\"2\" valign=\"top\" class=\"".$estilo_exrot_indicador."\">Indicador 6 [ ".$exame_rotina." ]</td>
			<td colspan=\"3\" rowspan=\"2\" valign=\"top\" class=\"indicador-c1-B\">Verificação de pressão arterial</td>
			<td colspan=\"4\" rowspan=\"2\" valign=\"top\" class=\"indicador-c1-centro-X-B\">.</td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-c1\">Semestre 1: Cons. ".$conta_consultas_s1." | Proc. ".$soma_proc_s1."</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-cx-B\">Semestre 2: Cons. ".$conta_consultas_s2." | Proc. ".$soma_proc_s2."</td>
		  </tr>
		  ";
		$linhas = $linhas + 13;
		echo $rel_dados_unitario;
		
		$texto = zesq($conta_geral,5).";".mcpf($rCPF).";".mcns($rCNS).";".str_replace("Ã","A",$hipertenso_final['cidadao_nome']).";".dtshow($hipertenso_final['data_nascimento']).";".str_replace("Ã","A",$hipertenso_final['cidadao_mae']).";".$idade.";".str_replace("Ã","A",$marcado_gestante).";".str_replace("Ã","A",$marcado_hipertenso).";".str_replace("Ã","A",$marcado_diabetico).";".str_replace("Ã","A",$exame_rotina).";".$hipertenso_final['cidadao_sexo'].";".$hipertenso_final['cnes'].";".$hipertenso_final['ine'].";".$ma_familiar."/".$hipertenso_final['cind_micro_area'].";".$conta_consultas_s1.";".$conta_consultas_s2.";".$soma_proc_s1.";".$soma_proc_s2."\r\n";
		fwrite($FT, $texto);
		
		if (count($consultas_ar) > 0){
			usort($consultas_ar, 'cmp');
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
					  <td width=\"169\" valign=\"top\" class=\"procedimentos-cabeca\">CID / CIAP</td>
					  <td width=\"75\" valign=\"top\" class=\"procedimentos-cabeca\">Data</td>
					  <td width=\"213\" valign=\"top\" class=\"procedimentos-cabeca\">CNS Prof.</td>
					  <td width=\"43\" valign=\"top\" class=\"procedimentos-cabeca\">CBO</td>
					  <td width=\"78\" valign=\"top\" class=\"procedimentos-cabeca\">INE</td>
					  <td width=\"56\" valign=\"top\" class=\"procedimentos-cabeca\">CNES</td>
					  <td width=\"62\" valign=\"top\" class=\"procedimentos-cabeca\">Semes.</td>
					</tr>
			";
			$soma_consultas = 0;
			for ($i=0;$i<count($consultas_ar);$i++){
				$soma_consultas++;

				$profissionalOK = 'procedimentos-dado';
				$profissionalAlerta = 'NAO';
				if (strlen($consultas_ar[$i]['ine']) == 10){
					if (file_exists('xml/cnes.xml')){
						if (!cnes('xml/cnes.xml',$consultas_ar[$i]['cns_prof'],$consultas_ar[$i]['cbo'],$consultas_ar[$i]['ine'],'I')){
							$profissionalOK = 'procedimentos-dado-amarelo';
							$profissionalAlerta = 'SIM';
						}
					}
				}
				$texto = $soma_consultas.";".mcpf($rCPF).";".mcns($rCNS).";".dtshow($consultas_ar[$i]['data']).";".$consultas_ar[$i]['cnes'].";".$consultas_ar[$i]['ine'].";".$consultas_ar[$i]['cbo'].";".$consultas_ar[$i]['semestre'].";".$consultas_ar[$i]['cids'].";".$consultas_ar[$i]['ciaps'].";".$consultas_ar[$i]['cns_prof'].";".$consultas_ar[$i]['no_profissional'].";".$profissionalAlerta."\r\n";
				fwrite($FC, $texto);
				$rel_dados_sub_tabela .= "
						<tr> 
						  <td height=\"18\" valign=\"top\" class=\"procedimentos-dado\">".$soma_consultas."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$consultas_ar[$i]['cids']." / ".$consultas_ar[$i]['ciaps']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".dtshow($consultas_ar[$i]['data'])."</td>
						  <td valign=\"top\" class=\"".$profissionalOK."\">".$consultas_ar[$i]['cns_prof']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$consultas_ar[$i]['cbo']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$consultas_ar[$i]['ine']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$consultas_ar[$i]['cnes']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$consultas_ar[$i]['semestre']."</td>
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
					  <td valign=\"top\" class=\"procedimentos-dado\">".$soma_consultas."</td>
					</tr>
				  </table>
				  </td>
			  </tr>
			";
			echo $rel_dados_sub_tabela;
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
					  <td width=\"213\" valign=\"top\" class=\"procedimentos-cabeca\">CNS Prof.</td>
					  <td width=\"43\" valign=\"top\" class=\"procedimentos-cabeca\">CBO</td>
					  <td width=\"78\" valign=\"top\" class=\"procedimentos-cabeca\">INE</td>
					  <td width=\"56\" valign=\"top\" class=\"procedimentos-cabeca\">CNES</td>
					  <td width=\"62\" valign=\"top\" class=\"procedimentos-cabeca\">Semes.</td>
					</tr>
			";
			$soma_procedimentos = 0;
			for ($i=0;$i<count($exames);$i++){
				$soma_procedimentos++;
				
				$consproced = '';
				$consproced_csv = 'N';
				if (in_array($exames[$i]['data'], $consultas_ar_data)) {
					$consproced = '*';
					$consproced_csv = 'S';
				}

				$profissionalOK = 'procedimentos-dado';
				$profissionalAlerta = 'NAO';
				if (strlen($exames[$i]['ine']) == 10){
					if (file_exists('xml/cnes.xml')){
						if (!cnes('xml/cnes.xml',$exames[$i]['cns_prof'],$exames[$i]['cbo'],$exames[$i]['ine'],'I')){
							$profissionalOK = 'procedimentos-dado-amarelo';
							$profissionalAlerta = 'SIM';
						}
					}
				}
				$texto = $soma_procedimentos.";".mcpf($rCPF).";".mcns($rCNS).";".dtshow($exames[$i]['data']).";".$consproced_csv.";".$exames[$i]['tabela'].";".$exames[$i]['cns_prof'].";".$exames[$i]['no_profissional'].";".$exames[$i]['cnes'].";".$exames[$i]['ine'].";".$exames[$i]['cbo'].";".$exames[$i]['semestre'].";".$exames[$i]['procedimento1']."/".$exames[$i]['procedimento2'].";".$profissionalAlerta."\r\n";
				fwrite($FP, $texto);
				$rel_dados_sub_tabela .= "
						<tr> 
						  <td height=\"18\" valign=\"top\" class=\"procedimentos-dado\">".$soma_procedimentos."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['procedimento1']." / ".$exames[$i]['procedimento2']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".dtshow($exames[$i]['data'])." ".$consproced."</td>
						  <td valign=\"top\" class=\"".$profissionalOK."\">".$exames[$i]['cns_prof']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['cbo']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['ine']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['cnes']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['semestre']."</td>
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
	$pini = $pini + $num_hipertensos_final;
	$num = $numerador_ind6;
	$cdes = $conta_geral_desconto;
	if ($num_hipertensos_final > 0){
		$link_pag = "<a href=\"rel_hipertensos.php?pini=".$pini."&num=".$num."&cdes=".$cdes."\"><img src=\"images/pagina.png\"></a>";
	}
}
// -----------------------------------------------------------------------------------------------------------

$show_desconto = "";
if ($conta_geral_desconto > 0){
	$show_desconto = " (descontar ".$conta_geral_desconto." hipertenso(s) por conta de inconsistências)";
}

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
	Total de ".$total_geral." hipertensos | 
	Ordenado por ".$show_ordem." | ".$dbdb." |
	".$link_pag."
	</td>
  </tr>
</table>
";
$linhas = $linhas + 1;
echo $rel_rodape;
// ===========================================================================================================

// ===========================================================================================================
if ($paginacao <= 0 || $num_hipertensos_final == 0){
	$rel_indicadores = "
	</table>
	<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
	  <!--DWLayoutTable-->
	  <tr> 
		<td width=\"214\" height=\"30\">&nbsp;</td>
		<td width=\"260\" valign=\"top\" class=\"resumo-sub-titulo\">Indicador 6</td>
		<td width=\"120\">&nbsp;</td>
	  </tr>
	  <tr> 
		<td height=\"30\" valign=\"top\" class=\"resumo-numerador\">Numerador&nbsp;&nbsp;</td>
		<td valign=\"top\" class=\"resumo-v-numerador\">".$numerador_ind6."</td>
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

fclose($FT);
fclose($FC);
fclose($FP);

?>