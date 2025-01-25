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
                <a href="\meus-componentes" class="text-decoration-none">Meus Componentes</a>
            </li>
            <li class="breadcrumb-item">Componente: <?=getJsonToObject($turma_disciplina->professor_disciplina)->disciplina->nome?></li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar turmas e estudantes')) {?>
        <div class="col-2 col-xl-6">
            <div class="float-end">
                <a class="btn btn-outline-primary"  data-bs-toggle="modal" data-bs-target="#linkClass"> + </a>
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
                    <form id="frequencia-form" action="/meus-componentes/<?=$turma_disciplina->uuid?>/frequencia" method="POST">
                        <div class="row mb-3">
                            <div class="col-md-4">                                           
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label for="data-frequencia" class="form-label">Selecione a Data</label>
                                            <input type="date" id="data-frequencia" name="data" class="form-control" required value="<?= $dataFilter ?>" max="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">                    
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Trimestre</label>
                                            <select class="form-select" name="period_id" id="bimester_id">
                                                <?php foreach ($periodos as $key => $value) {?>
                                                    <option value="<?=$value->id?>" <?= $bimestreFilter == $value->id ? 'selected' : ''?>><?=$value->periodo?>º</option>
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
                        // Transformar o array de frequências em um índice rápido por ID do estudante
                        $frequenciasMap = [];
                        foreach ($frequencias as $frequencia) {                            
                            $frequenciasMap[$frequencia->estudante_turma_id] = $frequencia->faltas;
                        }
                        foreach ($estudantes as $key => $estudante) {             
                          
                        ?>
                            <div class="row mb-3 me-0">
                                <div class="col-6">
                                    <span class="fw-2 mt-2"><?= getJsonToObject($estudante->estudante)->nome ?>
                                    -
                                    <?= getJsonToObject($estudante->turma)->nome ?></span>
                                </div>
                                <div class="col-6">
                                    <div class="form-check form-switch form-check-reverse mr-2 d-flex pe-0">
                                        <label class="form-check-label mt-2 me-2" style="width: 100px;" for="presenca-<?= $estudante->id ?>">Nº de faltas: </label>
                                        <select class="form-select" name="class_students_id[<?= $estudante->id ?>]" id="presenca-<?= $estudante->id ?>">
                                            <option value="0" <?= $frequenciasMap[$estudante->id] == 0 ? 'selected' : '' ?>>0</option>
                                            <option value="1" <?= $frequenciasMap[$estudante->id] == 1 ? 'selected' : '' ?>>1</option>
                                            <option value="2" <?= $frequenciasMap[$estudante->id] == 2 ? 'selected' : '' ?>>2</option>
                                        </select>                                        
                                    </div>
                                </div>
                            </div>     
                            <hr>                  
                        <?php } ?>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success">Salvar Frequência</button>
                        </div>
                    </form>
                </div>
            </div>  
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        <?=$links?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>

<script>
$(document).ready(function() {
    $('#search').click(function() {
        searchDate();       
    });

    $('#data-frequencia').change(function() {
        searchDate();       
    });

    const searchDate = function() {
         // Capturar valores do formulário
         var data = $('#data-frequencia').val();
        var bimester_id = $('#bimester_id').val();

        // Montar a URL
        var url = '/meus-componentes/<?= $turma_disciplina->uuid ?>/frequencia';
        url += '?data=' + data + '&bimester_id=' + bimester_id;

        // Redirecionar para a URL
        window.location.href = url;
    }
});

</script>
