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
                <a href="\meus-componentes" class="text-decoration-none">Meus Compoenentes</a>
            </li>
            <li class="breadcrumb-item">Componente: <?=getParamsToJson($turma_disciplina->professor_disciplina)->disciplina->nome?></li>
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
                                            <input type="date" id="data-frequencia" name="data" class="form-control" required value="<?= Date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">                    
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Bimestre</label>
                                            <select class="form-select" name="bimester_id" id="bimester_id">
                                                <?php foreach ($bimestres as $key => $value) {?>
                                                    <option value="<?=$value->id?>"><?=$value->bimestre?></option>
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
                                            <button type="button" id="buscar-estudantes" class="btn btn-primary w-100">Buscar</button>
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
                            $frequenciasMap['id'] = $frequencia->estudante_turma_id;
                        }

                        foreach ($estudantes as $key => $estudante) {             
                            $checked = $frequenciasMap['id'] == $estudante->id ? "checked" : "";
                        ?>
                            <div class="row mb-3">
                                <div class="col-7">
                                    <span class="fs-4"><?= getParamsToJson($estudante->estudante)->nome ?>
                                    ---
                                    <?= getParamsToJson($estudante->turma)->nome ?></span>
                                </div>
                                <div class="col-5">
                                    <div class="form-check form-switch form-check-reverse mr-2" style="padding-right: 5.5em;">
                                        <input 
                                            class="form-check-input fs-3" 
                                            type="checkbox" 
                                            role="switch" 
                                            id="presenca-<?= $estudante->id ?>" 
                                            name="class_student_id[]"
                                            value="<?= $estudante->id ?>"
                                            <?=$checked?>
                                        />
                                        <label class="form-check-label fs-3" for="presenca-<?= $estudante->id ?>">Não está Presente?</label>
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
