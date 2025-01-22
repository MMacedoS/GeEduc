
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
        <h1 class="text-center mb-4">Grade de Notas Bimestrais</h1>
        <div class="mb-3">
            <strong>Professor:</strong> <?= getJsonToObject($turma_disciplina->professor_disciplina)->professor->nome ?><br>
            <strong>Turma:</strong> <?= getJsonToObject($turma_disciplina->turma)->nome ?><br>
            <strong>Disciplina:</strong> <?= getJsonToObject($turma_disciplina->professor_disciplina)->disciplina->nome ?><br>
            <strong>Carga horária:</strong> <?= getJsonToObject($turma_disciplina->carga_horaria)->carga_horaria ?> horas
        </div>
        
        <?php 
        $frequenciasMap = [];
        $notasMap = [];

        foreach ($frequencias as $frequencia) {                            
            $frequenciasMap["$frequencia->estudante_turma_id$frequencia->bimestre_id"] = $frequencia->faltas;
        }
        
        foreach ($notas as $nota) {                            
            $notasMap["$nota->estudante_turma_id$nota->atividade_id$nota->bimestre_id"] = $nota->nota;
            $notasMap["$nota->estudante_turma_id$nota->bimestre_id"] += $nota->nota;
        }
        
        foreach($bimestres as $bimestre) {?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary text-center">
                <tr>
                    <th rowspan="2" class="align-middle">Alunos matriculados</th>
                    <th colspan="<?= count($atividades) + 3?>"><?= $bimestre->bimestre; ?>º Bimestre</th>
                </tr>
                <tr>
                    <?php foreach($atividades as $atividade) {?>
                    <th class="text-capitalize"><?= getJsonToObject($atividade->activies_details)->tipo; ?></th>
                    <?php };?>
                    <th>Total</th>
                    <th>Faltas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                foreach ($estudantes as $key => $estudante) { 
                    ?>
                    <tr>
                        <td><?= getJsonToObject($estudante->estudante)->nome ?></td>
                    <?php            
                    foreach($atividades as $atividade) {
                    ?>
                        <td><?= $notasMap["$estudante->id$atividade->id$bimestre->id"] ?? "-" ?></td>
                    <?php
                    }
                    ?>
                    <td><?= $notasMap["$estudante->id$bimestre->id"] ?? "0"?></td>
                    <td><?= $frequenciasMap["$estudante->id$bimestre->id"] ?? "0" ?></td>
                    <td><?= $notasMap["$estudante->id$bimestre->id"] >= MIN_SCORE ? "Aprovado" : "Reprovado" ?></td>
                    </tr>
                <?php
                }
            
                ?>
            </tbody>
        </table>
        <?php };?>
        <p class="mt-4"><strong>Legenda:</strong> Total = Soma das notas de todas as atividades, Status = Reprovado - Aprovado, Faltas = Total de Faltas.</p>
    </div>
</body>
</html>
<script>
window.onload = function() {
    window.print();
    window.close();
};

</script>
