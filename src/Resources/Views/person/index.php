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
            <li class="breadcrumb-item">Pessoas Contatos</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar pessoa')) {?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
            <a href="\pessoa" class="btn btn-outline-primary" > + </a>
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
        <form id="person-form" action="/pessoas" method="GET">            
            <div class="accordion mt-2" id="accordionSpecialTitle">
                <div class="accordion-item bg-transparent">
                    <h2 class="accordion-header" id="headingSpecialTitleTwo">
                    <button class=" bg-transparent accordion-button <?= isset($situation) || isset($name_email) ? '' : 'collapsed'?>" type="button" data-bs-toggle="collapse"
                       data-bs-target="#filters-person" aria-expanded="false"
                       aria-controls="collapseSpecialTitleTwo">
                      <h5 class="m-0">Filtros</h5>
                    </button>
                    </h2>
                    <div id="filters-person" class="accordion-collapse <?= isset($situation) || isset($name_email) ? '' : 'collapse'?>"
                       aria-labelledby="headingSpecialTitleTwo" data-bs-parent="#accordionSpecialTitle">
                      <div class="accordion-body">
                        <div class="row justify-content-start">
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="m-0">
                                            <label class="form-label">Busca por nome ou email</label>
                                            <input 
                                                class="form-input form-control" 
                                                type="text" 
                                                name="name_email" 
                                                id="name_email" 
                                                value="<?= isset($name_email) ? $name_email : null ?>" 
                                                placeholder="Digite nome ou email">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5 mb-2">
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
                                            <a href="\estudantes" class="btn btn-secondary <?= isset($situation) || isset($name_email) ? 'd-block' : 'd-none'?>">Limpar</a>
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
                                    <th>email</th>
                                    <th>Situação</th>
                                    <? if (hasPermission('editar pessoa contato') || hasPermission('deletar pessoa')) {?>
                                     <th>Ação</th>
                                     <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? 
                            foreach ($pessoas as $pessoa) { 
                                ?>
                                    <tr>
                                        <td><?=$pessoa->id?></td>
                                        <td class="fw-bold"> <?=getJsonToObject($pessoa->pessoa_fisica)->nome ?? 'não identificado'?>
                                        </td>
                                        <td>
                                        <?=getJsonToObject($pessoa->pessoa_fisica)->email ?? 'não identificado'?>
                                        </td>
                                        <td>    
                                            <div class="d-flex align-items-center">
                                                <? if($pessoa->ativo == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if($pessoa->ativo == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('editar pessoa') || hasPermission('deletar pessoa')) {?>
                                            <td class="d-flex">
                                                 <? if (hasPermission('editar pessoa')) {?>                                     
                                                    <a class="mb-1 me-2 mt-1" href="/pessoa/<?=$pessoa->uuid?>">
                                                        <div class="border p-2 rounded-3">
                                                            <i class="icon-edit fs-5"></i>
                                                        </div>
                                                    </a> 
                                                <? } ?>  
                                                <? if (hasPermission('deletar pessoa')) {?>                                                                           
                                                    <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?=$pessoa->uuid?>">                                                     
                                                        <div class="border p-2 rounded-3">
                                                            <span class="fs-5 text-danger icon-delete1"></span>
                                                        </div>
                                                    </button>
                                                <? }?>
                                                <div class="modal fade" id="exampleModal_<?=$pessoa->uuid?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Tem certeza que deseja excluir este registro? 
                                                                <p>pessoa: <?=getJsonToObject($pessoa->pessoa_fisica)->nome ?? 'não identificado'?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="button" onclick="deleteData('/pessoa/<?=$pessoa->uuid?>')" class="btn btn-danger">Confirmar Exclusão</button>
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
                        Total <b><?=count($pessoas)?></b> registros
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

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
