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
            <li class="breadcrumb-item">Contratos</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <?php if (hasPermission('cadastrar estudantes')) {?>
        <div class="col-2 col-xl-6">
            <div class="float-end">
            <a href="\contratos\gerar" class="btn btn-outline-primary" > Gerar contratos </a>
            </div>
        </div>
    <?php }?>
</div>
    <!-- Row end -->
<?php if(isset($success)){?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
      <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }?>
<?php if(isset($danger)){?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
       <b>Danger!</b>.
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php }?>
    <!-- Row start -->
<div class="row gx-3">

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
                                    <th>Nome</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">email</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Situação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <?php
                            foreach ($estudantes as $estudante) { 
                                ?>
                                    <tr>
                                        <td><?=$estudante->id?></td>
                                        <td class="fw-bold"> <?=getJsonToObject($estudante->contrato_infos)->nome ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                        <?=getJsonToObject($estudante->contrato_infos)->email ?? 'não identificado'?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">    
                                            <div class="d-flex align-items-center">
                                                <?php if(!isset(getJsonToObject($estudante->contrato_infos)->contrato_details->url_contrato_assinado)) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Não assinado
                                                <?php } ?>
                                                <?php if(isset(getJsonToObject($estudante->contrato_infos)->contrato_details->url_contrato_assinado)) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Assinado
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-none d-xl-flex d-lg-flex d-md-flex">
                                                <a class="mb-1 me-2 mt-1" href="<?= getJsonToObject($estudante->contrato_infos)->contrato_details->url_contrato ?>" target="_blank" rel="noreferrer noopener">
                                                    <div class="border p-2 rounded-3">
                                                        <i class="icon-link fs-5"></i>
                                                    </div>
                                                </a>                                              
                                            </div>
                                        </td>
                                    </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($estudantes)?></b> registros
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div class="row">
    <!-- <div class="float-end">
        <?=$data['links']?>
    </div> -->
</div>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
