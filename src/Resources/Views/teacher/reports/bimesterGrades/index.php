<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= URL_PREFIX_APP ?>/Public/assets/css/main.min.css">
    <style>
        body {
            background-color: #fff;
            font-size: 11px;
        }
        .table th, .table td {
            padding: 5px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }

        .bg-e {
            background-color: #3cb371 !important;
        }

        .bg-row {
            background-color:rgb(181, 230, 202) !important;
        }

        .bgb-1 {
            background-color: #dcdcdc !important;
        }

        .bgb-2 {
            background-color:rgb(134, 127, 127) !important;
        }

        .bgb-3 {
            background-color: #dcdcdc !important;
        }

        .bgb-4 {
            background-color:rgb(179, 159, 159) !important;
        }

        @media print {
            @page {
                size: A4 landscape !important;
                margin: 5mm;
            }

            body {
                margin: 0;
                padding: 0;
                color: #000;
                width: 100% !important;
            }

            .container {
                width: 100%;
                margin: 0 auto;
                width: 100% !important;
            }

            .table th, .table td {
                border: 1px solid #000 !important;
            }

            td {
                border: 1px solid #000 !important;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container" style="width: 100% !important; max-width: 100%">

    <table class="table table-bordered small mb-4">
        <thead>
            <tr><th colspan="100%" class="text-center fs-5"><?= NAME_SCHOOL ?></th></tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Endereço:</strong> <?= SCHOOL_ADDRESS ?></td>
                <td colspan="3"><strong>CEP:</strong> <?= SCHOOL_ZIP_CODE ?></td>
            </tr>
            <tr>
                <td><strong>Professor:</strong> <?= getJsonToObject($turma_disciplina->professor_disciplina)->professor->nome ?></td>
                <td colspan="3"><strong>Turma:</strong> <?= getJsonToObject($turma_disciplina->turma)->nome ?></td>
            </tr>
        </tbody>
    </table>

    <?php
    // Mapeia notas e faltas
    $frequenciasMap = [];
    $notasMap = [];

    foreach ($frequencias as $frequencia) {
        $frequenciasMap["$frequencia->estudante_turma_id$frequencia->periodo_id"] = $frequencia->faltas;
    }

    foreach ($notas as $nota) {
        $notasMap["$nota->estudante_turma_id$nota->atividade_id$nota->periodo_id"] = $nota->nota;
        $chavePeriodo = "$nota->estudante_turma_id$nota->periodo_id";
        $notasMap[$chavePeriodo] = ($notasMap[$chavePeriodo] ?? 0) + ($nota->nota ?? 0);
    }

    // Cabeçalho com colunas dinâmicas para cada bimestre
    ?>
    <table class="table table-bordered small table-striped">
        <thead class="table-primary text-center">
            <tr>
                <th rowspan="2" class="bg-e">Estudante</th>
                <?php foreach ($periodos as $periodo): ?>
                    <th colspan="<?= count($atividades) + 1 ?>" class="bgb-<?= $periodo->periodo ?>">
                        <?= $periodo->periodo ?>º Bimestre
                    </th>
                <?php endforeach; ?>
                <th colspan="2">Resultado</th>
            </tr>
            <tr>
                <?php foreach ($periodos as $periodo): ?>
                    <?php foreach ($atividades as $atividade): ?>
                        <th class="bgb-<?= $periodo->periodo ?>"><?= getJsonToObject($atividade->activies_details)->tipo ?></th>
                    <?php endforeach; ?>
                    <th>Soma</th>
                <?php endforeach; ?>
                    <th>Faltas</th>
                    <th>Situação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($estudantes as $estudante): ?>
                <tr>
                    <td class="bg-row"><?= getJsonToObject($estudante->estudante)->nome ?></td>
                    <?php foreach ($periodos as $periodo): ?>
                        <?php foreach ($atividades as $atividade): ?>
                            <td class="text-center">
                                <?= $notasMap["$estudante->id$atividade->id$periodo->id"] ?? '-' ?>
                            </td>
                        <?php endforeach; ?>
                        <?php
                            $total = $notasMap["$estudante->id$periodo->id"] ?? 0;
                            $faltas = $frequenciasMap["$estudante->id$periodo->id"] ?? 0;
                            $situacao = $total >= MIN_SCORE ? "Aprovado" : "Reprovado";
                        ?>
                        <td class="text-center"><?= $total ?></td>
                    <?php endforeach; ?>
                    <td class="text-center"><?= $faltas ?></td>
                    <td class="text-center"><?= $total > 0 ? $situacao : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="mt-3">
        <strong>Legenda:</strong> Total = Soma das notas, Faltas = Total por bimestre, Situação = Aprovado ou Reprovado com base na média.
    </p>
</div>

<script>
window.onload = function () {
    window.print();
    // window.close();
};
</script>
</body>
</html>
