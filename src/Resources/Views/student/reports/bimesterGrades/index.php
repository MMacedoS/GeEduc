
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
                    <th colspan="6">Bimestres</th>
                    <th colspan="4" rowspan="2" class="align-middle">Total</th>
                </tr>
                <tr>
                <?php foreach($bimestres as $bimestre) { ?>
                    <th colspan="2"><?= $bimestre->bimestre?>º Bimestre</th>
                <?php } ?>
                </tr>
                <tr>
                <?php foreach($bimestres as $bimestre) { ?>
                    <th>Nota</th>
                    <th>Faltas</th>
                <?php } ?>
                    <th>T.N</th>
                    <th>M.F</th>
                    <th>R.F</th>
                    <th>T.F</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $notasMap = []; 
                foreach($notas as $nota) {
                    $turma_disciplina_id = getJsonToObject($nota->turmas_details)->id;
                    if(isset($notasMap["$nota->bimestre_id$turma_disciplina_id"])) {
                        $notasMap["$nota->bimestre_id$turma_disciplina_id"] += $nota->nota;
                    } else {
                        $notasMap["$nota->bimestre_id$turma_disciplina_id"] = $nota->nota;
                    }
                    $notasMap[$turma_disciplina_id] += $nota->nota;
                }
                $frequenciaMap = []; 
                foreach($frequencias as $frequencia) {
                    
                    if(isset($frequenciaMap["$frequencia->bimestre_id$frequencia->turma_disciplina_id"])) {
                        $frequenciaMap["$frequencia->bimestre_id$frequencia->turma_disciplina_id"] += $frequencia->faltas;
                    } else {
                        $frequenciaMap["$frequencia->bimestre_id$frequencia->turma_disciplina_id"] = $frequencia->faltas;
                    }

                    $frequenciaMap[$frequencia->turma_disciplina_id] += $frequencia->faltas;
                }

               
                foreach(getJsonToObject($allDisciplines["resultado"])->disciplinas as $disciplina) {?>
                <tr class="text-center">
                    <td class="text-start text-capitalize"><?= $disciplina->disciplina_nome; ?></td>

                    <?php foreach($bimestres as $bimestre) { ?>
                    <td><?= $notasMap["$bimestre->bimestre$disciplina->turma_disciplina_id"] ?? "0" ?></td>
                    <td><?= $frequenciaMap["$bimestre->bimestre$disciplina->turma_disciplina_id"] ?? "0" ?></td>
                    <?php } ?>
                    <td><?= $notasMap[$disciplina->turma_disciplina_id] ?? "0" ?></td>
                    <td><?= isset($notasMap[$disciplina->turma_disciplina_id]) ? number_format($notasMap[$disciplina->turma_disciplina_id] / count($bimestres), 2) : "0" ?></td>
                    <td><?= isset($notasMap[$disciplina->turma_disciplina_id]) && number_format($notasMap[$disciplina->turma_disciplina_id] / count($bimestres) >= MIN_SCORE, 2) ? "APR" : "REP" ?></td>
                    <td><?= $frequenciaMap[$disciplina->turma_disciplina_id] ?? "0" ?></td>
                </tr>
                <?php } ?>
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
    window.close();
};
</script>
