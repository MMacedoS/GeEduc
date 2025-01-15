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
            <li class="breadcrumb-item">Meus Componentes Curriculares</li>
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
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th class="text-center">Cod.</th>
                                    <th class="text-center">Componente Curricular</th>
                                    <th class="text-center">Ano Letivo</th>
                                    <? if (hasPermission('realizar chamadas') || hasPermission('inserir notas') || hasPermission('professor')) {?>
                                     <th>Ação</th>
                                     <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($disciplinas as $disciplina) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?=$disciplina->id?></td>
                                        <td class="fw-bold text-center"> 
                                            <?=getParamsToJson($disciplina->professor_disciplina)->disciplina->nome ?? 'não identificado'?>
                                            ---
                                            <?=getParamsToJson($disciplina->turma)->nome ?? 'não identificado'?>
                                        <td class="text-center"> <?=$disciplina->ano_letivo ?? 'não identificado'?>
                                        </td>
                                        <? if (hasPermission('realizar chamadas') || hasPermission('inserir notas') || hasPermission('professor')) {?>
                                            <td class="d-flex">                                                 
                                                <? if (hasPermission('inserir notas') || hasPermission('professor')) {?>                                     
                                                    <a class="mb-1 me-2 mt-1" href="/meus-componentes/<?=$disciplina->uuid?>/notas">
                                                        <div class="border p-2 rounded-3" data-toggle="tooltip" title="Notas">
                                                            <i class="icon-edit fs-5"></i>
                                                        </div>
                                                    </a> 
                                                <? } ?>
                                                <? if (hasPermission('realizar chamadas') || hasPermission('professor')) {?>                                     
                                                    <a class="mb-1 me-2 mt-1" href="/meus-componentes/<?=$disciplina->uuid?>/frequencia">
                                                        <div class="border p-2 rounded-3" data-toggle="tooltip" title="Frequência">
                                                            <i class="icon-calendar fs-5"></i>
                                                        </div>
                                                    </a> 
                                                <? } ?>                                                                                
                                            </td>
                                        <? }?>
                                    </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($disciplinas)?></b> registros
                    </div>
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
