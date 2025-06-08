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
                    <i class="icon-archive lh-1"></i>
                    <a href="/turmas" class="text-decoration-none">Turmas</a>
            </li>
            <li class="breadcrumb-item">
                    <i class="icon-archive lh-1"></i>
                    <a href="/turmas/<?=$turma->uuid?>/disciplinas" class="text-decoration-none">Turma: <?=$turma->nome?></a>
            </li>
            <li class="breadcrumb-item">
                    <i class="icon-archive lh-1"></i>
                    <a href="/turmas/<?=$turma->uuid?>/disciplinas/<?=$turmas_disciplinas[0]->uuid?>/aulas" class="text-decoration-none">Componente: <?=getJsonToObject($turmas_disciplinas[0]->professor_disciplina)->disciplina->nome?></a>
            </li>
            <li class="breadcrumb-item">Aulas</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    
    <div class="col-2 col-xl-6">
        <div class="float-end">
        <? if (hasPermission('cadastrar aulas')) {?>
         <a href="/turmas/<?=$turma->uuid?>/disciplinas/<?=$turmas_disciplinas[0]->uuid?>/aula" class="btn btn-outline-primary" > + </a>
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
                                    <th>Dia</th>
                                    <th>Horario</th>
                                    <th>Turno</th>
                                    <? if (hasPermission('editar aulas') || hasPermission('deletar aulas')) {?>
                                    <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                                <? foreach ($aulas as $aula) { 
                                    // dd($aula);
                                    ?>
                                    <tr>
                                        <td><?=$aula->id?></td>
                                        <td> 
                                            <?=getJsonToObject($aula->dia)->nome ?? 'não identificado'?>
                                        </td>
                                        <td>
                                            <?=getJsonToObject($aula->dia)->horario ?? 'não identificado'?>ª
                                        </td>
                                        <td>
                                            <?=getJsonToObject($aula->dia)->turno ?? 'não identificado'?>
                                        </td>
                                        <? if (hasPermission('editar aulas') || hasPermission('deletar aulas')) {?>
                                        <td>
                                            <div class="d-none d-xl-flex d-lg-flex d-md-flex">
                                                
                                                <? if (hasPermission('deletar aulas')) {?>                                       
                                                    <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$aula->uuid?>">                                                     
                                                        <div class="border p-2 rounded-3">
                                                            <span class="fs-5 text-danger icon-delete1"></span>
                                                        </div>
                                                    </button>
                                                <? }?>                                           
                                            </div>
                                            <div class="d-block d-xl-none d-lg-none d-md-none dropdown ms-3">
                                                <a class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none"
                                                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="icon-menu"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <div class="header-action-links float-end">                                                        
                                                        <? if (hasPermission('deletar aulas')) {?>                                       
                                                            <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$aula->uuid?>">                                                     
                                                                <div class="border p-2 rounded-3">
                                                                    <span class="fs-5 text-danger icon-delete1"></span>
                                                                </div>
                                                            </button>
                                                        <? }?>   
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="exampleModal_<?=$aula->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Tem certeza que deseja excluir este registro? 
                                                                <p>Aula na(o): <?=getJsonToObject($aula->dia)->nome ?? 'não identificado'?></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="button" 
                                                                onclick="deleteData('/turmas/<?=$turma->uuid?>/disciplinas/<?=$turmas_disciplinas[0]->uuid?>/aula/<?=$aula->uuid?>')" 
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
                        Total <b><?=count($aulas)?></b> registros
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
