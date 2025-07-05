
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?=APP_NAME?></title>
        <link rel="stylesheet" href="<?=URL_PREFIX_APP?>/Public/assets/css/main.min.css" />
        <style>
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }
                .container {
                    width: 100%;
                    max-width: 210mm;
                    margin: 0 auto;
                    padding: 20px;
                }
                table {
                    margin: 0 auto;
                }
                h1 {
                    text-align: center;
                }
            }
        </style>

    </head>
    <body class="bg-light">
    <div class="container my-5">
    <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="6" class="text-center fs-2">
                    <?= NAME_SCHOOL ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>End:</strong> <?= SCHOOL_ADDRESS ?></td>
                    <td colspan="4"><strong>CEP:</strong> <?= SCHOOL_ZIP_CODE ?></td>
                </tr>
                <tr>
                    <td><strong>Aluno: </strong><?= getJsonToObject($allStudentClass->estudante)->nome?></td>
                    <td colspan="4"><strong>Turma: </strong><?= getJsonToObject($allStudentClass->turma)->nome?></td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered" style="margin-bottom: 0px;">
            <thead>
                <tr>
                    <th colspan="6" class="text-center">
                    Boletim
                    </th>
                </tr>
            </thead>
        </table>

       
        <table class="table table-bordered table-striped">
            <thead class="table-primary text-center">
                <tr>
                    <th rowspan="3" class="align-middle">Disciplina</th>
                    <th colspan="6">periodos</th>
                    <th colspan="4" rowspan="2" class="align-middle">Total</th>
                </tr>
                <tr>
                <?php foreach($periodos as $periodo) { ?>
                    <th colspan="2"><?= $periodo->periodo?>º periodo</th>
                <?php } ?>
                </tr>
                <tr>
                <?php foreach($periodos as $periodo) { ?>
                    <th>Nota</th>
                    <th>Faltas</th>
                <?php } ?>
                    <th>T.N</th>
                    <th>M.F</th>
                    <th>R.F</th>
                    <th>T.F</th>
                </tr>
            </thead>
            <?php
                $notasMap = [];
                $frequenciaMap = [];

                // Organiza notas por período e por disciplina
                foreach ($notas as $nota) {
                    $turmaDisciplinaId = getJsonToObject($nota->turmas_details)->id;
                    $periodoKey = "{$nota->periodo_id}{$turmaDisciplinaId}";

                    // Soma por bimestre
                    if (!isset($notasMap[$periodoKey])) {
                        $notasMap[$periodoKey] = 0;
                    }
                    $notasMap[$periodoKey] += $nota->nota;

                    // Soma total da disciplina
                    if (!isset($notasMap[$turmaDisciplinaId])) {
                        $notasMap[$turmaDisciplinaId] = 0;
                    }
                    $notasMap[$turmaDisciplinaId] += $nota->nota;
                }

                // Organiza faltas por período e por disciplina
                foreach ($frequencias as $frequencia) {
                    $periodoKey = "{$frequencia->periodo_id}{$frequencia->turma_disciplina_id}";

                    // Soma por bimestre
                    if (!isset($frequenciaMap[$periodoKey])) {
                        $frequenciaMap[$periodoKey] = 0;
                    }
                    $frequenciaMap[$periodoKey] += $frequencia->faltas;

                    // Soma total da disciplina
                    if (!isset($frequenciaMap[$frequencia->turma_disciplina_id])) {
                        $frequenciaMap[$frequencia->turma_disciplina_id] = 0;
                    }
                    $frequenciaMap[$frequencia->turma_disciplina_id] += $frequencia->faltas;
                }
                ?>

                <tbody>
                <?php foreach (getJsonToObject($allDisciplines["resultado"])->disciplinas as $disciplina): ?>
                    <tr class="text-center">
                        <td class="text-start text-capitalize"><?= $disciplina->disciplina_nome; ?></td>

                        <?php foreach ($periodos as $periodo): 
                            $notaBimestre = $notasMap["{$periodo->periodo}{$disciplina->turma_disciplina_id}"] ?? 0;
                            $faltasBimestre = $frequenciaMap["{$periodo->periodo}{$disciplina->turma_disciplina_id}"] ?? 0;
                        ?>
                            <td><?= $notaBimestre ?></td>
                            <td><?= $faltasBimestre ?></td>
                        <?php endforeach; ?>

                        <?php
                            $totalNota = $notasMap[$disciplina->turma_disciplina_id] ?? 0;
                            $media = count($periodos) ? number_format($totalNota / count($periodos), 2) : '0.00';
                            $situacao = $totalNota && $media >= MIN_SCORE ? 'APR' : 'REP';
                            $totalFaltas = $frequenciaMap[$disciplina->turma_disciplina_id] ?? 0;
                        ?>
                        <td><?= $totalNota ?></td>
                        <td><?= $media ?></td>
                        <td><?= $situacao ?></td>
                        <td><?= $totalFaltas ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>

        </table>
        <p class="mt-4"><strong>Legenda:</strong>TOTAL: somatório anual, M.F: média
        final, R.F: resultado final, T.F: total de faltas, APR: aprovado, REP: reprovado, T.N: total notas.</p>
    </div>
</body>
</html>
<script>
window.onload = function() {
    window.print();
};
</script>
