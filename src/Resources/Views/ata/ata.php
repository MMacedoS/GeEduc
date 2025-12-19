<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ata de Notas - <?= $data['turma']['nome'] ?? 'Turma' ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            padding: 15px;
            background: #fff;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }

        .header h1 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 6px;
        }

        .header .info {
            font-size: 11px;
            color: #666;
            margin-top: 4px;
        }

        .info-turma {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 3px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            font-size: 11px;
        }

        .info-turma .item {
            margin: 3px 10px;
        }

        .info-turma .item strong {
            color: #2c3e50;
        }

        .disciplinas-section {
            margin-bottom: 30px;
        }

        .disciplinas-section h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .disciplinas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .disciplina-card {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            border-left: 4px solid #4e73df;
        }

        .disciplina-card .nome {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .disciplina-card .professor,
        .disciplina-card .carga {
            font-size: 13px;
            color: #666;
        }

        .notas-section {
            margin-top: 30px;
        }

        .notas-section h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10px;
            border: 1px solid #333;
        }

        table thead {
            background: #52b788;
            color: white;
        }

        table th {
            padding: 6px 4px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #333;
            vertical-align: middle;
        }

        table th.estudantes-col {
            background: #52b788;
            color: white;
            text-align: left;
            padding-left: 8px;
            min-width: 140px;
            font-size: 11px;
        }

        table th.disciplina-col {
            background: #52b788;
            color: white;
            min-width: 32px;
            max-width: 32px;
            font-size: 9px;
            font-weight: bold;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            white-space: nowrap;
            padding: 8px 4px;
            height: 140px;
        }

        table td {
            padding: 5px 3px;
            border: 1px solid #333;
            text-align: center;
        }

        table td.nome-estudante {
            text-align: left;
            padding-left: 8px;
            background: white;
            font-size: 10px;
        }

        table tbody tr:nth-child(even) td.nome-estudante {
            background: #fffbcc;
        }

        table tbody tr:nth-child(odd) td.nome-estudante {
            background: white;
        }

        .nota-cell {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            color: #000;
        }

        /* Cores baseadas nas faixas de nota como na imagem */
        .nota-azul {
            background-color: #6baed6 !important;
        }

        .nota-verde {
            background-color: #74c476 !important;
        }

        .nota-amarelo {
            background-color: #ffeda0 !important;
        }

        .nota-laranja {
            background-color: #feb24c !important;
        }

        .nota-vermelho {
            background-color: #fc8d59 !important;
        }

        .nota-sem-nota {
            background-color: #f0f0f0 !important;
            color: #999;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            font-size: 9px;
            color: #666;
        }

        .assinaturas {
            margin-top: 25px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .assinatura {
            text-align: center;
            padding-top: 25px;
            border-top: 1px solid #333;
        }

        .assinatura .label {
            font-size: 10px;
            color: #666;
            margin-top: 6px;
        }

        .legenda {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .legenda h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .legenda-items {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .legenda-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
        }

        .legenda-cor {
            width: 20px;
            height: 20px;
            border-radius: 3px;
            display: inline-block;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }

            .assinaturas {
                page-break-before: avoid;
            }
        }

        .btn-print {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .btn-print:hover {
            background: #3d5fc4;
        }
    </style>
</head>

<body>
    <button class="btn-print no-print" onclick="window.print()">🖨️ Imprimir Ata</button>

    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <h1>ATA DE RESULTADOS FINAIS</h1>
            <div class="info">
                <?= APP_NAME ?? 'Sistema Escolar' ?>
            </div>
            <div class="info">
                Ano Letivo: <?= $data['turma']['ano_letivo'] ?? date('Y') ?>
            </div>
        </div>

        <!-- Informações da Turma -->
        <div class="info-turma">
            <div class="item">
                <strong>Turma:</strong> <?= $data['turma']['nome'] ?? 'N/A' ?>
            </div>
            <div class="item">
                <strong>Turno:</strong> <?= $data['turma']['turno'] ?? 'N/A' ?>
            </div>
            <div class="item">
                <strong>Ano Letivo:</strong> <?= $data['turma']['ano_letivo'] ?? date('Y') ?>
            </div>
            <div class="item">
                <strong>Total de Alunos:</strong> <?= count($data['estudantes'] ?? []) ?>
            </div>
            <div class="item">
                <strong>Data de Emissão:</strong> <?= date('d/m/Y') ?>
            </div>
        </div>

        <!-- Tabela de Notas -->
        <div class="notas-section">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2" class="estudantes-col">Estudantes</th>
                        <th colspan="<?= count($data['disciplinas'] ?? []) ?>" style="background: #52b788; color: white; font-size: 14px; padding: 12px;">
                            Componentes Curriculares
                        </th>
                    </tr>
                    <tr>
                        <?php if (isset($data['disciplinas']) && is_array($data['disciplinas'])): ?>
                            <?php foreach ($data['disciplinas'] as $disciplina): ?>
                                <th class="disciplina-col" title="<?= $disciplina->subject_name ?? 'N/A' ?>">
                                    <?= $disciplina->subject_name ?? 'N/A' ?>
                                </th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($data['estudantes']) && is_array($data['estudantes'])): ?>
                        <?php foreach ($data['estudantes'] as $indexAluno => $estudante): ?>
                            <tr>
                                <td class="nome-estudante"><?= $estudante['student']->name ?? 'N/A' ?></td>

                                <?php if (isset($data['disciplinas']) && is_array($data['disciplinas'])): ?>
                                    <?php foreach ($data['disciplinas'] as $disciplina): ?>
                                        <?php
                                        $notaFinal = 0;
                                        $notaService = $data['notaService'];

                                        if (isset($notaService) && isset($data['periodos'])) {
                                            $somaNotas = 0;
                                            $countPeriodos = 0;

                                            // Busca notas em cada período
                                            foreach ($data['periodos'] as $periodo) {
                                                $params = [
                                                    'student_class_id' => $estudante['code'],
                                                    'class_discipline_id' => $disciplina->code,
                                                    'period_id' => $periodo->id
                                                ];

                                                $busca_notas = $notaService->allSumScoresByParams($params);

                                                if (!empty($busca_notas) && isset($busca_notas[0]->media)) {
                                                    $somaNotas += (float)$busca_notas[0]->media;
                                                    $countPeriodos++;
                                                }
                                            }

                                            // Verifica se há recuperação final
                                            $params_rec_final = [
                                                'student_class_id' => $estudante['code'],
                                                'class_discipline_id' => $disciplina->code,
                                                'period_id' => 4,
                                                'ano_letivo' => $estudante['school_year']
                                            ];

                                            $busca_rec_final = $notaService->scoreFinalByStudentAndDisciplineAndPeriod($params_rec_final);

                                            if (!empty($busca_rec_final) && isset($busca_rec_final[0]->nota)) {
                                                $notaFinal = (float)$busca_rec_final[0]->nota;
                                            } else {
                                                $notaFinal = $countPeriodos > 0 ? $somaNotas / $countPeriodos : 0;
                                            }
                                        }

                                        // Define a classe de cor baseado na nota (igual à imagem)
                                        $classCor = 'nota-sem-nota';
                                        if ($notaFinal >= 9.0) {
                                            $classCor = 'nota-azul';
                                        } elseif ($notaFinal >= 8.0) {
                                            $classCor = 'nota-verde';
                                        } elseif ($notaFinal >= 7.0) {
                                            $classCor = 'nota-amarelo';
                                        } elseif ($notaFinal >= 5.0) {
                                            $classCor = 'nota-laranja';
                                        } elseif ($notaFinal > 0) {
                                            $classCor = 'nota-vermelho';
                                        }

                                        $notaExibir = $notaFinal > 0 ? number_format($notaFinal, 1) : '-';
                                        ?>
                                        <td class="nota-cell <?= $classCor ?>">
                                            <?= $notaExibir ?>
                                        </td>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="100" style="text-align: center; padding: 20px;">
                                Nenhum estudante encontrado
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Assinaturas -->
        <div class="assinaturas">
            <div class="assinatura">
                <div class="label">Diretor(a) / Coordenador(a)</div>
            </div>
            <div class="assinatura">
                <div class="label">Secretário(a) Escolar</div>
            </div>
        </div>

        <!-- Rodapé -->
        <div class="footer">
            <p>Este documento foi gerado eletronicamente pelo <?= APP_NAME ?? 'Sistema Escolar' ?></p>
            <p>Data de emissão: <?= date('d/m/Y H:i:s') ?></p>
        </div>
    </div>
</body>

</html>