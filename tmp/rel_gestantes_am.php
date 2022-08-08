<?php
require_once('session.php');
if (file_exists("cfg_db.php")){
	require_once('cfg_db.php');
} else {
	header('location:banco.php?ex=rel_gestantes');
}
if (file_exists("cfg_rel_g.php")){
	require_once('cfg_rel_g.php');
} else {
	header('location:gestantes.php?ex=rel_gestantes');
}

require_once('connect.php');
require_once('functions.php');
require_once('sobre.php');
require_once('dados.php');

// +++++++++++++++++++++++++++++++++++++++++++++
$file = "csv/gest_T_01.csv";
if (file_exists($file)){unlink($file);}
$FT = fopen($file,'w');
$file = "csv/gest_C_01.csv";
if (file_exists($file)){unlink($file);}
$FC = fopen($file,'w');
$file = "csv/gest_P_01.csv";
if (file_exists($file)){unlink($file);}
$FP = fopen($file,'w');
// +++++++++++++++++++++++++++++++++++++++++++++
$texto = "Seq;CPF;CNS;Nome;DtNascimento;Mae;Idade;MarcGestante;MarcHipertensa;MarcDiabetica;Interrupcao;InterCC;DUM;20s;DPP;FimPuerperio;NumConsultas;DtPrimConsulta;NumConsOdonto;Indicador1;Indicador2;Indicador3;Sexo;CNES;INE;MA\r\n";
fwrite($FT, $texto);
$texto = "Seq;CPF;CNS;DtConsulta;DUMdaCons;CNES;INE;CBO;CNSProf;ProfAlerta\r\n";
fwrite($FC, $texto);
$texto = "Seq;CPF;CNS;DtProced;DtCons;Tabela;CNSProf;CNES;INE;CBO;Exame;Procedimento(A/S);ProfAlerta\r\n";
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
$num1 = isset($_GET["num1"]) ? trim($_GET["num1"]) : 0;
$num2 = isset($_GET["num2"]) ? trim($_GET["num2"]) : 0;
$num3 = isset($_GET["num3"]) ? trim($_GET["num3"]) : 0;
$cdes = isset($_GET["cdes"]) ? trim($_GET["cdes"]) : 0;
$esq_pag = "";
if ($paginacao > 0){
	$esq_pag = "LIMIT ".$paginacao." OFFSET ".$pini;
}
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if ($gpa == 0){
	$ndti = datasomadias($dti,$dpp,'-');
	$ndtf = datasomadias($dtf,($dpp+1),'-');
	$dtf = datasomadias($dtf,1);
} else {
	$ndti = datasomadias(date('Ymd'),$dpp,'-');
	$ndtf = 30001231;
	$dtf = 30001231;
}
$cdum = ", co_dim_tempo DESC";
if ($dum == 'A'){
	$cdum = ", co_dim_tempo_dum";
} else {
	if ($dum == 'N'){
		$cdum = ", co_dim_tempo_dum DESC";
	}
}
// ---------------------------------------------------------------------------------
// ---------------------------------------------------------------------------------
$duplicados = array();
$conta_duplicados = 0;

$gestantes = "
SELECT 
	* 
FROM
(
	SELECT DISTINCT ON (nu_cns, nu_cpf_cidadao) 
		CASE WHEN nu_cns IS NULL THEN '0'
			ELSE TRIM(FROM nu_cns) END
			nu_cns,
		CASE WHEN nu_cpf_cidadao IS NULL THEN '0'
			ELSE TRIM(FROM nu_cpf_cidadao) END
			nu_cpf_cidadao,
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
		dt_nascimento,
		co_dim_tempo_dum
	FROM
		tb_fat_atendimento_individual
	WHERE
		co_dim_tempo_dum >= ".$ndti." AND 
		co_dim_tempo_dum < ".$ndtf."
		AND (
			ds_filtro_ciaps LIKE ANY (
				array[
					'%|W03|%',
					'%|W05|%',
					'%|W29|%',
					'%|W71|%',
					'%|W78|%',
					'%|W79|%',
					'%|W80|%',
					'%|W81|%',
					'%|W84|%',
					'%|W85|%',
					--'%|W72|%',
					--'%|W73|%',
					--'%|W75|%',
					--'%|W76|%',
					'%|ABP001|%'
				]
			) OR
			ds_filtro_cids LIKE ANY (
				array[
					'%|O11|%',
					'%|O120|%',
					'%|O121|%',
					'%|O122|%',
					'%|O13|%',
					'%|O140|%',
					'%|O141|%',
					'%|O149|%',
					'%|O150|%',
					'%|O151|%',
					'%|O159|%',
					'%|O16|%',
					'%|O200|%',
					'%|O208|%',
					'%|O209|%',
					'%|O210|%',
					'%|O211|%',
					'%|O212|%',
					'%|O218|%',
					'%|O219|%',
					'%|O220|%',
					'%|O221|%',
					'%|O222|%',
					'%|O223|%',
					'%|O224|%',
					'%|O225|%',
					'%|O228|%',
					'%|O229|%',
					'%|O230|%',
					'%|O231|%',
					'%|O232|%',
					'%|O233|%',
					'%|O234|%',
					'%|O235|%',
					'%|O239|%',
					'%|O299|%',
					'%|O300|%',
					'%|O301|%',
					'%|O302|%',
					'%|O308|%',
					'%|O309|%',
					'%|O311|%',
					'%|O312|%',
					'%|O318|%',
					'%|O320|%',
					'%|O321|%',
					'%|O322|%',
					'%|O323|%',
					'%|O324|%',
					'%|O325|%',
					'%|O326|%',
					'%|O328|%',
					'%|O329|%',
					'%|O330|%',
					'%|O331|%',
					'%|O332|%',
					'%|O333|%',
					'%|O334|%',
					'%|O335|%',
					'%|O336|%',
					'%|O337|%',
					'%|O338|%',
					'%|O752|%',
					'%|O753|%',
					'%|O990|%',
					'%|O991|%',
					'%|O992|%',
					'%|O993|%',
					'%|O994|%',
					'%|O240|%',
					'%|O241|%',
					'%|O242|%',
					'%|O243|%',
					'%|O244|%',
					'%|O249|%',
					'%|O25|%',
					'%|O260|%',
					'%|O261|%',
					'%|O263|%',
					'%|O264|%',
					'%|O265|%',
					'%|O268|%',
					'%|O269|%',
					'%|O280|%',
					'%|O281|%',
					'%|O282|%',
					'%|O283|%',
					'%|O284|%',
					'%|O285|%',
					'%|O288|%',
					'%|O289|%',
					'%|O290|%',
					'%|O291|%',
					'%|O292|%',
					'%|O293|%',
					'%|O294|%',
					'%|O295|%',
					'%|O296|%',
					'%|O298|%',
					'%|O009|%',
					'%|O339|%',
					'%|O340|%',
					'%|O341|%',
					'%|O342|%',
					'%|O343|%',
					'%|O344|%',
					'%|O345|%',
					'%|O346|%',
					'%|O347|%',
					'%|O348|%',
					'%|O349|%',
					'%|O350|%',
					'%|O351|%',
					'%|O352|%',
					'%|O353|%',
					'%|O354|%',
					'%|O355|%',
					'%|O356|%',
					'%|O357|%',
					'%|O358|%',
					'%|O359|%',
					'%|O360|%',
					'%|O361|%',
					'%|O362|%',
					'%|O363|%',
					'%|O365|%',
					'%|O366|%',
					'%|O367|%',
					'%|O368|%',
					'%|O369|%',
					'%|O40|%',
					'%|O410|%',
					'%|O411|%',
					'%|O418|%',
					'%|O419|%',
					'%|O430|%',
					'%|O431|%',
					'%|O438|%',
					'%|O439|%',
					'%|O440|%',
					'%|O441|%',
					'%|O460|%',
					'%|O468|%',
					'%|O469|%',
					'%|O470|%',
					'%|O471|%',
					'%|O479|%',
					'%|O48|%',
					'%|O995|%',
					'%|O996|%',
					'%|O997|%',
					'%|Z640|%',
					'%|O00|%',
					'%|O10|%',
					'%|O12|%',
					'%|O14|%',
					'%|O15|%',
					'%|O20|%',
					'%|O21|%',
					'%|O22|%',
					'%|O23|%',
					'%|O24|%',
					'%|O26|%',
					'%|O28|%',
					'%|O29|%',
					'%|O30|%',
					'%|O31|%',
					'%|O32|%',
					'%|O33|%',
					'%|O34|%',
					'%|O35|%',
					'%|O36|%',
					'%|O41|%',
					'%|O43|%',
					'%|O44|%',
					'%|O46|%',
					'%|O47|%',
					'%|O98|%',
					'%|Z34|%',
					'%|Z35|%',
					'%|Z36|%',
					'%|Z321|%',
					'%|Z33|%',
					'%|Z340|%',
					'%|Z340|%',
					'%|Z348|%',
					'%|Z349|%',
					'%|Z350|%',
					'%|Z351|%',
					'%|Z352|%',
					'%|Z353|%',
					'%|Z354|%',
					'%|Z357|%',
					'%|Z358|%',
					--'%|Z356|%',
					'%|Z359|%'
				]
			)
		)
	ORDER BY nu_cns, nu_cpf_cidadao".$cdum."
) AS t1
ORDER BY co_dim_tempo_dum 
".$esq_pag;

// ===========================================================================================================
$linhas = 0;
$rel_pagina_inicio = "
<!DOCTYPE html>
<html>
<head>
  <meta charset=\"UTF-8\">
  <link rel=\"sortcut icon\" href=\"images/favicon.ico\" type=\"image/x-icon\" />
  <title>Relatório de gestantes</title>
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
CREATE TEMPORARY TABLE tmp_gestantes (
	sequencia serial PRIMARY KEY,
	cns varchar(15),
	cpf varchar(11),
	data_nascimento bigint,
	g_co_dim_tempo_dum bigint,
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
	calc_ddp bigint,
	calc_20s bigint,
	calc_fimpuerp bigint
)
";
$run_tt = pg_query($cdb,$tabela_temp);
	
$num_getantes = 0;
$run_s1 = pg_query($cdb,$gestantes);
$num_getantes = pg_num_rows($run_s1);
if ($num_getantes > 0){
	while ($gestante = pg_fetch_array($run_s1)){
		$CNS = '0';
		$CPF = '0';
		$campo_busca = "";
		$campo_ordem = "";
		$busca_cns = false;
		$busca_cpf = false;
		$busca = false;
		if (strlen(trim($gestante['nu_cns'])) == 15){
			$busca_cns = true;
			$CNS = trim($gestante['nu_cns']);
		}
		if (strlen(trim($gestante['nu_cpf_cidadao'])) == 11){
			$busca_cpf = true;
			$CPF = trim($gestante['nu_cpf_cidadao']);
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
			$cidadao_nome = "# GESTANTE NÃO ENCONTRADA #";
			$cidadao_mae = "# SEM NOME DA MAE #";
			$cidadao_nascimento = $gestante['dt_nascimento'];
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
				if (strlen($cind_micro_area) == 1){
					$cind_micro_area = '0'.$cind_micro_area;
				}
				if (strlen($cind_micro_area) == 0 || $cind_micro_area == NULL){
					$cind_micro_area = '00';
				}
				if ($cind_micro_area == '00'){
					$cind_micro_area = $cidadao_ma;
				}
				if ($cind_micro_area == 'FA' && $cidadao_ma != '00'){
					$cind_micro_area = $cind_micro_area.' '.$cidadao_ma;
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
				$calc_20s = datasomadias($gestante['co_dim_tempo_dum'],140);
				$calc_ddp = datasomadias($gestante['co_dim_tempo_dum'],$dpp);
				$calc_fimpuerp = datasomadias($gestante['co_dim_tempo_dum'],336);
				$data_nascimento = dataint($gestante['dt_nascimento']); // ou dataint($cidadao_nascimento)
				$data_falecimento = "0";
				if (strlen(trim($cidadao_obito)) > 5){
					$data_falecimento = dataint($cidadao_obito);
				} else {
					if (strlen(trim($cind_obito)) > 5){
						$data_falecimento = dataint($cind_obito);
					}
				}
				$cidadao_nome = htmlspecialchars($cidadao_nome, ENT_QUOTES);
				$cidadao_mae = htmlspecialchars($cidadao_mae, ENT_QUOTES);
				if ($cind_micro_area == '-' || $cind_micro_area == '--' || $cind_micro_area == '0-'){
					$cind_micro_area = '00';
				}
				if ($ine == '-'){
					$ine = '0000000000';
				}

				$inserir = "
				INSERT INTO tmp_gestantes(
					cns,
					cpf,
					data_nascimento,
					g_co_dim_tempo_dum,
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
					calc_ddp,
					calc_20s,
					calc_fimpuerp
				) VALUES (
					'".$CNS."',
					'".$CPF."',
					".$data_nascimento.",
					".$gestante['co_dim_tempo_dum'].",
					".$gestante['co_dim_tempo'].",
					".$cidadao_ativo.",
					".$data_falecimento.",
					'".$cidadao_nome."',
					'".$cidadao_mae."',
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
					".$calc_ddp.",
					".$calc_20s.",
					".$calc_fimpuerp."
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
					$run_ins = pg_query($cdb,$inserir);
					$conta_gravacao++;
				}
			}
		}
	}
}

$dum_show = "Último DUM informado";
if ($dum == 'A'){
	$dum_show = "DUM mais antigo";
} else {
	if ($dum == 'N'){
		$dum_show = "DUM mais novo";
	}
}
$periodo_show = "DPP a partir de ".date('d/m/Y');
$calcular_dias_parto = true;
$calcular_dias_dt = date('Ymd');
if ($gpa == 0){
	$periodo_show = "DPP entre ".dtshow($dti)." e ".dtshow(datasomadias($dtf,1,'-'));
	$calcular_dias_parto = false;
	if ($dti >= date('Ymd')){
		$calcular_dias_parto = true;
		$calcular_dias_dt = $dti;
	}
}

// ===========================================================================================================
$rel_cabecalho_2 = "
<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"relatorio-tabela\">
  <!--DWLayoutTable-->
  <tr> 
    <td width=\"440\" height=\"32\" valign=\"top\" class=\"relatorio-titulo\">Relatório de Gestantes</td>
    <td width=\"392\" valign=\"top\" class=\"relatorio-periodo\">".$dum_show." | ".$periodo_show."</td>
    <td width=\"177\" valign=\"top\" class=\"relatorio-pagina\">DPP com ".$dpp." dias / ".qdata($dti)."</td>
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
$gestantes_final = "
SELECT
	*
FROM
	tmp_gestantes
ORDER BY ".$ordena_grupo." ".$ordenar;
//++++++++++++++++++++++++

$num_getantes_final = 0;
$run_gf = pg_query($cdb,$gestantes_final);
$num_getantes_final = pg_num_rows($run_gf);
$conta_geral = $pini;
$conta_geral_desconto = $cdes;
$numerador_ind1 = $num1;
$numerador_ind2 = $num2;
$numerador_ind3 = $num3;

if ($num_getantes_final > 0){
	while ($gestante_final = pg_fetch_array($run_gf)){
		$duplicados_cons = array();
		$conta_duplicados_cons = 0;
		$duplicados_exames = array();
		$conta_duplicados_exames = 0;

		$conta_geral++;
		
		$rCNS = trim($gestante_final['cns']);
		$rCPF = trim($gestante_final['cpf']);
		
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
			$grupo_id = $gestante_final[$grupo];
			
			$grupo_nome = "Micro-Área";
			$show_gp_inverso = "Unidade";	
			$show_gp_id_inverso = $gestante_final['cnes'];	
			$show_gp_nm_inverso = $gestante_final['nome_unidade'];
			$show_gp_id2_inverso = $gestante_final['ine'];
			$show_gp_nm2_inverso = "Equipe";
			if ($grupo == "ine"){
				$grupo_nome = $gestante_final['nome_equipe'];	
				$show_gp_id2_inverso = $gestante_final['cind_micro_area'];
				$show_gp_nm2_inverso = "Micro-&Aacute;rea";
			}
			if ($grupo == "cnes"){
				$grupo_nome = $gestante_final['nome_unidade'];
				$show_gp_inverso = "Equipe";	
				$show_gp_id_inverso = $gestante_final['ine'];	
				$show_gp_nm_inverso = $gestante_final['nome_equipe'];
				$show_gp_id2_inverso = $gestante_final['cind_micro_area'];
				$show_gp_nm2_inverso = "Micro-&Aacute;rea";
			}
			
			if ($quebra != $grupo_id){
				// ===========================================================================================================
				$rel_dados_grupo = "
				  <tr> 
					<td height=\"19\" colspan=\"15\" valign=\"top\" class=\"lista-equipe\">[ ".$grupo_id." ] ".$grupo_nome."</td>
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
				$linhas = $linhas + 2;
				echo $rel_dados_grupo;
				// ===========================================================================================================
				$quebra = $grupo_id;
			}
		} else {
			$show_gp_inverso = "Unidade";	
			$show_gp_id_inverso = $gestante_final['cnes'];	
			$show_gp_nm_inverso = $gestante_final['nome_unidade']." (".$gestante_final['ine'].")";
			$show_gp_id2_inverso = $gestante_final['cind_micro_area'];
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
		$consultas = "
			SELECT
				*
			FROM
			(
				-------------------------------------------------------
				-------------------------------------------------------
				-------------------------------------------------------
				SELECT
					t5.co_dim_tempo,
					t5.co_dim_tempo_dum,
					t5.nu_ine,
					t5.no_equipe,
					t5.nu_cnes,
					t5.no_unidade_saude,
					t5.ds_filtro_ciaps,
					t5.ds_filtro_cids,
					t5.nu_cbo,
					t5.no_cbo,
					tb_dim_profissional.nu_cns AS cns_prof
				FROM
				(
					SELECT
						t3.*,
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
									co_dim_tempo, 
									co_dim_tempo_dum,
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
									(co_dim_tempo >= ".$gestante_final['g_co_dim_tempo_dum']." AND co_dim_tempo < ".$dtf.")
									AND (
										ds_filtro_ciaps LIKE ANY (
											array[
												'%|W03|%',
												'%|W05|%',
												'%|W29|%',
												'%|W71|%',
												'%|W78|%',
												'%|W79|%',
												'%|W80|%',
												'%|W81|%',
												'%|W84|%',
												'%|W85|%',
												--'%|W72|%',
												--'%|W73|%',
												--'%|W75|%',
												--'%|W76|%',
												'%|ABP001|%'
											]
										) OR
										ds_filtro_cids LIKE ANY (
											array[
												'%|O11|%',
												'%|O120|%',
												'%|O121|%',
												'%|O122|%',
												'%|O13|%',
												'%|O140|%',
												'%|O141|%',
												'%|O149|%',
												'%|O150|%',
												'%|O151|%',
												'%|O159|%',
												'%|O16|%',
												'%|O200|%',
												'%|O208|%',
												'%|O209|%',
												'%|O210|%',
												'%|O211|%',
												'%|O212|%',
												'%|O218|%',
												'%|O219|%',
												'%|O220|%',
												'%|O221|%',
												'%|O222|%',
												'%|O223|%',
												'%|O224|%',
												'%|O225|%',
												'%|O228|%',
												'%|O229|%',
												'%|O230|%',
												'%|O231|%',
												'%|O232|%',
												'%|O233|%',
												'%|O234|%',
												'%|O235|%',
												'%|O239|%',
												'%|O299|%',
												'%|O300|%',
												'%|O301|%',
												'%|O302|%',
												'%|O308|%',
												'%|O309|%',
												'%|O311|%',
												'%|O312|%',
												'%|O318|%',
												'%|O320|%',
												'%|O321|%',
												'%|O322|%',
												'%|O323|%',
												'%|O324|%',
												'%|O325|%',
												'%|O326|%',
												'%|O328|%',
												'%|O329|%',
												'%|O330|%',
												'%|O331|%',
												'%|O332|%',
												'%|O333|%',
												'%|O334|%',
												'%|O335|%',
												'%|O336|%',
												'%|O337|%',
												'%|O338|%',
												'%|O752|%',
												'%|O753|%',
												'%|O990|%',
												'%|O991|%',
												'%|O992|%',
												'%|O993|%',
												'%|O994|%',
												'%|O240|%',
												'%|O241|%',
												'%|O242|%',
												'%|O243|%',
												'%|O244|%',
												'%|O249|%',
												'%|O25|%',
												'%|O260|%',
												'%|O261|%',
												'%|O263|%',
												'%|O264|%',
												'%|O265|%',
												'%|O268|%',
												'%|O269|%',
												'%|O280|%',
												'%|O281|%',
												'%|O282|%',
												'%|O283|%',
												'%|O284|%',
												'%|O285|%',
												'%|O288|%',
												'%|O289|%',
												'%|O290|%',
												'%|O291|%',
												'%|O292|%',
												'%|O293|%',
												'%|O294|%',
												'%|O295|%',
												'%|O296|%',
												'%|O298|%',
												'%|O009|%',
												'%|O339|%',
												'%|O340|%',
												'%|O341|%',
												'%|O342|%',
												'%|O343|%',
												'%|O344|%',
												'%|O345|%',
												'%|O346|%',
												'%|O347|%',
												'%|O348|%',
												'%|O349|%',
												'%|O350|%',
												'%|O351|%',
												'%|O352|%',
												'%|O353|%',
												'%|O354|%',
												'%|O355|%',
												'%|O356|%',
												'%|O357|%',
												'%|O358|%',
												'%|O359|%',
												'%|O360|%',
												'%|O361|%',
												'%|O362|%',
												'%|O363|%',
												'%|O365|%',
												'%|O366|%',
												'%|O367|%',
												'%|O368|%',
												'%|O369|%',
												'%|O40|%',
												'%|O410|%',
												'%|O411|%',
												'%|O418|%',
												'%|O419|%',
												'%|O430|%',
												'%|O431|%',
												'%|O438|%',
												'%|O439|%',
												'%|O440|%',
												'%|O441|%',
												'%|O460|%',
												'%|O468|%',
												'%|O469|%',
												'%|O470|%',
												'%|O471|%',
												'%|O479|%',
												'%|O48|%',
												'%|O995|%',
												'%|O996|%',
												'%|O997|%',
												'%|Z640|%',
												'%|O00|%',
												'%|O10|%',
												'%|O12|%',
												'%|O14|%',
												'%|O15|%',
												'%|O20|%',
												'%|O21|%',
												'%|O22|%',
												'%|O23|%',
												'%|O24|%',
												'%|O26|%',
												'%|O28|%',
												'%|O29|%',
												'%|O30|%',
												'%|O31|%',
												'%|O32|%',
												'%|O33|%',
												'%|O34|%',
												'%|O35|%',
												'%|O36|%',
												'%|O41|%',
												'%|O43|%',
												'%|O44|%',
												'%|O46|%',
												'%|O47|%',
												'%|O98|%',
												'%|Z34|%',
												'%|Z35|%',
												'%|Z36|%',
												'%|Z321|%',
												'%|Z33|%',
												'%|Z340|%',
												'%|Z340|%',
												'%|Z348|%',
												'%|Z349|%',
												'%|Z350|%',
												'%|Z351|%',
												'%|Z352|%',
												'%|Z353|%',
												'%|Z354|%',
												'%|Z357|%',
												'%|Z358|%',
												--'%|Z356|%',
												'%|Z359|%'
											]
										)
									)
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
			ORDER BY co_dim_tempo";

		$num_consultas = 0;
		$run_s7 = pg_query($cdb,$consultas);
		$num_consultas = pg_num_rows($run_s7);
		$I1_c1 = false; // minimo de 6 consultas
		$I1_c2 = false; // primeira consulta antes da 20 semana
		$dt_primeira_consulta = 0;
		$array_consultas = array();
		$array_consultas_data = array();
		$conta_array_consultas = 0;
		if ($num_consultas > 0){
			if ($num_consultas >= 6){
				$I1_c1 = true;
			}
			while ($consulta = pg_fetch_array($run_s7)){
				if ($dt_primeira_consulta == 0){
					$dt_primeira_consulta = $consulta['co_dim_tempo'];
				}

				$nu_cbo = $consulta['nu_cbo'];
				$no_cbo = $consulta['no_cbo'];
				$cnes = $consulta['nu_cnes'];
				$nome_unidade = $consulta['no_unidade_saude'];
				$ine = $consulta['nu_ine'];
				$nome_equipe = $consulta['no_equipe'];

				$data_dum_consulta = "00000000";
				if ($consulta['co_dim_tempo_dum'] != 30001231){
					$data_dum_consulta = $consulta['co_dim_tempo_dum'];
				}
				$indicativo_dum = '';
				$indicativo_consulta = '';
				if ($data_dum_consulta == $gestante_final['g_co_dim_tempo_dum']){
					$indicativo_dum = '*';
				}
				if ($consulta['co_dim_tempo'] == $gestante_final['g_co_dim_tempo']){
					$indicativo_consulta = '*';
				}
				/*
					$nu_cbo
					$no_cbo
					$data_dum_consulta
					$consulta['co_dim_tempo']
					$indicativo_dum
					$indicativo_consulta
				*/
				
				// local para definir as consultas por CBO

				$array_consultas[$conta_array_consultas]['nu_cbo'] = $nu_cbo;
				$array_consultas[$conta_array_consultas]['no_cbo'] = $no_cbo;
				$array_consultas[$conta_array_consultas]['data_dum_consulta'] = $data_dum_consulta;
				$array_consultas[$conta_array_consultas]['data'] = $consulta['co_dim_tempo'];
				$array_consultas[$conta_array_consultas]['indicativo_dum'] = $indicativo_dum;
				$array_consultas[$conta_array_consultas]['indicativo_consulta'] = $indicativo_consulta;
				$array_consultas[$conta_array_consultas]['cnes'] = $cnes;
				$array_consultas[$conta_array_consultas]['nome_unidade'] = $nome_unidade;
				$array_consultas[$conta_array_consultas]['ine'] = $ine;
				$array_consultas[$conta_array_consultas]['nome_equipe'] = $nome_equipe;
				$array_consultas[$conta_array_consultas]['ciaps'] = $consulta['ds_filtro_ciaps'];
				$array_consultas[$conta_array_consultas]['cids'] = $consulta['ds_filtro_cids'];
				$array_consultas[$conta_array_consultas]['cns_prof'] = $consulta['cns_prof'];
				$array_consultas_data[$conta_array_consultas] = $consulta['co_dim_tempo'];
				$conta_array_consultas++;
			}
			if ($dt_primeira_consulta <= $gestante_final['calc_20s']){
				$I1_c2 = true;
			}
		} else {
			//echo "SEM CONSULTAS<br>";
		}
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		$interrupcoes = "
			SELECT
				*
			FROM
			(
				SELECT
					t3.co_dim_tempo,
					t3.co_dim_tempo_dum,
					t3.nu_ine,
					t3.no_equipe,
					t3.nu_cnes,
					t3.no_unidade_saude,
					t3.ds_filtro_cids,
					t3.ds_filtro_ciaps,
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
								co_dim_tempo, 
								co_dim_tempo_dum,
								CASE WHEN co_dim_cbo_1 = 1 THEN co_dim_cbo_2
									ELSE co_dim_cbo_1 END
									co_dim_cbo,
								CASE WHEN co_dim_unidade_saude_1 = 1 THEN co_dim_unidade_saude_2
									ELSE co_dim_unidade_saude_1 END
									co_dim_unidade_saude,
								CASE WHEN co_dim_equipe_1 = 1 THEN co_dim_equipe_2
									ELSE co_dim_equipe_1 END
									co_dim_equipe,
								ds_filtro_cids,
								ds_filtro_ciaps
							FROM
								tb_fat_atendimento_individual
							WHERE
								".$campo_busca." AND
								(co_dim_tempo >= ".$gestante_final['g_co_dim_tempo_dum']." AND co_dim_tempo < ".$dtf.")
								AND (
									ds_filtro_ciaps LIKE ANY (
										array[
											'%|W82|%',
											'%|W83|%',
											'%|W91|%',
											'%|W92|%',
											
											'%|W93|%',
											'%|W90|%'
										]
									) OR
									ds_filtro_cids LIKE ANY (
										array[
											'%|Z371|%',
											'%|Z373|%',
											'%|Z374|%',
											'%|Z376|%',
											'%|Z377|%',
											'%|O04|%',
											'%|Z303|%',
											'%|O02|%',
											'%|O03|%',
											'%|O05|%',
											'%|O06|%',
											
											'%|O42|%',
											'%|O45|%',
											'%|O60|%',
											'%|O61|%',
											'%|O62|%',
											'%|O63|%',
											'%|O64|%',
											'%|O65|%',
											'%|O66|%',
											'%|O67|%',
											'%|O68|%',
											'%|O69|%',
											'%|O70|%',
											'%|O71|%',
											'%|O73|%',
											'%|O750|%',
											'%|O751|%',
											'%|O754|%',
											'%|O755|%',
											'%|O756|%',
											'%|O757|%',
											'%|O758|%',
											'%|O759|%',
											'%|O81|%',
											'%|O82|%',
											'%|O83|%',
											'%|O84|%',
											'%|Z372|%',
											'%|Z375|%',
											'%|Z379|%',
											'%|Z38|%',
											'%|Z39|%',
											'%|O80|%',
											'%|Z370|%'
										]
									)
								)
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
					tb_dim_cbo
				ON tb_dim_cbo.co_seq_dim_cbo = t3.co_dim_cbo
			) AS t4
			WHERE
				nu_cbo LIKE ANY (array['2251%','2252%','2253%','2231%','2235%'])";
		$num_interrupcoes = 0;
		$data_interrupcao = 0;
		$inter_cidciap = '';
		$run_s10 = pg_query($cdb,$interrupcoes);
		$num_interrupcoes = pg_num_rows($run_s10);
		$interrup_show = "NÃO";
		if ($num_interrupcoes > 0){
			$data_interrupcao = pg_fetch_result($run_s10,0,'co_dim_tempo');
			$interrup_show = dtshow($data_interrupcao);
			$inter_cidciap = str_replace("|"," ",pg_fetch_result($run_s10,0,'ds_filtro_ciaps')).str_replace("|"," ",pg_fetch_result($run_s10,0,'ds_filtro_cids'));
		}
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		$exames = array();
		$cont_exames = 0;
		$contro_cbo = "'2251%','2252%','2253%','2231%','2235%','3222%'";
		$procedimentos_s = "'0214010082','0214010074','0202031110','0202031179','ABPG026','ABEX019'";
		$procedimentos_h = "'0214010058','0214010040','0202030300','ABPG024','ABEX018'";
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		for ($v=0;$v<2;$v++){
			$procedimentos = $procedimentos_s;
			$tex = 'Sífilis';
			if ($v == 1){
				$procedimentos = $procedimentos_h;
				$tex = 'HIV';
			}
			$select_tb1 = "
				SELECT
					t6.co_dim_tempo,
					t6.nu_cbo,
					t6.nu_cnes,
					t6.nu_ine,
					t6.avaliado,
					t6.solicitado,
					t6.cns_prof,
					text 'tb_fat_atd_ind_procedimentos' as tabela
				FROM
				(
					---------------------------------------------------------
					---------------------------------------------------------
					---------------------------------------------------------
					SELECT
						t7.*,
						tb_dim_profissional.nu_cns AS cns_prof
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
												(co_dim_tempo >= ".$gestante_final['g_co_dim_tempo_dum']." AND co_dim_tempo <= ".$dtf.") AND
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
						$exames[$cont_exames]['tipo'] = $tex;
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
					text 'tb_fat_proced_atend_proced' as tabela
				FROM
				(
					---------------------------------------------------------
					---------------------------------------------------------
					---------------------------------------------------------
					SELECT
						t7.*,
						tb_dim_profissional.nu_cns AS cns_prof
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
											(co_dim_tempo >= ".$gestante_final['g_co_dim_tempo_dum']." AND co_dim_tempo <= ".$dtf.") AND
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
						$exames[$cont_exames]['tipo'] = $tex;
						$cont_exames++;
					}
				}
			}
			// -------------------------------------------------------------
			// -------------------------------------------------------------
		}
		// -------------------------------------------------------------
		// -------------------------------------------------------------

		if ($tbodonto == 1){
			// tb_fat_atend_odonto_proced
		}
		
		// -------------------------------------------------------------
		// -------------------------------------------------------------
		
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		// ***********************************************************************************************************************
		$codontos = "
			SELECT
				*
			FROM
			(
				SELECT
					t4.st_gestante,
					t4.co_dim_tempo,
					t4.ds_filtro_procedimentos,
					t4.nu_ine,
					t4.no_equipe,
					t4.nu_cnes,
					t4.no_unidade_saude,
					t4.nu_cbo_1,
					tb_dim_cbo.nu_cbo AS nu_cbo_2
				FROM
				(
					SELECT
						t3.*,
						tb_dim_cbo.nu_cbo AS nu_cbo_1
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
									CASE WHEN st_gestante IS NULL THEN '0'
										ELSE st_gestante END
										st_gestante,
									CASE WHEN co_dim_unidade_saude_1 = 1 THEN co_dim_unidade_saude_2
										ELSE co_dim_unidade_saude_1 END
										co_dim_unidade_saude,
									CASE WHEN co_dim_equipe_1 = 1 THEN co_dim_equipe_2
										ELSE co_dim_equipe_1 END
										co_dim_equipe,
									co_dim_cbo_1,
									co_dim_cbo_2,
									co_dim_tempo,
									ds_filtro_procedimentos
								FROM 
									tb_fat_atendimento_odonto
								WHERE
									".$campo_busca." AND
									(co_dim_tempo >= ".$gestante_final['g_co_dim_tempo_dum']." AND co_dim_tempo < ".$dtf.")
								ORDER BY co_dim_tempo DESC
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
					ON tb_dim_cbo.co_seq_dim_cbo = t3.co_dim_cbo_1
				) AS t4
				LEFT JOIN
					tb_dim_cbo
				ON tb_dim_cbo.co_seq_dim_cbo = t4.co_dim_cbo_2
			) AS t5
			WHERE
				nu_cbo_1 LIKE '2232%'
				--nu_cbo_1 LIKE '2232%' OR nu_cbo_2 LIKE '2232%'
				--nu_cbo_1 LIKE ANY (array['2232%','322425','322405','322430','322415']) OR
				--nu_cbo_2 LIKE ANY (array['2232%','322425','322405','322430','322415'])
		";
		$num_codontos = 0;
		$run_s9 = pg_query($cdb,$codontos);
		$num_codontos = pg_num_rows($run_s9);
		$marcado_gestante_odonto = 0;
		$dt_marcado_gestante_odonto = 0;
		$profod1 = '';
		$profod2 = '';
		$I3_c1 = false; // consulta odontologica ok
		$conta_whi_od = 0;
		if ($num_codontos > 0){
			$I3_c1 = true;
			while ($codonto = pg_fetch_array($run_s9)){
				$conta_whi_od++;
				if ($codonto['st_gestante'] == 1){
					$marcado_gestante_odonto = 1;
					$dt_marcado_gestante_odonto = $codonto['co_dim_tempo'];
				}
				if (strlen($codonto['nu_cbo_1']) > 1){
					$profod1 .= $codonto['nu_cbo_1'].'-'.$conta_whi_od.' ';
				}
				if (strlen($codonto['nu_cbo_2']) > 1){
					$profod2 .= $codonto['nu_cbo_2'].'-'.$conta_whi_od.' ';
				}
			}
		}

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
			if (strlen($gestante_final['cns_resp']) == 15 && strlen($gestante_final['cpf_resp']) == 11){
				$campo_busca_res = "(nu_cns_responsavel = '".$gestante_final['cns_resp']."' OR nu_cpf_responsavel = '".$gestante_final['cpf_resp']."')";
			} else {
				if (strlen($gestante_final['cns_resp']) == 15){
					$campo_busca_res = "nu_cns_responsavel = '".$gestante_final['cns_resp']."'";
				}
				if (strlen($gestante_final['cpf_resp']) == 11){
					$campo_busca_res = "nu_cpf_responsavel = '".$gestante_final['cpf_resp']."'";
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
		
		$total_consultas = count($array_consultas);
		
		$marcado_gestante = "NÃO";
		$marcado_hipertenso = "NÃO";
		$marcado_diabetico = "NÃO";
		if ($gestante_final['cind_gestante'] == 1){
			$marcado_gestante = "SIM";
		}
		if ($gestante_final['cind_hipertenso'] == 1){
			$marcado_hipertenso = "SIM";
		}
		if ($gestante_final['cind_diabetico'] == 1){
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
			$inconsistencias .= "-<br>";
		}
		if ($rCNS == '0' && $rCPF == '0'){
			$inconsistencias .= "Sem CNS e sem CPF.<br>-<br>";
			$conta_geral_desconto++;
		}
		if ($num_interrupcoes > 0){
			if ($data_interrupcao < $dti){
				$conta_geral_desconto++;
				$inconsistencias .= "Teve inter. de gestação em: ".dtshow($data_interrupcao)."<br>".$inter_cidciap."<br>-<br>";
			} else {
				$inconsistencias .= "Teve inter. de gestação em: ".dtshow($data_interrupcao)."<br>".$inter_cidciap."<br>";
			}
		}
		if ($ma_familiar == '00' && $gestante_final['cind_micro_area'] == '00'){
			$inconsistencias .= "Gestante sem cadastro familiar.<br>";
		}
		if ($gestante_final['cidadao_sexo'] != 'FEMININO'){
			$inconsistencias .= "Cadastro está como do sexo Masculino.<br>";
		}
		$inativo2 = 0;
		if ($gestante_final['cidadao_ativo'] != 1){
			//$inconsistencias .= "Cadastro está como inativo (Cidadao).<br>";
			$inativo2++;
		}
		if ($gestante_final['cind_inativo'] != 0){
			$inconsistencias .= "Cadastro está como inativo (Cad. Ind.).<br>";
			$inativo2++;
		}
		if ($inativo2 >= 2){
			$conta_geral_desconto++;
			$inconsistencias .= "-<br>";
		}
		$faleceu2 = 0;
		if ($gestante_final['cidadao_faleceu'] != 0){
			$inconsistencias .= "Faleceu (Cidadao)<br>";
			$faleceu2++;
		}
		if (strlen($gestante_final['data_falecimento']) > 5){
			$inconsistencias .= "Faleceu em ".dtshow($gestante_final['data_falecimento'])."<br>";
			$faleceu2++;
		}
		if ($faleceu2 >= 2){
			$conta_geral_desconto++;
			$inconsistencias .= "-<br>";
		}
		if (substr($gestante_final['cidadao_nome'],0,1) == '#'){
			$inconsistencias .= "Gestante não localizada em tb_cidadao.<br>";
		}
		if (strlen($inconsistencias) > 0){
			$estilo_inco = "lista-status-Vermelho";
		}
		if ($calcular_dias_parto){
			$falta = difdatas($calcular_dias_dt,$gestante_final['calc_ddp']);
			if ($falta <= 7){
				if ($falta <= 0){
					$inconsistencias .= "[ NASCE HOJE!!! ]<br>";
					if ($estilo_inco != "lista-status-Vermelho"){
						$estilo_inco = "lista-status-Amarelo";
					}
				} else {
					$inconsistencias .= "[ VAI NASCER ] Falta(m) ".$falta." dia(s)<br>";
					if ($estilo_inco != "lista-status-Vermelho"){
						$estilo_inco = "lista-status-Amarelo";
					}	
				}
			} else {
				if ($falta <= 30){
					$inconsistencias .= "[ Atenção ] Faltam ".$falta." dias para DPP<br>";
					if ($estilo_inco != "lista-status-Vermelho"){
						$estilo_inco = "lista-status-Azul";
					}
				} else {
					$inconsistencias .= "Faltam ".$falta." dias para DPP<br>";
				}
			}
		}

		$idade = idadeint($gestante_final['data_nascimento'],$dtf);
		
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		$indicador1_c1 = "NÃO";
		$estilo_ind1_c1 = "indicador-c1-NAO";
		if ($I1_c1){
			$indicador1_c1 = "SIM";
			$estilo_ind1_c1 = "indicador-c1-SIM";
		}
		$indicador1_c2 = "NÃO";
		$estilo_ind1_c2 = "indicador-cx-NAO";
		if ($I1_c2){
			$indicador1_c2 = "SIM";
			$estilo_ind1_c2 = "indicador-cx-SIM";
		}
		$indicador1 = "NÃO";
		$estilo_indicador1 = "indicador-titulo-Vermelho";
		if ($I1_c1 && $I1_c2){
			$indicador1 = "SIM";
			$estilo_indicador1 = "indicador-titulo-Verde";
			$numerador_ind1++;
		} else {
			if ($I1_c1){
				$estilo_indicador1 = "indicador-titulo-Amarelo";
			}
			if ($I1_c2){
				$estilo_indicador1 = "indicador-titulo-Amarelo";
			}
		}
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		$exsif = false;
		$exhiv = false;
		if (count($exames) > 0){
			if (false !== $key = array_search('HIV', array_column($exames, 'tipo'))) {
				$exhiv = true;
			}
			if (false !== $key = array_search('Sífilis', array_column($exames, 'tipo'))) {
				$exsif = true;
			}
			
		}
		$estilo_hiv = "indicador-cx-NAO";
		$show_ex_h = "NÃO";
		if ($exhiv){
			$show_ex_h = "SIM";
			$estilo_hiv = "indicador-cx-SIM";
		}
		$estilo_sifilis = "indicador-c1-NAO";
		$show_ex_s = "NÃO";
		if ($exsif){
			$show_ex_s = "SIM";
			$estilo_sifilis = "indicador-c1-SIM";
		}
		$estilo_indicador2 = "indicador-titulo-Vermelho";
		$show_indicador_2 = "NÃO";
		if ($exhiv && $exsif){
			$estilo_indicador2 = "indicador-titulo-Verde";
			$show_indicador_2 = "SIM";
			$numerador_ind2++;
		} else {
			if ($exhiv){
				$estilo_indicador2 = "indicador-titulo-Amarelo";
			}
			if ($exsif){
				$estilo_indicador2 = "indicador-titulo-Amarelo";
			}
		}
		
		$idade_gestacional_dias = 280;
		$idade_gestacional_semanas = 40;
		$idade_gestacional_meses = 9;
		$idade_gest = "";
		if ($gestante_final['calc_ddp'] > date('Ymd')){
			$idade_gestacional_dias = difdatas($gestante_final['g_co_dim_tempo_dum'],date('Ymd'));
			$idade_gestacional_semanas = intval($idade_gestacional_dias / 7);
			$idade_gestacional_meses = intval($idade_gestacional_dias / 30);
			$idade_gest = "Idade gestacional: ".intval($idade_gestacional_dias)." D | ".$idade_gestacional_semanas." S | ".$idade_gestacional_meses." M";
		}
		$num_cons_od = "";
		$num_trimestres = intval($idade_gestacional_meses / 3);
		if ($num_trimestres > 0){
			$num_cons_od .= "!! É bom ter ao menos ".$num_trimestres." consultas !!";
		}
		/*
		if (strlen($profod1.$profod2) > 0){
			$num_cons_od = "<font size=\"-3\">".$profod1."|".$profod2."</fonte><br>".$num_cons_od;
		}
		*/
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		$consulta_odontologica = "NÃO";
		$estilo_odonto = "indicador-c1-NAO-B";
		$estilo_odonto_indicador = "indicador-titulo-Vermelho-B";
		if ($I3_c1){
			$consulta_odontologica = "SIM";
			$estilo_odonto = "indicador-c1-SIM-B";
			$estilo_odonto_indicador = "indicador-titulo-Verde-B";
			$numerador_ind3++;
			if ($num_trimestres > $num_codontos){
				$consulta_odontologica = "SIM";
				$estilo_odonto = "indicador-c1-SIM-amarelo-B";
			}
		}
		$marcado_gestante_odonto_show = "Não marcada como gestante";
		if ($marcado_gestante_odonto == 1){
			$marcado_gestante_odonto_show = "Marcada como gestante em ".dtshow($dt_marcado_gestante_odonto);
		}
		// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		
		// ===========================================================================================================
		$rel_dados_unitario = "
		  <tr> 
			<td height=\"18\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-BB\">".zesq($conta_geral,5)."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".mcpf($rCPF)."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado1-centro-B\">".mcns($rCNS)."</td>
			<td colspan=\"8\" valign=\"top\" class=\"lista-dado1-esquerdo-B\">".$gestante_final['cidadao_nome']."</td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\">".dtshow($gestante_final['data_nascimento'])."</td>
			<td colspan=\"2\" rowspan=\"6\" valign=\"top\" class=\"".$estilo_inco."\">
			<p>
				".$inconsistencias."
			</p></td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td valign=\"top\" class=\"lista-dado1-centro-B\"><!--DWLayoutEmptyCell-->&nbsp;</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Nome da m&atilde;e</td>
			<td colspan=\"9\" valign=\"top\" class=\"lista-dado2-centro-B\">".$gestante_final['cidadao_mae']."</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">| Idade: ".$idade." anos</td>
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
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">(Cad. Ind.) Declarada gestante</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_gestante."</td>
			<td colspan=\"3\" valign=\"top\" class=\"lista-sub-dados-B\">Inter. Cl&iacute;nica </td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$interrup_show."</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">".$show_gp_nm2_inverso."</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".$show_gp_id2_inverso."</td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Declarada Diab&eacute;tica</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_diabetico."</td>
			<td colspan=\"3\" valign=\"top\" class=\"lista-sub-dados-B\">DUM</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\"><b>".dtshow($gestante_final['g_co_dim_tempo_dum'])."</b></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">20&ordf; Semana</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".dtshow($gestante_final['calc_20s'])."</td>
		  </tr>
		  <tr> 
			<td height=\"18\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-BB\">Declarada Hipertensa</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\">".$marcado_hipertenso."</td>
			<td colspan=\"3\" valign=\"top\" class=\"lista-sub-dados-B\">DPP</td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-dado2-centro-B\"><b>".dtshow($gestante_final['calc_ddp'])."</b></td>
			<td colspan=\"2\" valign=\"top\" class=\"lista-sub-dados-B\">Fim Puerp&eacute;rio</td>
			<td valign=\"top\" class=\"lista-dado2-centro-B\">".dtshow($gestante_final['calc_fimpuerp'])."</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" rowspan=\"2\" valign=\"top\" class=\"".$estilo_indicador1."\">Indicador 1 [ ".$indicador1." ]</td>
			<td colspan=\"9\" valign=\"top\" class=\"indicador-c1\">Crit&eacute;rio 1: M&iacute;nimo de 6 consultas de Pr&eacute;-Natal</td>
			<td valign=\"top\" class=\"".$estilo_ind1_c1."\">".$indicador1_c1."</td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-c1\">".$total_consultas." consulta(s)</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"9\" valign=\"top\" class=\"indicador-cx\">Crit&eacute;rio 2: Primeira consulta antes da 20&ordf; semana de gesta&ccedil;&atilde;o</td>
			<td valign=\"top\" class=\"".$estilo_ind1_c2."\">".$indicador1_c2."</td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-cx\">Primeira consulta em ".dtshow($dt_primeira_consulta)."</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" rowspan=\"2\" valign=\"top\" class=\"".$estilo_indicador2."\">Indicador 2 [ ".$show_indicador_2." ]</td>
			<td colspan=\"5\" valign=\"top\" class=\"indicador-c1\">Crit&eacute;rio 1: Exame de S&iacute;filis</td>
			<td colspan=\"4\" rowspan=\"2\" valign=\"top\" class=\"indicador-c1-centro-X\">".$idade_gest."</td>
			<td valign=\"top\" class=\"".$estilo_sifilis."\">".$show_ex_s."</td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-c1\">.</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"5\" valign=\"top\" class=\"indicador-cx\">Crit&eacute;rio 2: Exame de HIV</td>
			<td valign=\"top\" class=\"".$estilo_hiv."\">".$show_ex_h."</td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-cx\">.</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" rowspan=\"2\" valign=\"top\" class=\"".$estilo_odonto_indicador."\">Indicador 3 [ ".$consulta_odontologica." ]</td>
			<td colspan=\"5\" rowspan=\"2\" valign=\"top\" class=\"indicador-c1-B\">Crit&eacute;rio 1: Consulta odontol&oacute;gica</td>
			<td colspan=\"4\" rowspan=\"2\" valign=\"top\" class=\"indicador-c1-centro-X-B\">".$num_cons_od."</td>
			<td rowspan=\"2\" valign=\"top\" class=\"".$estilo_odonto."\">".$consulta_odontologica."</td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-c1\">".$num_codontos." consulta(s)</td>
		  </tr>
		  <tr> 
			<td height=\"16\"></td>
			<td></td>
			<td colspan=\"2\" valign=\"top\" class=\"indicador-cx-B\">".$marcado_gestante_odonto_show."</td>
		  </tr>";
		$linhas = $linhas + 13;
		echo $rel_dados_unitario;
		
		
		if (strlen($dt_primeira_consulta) < 8){
			$dt_primeira_consulta = '00000000';
		}

		$texto = zesq($conta_geral,5).";".mcpf($rCPF).";".mcns($rCNS).";".str_replace("Ã","A",$gestante_final['cidadao_nome']).";".dtshow($gestante_final['data_nascimento']).";".str_replace("Ã","A",$gestante_final['cidadao_mae']).";".$idade.";".str_replace("Ã","A",$marcado_gestante).";".str_replace("Ã","A",$marcado_hipertenso).";".str_replace("Ã","A",$marcado_diabetico).";".str_replace("Ã","A",$interrup_show).";".$inter_cidciap.";".dtshow($gestante_final['g_co_dim_tempo_dum']).";".dtshow($gestante_final['calc_20s']).";".dtshow($gestante_final['calc_ddp']).";".dtshow($gestante_final['calc_fimpuerp']).";".$total_consultas.";".dtshow($dt_primeira_consulta).";".$num_codontos.";".str_replace("Ã","A",$indicador1).";".str_replace("Ã","A",$show_indicador_2).";".str_replace("Ã","A",$consulta_odontologica).";".$gestante_final['cidadao_sexo'].";".$gestante_final['cnes'].";".$gestante_final['ine'].";".$ma_familiar."/".$gestante_final['cind_micro_area']."\r\n";
		fwrite($FT, $texto);

		if ($mconsultas == 1){
			usort($array_consultas, 'cmp');
			$rel_dados_sub_tabela = "
			  <tr> 
				<td></td>
				<td></td>
				<td></td>
				<td width=\"86\"></td>
				<td colspan=\"3\"></td>
				<td width=\"17\"></td>
				<td colspan=\"2\"></td>
				<td width=\"17\"></td>
				<td width=\"65\"></td>
				<td colspan=\"4\"></td>
			  </tr>
			  <tr> 
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td colspan=\"12\" valign=\"top\">
				<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"procedimentos-tabela\">
					<!--DWLayoutTable-->
					<tr> 
					  <td width=\"36\" height=\"18\"></td>
					  <td width=\"220\" valign=\"top\" class=\"procedimentos-cabeca\">CID / CIAP</td>
					  <td width=\"75\" valign=\"top\" class=\"procedimentos-cabeca\">Data da cons.</td>
					  <td width=\"113\" valign=\"top\" class=\"procedimentos-cabeca\">DUM informado</td>
					  <td width=\"43\" valign=\"top\" class=\"procedimentos-cabeca\">CBO</td>
					  <td width=\"78\" valign=\"top\" class=\"procedimentos-cabeca\">INE</td>
					  <td width=\"56\" valign=\"top\" class=\"procedimentos-cabeca\">CNES</td>
					  <td width=\"111\" valign=\"top\" class=\"procedimentos-cabeca\">CNS Prof.</td>
					</tr>
			";
			$soma_consultas = 0;
			for ($i=0;$i<$total_consultas;$i++){
				$soma_consultas++;

				$profissionalOK = 'procedimentos-dado';
				$profissionalAlerta = 'NAO';
				if (strlen($array_consultas[$i]['ine']) == 10){
					if (file_exists('xml/cnes.xml')){
						if (!cnes('xml/cnes.xml',$array_consultas[$i]['cns_prof'],$array_consultas[$i]['nu_cbo'],$array_consultas[$i]['ine'],'I')){
							$profissionalOK = 'procedimentos-dado-amarelo';
							$profissionalAlerta = 'SIM';
						}
					}
				}
				$texto = zesq($soma_consultas,5).";".mcpf($rCPF).";".mcns($rCNS).";".dtshow($array_consultas[$i]['data']).";".dtshow($array_consultas[$i]['data_dum_consulta']).";".$array_consultas[$i]['cnes'].";".$array_consultas[$i]['ine'].";".$array_consultas[$i]['nu_cbo'].";".$array_consultas[$i]['cns_prof'].";".$profissionalAlerta."\r\n";
				fwrite($FC, $texto);
				$rel_dados_sub_tabela .= "
						<tr> 
						  <td height=\"18\" valign=\"top\" class=\"procedimentos-dado\">".$soma_consultas."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".str_replace("|"," ",$array_consultas[$i]['cids'])."/".str_replace("|"," ",$array_consultas[$i]['ciaps'])."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".dtshow($array_consultas[$i]['data'])." ".$array_consultas[$i]['indicativo_consulta']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".dtshow($array_consultas[$i]['data_dum_consulta'])." ".$array_consultas[$i]['indicativo_dum']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$array_consultas[$i]['nu_cbo']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$array_consultas[$i]['ine']."</td>
						  <td valign=\"top\" class=\"procedimentos-dado\">".$array_consultas[$i]['cnes']."</td>
						  <td valign=\"top\" class=\"".$profissionalOK."\">".$array_consultas[$i]['cns_prof']."</td>
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
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td colspan=\"12\" valign=\"top\">
				<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"procedimentos-tabela\">
					<!--DWLayoutTable-->
					<tr> 
					  <td width=\"36\" height=\"18\"></td>
					  <td width=\"169\" valign=\"top\" class=\"procedimentos-cabeca\">Procedimento (A/S)</td>
					  <td width=\"75\" valign=\"top\" class=\"procedimentos-cabeca\">Data do proc.</td>
					  <td width=\"213\" valign=\"top\" class=\"procedimentos-cabeca\">CNS Prof.</td>
					  <td width=\"43\" valign=\"top\" class=\"procedimentos-cabeca\">CBO</td>
					  <td width=\"78\" valign=\"top\" class=\"procedimentos-cabeca\">INE</td>
					  <td width=\"56\" valign=\"top\" class=\"procedimentos-cabeca\">CNES</td>
					  <td width=\"62\" valign=\"top\" class=\"procedimentos-cabeca\">Exame</td>
					</tr>
			";
			$soma_procedimentos = 0;
			for ($i=0;$i<count($exames);$i++){
				$soma_procedimentos++;

				$consproced = '';
				$consproced_csv = 'N';
				if (in_array($exames[$i]['data'], $array_consultas_data)) {
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
				$texto = $soma_procedimentos.";".mcpf($rCPF).";".mcns($rCNS).";".dtshow($exames[$i]['data']).";".$consproced_csv.";".$exames[$i]['tabela'].";".$exames[$i]['cns_prof'].";".$exames[$i]['cnes'].";".$exames[$i]['ine'].";".$exames[$i]['cbo'].";".str_replace("í","i",$exames[$i]['tipo']).";".$exames[$i]['procedimento1']."/".$exames[$i]['procedimento2'].";".$profissionalAlerta."\r\n";
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
						  <td valign=\"top\" class=\"procedimentos-dado\">".$exames[$i]['tipo']."</td>
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
    <td></td>
    <td width=\"64\"></td>
    <td width=\"19\"></td>
    <td width=\"62\"></td>
    <td></td>
    <td width=\"27\"></td>
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
	$pini = $pini + $num_getantes_final;
	$num1 = $numerador_ind1;
	$num2 = $numerador_ind2;
	$num3 = $numerador_ind3;
	$cdes = $conta_geral_desconto;
	if ($num_getantes_final > 0){
		$link_pag = "<a href=\"rel_gestantes.php?pini=".$pini."&num1=".$num1."&num2=".$num2."&num3=".$num3."&cdes=".$cdes."\"><img src=\"images/pagina.png\"></a>";
	}
}
// -----------------------------------------------------------------------------------------------------------

$show_desconto = "";
if ($conta_geral_desconto > 0){
	$show_desconto = " (descontar ".$conta_geral_desconto." gestante(s) por conta de inconsistências)";
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
	Total de ".$total_geral." gestantes | 
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
if ($paginacao <= 0 || $num_getantes_final == 0){
	$rel_indicadores = "
	</table>
	<table width=\"1009\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
	  <!--DWLayoutTable-->
	  <tr> 
		<td width=\"214\" height=\"30\">&nbsp;</td>
		<td width=\"260\" valign=\"top\" class=\"resumo-sub-titulo\">Indicador 1</td>
		<td width=\"223\" valign=\"top\" class=\"resumo-sub-titulo\">Indicador 2</td>
		<td width=\"192\" valign=\"top\" class=\"resumo-sub-titulo\">Indicador 3</td>
		<td width=\"120\">&nbsp;</td>
	  </tr>
	  <tr> 
		<td height=\"30\" valign=\"top\" class=\"resumo-numerador\">Numerador&nbsp;&nbsp;</td>
		<td valign=\"top\" class=\"resumo-v-numerador\">".$numerador_ind1."</td>
		<td valign=\"top\" class=\"resumo-v-numerador\">".$numerador_ind2."</td>
		<td valign=\"top\" class=\"resumo-v-numerador\">".$numerador_ind3."</td>
		<td></td>
	  </tr>
	  <tr> 
		<td height=\"30\" valign=\"top\" class=\"resumo-denominador\">Denominador&nbsp;&nbsp;</td>
		<td colspan=\"3\" valign=\"top\" class=\"resumo-v-denominador\">".$conta_geral.$show_desconto."</td>
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
