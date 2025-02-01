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
                <a href="\meus-componentes" class="text-decoration-none">Meus Componentes</a>
            </li>
            <li class="breadcrumb-item">
                <i class="icon-archive lh-1"></i>
                <a href="/meus-componentes/<?=$turma->uuid?>/disciplina/<?=$turmas_disciplinas[0]->uuid?>/atividades" class="text-decoration-none">Atividades do Componente: <?=getJsonToObject($turmas_disciplinas[0]->professor_disciplina)->disciplina->nome?></a>
            </li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    
    <div class="col-2 col-xl-6">
        <div class="float-end">
        <? if (hasPermission('cadastrar atividades') || hasPermission('professor')) {?>
         <a href="/meus-componentes/<?=$turma->uuid?>/disciplina/<?=$turmas_disciplinas[0]->uuid?>/atividade" class="btn btn-outline-primary" > + </a>
        <? }?>
        </div>
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
    <!-- Row start -->

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
                                    <th class="text-center">Atividade</th>
                                    <th class="text-center <?=$total_maximo > 10 ? 'text-danger': 'text-success'?>">Pontuação Maxima: <?=$total_maximo?></th>
                                    <th>Situação</th>
                                    <? if (hasPermission('professor')) {?>
                                    <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <? foreach ($atividades as $atividade) { 
                                    ?>
                                    <tr>
                                        <td><?=$atividade->id?></td>
                                        <td class="text-center"> 
                                            <?=$atividade->tipo ?? 'não identificado'?>
                                        </td>
                                        <td class="text-center">
                                            <?=$atividade->valor ?? 'não identificado'?>
                                        </td>
                                        <td>    
                                            <div class="d-flex align-items-center">
                                                <? if($atividade->ativo == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if($atividade->ativo == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('professor')) {?>
                                        <td class="d-flex">
                                            <? if (hasPermission('professor')) {?>
                                            <a class="mb-1 me-2 mt-1" href="/meus-componentes/<?=$turma->uuid?>/disciplina/<?=$turmas_disciplinas[0]->uuid?>/atividade/<?=$atividade->uuid?>">
                                                <div class="border p-2 rounded-3">
                                                    <i class="icon-edit fs-5"></i>
                                                </div>
                                            </a> 
                                        <? }?> 
                                        <? if (hasPermission('professor')) {?>                                       
                                            <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$atividade->uuid?>">                                                     
                                                <div class="border p-2 rounded-3">
                                                    <span class="fs-5 text-danger icon-delete1"></span>
                                                </div>
                                            </button>
                                        <? }?>
                                        <div class="modal fade" id="exampleModal_<?=$atividade->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Tem certeza que deseja excluir este registro? 
                                                                <p>Atividade: <?=$atividade->tipo ?? 'não identificado'?></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="button" 
                                                                onclick="deleteData('/meus-componentes/<?=$turma->uuid?>/disciplina/<?=$turmas_disciplinas[0]->uuid?>/atividade/<?=$atividade->uuid?>')" 
                                                                class="btn btn-danger">Confirmar Exclusão</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>                                          
                                        </td>
                                    <? }?>
                                    </tr>
                                <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">                        
                        Total <b><?=count($atividades)?></b> registros
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

<?php require_once __DIR__ . '/../../../layout/bottom.php'; ?>
