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
            <li class="breadcrumb-item">Periodos</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <div class="col-2 col-xl-6">
        <div class="float-end">
         <a href="\periodos\criar" class="btn btn-outline-primary" > + </a>
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
                                    <th>Bimestre</th>    
                                    <th>Situação</th>    
                                    <th class="float-end me-4">Ações</th>    
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($periodos as $periodo) { ?>
                                    <tr>
                                        <td><?=$periodo->id?></td>
                                        <td class="fw-bold"> 
                                            <?=$periodo->periodo ?>º periodo
                                        </td>
                                        <td>    
                                            <div class="d-flex align-items-center">
                                                <? if($periodo->ativo == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if($periodo->ativo == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <td>
                                           <div class="float-end d-flex">
                                           <a class="mb-1 me-2 mt-1" href="/periodos/<?=$periodo->uuid?>/editar">
                                                <div class="border p-2 rounded-3">
                                                    <i class="icon-edit fs-5"></i>
                                                </div>
                                            </a>  
                                            <a class="mb-1 me-2 mt-1" href="/periodos/<?=$periodo->uuid?>/active">
                                                <div class="border p-2 rounded-3">
                                                    <i class="icon-circle1 text-danger fs-5"></i>
                                                </div>
                                            </a> 
                                           </div> 
                                        </td>
                                    </tr>                                          
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($periodos)?></b> registros
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
