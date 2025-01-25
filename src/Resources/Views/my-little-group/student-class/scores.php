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
            <li class="breadcrumb-item">
                <i class="icon-person_outline lh-1"></i>
                <a href="\minha-galerinha\estudante\<?=$estudante->uuid?>" class="text-decoration-none"><?=getJsonToObject($estudante->pessoa_fisica)->nome?></a>
            </li>
            <li class="breadcrumb-item">Notas</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
</div>
<!-- Row end -->

<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="table-outer">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle m-0">
                           <thead>
                                <tr>
                                    <th class="text-center">Professor</th>
                                    <th class="text-center">Componentes Curricular</th>
                                    <th class="text-center">Bimestre</th>
                                    <th class="text-left">Atividades e Notas</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                            <? foreach ($notas as $nota) { 
                                ?>
                                <tr>
                                    <td class="text-center"><?=$nota->professor?></td>        
                                    <td class="text-center"><?=$nota->disciplina?></td>        
                                    <td class="text-center"><?=$nota->bimestre?></td>        
                                    <td class="text-left"><?=$nota->atividades_notas?></td>        
                                </tr>
                            <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?=count($notas)?></b> registros
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

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>
