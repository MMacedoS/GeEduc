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

        $aluno = $estudante->id;
        $pagina = "";
        $pagina .= '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            ';
        $pagina .= '<html>';
        $pagina .= '<head>
                <style>                
                body {
                    -webkit-print-color-adjust: exact;
                }
                #customers {
                margin: 16px 0 0 2px;        
                font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
                border: 1px solid #ddd;
                font-size: 9pt;
                }
            .page {
                width: 210mm;
                min-height: 297mm;
                padding: 0 18px 0 4mm;
                margin: 4mm auto;
                border: 1px #D3D3D3 solid;
                border-radius: 5px;
                background: white;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
                }
            .subpage {
                padding: 0cm;
                height: 272mm;
                outline: 0cm #FFEAEA solid;
            }
            #customers tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            #customers tr:hover {
                background-color: #ddd;
                }
            #customers th {
                padding-top: 0px;
                padding-bottom: 0px;
                background-color: #' . $bg . ';
                color: white;
            }              

            table,th,td {
                border: 1px solid black;
                white-space: nowrap;
            }
            .titulo{
                text-align:center;
            }
            .rodape{
            // margin-top:100px;
            }
            @page {
            size: A4;
            margin: 0;
            }
            @media print {
                html, body {
                width: 210mm;
                height: 297mm;        
            }
            .page {
                margin: 5px 0 0 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
        .container {
            display: flex;
            margin: 30px 0 0 0;
        }
            
        .left-column {
            flex: 0.4;
        }
        
        .right-column {
            flex: 0.4;
        }  

        .left-column, .center-column, .right-column {
        padding: 10px;
        }
        </style>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
            <title>Boletim Alunos</title>
        </head>';
        $pagina .= '<body> <div class="page"> <div class="subpage"> ';
        $pagina .= '<table id="customers" width="100%">
            <tr> 
                <th width="33%" colspan="3" style="font-size: 18;" align="center">
                    <img src="' . ROTA_GERAL . '/Public/assets/images/novaLogo.png" alt="logo" width="100" height="40">
                    <span style="display: flow-root;">Instituto Social de Tucano</span>
                </th>
            </tr>
            <tr>
                <td width="50%">Endereço: Avenida Francisco Araújo de Souza, s/n.</td>
                <td width="25%" align="center">CEP: 48790-000</td>
                <td width="25%" style="text-align: right;">Cidade: Tucano-Ba</td>
            </tr>
            <tr>
                <td width="50%">Estudante:<strong> ' . getJsonToObject($estudante->estudante)->nome . '</strong></td>
                <td width="25%" align="center">Turma/Série/Ano Letivo: ' . getJsonToObject($estudante->turma)->nome . " / " . $estudante->ano_letivo . '</td>
                <td width="25%" style="text-align: right;">Código: ' . $aluno . '</td>
            </tr>
        </table>';

        $pagina .= '<p style="text-align: center; background: #ddd;">Boletim Escolar</p>';
        $pagina .= '<table id="customers">';
        $pagina .= '<tr>';
        $pagina .= '<th colspan="1" rowspan="3"><center>Componentes Curriculares</center></th>';        
        $countBimestre = count($periodos);
        $pagina .= '<th colspan="' . ($countBimestre + 7) . '"><center>Bimestres</center></th>';
        $pagina .= '<th colspan="5" ><center>Total</center></th>';
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
        $pagina .= '<th colspan="1" rowspan="2"><center>E.F</center></th>';
        $pagina .= '<th colspan="1" rowspan="2"><center>R.F</center></th>';
        $pagina .= '<th colspan="1" rowspan="2"><center>T.F</center></th>';
        $pagina .= '</tr>';
        $pagina .= '<tr>';

        foreach ($periodos as $bim => $res_bim) {
            $pagina .= '<th><center>Soma</center></th>
                        <th><center>Faltas</center></th>';
            if ($res_bim->periodo == 2 || $res_bim->periodo == 4) {
                $pagina .='<th><center>Rec</center></th>';
            }
        }

        $pagina .= '</tr>';
        $pagina .= '<tr>';
        $disciplinas = [];
        $valores = [];
        foreach ($allDisciplines as $key => $value) {

            $pagina .= '<tr>';

            array_push($disciplinas, getJsonToObject($value->disciplinas)->nome);

            $pagina .= '<td colspan="1"><center>' . getJsonToObject($value->disciplinas)->nome . '</center></td>';

            $array_nota = [];
            $totalFalta =  0;
            $totalPontos =  0;

            for ($i = 1; $i <= $countBimestre; $i++) {

                // Exemplo de uso:
                $params = [
                    'student_class_id' => $estudante->id,
                    'class_discipline_id' => $value->id,
                    'period_id' => $i,
                    'period' => $i>3 ? "II Semestre": "I Semestre"
                ];

                $busca_notas = $notaService->allSumScoresByParams($params); 

                $busca_rec = $notaService->allRecuperationScoreByParams($params); 
                
                $params['period'] = 'Exames Finais';

                $busca_rec_final = $notaService->allRecuperationScoreByParams($params); 
                
                $countNota = count($busca_notas);
                
                $a = 0;

                if ($busca_notas == false) {
                    $pagina .= '<td></td>';
                    $pagina .= '<td></td>';
                    if ($i == 2 || $i == 4) {
                        $pagina .= '<td style="background-color: #'. $bg .';"></td>';
                    }

                    array_push($array_nota, 0);
                }

                if ($countNota > 0) {
                    foreach ($busca_notas as $notas) {
                        $nota = $notas->media;
                        
                        if($nota == 6.9) {
                            $nota = 7;
                        }

                        $paralela = 0;
                        $falta = 0;
                        $totalFalta += $falta;
                        $totalPontos += $nota;

                        array_push($array_nota, $nota);
                                                
                        $pagina .= "<td><center>" . $nota ."</center></td>";
                        $pagina .= '<td><center>-</center></td>';
                        if ($i == 2 || $i == 4) {
                            $rec = $busca_rec ? $busca_rec['nota'] : 0;
                            $totalPontos += $rec; 
                            $pagina .= '<td style="background-color: #'. $bg .';"><center>'. $rec .'</center></td>';
                        }                        
                    }
                }
            }

            $pagina .= "<td><center>$totalPontos</center></td>";
            $pagina .= "<td><center>". number_format($totalPontos / 4, 1, '.', '') . "</center></td>";
            $rec = $busca_rec_final ? $busca_rec_final['nota'] : 0;
            $pagina .= "<td><center>". number_format($rec, 1, '.', '') . "</center></td>";
            $pagina .= '<td><center><font color="red">-</font></center></td>';
            $pagina .= '<td><center><font color="red">-</font></center></td>';

            
            array_push($valores, $array_nota);
            
        }
        $pagina .= '</tr>';
        $pagina .= '</table>';

        $pagina .= '<small style="width: 30%; text-align: justify; font-style:italic; font-size: 7pt;">Legenda: Soma: somatório da etapa, REC: recuperação, RES: resultado, TOTAL: somatório anual, M.F: média final, E.F: Exame final, R.F: (A => Aprovado; A.B => aprovado com bonus, A.C => aprovado conselho, R => reprovado), T.F: total de faltas</small>';
        $pagina .= '<p style="text-align: center; background: #ddd;">Gráfico de Desempenho</p>';
        $pagina .= gerar_grafico_barras($disciplinas, $valores, $aluno);
        $pagina .= gerar_grafico_pizza_soma($disciplinas, $valores, $aluno);
        $pagina .= '<table id="customers" width="100%">
                    <tr> 
                        <td width="33%" colspan="3"  style="font-size: 12;" align="center"><strong>Informações Complementares</strong></td>
                        <td style="font-size: 12;" align="center">1º Bimestre</td>
                        <td style="font-size: 12;" align="center">2º Bimestre</td>
                        <td style="font-size: 12;" align="center">3º Bimestre</td>
                        <td style="font-size: 12;" align="center">4º Bimestre</td>
                    </tr>
                    <tr>
                        <td width="33%" colspan="3"  style="font-size: 12;" align="center"><strong>Valor</strong></td>
                        <td style="font-size: 12;" align="center">10.0</td>
                        <td style="font-size: 12;" align="center">10.0</td>
                        <td style="font-size: 12;" align="center">10.0</td>
                        <td style="font-size: 12;" align="center">10.0</td>
                    </tr>

                    <tr>
                        <td width="33%" colspan="3"  style="font-size: 12;" align="center"><strong>Média</strong></td>
                        <td style="font-size: 12;" align="center">7.0</td>
                        <td style="font-size: 12;" align="center">7.0</td>
                        <td style="font-size: 12;" align="center">7.0</td>
                        <td style="font-size: 12;" align="center">7.0</td>
                    </tr>

                    <tr>
                        <td width="33%" colspan="3"  style="font-size: 12;" align="center"><strong>Período</strong></td>
                        <td style="font-size: 12;" align="center">01/02 até 12/04 = 46 dias</td>
                        <td style="font-size: 12;" align="center">13/04 até 16/06 = 43 dias</td>
                        <td style="font-size: 12;" align="center">04/07 até 20/09 = 56 dias</td>
                        <td style="font-size: 12;" align="center">21/09 até 08/12 = 55 dias</td>
                    </tr>

                </table>';

        $pagina .= '<small id="info" style="width: 100%; text-align: justify; font-style:italic; font-size: 7pt;">Entregar este canhoto assinado pelos pais ou responsáveis em até 3 dias úteis</small>
                        <hr style="border-style: dotted; border-color: black; border-width: 2px; margin: 10px 0; padding: 0;">

                    <table id="customers" width="100%">
                    <tr> 
                        <th width="33%" colspan="3" style="font-size: 18;" align="center">
                            <img src="' . ROTA_GERAL . '/Public/assets/images/novaLogo.png" alt="logo" width="100" height="30">
                            <span style="display: flow-root;">Instituto Social de Tucano</span>
                        </th>
                    </tr>
                    <tr>
                        <td width="75%">Estudante:<strong> ' . getJsonToObject($estudante->estudante)->nome . '</strong></td>

                        <td width="25%" style="text-align: center;">Código: ' . $aluno . '</td>
                    </tr>

                    <tr>
                        <td width="25%" align="left">Turma/Série/Ano Letivo: ' . getJsonToObject($estudante->turma)->nome  . " / " . $estudante->ano_letivo . '</td>
                        <td width="25%" align="center">Emitido em: ' . implode('/', array_reverse(explode('-', date('Y-m-d')))) . '</td>
                    </tr>

                    </table>';

        $pagina .= '<div class="container">
                    <div style="width: 55%;  line-height: 0; padding: 0 0 0 16px; text-align: center;">
                    <hr>
                    <p>Assinatura do responsável</p>
                    </div>
                    <div style="width: 30%; padding: 0 0 0 16px; text-align: center; line-height: 0.5; font-style:normal; font-size: 10pt;">
                        ______/_________/______
                        <p>data</p>
                    </div>
                    </div>
                    ';

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