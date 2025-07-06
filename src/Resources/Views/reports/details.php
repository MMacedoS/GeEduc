
<script src="<?=URL_PREFIX_APP?>/Public/assets/js/jquery.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/apex/apexcharts.min.js"></script>

<?php
    define('ROTA_GERAL', "http://$_SERVER[HTTP_HOST]");

    $clientes = [];

    foreach ($allStudentClass as $est => $estudante): 
        
        $atividades = $activitiesService->allActivities(
            ['class_discipline_id' => $allDisciplines[0]->id]
        );
    ?>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <html>
            <head>
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
                        background-color: #00ABF1;
                        color: white;
                    }              

                    table,th,td {
                        border: 1px solid black;
                        white-space: nowrap;
                    }

                    .table {
                        width: 100%;
                    }

                    .titulo{
                        text-align:center;
                    }

                    .rodape{
                    /* margin-top:100px; */
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
                            width: 210mm;
                            border: initial;
                            border-radius: initial;
                            width: initial;
                            min-height: initial;
                            box-shadow: initial;
                            background: initial;
                            page-break-after: always;
                        }

                        .subpage:first-child
                        {
                            padding-top: 5px;
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
            </head>
            <body> 
                <div class="page"> 
                    <div class="subpage"> 
                        <table id="customers" width="100%">
                            <tr> 
                                <th width="33%" colspan="3" style="font-size: 18;" align="center">
                                    <img src="<?= ROTA_GERAL ?>/Public/assets/images/novaLogo.png" alt="logo" width="100" height="40">
                                    <span style="display: flow-root;">Instituto Social de Tucano</span>
                                </th>
                            </tr>
                            <tr>
                                <td width="50%">Endereço: Avenida Francisco Araújo de Souza, s/n.</td>
                                <td width="25%" align="center">CEP: 48790-000</td>
                                <td width="25%" style="text-align: right;">Cidade: Tucano-Ba</td>
                            </tr>
                            <tr>
                                <td width="50%">Estudante:<strong> <?= getJsonToObject($estudante->estudante)->nome ?></strong></td>
                                <td width="25%" align="center">Turma/Série/Ano Letivo: <?= getJsonToObject($estudante->turma)->nome . " / " . $estudante->ano_letivo ?></td>
                                <td width="25%" style="text-align: right;">Código: <?= $estudante->id ?></td>
                            </tr>
                        </table>

                       <p style="text-align: center; background: #ddd;">Boletim escolar detalhado</p>

                        <table class="table">
                                <thead>
                                <tr>
                                    <th rowspan="2">Disciplinas</th>
                                    <th colspan="<?=count($atividades)?>">Bimestre <?=$periodos[0]->periodo?></th>
                                    <th colspan="4">Resultado</th>
                                </tr>
                                <tr>
                                        <?
                                        foreach($atividades as $atv):
                                        ?>
                                            <th><?=$atv->tipo?></th>
                                        <? endforeach;?>
                                        <th>Rec</th>
                                        <th>Faltas</th>
                                        <th>Total</th>
                                        <th>Situação</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <? foreach($allDisciplines as $value):
                                        $total = 0;
                                        $atividades = $activitiesService->allActivities(
                                                ['class_discipline_id' => $value->id]
                                            );
                                        ?>
                                    <tr>
                                            <td>
                                                <?=getJsonToObject($value->disciplinas)->nome?>
                                            </td>
                                            <? 
                                                foreach($atividades as $atividade):                                    
                                                    $notas = $notaService->scoreByStudentsAndActiviteAndPeriod(
                                                        [
                                                            'activitie_id' => $atividade->id,
                                                            'student_class_id' => $estudante->id
                                                        ]
                                                    );

                                                    $total += $notas ? $notas['nota'] : 0;
                                            ?>
                                                <td class="titulo">
                                                    <?= $notas ? $notas['nota'] : 0;?>
                                                </td>
                                            <? endforeach;?>
                                            <td class="titulo">
                                                <?
                                                    $params['class_discipline_id'] = $value->id;
                                                    $params['student_class_id'] = $estudante->id;
                                                    $params['period_id'] = $periodos[0]->id;
                                                    $params['params'] = 'Exames Finais';
                                                    $busca_rec_final = $notaService->allRecuperationScoreByParams($params);
                                                    $total += $busca_rec_final ? $busca_rec_final['nota'] : 0;
                                                    echo $busca_rec_final ? $busca_rec_final['nota'] : 0;
                                                    
                                                ?>
                                            </td>
                                            <td class="titulo">-</td>
                                            <td class="titulo"><?=$total?></td>
                                            <td class="titulo">
                                                <?= $total >= MIN_SCORE ? "Aprovado" : "Reprovado"?>
                                            </td>
                                    </tr> 
                                    <? endforeach;?>
                                </tbody>
                        </table>
                        <small class="mt-3">
                            <strong>Legenda:</strong> Total = Soma das notas, REC = Recuperação, Situação = Aprovado ou Reprovado com base na média.
                        </small>

                        <?
                        $scores = $notaService->totalScoreByStudentsAndDisciplines(
                            [
                            'student_class_id' => $estudante->id,
                            ]
                        );
                        ?>

                        <div class="col-xl-6">
                            <div class="card mb-3">
                                <div class="card-body">
                                <h5 class="card-title">Desempenho</h5>
                                <div id="scoresByDiscipline-<?=$estudante->id?>" class="auto-align-graph"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    var notas = JSON.parse('<?=json_encode($scores)?>');
                    console.log(notas);
                    var options = {
                    chart: {
                        height: 350,
                        width: "100%",
                        type: "bar",
                        toolbar: {
                        show: false,
                        },
                    },
                    plotOptions: {
                        bar: {
                        horizontal: false,
                        columnWidth: "60%",
                        borderRadius: 8,
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    stroke: {
                        show: true,
                        width: 0,
                        colors: ["#ec5757"],
                    },
                    series: [
                        {
                        name: "Pontos",
                        data: notas.map((item) => item.total),
                        },
                    ],
                    legend: {
                        show: false,
                    },
                    xaxis: {
                        categories: notas.map((item) => item.nome),
                    },
                    yaxis: {
                        show: false,
                    },
                    fill: {
                        colors: ["#e73737"],
                    },
                    tooltip: {
                        y: {
                        formatter: function (val) {
                            return +val;
                        },
                        },
                    },
                    grid: {
                        borderColor: "#c8cfcc",
                        strokeDashArray: 5,
                        xaxis: {
                        lines: {
                            show: true,
                        },
                        },
                        yaxis: {
                        lines: {
                            show: false,
                        },
                        },
                        padding: {
                        top: 0,
                        right: 0,
                        bottom: -10,
                        left: 0,
                        },
                    },
                    };
                    var chart = new ApexCharts(document.querySelector("#scoresByDiscipline-<?=$estudante->id?>"), options);
                    chart.render();
                
                </script>
            </body>
        </html>
    <? 
    endforeach;
    ?>

