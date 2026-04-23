<?php 

include('camp_config.php');

$n=0;
$m=0;

/////////////////////////////////
$replace = "";
for($i=1;$i<=count($dados);$i++){
	$replace .= "REPLACE(";
}
$replace .= "p.Carteira,";
foreach($dados as $dd){
	$n++;
	$replace .= $dd . ($n<count($dados)?"),":")");
}

$querys = "";

foreach($andamentos as $and => $aa){
	$m++;
	
	$querys .= $m>1?"UNION ALL ":"";
	$querys .= "SELECT ";
	$querys .= $replace . " AS Banco, ";
	$querys .= "'" . $and . "' AS 'Indice', ";
	if($and=='Faturamento'){
		$querys .= " SUM(l.Valor) AS Qtd, ";
		$querys .= " CASE " . $replace ." ";
			foreach($bancos as $bb => $b){
				if($arr_meta[$bb][$and]!=0){
					$querys .= " WHEN '".$bb."' THEN (SUM(l.Valor) * 100 ) / " . $arr_meta[$bb][$and] . " ";
					${$bcs.$m}[] = $b;
				}
			}
		$querys .= " END AS 'Pontos'";
		$querys .= " FROM Processo AS p WITH (NOLOCK) ";
		$querys .= " JOIN Lancamento_Processo AS l WITH (NOLOCK) ON l.CodigoProcesso=p.CodigoProcesso ";
		$querys .= " AND p.Carteira IN ( " . implode(',',${$bcs.$m}) . " ) ";  
		//$querys .= " AND l.ClassificaoLancamento = '".'Honorário'."' ";
		//$querys .= " AND p.Area ='ATIVAS' ";
		//$querys .= " AND p.TipoJustica='Comum' ";
		$querys .= " AND l.StatusLancamento IN ('Pago pela Assessoria','Pendente na Assessoria','Enviado ao Contratante','Aprovado pelo Contratante','Pago pelo Contratante') ";
		$querys .= " AND (p.Funcionario IN ('JOYCE NANCY RIOS JUSTINIANO DOS REIS','BRUNA ROBERTA NASCIMENTO RIOS','RANNY BRITO DOS SANTOS') OR p.Funcionario IS NULL) ";
		$querys .= " AND (p.NumeroContratoNeoCobranca=(SELECT top 1 pp.NumeroContratoNeoCobranca FROM Processo AS pp WITH (NOLOCK) WHERE pp.CodigoProcesso=p.CodigoProcesso ORDER BY pp.NumeroContratoNeoCobranca ASC) OR p.NumeroContratoNeoCobranca IS NULL) ";
		$querys .= " AND MONTH(l.DataHora_Evento)=$mes ";
		$querys .= " AND YEAR(l.DataHora_Evento)=$ano ";
		if($day_ini!="" && $day_fim !=""){
			$querys .= " AND day(l.DataHora_Evento)>=$day_ini AND day(l.DataHora_Evento)<=$day_fim ";
		}
		
	}else{
		$querys .= " COUNT(DISTINCT(a.CodigoAndamento)) as 'Qtd', ";
		$querys .= " CASE " . $replace ." ";
			foreach($bancos as $bb => $b){
				if($arr_meta[$bb][$and]!=0){
					$querys .= " WHEN '".$bb."' THEN (COUNT(DISTINCT(a.CodigoAndamento)) * 100 ) / " . $arr_meta[$bb][$and] . " ";
					${$bcs.$m}[] = $b;
				}
			}
		$querys .= "END AS 'Pontos'";
		$querys .= "FROM Andamento_Processo AS a WITH (NOLOCK) ";
		$querys .= "JOIN Processo AS p ON p.CodigoProcesso=a.CodigoProcesso ";
		$querys .= "WHERE a.TipoAndamentoProcesso in (" . $aa . ") ";
		if($and!='Carta Precatória'){
			$querys .= "AND p.TipoProcesso NOT IN ('CARTA PRECATÓRIA') ";
		}
		$querys .= "AND p.Carteira IN ( " . implode(',',${$bcs.$m}) . " ) "; 
		$querys .= "AND MONTH(a.DataHoraEvento)=$mes ";
		$querys .= "AND YEAR(a.DataHoraEvento)=$ano ";
		if($day_ini!="" && $day_fim !=""){
			$querys .= " AND day(a.DataHoraEvento)>=$day_ini AND day(a.DataHoraEvento)<=$day_fim ";
		}
		$querys .= "AND p.TipoDesdobramento IS NULL ";
		$querys .= "AND a.Invalido='False' ";
	}
		$querys .= "GROUP BY ". $replace. " ";
}
//echo $querys;
$campanha = $querys;

$qr = sqlsrv_query($conexao1,$campanha);

foreach($bancos as $bk => $k){
	$arr_banco[$bk] = array();
	$arr_indic[$bk] = array();
	foreach($andamentos as $andam => $an){
		$arr_banco[$bk][$andam] = 0;
		$arr_indic[$bk][$andam] = 0;
	}
}

while($wr = sqlsrv_fetch_array($qr, SQLSRV_FETCH_ASSOC)){
	$arr_banco[$wr['Banco']][$wr['Indice']] += $wr['Pontos'];
	$arr_indic[$wr['Banco']][$wr['Indice']]	+= $wr['Qtd'];
}

?>