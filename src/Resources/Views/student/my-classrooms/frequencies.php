<?php require_once __DIR__ . '/../../layout/top.php'; ?>

<!-- Row start -->
<div class="row gx-3">
    <div class="col-8 col-xl-6">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\minhas-turmas" class="text-decoration-none">Minhas Turmas</a>
            </li>
            <li class="breadcrumb-item">Frequência</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
</div>
<!-- Row end -->

<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-center">Data Falta</th>
                                    <th class="text-center">Componentes Curricular</th>
                                    <th class="text-center">Bimestre</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($frequencias as $frequencia) { 
                                ?>
                                    <tr>
                                        <td><?=$frequencias->id?></td>
                                        <td class="fw-bold text-center"> 
                                            <?= brDate($frequencia->data) ?? 'não identificado'?>
                                        </td>
                                        <td class="text-center">
                                            <?=getJsonToObject($frequencia->turma_disciplina_details)->professor_disciplina->disciplina->nome ?? 'não identificado'?>
                                            --
                                            <?=getJsonToObject($frequencia->turma_disciplina_details)->professor_disciplina->professor->nome ?? 'não identificado'?>
                                        </td>
                                        <td class="text-center">
                                            <?= getJsonToObject($frequencia->turma_disciplina_details)->bimestres->bimestre ?? 'não identificado'?>
                                        </td>
                                    </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($frequencias)?></b> registros
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        <?=$data['links']?>
    </div>
</div>

<div class="col-xl-6">
    <div class="card mb-3">
       <div class="card-header">
            <h5 class="card-title">Gráficos de Faltas</h5>
        </div>
        <div class="card-body">
           <div id="donutFormatter" class="chart-height-xl"></div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>

<script>
    Morris.Donut({
        element: "donutFormatter",
        data: [
            {
                value: "<?php echo $total_faltas; ?>",
                label: "Faltas",
                formatted: "<?php echo $percentual_faltas; ?>%"
            },
            {
                value: "<?php echo $presenca; ?>",
                label: "Presença",
                formatted: "<?php echo $percentual_presenca; ?>%"
            }
        ],
        resize: true,
        hideHover: "auto",
        formatter: function (x, data) {
            return data.formatted;
        },
        labelColor: "#507D0C",
        colors: ["#e94235", "#34a853"]
    });
</script>
