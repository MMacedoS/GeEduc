<?php require_once __DIR__ . '/../layout/top.php'; ?>

<!-- Row start -->
<div class="row gx-3">
    <div class="col-8 col-xl-6">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">Estudantes</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar_estudante')) {?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
            <a href="\estudante" class="btn btn-outline-primary" > + </a>
            </div>
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
    <!-- Row start -->
<div class="row gx-3">
    <div class="col-12">
        <form id="students-form" action="/estudantes" method="GET">            
            <div class="accordion mt-2" id="accordionSpecialTitle">
                <div class="accordion-item bg-transparent">
                    <h2 class="accordion-header" id="headingSpecialTitleTwo">
                    <button class=" bg-transparent accordion-button <?= isset($situation) || isset($searchFilter) ? '' : 'collapsed'?>" type="button" data-bs-toggle="collapse"
                       data-bs-target="#filters-students" aria-expanded="false"
                       aria-controls="collapseSpecialTitleTwo">
                      <h5 class="m-0">Filtros</h5>
                    </button>
                    </h2>
                    <div id="filters-students" class="accordion-collapse <?= isset($situation) || isset($searchFilter) ? '' : 'collapse'?>"
                       aria-labelledby="headingSpecialTitleTwo" data-bs-parent="#accordionSpecialTitle">
                      <div class="accordion-body">
                        <div class="row justify-content-start">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Busca por nome ou email</label>
                                            <input 
                                                class="form-input form-control" 
                                                type="text" 
                                                name="name_email" 
                                                id="name_email" 
                                                value="<?= isset($searchFilter) ? $searchFilter : null ?>" 
                                                placeholder="Digite nome ou email">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Situação</label>
                                            <select class="form-select form-control" name="situation" id="situation">
                                                <option <?= (isset($situation) && $situation == '') ? 'selected' : ''?> value="">Ambas</option>
                                                <option value="1" <?= (isset($situation) && $situation == 1) ? 'selected' : ''?>>Disponível</option>
                                                <option value="0" <?= (isset($situation) && $situation == 0) ? 'selected' : ''?>>Impedido</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-12">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                                            <a href="\estudantes" class="btn btn-secondary <?= isset($situation) || isset($searchFilter) ? 'd-block' : 'd-none'?>">Limpar</a>
                                            <button type="submit" class="btn btn-primary">Buscar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th></th>
                                    <th>Nome</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">email</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Situação</th>
                                    <? if (hasPermission('editar_estudante') || hasPermission('deletar_professor')) {?>
                                     <th>Ação</th>
                                     <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($data['estudantes'] as $estudante) { 
                                ?>
                                    <tr>
                                        <td><?=$estudante->id?></td>
                                        <td class="fw-bold"> <?=getJsonToObject($estudante->pessoa_fisica)->nome ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                        <?=getJsonToObject($estudante->pessoa_fisica)->email ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">    
                                            <div class="d-flex align-items-center">
                                                <? if($estudante->ativo == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if($estudante->ativo == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('editar_estudante') || hasPermission('deletar_estudante') || hasPermission('visualizar_turmas_estudantes')) {?>
                                            <td>
                                                 <div class="d-none d-xl-flex d-lg-flex d-md-flex">
                                                    <? if (hasPermission('editar_estudante')) {?>                                     
                                                        <a class="mb-1 me-2 mt-1" href="/estudante/<?=$estudante->uuid?>">
                                                            <div class="border p-2 rounded-3">
                                                                <i class="icon-edit fs-5"></i>
                                                            </div>
                                                        </a> 
                                                    <? } ?>

                                                    <? if (hasPermission('visualizar_turmas_estudantes')) {?>                                     
                                                        <a class="mb-1 me-2 mt-1" href="/estudantes/<?=$estudante->uuid?>/turma">
                                                            <div class="border p-2 rounded-3">
                                                                <i class="icon-link fs-5"></i>
                                                            </div>
                                                        </a> 
                                                    <? } ?>  
                                                    <? if (hasPermission('visualizar_mensalidades')) {?>                                     
                                                        <a class="mb-1 me-2 mt-1" href="/estudantes/<?=$estudante->uuid?>/mensalidades">
                                                            <div class="border p-2 rounded-3">
                                                                <i class="icon-dollar-sign fs-5"></i>
                                                            </div>
                                                        </a> 
                                                    <? } ?>  
    
                                                    <? if (hasPermission('deletar_estudante')) {?>                                                                           
                                                        <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$estudante->uuid?>">                                                     
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
                                                        <div class="header-action-links">
                                                            <? if (hasPermission('editar_estudante')) {?>                                     
                                                                <a class="mb-1 me-2 mt-1" href="/estudante/<?=$estudante->uuid?>">
                                                                    <div class="border p-2 rounded-3">
                                                                        <i class="icon-edit fs-5"></i>
                                                                    </div>
                                                                </a> 
                                                            <? } ?>

                                                            <? if (hasPermission('visualizar_turmas_estudantes')) {?>                                     
                                                                <a class="mb-1 me-2 mt-1" href="/estudantes/<?=$estudante->uuid?>/turma">
                                                                    <div class="border p-2 rounded-3">
                                                                        <i class="icon-link fs-5"></i>
                                                                    </div>
                                                                </a> 
                                                            <? } ?>  
                                                            <? if (hasPermission('visualizar_mensalidades')) {?>                                     
                                                                <a class="mb-1 me-2 mt-1" href="/estudantes/<?=$estudante->uuid?>/mensalidades">
                                                                    <div class="border p-2 rounded-3">
                                                                        <i class="icon-dollar-sign fs-5"></i>
                                                                    </div>
                                                                </a> 
                                                            <? } ?>  
                                                            <? if (hasPermission('deletar_estudante')) {?>                                                                           
                                                                <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$estudante->uuid?>">                                                     
                                                                    <div class="border p-2 rounded-3">
                                                                        <span class="fs-5 text-danger icon-delete1"></span>
                                                                    </div>
                                                                </button>
                                                            <? }?>
                                                        </div>
                                                    </div>
                                                 </div>
                                                <div class="modal fade" id="exampleModal_<?=$estudante->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Tem certeza que deseja excluir este registro? 
                                                                <p>Estudante: <?=getJsonToObject($estudante->pessoa_fisica)->nome ?? 'não identificado'?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="button" onclick="deleteData('/estudante/<?=$estudante->uuid?>')" class="btn btn-danger">Confirmar Exclusão</button>
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
                        Total <b><?=count($data['estudantes'])?></b> registros
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

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
