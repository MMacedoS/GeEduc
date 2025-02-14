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
            <li class="breadcrumb-item">Turmas</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    
    <div class="col-2 col-xl-6">
        <div class="float-end">
        <? if (hasPermission('cadastrar turmas')) {?>
         <a href="\turma" class="btn btn-outline-primary" > + </a>
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
        <form id="classrooms-form" action="/turmas" method="GET">            
            <div class="accordion mt-2" id="accordionSpecialTitle">
                <div class="accordion-item bg-transparent">
                    <h2 class="accordion-header" id="headingSpecialTitleTwo">
                        <button 
                            class="bg-transparent accordion-button <?= isset($situation) || isset($searchFilter) ? '' : 'collapsed'?>" 
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#filters-classrooms" 
                            aria-expanded="false"
                            aria-controls="collapseSpecialTitleTwo">
                            <h5 class="m-0">Filtros</h5>
                        </button>
                    </h2>
                    <div id="filters-classrooms" 
                        class="accordion-collapse <?= isset($situation) || isset($searchFilter) ? '' : 'collapse'?>"
                        aria-labelledby="headingSpecialTitleTwo" 
                        data-bs-parent="#accordionSpecialTitle">
                      <div class="accordion-body">
                        <div class="row justify-content-start">
                            <div class="col-sm-6 col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Busca por nome</label>
                                            <input 
                                                class="form-input form-control" 
                                                type="text" 
                                                name="classroom" 
                                                id="classroom" 
                                                value="<?= isset($searchFilter) ? $searchFilter : null ?>" 
                                                placeholder="Digite o nome da turma">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-2 mb-2">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Turno</label>
                                            <select class="form-select form-control" name="shift" id="shift">
                                                <option <?= (isset($shift) && $shift == '') ? 'selected' : ''?> value="">Ambos</option>
                                                <option value="matutino" <?= (isset($shift) && $shift == "matutino") ? 'selected' : ''?>>Matutino</option>
                                                <option value="vespertino" <?= (isset($shift) && $shift == "vespertino") ? 'selected' : ''?>>Vespertino</option>
                                                <option value="noturno" <?= (isset($shift) && $shift == "noturno") ? 'selected' : ''?>>Noturno</option>
                                                <option value="integral" <?= (isset($shift) && $shift == "integral") ? 'selected' : ''?>>Integral</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Busca por coordenador</label>
                                            <input 
                                                class="form-input form-control" 
                                                type="text" 
                                                name="coordinator" 
                                                id="coordinator" 
                                                value="<?= isset($coordinator) ? $coordinator : null ?>" 
                                                placeholder="Digite o nome do coordenador">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-2 mb-2">
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
                                            <a href="\turmas" class="btn btn-secondary <?= isset($situation) || isset($searchFilter) || isset($shift) || isset($coordinator) ? 'd-block' : 'd-none'?>">Limpar</a>
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
                                    <th>#</th>
                                    <th>Turma</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Turno</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Coordenador</th>
                                    <th>Situação</th>
                                    <? if (hasPermission('editar turmas') || hasPermission('deletar turmas')) {?>
                                    <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($data['turmas'] as $turma) { 
                                ?>
                                    <tr>
                                        <td><?=$turma->id?></td>
                                        <td class="fw-bold"> <?=$turma->nome ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell text-capitalize">
                                        <?=$turma->turno ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <?=getCustomers(getJsonToObject($turma->detalhes)->coordenadores)?>
                                        </td>
                                        <td>    
                                            <div class="d-flex align-items-center">
                                                <? if($turma->ativo == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if($turma->ativo == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('editar turmas') || hasPermission('deletar turmas')) {?>
                                        <td>
                                            <div class="d-none d-xl-flex d-lg-flex d-md-flex">
                                                <? if (hasPermission('editar turmas')) {?>
                                                    <a class="mb-1 me-2 mt-1" href="/turma/<?=$turma->uuid?>">
                                                        <div class="border p-2 rounded-3">
                                                            <i class="icon-edit fs-5"></i>
                                                        </div>
                                                    </a> 
                                                <? }?> 
                                                <? if (hasPermission('visualizar turmas estudantes')) {?>                                     
                                                    <a class="mb-1 me-2 mt-1" href="/turmas/<?=$turma->uuid?>/disciplinas">
                                                        <div class="border p-2 rounded-3">
                                                        <i class="icon-link fs-5"></i>
                                                        </div>
                                                    </a> 
                                                <? } ?>  
                                                <? if (hasPermission('deletar turmas')) {?>                                       
                                                    <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$turma->uuid?>">                                                     
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
                                                <div class="dropdown-menu">
                                                    <div class="header-action-links float-end">
                                                        <? if (hasPermission('editar turmas')) {?>
                                                            <a class="mb-1 me-2 mt-1" href="/turma/<?=$turma->uuid?>">
                                                                <div class="border p-2 rounded-3">
                                                                    <i class="icon-edit fs-5"></i>
                                                                </div>
                                                            </a> 
                                                        <? }?> 
                                                        <? if (hasPermission('visualizar turmas estudantes')) {?>                                     
                                                            <a class="mb-1 me-2 mt-1" href="/turmas/<?=$turma->uuid?>/disciplinas">
                                                                <div class="border p-2 rounded-3">
                                                                <i class="icon-link fs-5"></i>
                                                                </div>
                                                            </a> 
                                                        <? } ?>  
                                                        <? if (hasPermission('deletar turmas')) {?>                                       
                                                            <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$turma->uuid?>">                                                     
                                                                <div class="border p-2 rounded-3">
                                                                    <span class="fs-5 text-danger icon-delete1"></span>
                                                                </div>
                                                            </button>
                                                        <? }?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="exampleModal_<?=$turma->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Tem certeza que deseja excluir este registro? 
                                                               <p>Turma <?=$turma->nome ?? 'não identificado'?></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="button" onclick="deleteData('/turma/<?=$turma->uuid?>')" class="btn btn-danger">Confirmar Exclusão</button>
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
                        Total <b><?=count($data['turmas'])?></b> registros
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
