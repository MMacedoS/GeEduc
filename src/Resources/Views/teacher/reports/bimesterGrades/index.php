
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
                    <td colspan="4"><strong>Cep:</strong> <?= SCHOOL_ZIP_CODE ?></td>
                </tr>
                <tr>
                    <td><strong>Professor:</strong> <?= getJsonToObject($turma_disciplina->professor_disciplina)->professor->nome ?></td>
                    <td colspan="4"><strong>Turma:</strong> <?= getJsonToObject($turma_disciplina->turma)->nome ?></td>
                </tr>
            </tbody>
        </table>
        <table class="table table-bordered" style="margin-bottom: 0px;">
            <thead>
                <tr>
                    <th colspan="6" class="text-center">
                    Grades de Notas Bimestrais
                    </th>
                </tr>
            </thead>
        </table>
        <?php 
        $frequenciasMap = [];
        $notasMap = [];

        foreach ($frequencias as $frequencia) {                            
            $frequenciasMap["$frequencia->estudante_turma_id$frequencia->periodo_id"] = $frequencia->faltas;
        }
        
        foreach ($notas as $nota) {                            
            $notasMap["$nota->estudante_turma_id$nota->atividade_id$nota->periodo_id"] = $nota->nota;
            if (isset($notasMap["$nota->estudante_turma_id$nota->periodo_id"])) {
                $notasMap["$nota->estudante_turma_id$nota->periodo_id"] += $nota->nota ?? 0;
            } else {
                $notasMap["$nota->estudante_turma_id$nota->periodo_id"] = $nota->nota ?? 0;
            }
        }

        foreach($periodos as $periodo) {?>
        <table class="table table-bordered table-striped">
            <thead class="table-primary text-center">
                <tr>
                    <th rowspan="2" class="align-middle">Alunos matriculados</th>
                    <th colspan="<?= count($atividades) + 3?>"><?= $periodo->periodo; ?>º Período</th>
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
                        <td><?= $notasMap["$estudante->id$atividade->id$periodo->id"] ?? "-" ?></td>
                    <?php
                    }
                    ?>
                    <td><?= $notasMap["$estudante->id$periodo->id"] ?? "0"?></td>
                    <td><?= $frequenciasMap["$estudante->id$periodo->id"] ?? "0" ?></td>
                    <td><?= isset($notasMap["$estudante->id$periodo->id"]) ? ($notasMap["$estudante->id$periodo->id"] >= MIN_SCORE ? "Aprovado" : "Reprovado") : '-' ?></td>
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
