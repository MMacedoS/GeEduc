<script src="<?= URL_PREFIX_APP ?>/Public/assets/js/jquery.min.js"></script>
<script src="<?= URL_PREFIX_APP ?>/Public/assets/vendor/apex/apexcharts.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<html>

<head>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            -webkit-print-color-adjust: exact;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #f5f5f5;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 8mm 6mm;
            margin: 2mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .subpage {
            padding: 0;
            min-height: 281mm;
        }

        /* Header Styles */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border: 2px solid #00ABF1;
        }

        .header-table th {
            background: linear-gradient(135deg, #00ABF1 0%, #0088c7 100%);
            color: white;
            padding: 8px;
            font-size: 16px;
            font-weight: 600;
            border: none;
        }

        .header-table td {
            padding: 5px 8px;
            border: 1px solid #ddd;
            font-size: 8pt;
            background: #f9f9f9;
        }

        .header-table td strong {
            color: #00ABF1;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        /* Student Info Card */
        .student-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 8px;
            border-left: 3px solid #00ABF1;
        }

        .student-info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .info-item {
            padding: 4px;
        }

        .info-label {
            font-size: 7pt;
            color: #666;
            font-weight: 600;
            display: block;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 11pt;
            color: #333;
            font-weight: 500;
        }

        /* Title Styles */
        .section-title {
            background: linear-gradient(135deg, #00ABF1 0%, #0088c7 100%);
            color: white;
            padding: 6px 12px;
            text-align: center;
            font-size: 11pt;
            font-weight: 600;
            border-radius: 4px;
            margin: 8px 0 6px 0;
            box-shadow: 0 2px 5px rgba(0, 171, 241, 0.3);
        }

        /* Table Styles */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 7pt;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .grades-table thead tr:first-child th {
            background: linear-gradient(135deg, #00ABF1 0%, #0088c7 100%);
            color: white;
            padding: 5px 4px;
            font-weight: 600;
            border: 1px solid #0088c7;
            font-size: 8pt;
        }

        .grades-table thead tr:nth-child(2) th {
            background: #0088c7;
            color: white;
            padding: 4px 3px;
            font-weight: 500;
            border: 1px solid #0077b3;
            font-size: 7pt;
        }

        .grades-table tbody td {
            padding: 4px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .grades-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .grades-table tbody tr:hover {
            background-color: #e9f5ff;
            transition: background-color 0.3s ease;
        }

        .discipline-name {
            text-align: left !important;
            font-weight: 600;
            color: #333;
            padding-left: 12px !important;
        }

        .status-approved {
            color: #28a745;
            font-weight: 600;
        }

        .status-failed {
            color: #dc3545;
            font-weight: 600;
        }

        .status-recovery {
            color: #ffc107;
            font-weight: 600;
        }

        /* Statistics Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin: 8px 0;
        }

        .stat-card {
            background: white;
            padding: 8px;
            border-radius: 5px;
            border-left: 3px solid #00ABF1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-label {
            font-size: 7pt;
            color: #666;
            margin-bottom: 3px;
        }

        .stat-value {
            font-size: 14pt;
            font-weight: 700;
            color: #333;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            padding: 8px;
            border-radius: 5px;
            margin: 8px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .chart-title {
            font-size: 10pt;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            padding-bottom: 4px;
            border-bottom: 2px solid #00ABF1;
        }

        /* Legend */
        .legend {
            background: #f8f9fa;
            padding: 6px 8px;
            border-radius: 4px;
            border-left: 2px solid #00ABF1;
            margin-top: 6px;
        }

        .legend strong {
            color: #00ABF1;
            font-size: 7pt;
        }

        .legend-content {
            font-size: 6pt;
            color: #666;
            margin-top: 2px;
            line-height: 1.4;
        }

        /* Footer */
        .footer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 2px solid #00ABF1;
            font-size: 6pt;
            color: #666;
            text-align: center;
        }

        @page {
            size: A4;
            margin: 8mm 5mm;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
                background: white;
            }

            .page {
                margin: 0 auto;
                padding: 8mm 6mm;
                border: none;
                border-radius: 0;
                box-shadow: none;
                page-break-after: always;
            }

            .subpage:first-child {
                padding-top: 0;
            }

            .stats-container,
            .chart-container {
                page-break-inside: avoid;
            }
        }
    </style>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>Boletim Detalhado - Instituto Social de Tucano</title>
</head>

<body>
    <?php
    define('ROTA_GERAL', "http://$_SERVER[HTTP_HOST]");

    $clientes = [];

    foreach ($allStudentClass as $est => $estudante):
        // Converter array para objeto para facilitar o acesso
        $estudante = (object) $estudante;

        // Converter primeira disciplina para objeto também
        $primeiraDisciplina = !empty($allDisciplines) ? (object) $allDisciplines[0] : null;

        $atividades = $activitiesService->allActivities(
            ['class_discipline_id' => $primeiraDisciplina ? $primeiraDisciplina->code : 0]
        );

        // Calcular estatísticas do aluno
        $totalDisciplinas = count($allDisciplines);
        $disciplinasAprovadas = 0;
        $disciplinasReprovadas = 0;
        $disciplinasRecuperacao = 0;
        $mediaGeral = 0;
        $somaNotas = 0;
    ?>
        <div class="page">
            <div class="subpage">
                <!-- Header -->
                <table class="header-table">
                    <tr>
                        <th colspan="3">
                            <div class="logo-container">
                                <img src="<?= ROTA_GERAL ?>/Public/assets/images/novaLogo.png" alt="logo" width="120" height="48">
                                <span>Instituto Social de Tucano</span>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <td width="50%"><strong>Endereço:</strong> Avenida Francisco Araújo de Souza, s/n.</td>
                        <td width="25%"><strong>CEP:</strong> 48790-000</td>
                        <td width="25%"><strong>Cidade:</strong> Tucano-BA</td>
                    </tr>
                </table>

                <!-- Student Info Card -->
                <div class="student-info">
                    <div class="student-info-grid">
                        <div class="info-item">
                            <span class="info-label">Estudante</span>
                            <span class="info-value"><?= $estudante->student->name ?? 'N/A' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Turma/Série</span>
                            <span class="info-value"><?= $estudante->class_name ?? 'N/A' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ano Letivo</span>
                            <span class="info-value"><?= $estudante->school_year ?? date('Y') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Matrícula</span>
                            <span class="info-value"><?= $estudante->code ?? 'N/A' ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Bimestre</span>
                            <span class="info-value"><?= $periodos[0]->periodo ?>º Bimestre</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Data de Emissão</span>
                            <span class="info-value"><?= date('d/m/Y') ?></span>
                        </div>
                    </div>
                </div>

                <div class="section-title">Boletim Escolar Detalhado - <?= $periodos[0]->periodo ?>º Bimestre</div>

                <table class="grades-table">
                    <thead>
                        <tr>
                            <th rowspan="2">Disciplinas</th>
                            <th colspan="<?= count($atividades) ?>">Atividades Avaliativas</th>
                            <th colspan="5">Resultado Final</th>
                        </tr>
                        <tr>
                            <?php foreach ($atividades as $key => $atv): ?>
                                <th><?= $key + 1 ?>ª Atv.</th>
                            <?php endforeach; ?>
                            <th>Rec.</th>
                            <th>Faltas</th>
                            <th>Total</th>
                            <th>%</th>
                            <th>Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($allDisciplines as $value):
                            // Converter disciplina para objeto
                            $value = (object) $value;
                            $total = 0;
                            $atividades = $activitiesService->allActivities(
                                ['class_discipline_id' => $value->code]
                            );
                            $maxPontos = count($atividades) * 10; // Assumindo 10 pontos por atividade
                        ?>
                            <tr>
                                <td class="discipline-name">
                                    <?= $value->subject_name ?? 'N/A' ?>
                                </td>
                                <?php
                                foreach ($atividades as $atividade):
                                    $notas = $notaService->scoreByStudentsAndActiviteAndPeriod(
                                        [
                                            'activitie_id' => $atividade->id,
                                            'student_class_id' => $estudante->code,
                                            'period' => $periodos[0]->id
                                        ]
                                    );

                                    $notaAtual = $notas ? $notas['nota'] : 0;
                                    $total += $notaAtual;
                                ?>
                                    <td>
                                        <?= $notas ? number_format($notaAtual, 1, ',', '.') : '-'; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td>
                                    <?php
                                    $params['class_discipline_id'] = $value->code;
                                    $params['student_class_id'] = $estudante->code;
                                    $params['period_id'] = $periodos[0]->id;
                                    $params['params'] = 'Exames Finais';
                                    $busca_rec_final = $notaService->allRecuperationScoreByParams($params);
                                    $notaRec = $busca_rec_final ? $busca_rec_final['nota'] : 0;
                                    $total += $notaRec;
                                    echo $notaRec > 0 ? number_format($notaRec, 1, ',', '.') : '-';
                                    ?>
                                </td>
                                <td>-</td>
                                <td><strong><?= number_format($total, 1, ',', '.') ?></strong></td>
                                <td>
                                    <?php
                                    $percentual = $maxPontos > 0 ? ($total / $maxPontos) * 100 : 0;
                                    echo number_format($percentual, 1, ',', '.') . '%';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($total >= 6.9) {
                                        echo '<span class="status-approved">Aprovado</span>';
                                        $disciplinasAprovadas++;
                                    } else {
                                        // Se não atingiu a média mínima, está em recuperação
                                        // (independente de ter feito ou não a recuperação ainda)
                                        echo '<span class="status-recovery">Recuperação</span>';
                                        $disciplinasRecuperacao++;
                                    }
                                    $somaNotas += $total;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach;
                        $mediaGeral = $totalDisciplinas > 0 ? $somaNotas / $totalDisciplinas : 0;
                        ?>
                    </tbody>
                </table>

                <!-- Statistics Cards -->
                <div class="stats-container">
                    <div class="stat-card success">
                        <div class="stat-label">Disciplinas Aprovadas</div>
                        <div class="stat-value"><?= $disciplinasAprovadas ?></div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-label">Disciplinas Reprovadas</div>
                        <div class="stat-value"><?= $disciplinasReprovadas ?></div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-label">Em Recuperação</div>
                        <div class="stat-value"><?= $disciplinasRecuperacao ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Média Geral</div>
                        <div class="stat-value"><?= number_format($mediaGeral, 1, ',', '.') ?></div>
                    </div>
                </div>

                <div class="legend">
                    <strong>Legenda e Informações:</strong>
                    <div class="legend-content">
                        <strong>Atv.</strong> = Atividade Avaliativa |
                        <strong>Rec.</strong> = Recuperação |
                        <strong>Total</strong> = Soma das notas das atividades |
                        <strong>%</strong> = Percentual de aproveitamento |
                        <strong>Média Mínima:</strong> 7 pontos<br>
                        <strong>Situação:</strong> <span style="color: #28a745;">Aprovado</span> (nota ≥ 7),
                        <span style="color: #ffc107;">Recuperação</span> (nota < 7 - aluno precisa fazer ou já fez recuperação)
                            </div>
                    </div>

                    <?php
                    $scores = $notaService->totalScoreByStudentsAndDisciplines(
                        [
                            'student_class_id' => $estudante->code,
                        ]
                    );
                    ?>

                    <!-- Chart Container -->
                    <div class="chart-container">
                        <div class="chart-title">Gráfico de Desempenho por Disciplina</div>
                        <div id="scoresByDiscipline-<?= $estudante->code ?>" class="auto-align-graph"></div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <p><strong>Instituto Social de Tucano</strong> - Sistema de Gestão Escolar</p>
                        <p>Documento gerado em <?= date('d/m/Y \à\s H:i') ?> | Este documento possui validade acadêmica</p>
                    </div>
                </div>
            </div>
            <script>
                var notas = JSON.parse('<?= json_encode($scores) ?>');
                var minScore = 7;

                var options = {
                    chart: {
                        height: 180,
                        width: "100%",
                        type: "bar",
                        toolbar: {
                            show: false,
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: "65%",
                            borderRadius: 6,
                            dataLabels: {
                                position: 'top',
                            },
                        },
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val.toFixed(1);
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '10px',
                            colors: ["#304758"]
                        }
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    series: [{
                        name: "Pontuação Total",
                        data: notas.map((item) => parseFloat(item.total)),
                    }],
                    legend: {
                        show: true,
                        position: 'top',
                    },
                    xaxis: {
                        categories: notas.map((item) => item.nome),
                        labels: {
                            style: {
                                fontSize: '9px',
                            },
                            rotate: -45,
                            rotateAlways: true,
                        }
                    },
                    yaxis: {
                        show: true,
                        title: {
                            text: 'Pontos'
                        },
                        labels: {
                            formatter: function(val) {
                                return val.toFixed(1);
                            }
                        }
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            shadeIntensity: 0.25,
                            gradientToColors: ['#0088c7'],
                            inverseColors: false,
                            opacityFrom: 0.85,
                            opacityTo: 0.85,
                            stops: [50, 0, 100]
                        },
                        colors: ["#00ABF1"],
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toFixed(2) + " pontos";
                            },
                        },
                        theme: 'light',
                    },
                    grid: {
                        borderColor: "#e0e0e0",
                        strokeDashArray: 3,
                        xaxis: {
                            lines: {
                                show: true,
                            },
                        },
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                        padding: {
                            top: 0,
                            right: 10,
                            bottom: 0,
                            left: 10,
                        },
                    },
                    annotations: {
                        yaxis: [{
                            y: minScore,
                            borderColor: '#28a745',
                            strokeDashArray: 4,
                            label: {
                                borderColor: '#28a745',
                                style: {
                                    color: '#fff',
                                    background: '#28a745',
                                    fontSize: '10px',
                                },
                                text: 'Média Mínima: ' + minScore,
                            }
                        }]
                    }
                };
                var chart = new ApexCharts(document.querySelector("#scoresByDiscipline-<?= $estudante->code ?>"), options);
                chart.render();
            </script>
        <?
    endforeach;
        ?>

</body>

</html>

<script defer>
    document.addEventListener("DOMContentLoaded", function(event) {
        console.log("DOM completamente carregado e analisado");
        setTimeout(() => {
            window.print();
        }, 5000)
    });
</script>