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
                <i class="icon-group lh-1"></i>
                <a href="\minha-galerinha" class="text-decoration-none">Minha Galerinha</a>
            </li>
            <li class="breadcrumb-item"><?=getJsonToObject($estudante->pessoa_fisica)->nome?></li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
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
            <div class="card-header">   
                <h5 class="card-title">Histórico de Turmas</h5>
            </div>
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th></th>
                                    <th>Turma</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell" >Coordenador</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Ano</th>
                                    <? if (hasPermission('responsavel_legal')) {?>
                                     <th>Ação</th>
                                     <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? 
                                foreach ($estudante_turma as $turma) { 
                                ?>
                                    <tr>
                                        <td><?=$turma->id?></td>
                                        <td class="fw-bold"> <?=getJsonToObject($turma->turma)->nome ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                        <?=getJsonToObject($turma->turma)->coordenador->nome ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">    
                                            <?=$turma->ano_letivo?>
                                        </td>
                                        <? if (hasPermission('responsavel_legal')) {?>
                                            <td class="d-flex">                                 
                                               <a class="mb-1 me-2 mt-1" href="/minha-galerinha/estudante/<?=$estudante->uuid?>/turma/<?=$turma->uuid?>/notas">
                                                   <div class="border p-2 rounded-3" data-toggle="tooltip" title="Notas">
                                                      <i class="icon-edit fs-5"></i>
                                                   </div>
                                               </a> 
                                               <a class="mb-1 me-2 mt-1" href="/minha-galerinha/estudante/<?=$estudante->uuid?>/turma/<?=$turma->uuid?>/frequencia">
                                                   <div class="border p-2 rounded-3" data-toggle="tooltip" title="Frequência">
                                                       <i class="icon-calendar fs-5"></i>
                                                   </div>
                                               </a> 
                                            </td>
                                        <? }?>
                                    </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($estudante_turma)?></b> registros
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>
