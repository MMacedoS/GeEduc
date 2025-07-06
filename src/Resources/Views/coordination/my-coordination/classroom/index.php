<?php require_once __DIR__ . '/../../../layout/top.php';?>
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
            <li class="breadcrumb-item">Turma: <?=$class->nome ?? 'não identificado'?></li>
            <li class="breadcrumb-item">Estudantes</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('coordenador')) {?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a href="/relatorio/turma/<?=$class->uuid?>/estudantes" class="btn btn-outline-primary" 
                target="_blank"> 
                    <i class="icon-file-minus fs-5"></i>    
                </a>
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

<div class="col-12">
    <form id="coordinators-form" 
        action="\minha-coordenacao\turma\<?=$class->uuid?>\estudantes" 
        method="GET">            
        <div class="accordion mt-2" id="accordionSpecialTitle">
            <div class="accordion-item bg-transparent">
                <h2 class="accordion-header" id="headingSpecialTitleTwo">
                <button class=" bg-transparent accordion-button <?= isset($situation) || isset($searchFilter) ? '' : 'collapsed'?>" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filters-coordinators" aria-expanded="false"
                    aria-controls="collapseSpecialTitleTwo">
                    <h5 class="m-0">Filtros</h5>
                </button>
                </h2>
                <div id="filters-coordinators" class="accordion-collapse <?= isset($situation) || isset($searchFilter) ? '' : 'collapse'?>"
                    aria-labelledby="headingSpecialTitleTwo" data-bs-parent="#accordionSpecialTitle">
                    <div class="accordion-body">
                    <div class="row justify-content-start">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="m-0">
                                        <label class="form-label">Busca por nome</label>
                                        <input 
                                            class="form-input form-control" 
                                            type="text" 
                                            name="student_name" 
                                            id="name" 
                                            value="<?= isset($searchFilter) ? $searchFilter : null ?>" 
                                            placeholder="Digite nome">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xxl-12">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        <a href="\minha-coordenacao\turma\<?=$class->uuid?>\estudantes" class="btn btn-secondary <?= isset($situation) || isset($searchFilter) ? 'd-block' : 'd-none'?>">Limpar</a>
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

<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">Estudantes</th>
                                    <th class="text-center">Ano Letivo</th>
                                    <? if (hasPermission('coordenador')) {?>
                                    <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($students as $estudante) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?=$estudante->id?></td>
                                        <td class="fw-bold text-center"> 
                                            <?=getJsonToObject($estudante->estudante)->nome ?? 'não identificado'?>
                                        </td>
                                        <td class="text-center"> <?=$estudante->ano_letivo ?? 'não identificado'?>
                                        </td>
                                        <? if (hasPermission('coordenador')) {?>
                                            <td class="d-flex">                                                 
                                                <div class="d-none d-xl-flex d-lg-flex d-md-flex">
                                                    <a class="mb-1 me-2 mt-1" 
                                                        href="/relatorio/turma/<?=$class->uuid?>/estudante/<?=$estudante->uuid?>"
                                                        target="_blank">
                                                        <div class="border p-2 rounded-3" data-toggle="tooltip" title="notas detalhadas">
                                                            <i class="icon-file-minus fs-5"></i>   
                                                        </div>
                                                    </a>                                    
                                                </div>
                                                <div class="d-block d-xl-none d-lg-none d-md-none dropdown ms-3">
                                                    <a class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none"
                                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="icon-menu"></i>
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <div class="header-action-links float-end">
                                                            <a class="mb-1 me-2 mt-1" 
                                                                href="/relatorio/turma/<?=$class->uuid?>/estudante/<?=$estudante->uuid?>"
                                                                target="_blank">
                                                                <div class="border p-2 rounded-3" data-toggle="tooltip" title="notas detalhadas">
                                                                    <i class="icon-file-minus fs-5"></i>   
                                                                </div>
                                                            </a>   
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
                        Total <b><?=count($students)?></b> registros
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