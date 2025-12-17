<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<?php
define('ROTA_GERAL', "http://$_SERVER[HTTP_HOST]");

$disciplinas = [];
$valores = array();

function gerar_grafico_barras($disciplinas, $valores, $canva)
{
    $cores = array("rgba(255, 99, 132, 0.8)", "rgba(54, 162, 235, 0.8)", "rgba(255, 206, 86, 0.8)", "rgba(75, 192, 192, 0.8)");

    $html = '<canvas id="grafico-barras' . $canva . '" width="750" height="200"></canvas>';
    $html .= '<script>';
    $html .= 'var ctx = document.getElementById("grafico-barras' . $canva . '").getContext("2d");';
    $html .= 'var meuGrafico = new Chart(ctx, {';
    $html .= '  type: "bar",';
    $html .= '  data: {';
    $html .= '    labels: ' . json_encode($disciplinas) . ',';
    $html .= '    datasets: [';
    for ($i = 0; $i < count($valores[0]); $i++) {
        $bimestre = $i + 1;
        $html .= '{';
        $html .= 'label: "Bimestre ' . $bimestre . 'º",';
        $html .= 'data: [';
        for ($j = 0; $j < count($valores); $j++) {
            $html .= $valores[$j][$i] . ',';
        }
        $html = rtrim($html, ',');
        $html .= '],';
        $html .= 'backgroundColor: "' . $cores[$i] . '",';
        $html .= 'borderColor: "' . $cores[$i] . '",';
        $html .= 'borderWidth: 1';
        $html .= '},';
    }
    $html .= ']},';
    $html .= 'options: 
                    {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }, 
                        plugins: {
                            legend: {
                                position: "bottom", 
                                labels: {
                                    boxHeight: 20
                                } 
                            }
                        }
                    }
                });';
    $html .= '</script>';
    return $html;
}

function gerar_grafico_pizza_soma($disciplinas, $valores, $canva)
{
    // Somar os valores das disciplinas iguais
    $disciplina_valores = array_combine($disciplinas, $valores);

    $soma_valores = array();
    foreach ($disciplina_valores as $disciplina => $valor) {
        if (!isset($soma_valores[$disciplina])) {
            $soma_valores[$disciplina] = 0;
        }
        if (!empty($valor)) {
            foreach ($valor as $value) {
                $soma_valores[$disciplina] += floatval($value);
            }
        }
    }
    // Encontrar a disciplina com a maior e menor soma
    $maior_soma_disciplina = array_keys($soma_valores, max($soma_valores))[0];
    $menor_soma_disciplina = array_keys($soma_valores, min($soma_valores))[0];

    $html = '<p>';
    $html .= '<b>Maior desempenho:</b> ' . $maior_soma_disciplina . ' | <b>Desempenho: </b>' . max($soma_valores);
    $html .= '</br> <b>Menor desempenho:</b> ' . $menor_soma_disciplina . ' | <b>Desempenho: </b>' . min($soma_valores);
    $html .= '</p>';
    return $html;
}

function prepareSituation($situation): string
{
    switch (strtolower($situation)) {
        case 'Aprovado':
            return 'A';
        case 'reprovado':
            return 'R';
        case 'reprovado no conselho':
            return 'R.C';
        case 'Aprovado por bônus':
            return 'A.B';
        case 'aprovado na final':
            return 'A.F';
        case 'aprovado no conselho':
            return 'A.C';
        default:
            return 'N/A'; // Caso não encontre uma correspondência
    }
}

$bg = '00ABF1';

$clientes = [];

foreach ($allStudentClass as $est => $estudante) {
    $estudante = (object)$estudante;

    $aluno = $estudante->student_id;
    $pagina = "";
    $pagina .= '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
    $pagina .= '<html>';
    $pagina .= '<head>
                <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    -webkit-print-color-adjust: exact;
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    color: #333;
                    background: #f5f5f5;
                }
                
                .page {
                    width: 210mm;
                    min-height: 297mm;
                    padding: 3mm 8mm;
                    margin: 0 auto;
                    border: 1px #D3D3D3 solid;
                    border-radius: 5px;
                    background: white;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
                }
                
                .subpage {
                    padding: 0;
                    min-height: 289mm;
                }
                
                /* Header Styles */
                .header-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 3px;
                    border: 2px solid #00ABF1;
                }
                
                .header-table th {
                    background: linear-gradient(135deg, #00ABF1 0%, #0088c7 100%);
                    color: white;
                    padding: 3px 6px;
                    font-size: 12px;
                    font-weight: 600;
                    border: 1px solid #0088c7;
                }
                
                .header-table td {
                    padding: 3px 6px;
                    border: 1px solid #ddd;
                    font-size: 8pt;
                    background: #f9f9f9;
                }
                
                .header-table td strong {
                    color: #00ABF1;
                }
                
                .logo-container {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                /* Title Styles */
                .section-title {
                    background: linear-gradient(135deg, #00ABF1 0%, #0088c7 100%);
                    color: white;
                    padding: 3px 8px;
                    text-align: center;
                    font-size: 9pt;
                    font-weight: 600;
                    border-radius: 3px;
                    margin: 3px 0 2px 0;
                    box-shadow: 0 1px 3px rgba(0, 171, 241, 0.3);
                }
                
                /* Table Styles */
                #customers {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                    font-size: 7pt;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    margin-bottom: 3px;
                }
                
                #customers tr:nth-child(even) {
                    background-color: #f8f9fa;
                }
                
                #customers tr:hover {
                    background-color: #e9f5ff;
                    transition: background-color 0.3s ease;
                }
                
                #customers th {
                    padding: 3px 2px;
                    background: linear-gradient(135deg, #00ABF1 0%, #0088c7 100%);
                    color: white;
                    border: 1px solid #0088c7;
                    font-weight: 600;
                    font-size: 7pt;
                }
                
                #customers td {
                    padding: 2px 1px;
                    border: 1px solid #ddd;
                    text-align: center;
                }
                
                .status-approved {
                    color: #28a745;
                    font-weight: 600;
                }
                
                .status-recovery {
                    color: #ffc107;
                    font-weight: 600;
                }
                
                .status-failed {
                    color: #dc3545;
                    font-weight: 600;
                }
                
                table, th, td {
                    border: 1px solid #ddd;
                }
                
                .titulo {
                    text-align: center;
                }
                
                /* Chart Container */
                .chart-container {
                    background: white;
                    padding: 3px;
                    border-radius: 3px;
                    margin: 3px 0;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                }
                
                /* Legend */
                .legend {
                    background: #f8f9fa;
                    padding: 3px 6px;
                    border-radius: 3px;
                    border-left: 2px solid #00ABF1;
                    margin: 3px 0;
                    font-size: 7pt;
                    font-style: italic;
                    color: #666;
                    line-height: 1.2;
                }
                
                /* Performance Info */
                .performance-info {
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    padding: 3px;
                    border-radius: 3px;
                    margin: 3px 0;
                    border-left: 3px solid #00ABF1;
                    font-size: 7pt;
                }
                
                .performance-info b {
                    color: #00ABF1;
                }
                
                /* Info Table */
                .info-table {
                    margin: 3px 0;
                }
                
                /* Separator */
                .separator {
                    border-style: dotted;
                    border-color: #00ABF1;
                    border-width: 1px;
                    margin: 5px 0;
                    padding: 0;
                }
                
                /* Signature Container */
                .container {
                    display: flex;
                    margin: 5px 0;
                    gap: 12px;
                }
                
                .signature-box {
                    flex: 1;
                    text-align: center;
                    padding: 0 10px;
                }
                
                .signature-line {
                    border-top: 1px solid #333;
                    margin: 8px 0 4px 0;
                }
                
                .signature-label {
                    font-size: 7pt;
                    color: #666;
                }
                
                .date-box {
                    text-align: center;
                    font-size: 7pt;
                }
                
                @page {
                    size: A4;
                    margin: 5mm;
                }
                
                @media print {
                    html, body {
                        width: 210mm;
                        height: 297mm;
                        background: white;
                    }
                    
                    .page {
                        margin: 0 auto;
                        padding: 3mm 8mm;
                        border: none;
                        border-radius: 0;
                        box-shadow: none;
                        page-break-after: always;
                    }
                    
                    .chart-container {
                        page-break-inside: avoid;
                    }
                }
        </style>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
            <title>Boletim Escolar - Instituto Social de Tucano</title>
        </head>';
    $pagina .= '<body> <div class="page"> <div class="subpage"> ';
    $pagina .= '<table class="header-table" width="100%">
            <tr> 
                <td rowspan="3" width="10%" style="text-align: center; vertical-align: middle; border-right: 2px solid #00ABF1;">
                    <img src="' . ROTA_GERAL . '/Public/assets/images/novaLogo.png" alt="logo" width="60" height="24">
                </td>
                <th colspan="4" style="font-size: 12px;">Instituto Social de Tucano</th>
            </tr>
            <tr>
                <td width="35%"><strong>Estudante:</strong> ' . $estudante->student->name . '</td>
                <td width="20%"><strong>Turma:</strong> ' . $estudante->class_name . '</td>
                <td width="15%"><strong>Código:</strong> ' . $aluno . '</td>
                <td width="20%"><strong>CEP:</strong> 48790-000</td>
            </tr>
            <tr>
                <td colspan="4"><strong>Endereço:</strong> Av. Francisco Araújo de Souza, s/n. - Tucano-BA</td>
            </tr>
        </table>';

    $pagina .= '<div class="section-title">Boletim Escolar</div>';
    $pagina .= '<table id="customers">';
    $pagina .= '<tr>';
    $pagina .= '<th colspan="1" rowspan="3"><center>Componentes Curriculares</center></th>';
    $countBimestre = count($periodos);
    $pagina .= '<th colspan="' . ($countBimestre + 7) . '"><center>Bimestres</center></th>';
    $pagina .= '<th colspan="6" ><center>Total</center></th>';
    $pagina .= '</tr>';
    $pagina .= '<tr>';

    foreach ($periodos as $bim => $res_bim) {
        if ($res_bim->periodo == 2 || $res_bim->periodo == 4) {
            $pagina .= '<th colspan="3">';
        }

        if ($res_bim->periodo == 1 || $res_bim->periodo == 3) {
            $pagina .= '<th colspan="2">';
        }
        $pagina .= '<center>' . $res_bim->periodo . 'º Bimestre:</center></th>';
    }

    $pagina .= '<th colspan="1" rowspan="2"><center>Total</center></th>';
    $pagina .= '<th colspan="1" rowspan="2"><center>M.F</center></th>';
    $pagina .= '<th colspan="1" rowspan="2"><center>%</center></th>';
    $pagina .= '<th colspan="1" rowspan="2"><center>E.F</center></th>';
    $pagina .= '<th colspan="1" rowspan="2"><center>R.F</center></th>';
    $pagina .= '<th colspan="1" rowspan="2"><center>T.F</center></th>';
    $pagina .= '</tr>';
    $pagina .= '<tr>';

    foreach ($periodos as $bim => $res_bim) {
        $pagina .= '<th><center>Soma</center></th>
                        <th><center>Faltas</center></th>';
        if ($res_bim->periodo == 2 || $res_bim->periodo == 4) {
            $pagina .= '<th><center>Rec</center></th>';
        }
    }

    $pagina .= '</tr>';
    $pagina .= '<tr>';
    $disciplinas = [];
    $valores = [];
    foreach ($allDisciplines as $key => $value) {

        $pagina .= '<tr>';

        array_push($disciplinas, $value->subject_name);

        $pagina .= '<td colspan="1"><center>' . $value->subject_name . '</center></td>';

        $array_nota = [];
        $totalFalta =  0;
        $totalPontos =  0;

        for ($i = 1; $i <= $countBimestre; $i++) {
            $params = [
                'student_class_id' => $estudante->code,
                'class_discipline_id' => $value->code,
                'period_id' => $i,
                'period' => $i > 3 ? "II Semestre" : "I Semestre"
            ];

            $busca_notas = $notaService->allSumScoresByParams($params);

            $busca_rec = $notaService->allRecuperationScoreByParams($params);

            $params['ano_letivo'] = $estudante->school_year;
            $busca_rec_final = null;
            if ($i == 4) {
                $busca_rec_final = $notaService->scoreFinalByStudentAndDisciplineAndPeriod($params);
            }

            $countNota = count($busca_notas);

            $a = 0;

            if ($busca_notas == false) {
                $pagina .= '<td></td>';
                $pagina .= '<td></td>';
                if ($i == 2 || $i == 4) {
                    $pagina .= '<td style="background-color: #' . $bg . ';"></td>';
                }

                array_push($array_nota, 0);
            }

            if ($countNota > 0) {
                foreach ($busca_notas as $notas) {
                    $nota = $notas->media;

                    if ($nota == 6.9) {
                        $nota = 7;
                    }

                    $paralela = 0;
                    $falta = 0;
                    $totalFalta += $falta;
                    $totalPontos += $nota;

                    array_push($array_nota, $nota);

                    $pagina .= "<td><center>" . number_format($nota, 1, ',') . "</center></td>";
                    $pagina .= '<td><center>-</center></td>';
                    if ($i == 2 || $i == 4) {
                        $rec = $busca_rec ? $busca_rec['nota'] : 0;
                        $totalPontos += $rec;
                        $pagina .= '<td style="background-color: #' . $bg . ';"><center>' . $rec . '</center></td>';
                    }
                }
            }
        }

        $mediaFinal = $totalPontos / 4;
        $percentual = ($mediaFinal / 10) * 100;

        $exameFinal = $busca_rec_final ? $busca_rec_final['nota'] : 0;

        // Determinar situação
        $situacao = '-';
        $corSituacao = 'black';
        if ($totalPontos >= 27.8 || $exameFinal >= 6) {
            $situacao = 'Aprovado';
            $corSituacao = '#28a745';
        } elseif ($totalPontos < 27.8) {
            $situacao = 'Recuperação';
            $corSituacao = '#ffc107';
        }

        $pagina .= "<td><center>$totalPontos</center></td>";
        $pagina .= "<td><center>" . number_format($mediaFinal, 1, ',', '') . "</center></td>";
        $pagina .= "<td><center>" . number_format($percentual, 1, ',', '') . "%</center></td>";
        $pagina .= "<td><center>" . number_format($exameFinal, 1, ',', '') . "</center></td>";
        $pagina .= '<td><center><font color="' . $corSituacao . '"><strong>' . $situacao . '</strong></font></center></td>';
        $pagina .= '<td><center>' . $totalFalta . '</center></td>';


        array_push($valores, $array_nota);
    }
    $pagina .= '</tr>';
    $pagina .= '</table>';

    $pagina .= '<div class="legend"><strong>Legenda:</strong> Soma = somatório da etapa | REC = recuperação | TOTAL = somatório anual | M.F = média final | % = percentual de aproveitamento | E.F = Exame final | R.F = Resultado Final (Aprovado, Recuperação) | T.F = total de faltas</div>';
    $pagina .= '<div class="section-title">Gráfico de Desempenho</div>';
    $pagina .= '<div class="chart-container">';
    $pagina .= gerar_grafico_barras($disciplinas, $valores, $aluno);
    $pagina .= '</div>';
    $pagina .= '<div class="performance-info">';
    $pagina .= gerar_grafico_pizza_soma($disciplinas, $valores, $aluno);
    $pagina .= '</div>';
    $pagina .= '<div class="section-title">Informações Complementares</div>';
    $pagina .= '<table id="customers" class="info-table" width="100%">
                    <tr> 
                        <th colspan="3"><strong>Descrição</strong></th>
                        <th>1º Bimestre</th>
                        <th>2º Bimestre</th>
                        <th>3º Bimestre</th>
                        <th>4º Bimestre</th>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Valor</strong></td>
                        <td>10.0</td>
                        <td>10.0</td>
                        <td>10.0</td>
                        <td>10.0</td>
                    </tr>

                    <tr>
                        <td colspan="3"><strong>Média</strong></td>
                        <td>7.0</td>
                        <td>7.0</td>
                        <td>7.0</td>
                        <td>7.0</td>
                    </tr>

                    <tr>
                        <td colspan="3"><strong>Período</strong></td>
                        <td>01/02 até 12/04 = 46 dias</td>
                        <td>13/04 até 16/06 = 43 dias</td>
                        <td>04/07 até 20/09 = 56 dias</td>
                        <td>21/09 até 08/12 = 55 dias</td>
                    </tr>

                </table>';

    $pagina .= '<div class="legend" style="margin-top: 8px;"><strong>Importante:</strong> Entregar este canhoto assinado pelos pais ou responsáveis em até 3 dias úteis</div>
                        <hr class="separator">

                    <table class="header-table" width="100%">
                    <tr> 
                        <td rowspan="2" width="8%" style="text-align: center; vertical-align: middle; border-right: 2px solid #00ABF1;">
                            <img src="' . ROTA_GERAL . '/Public/assets/images/novaLogo.png" alt="logo" width="45" height="18">
                        </td>
                        <th colspan="3" style="font-size: 10px;">Instituto Social de Tucano - Canhoto</th>
                    </tr>
                    <tr>
                        <td width="46%"><strong>Estudante:</strong> ' . $estudante->student->name . '</td>
                        <td width="23%"><strong>Turma:</strong> ' . $estudante->class_name . '</td>
                        <td width="23%"><strong>Código:</strong> ' . $aluno . '</td>
                    </tr>
                    </table>';

    $pagina .= '<div class="container">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <p class="signature-label">Assinatura do responsável</p>
                    </div>
                    <div class="signature-box" style="flex: 0.4;">
                        <div class="date-box">
                            <p>______/_____/______</p>
                            <p class="signature-label">Data</p>
                        </div>
                    </div>
                </div>';

    $pagina .= '</div></div></body>';
    $pagina .= '</html>';

    array_push($clientes, $pagina);
}

foreach ($clientes as $boletim) {
    $html = $boletim;
    echo $html;
}
?>
<script defer>
    document.addEventListener("DOMContentLoaded", function(event) {
        console.log("DOM completamente carregado e analisado");
        window.print();
    });
</script>