<?php require_once __DIR__ . '/../../../layout/top.php'; ?>

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
                <a href="\minha-coordenacao" class="text-decoration-none">Minha Coordenação</a>
            </li>
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="/minha-coordenacao\turma\<?= getJsonToObject($turma_disciplina->turma)->uuid?>/disciplinas" class="text-decoration-none">Disciplinas Turma: <?= getJsonToObject($turma_disciplina->turma)->nome?></a>
            </li>
            <li class="breadcrumb-item">Componente: <?=getJsonToObject($turma_disciplina->professor_disciplina)->disciplina->nome?></li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar turmas e estudantes')) {?>
        <div class="col-2 col-xl-6">
            <div class="float-end">
                <a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#linkClass"> + </a>
            </div>
        </div>
    <? }?>

    <? if(isset($_GET['error'])){?>
        <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
            <b>Sem permissão!</b>.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <? }?>
</div>
    <!-- Row end -->
<? if(isset($success)){?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
      <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
<? if(isset($danger)){?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
       <b>Danger!</b>.
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>

<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="container mt-4">   
                    <form id="frequencia-form" action="/minha-coordenacao/turma/<?=$turma_disciplina->uuid?>/notas" method="POST">
                        <div class="row mb-3">

                            <div class="col-md-4">                    
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Trimestre</label>
                                            <select class="form-select" name="period_id" id="period_id">
                                                <?php foreach ($periodos as $key => $value) {?>
                                                    <option value="<?=$value->id?>" <?= $periodFilter == $value->id ? 'selected' : ''?>><?=$value->periodo?>º</option>
                                                <? } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>               
                            <div class="col-md-2 d-flex align-items-end">             
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <button type="button" id="search" class="btn btn-primary w-100">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>        
                        <hr>
                        <?php 
                        $notasMap = [];
                        $paralelaMap = [];
                        
                        foreach ($notas as $nota) {                            
                            $notasMap["$nota->estudante_turma_id$nota->atividade_id"] = $nota->nota;
                            if (isset($notasMap[$nota->estudante_turma_id])) {
                                $notasMap[$nota->estudante_turma_id] += $nota->nota ?? 0;
                            } else {
                                $notasMap[$nota->estudante_turma_id] = $nota->nota ?? 0;
                            }
                        }
    
                        foreach ($paralelas as $paralela) {        
                            if (isset($paralelaMap[$paralela->estudante_turma_id])) {
                                $paralelaMap[$paralela->estudante_turma_id] += $paralela->nota ?? 0;
                            } else {
                                $paralelaMap[$paralela->estudante_turma_id] = $paralela->nota ?? 0;
                            }                    
                        }
                    
                
                        foreach ($estudantes as $key => $estudante) {             
                          
                        ?>
                            <div class="row mb-3 me-0">
                                <div class="col-4">
                                    <span class="fw-2 mt-2"><?= getJsonToObject($estudante->estudante)->nome ?>
                                    -
                                    <?= getJsonToObject($estudante->turma)->nome ?></span>
                                </div>
                                <?php foreach($atividades as $atividade) { ?> 
                                <div class="col-1">
                                    <div class="mr-2 d-flex flex-column pe-0">
                                        <label class="form-check-label mt-2 me-2 text-capitalize" style="width: 100px;" for="notas[<?= "$estudante->id,$atividade->id"?>]"><?= getJsonToObject($atividade->activies_details)->tipo ?>: </label>
                                        <input 
                                            class="form-floating" 
                                            type="number" 
                                            name="notas[<?= "$estudante->id,$atividade->id"?>]" 
                                            min="0" 
                                            step="0.01" 
                                            max="<?= $atividade->valor ?>" 
                                            value="<?= $notasMap["$estudante->id$atividade->id"] ?? 0?>">                                   
                                    </div>
                                </div>
                                <?php } ?> 
                                <?php if (isset($notasMap[$estudante->id]) && $notasMap[$estudante->id] < 6.9) {?>
                                    <div class="col-1">
                                        <div class="mr-2 d-flex flex-column pe-0">
                                            <label class="form-check-label mt-2 me-2 text-capitalize" style="width: 100px;" for="paralela">Paralela: </label>
                                            <input type="number" min="0" step="0.01" max="2" 
                                            name="parallel[<?= $estudante->id ?>]" 
                                            value="<?= $paralelaMap[$estudante->id] ?>">                                   
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-1">
                                    <div class="mr-2 d-flex flex-column pe-0">
                                        <label class="form-check-label mt-2 me-2 text-capitalize" style="width: 100px;" for="total">Total: </label>
                                        <input type="number" min="0" step="0.01" max="10" 
                                        name="total[<?= $estudante->id ?>]" 
                                        value="<?= $notasMap[$estudante->id] +($paralelaMap[$estudante->id] ?? 0)?>" disabled>                                   
                                    </div>
                                </div>
                            </div>     
                            <hr>                  
                        <?php } ?>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success">Salvar notas</button>
                        </div>
                    </form>
                </div>
            </div>  
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../../../layout/bottom.php'; ?>

<script>
$(document).ready(function() {
    $('#search').click(function() {
        searchDate();       
    });

    $('#period_id').change(function() {
        searchDate();       
    });

    const searchDate = function() {
        // Capturar valores do formulário
        var period_id = $('#period_id').val();

        // Montar a URL
        var url = '/minha-coordenacao/turma/<?= $turma_disciplina->uuid ?>/notas';
        url += '?period_id=' + period_id;

        // Redirecionar para a URL
        window.location.href = url;
    }
});

</script>
