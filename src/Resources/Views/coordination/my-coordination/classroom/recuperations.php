<?php 
$name = "Lista de recuperação Turma: $turma->nome";
require_once __DIR__ . '/../../../layout/top.php'; ?>
<!-- Data Tables -->
<link rel="stylesheet" href="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/dataTables.bs5.css" />
<link rel="stylesheet" href="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/dataTables.bs5-custom.css" />
<link rel="stylesheet" href="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/dataTables.bs5-custom.css" />

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
            <li class="breadcrumb-item">
                <i class="icon-archive lh-1"></i>
                <a href="/minha-coordenacao/turma/<?=$turma->uuid?>/disciplinas" 
                    class="text-decoration-none">
                    Turma: <?=$turma->nome ?? 'não identificado'?>
                </a>
            </li>
            <li class="breadcrumb-item">Recuperação</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    
    <div class="col-4 col-xl-6">
        <div class="float-end">
        <? if (hasPermission('cadastrar_recuperacao') || hasPermission('professor')) :?>
         <a href="/meus-componentes" class="btn btn-outline-primary" > Voltar </a>
        <? endif;?>
        </div>
    </div>
</div>

    <!-- Row end -->
<? if(isset($success)):?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
      <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? endif;?>
<? if(isset($danger)):?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
       <b>Danger!</b>.
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? endif;?>
    <!-- Row start -->

<div class="row gx-3">
    <div class="col-xxl-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="custom-tabs-container">
                    <ul class="nav nav-tabs" id="customTab" role="tablist">
                        <?if($periodos[0]->periodo >= '2'):?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-one" data-bs-toggle="tab" href="#one" role="tab"
                                aria-controls="one" aria-selected="true"><b>Semestre I</b></a>
                            </li>
                        <?endif;?>
                        <?if($periodos[0]->periodo == '4'):?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-two" data-bs-toggle="tab" href="#two" role="tab"
                                aria-controls="two" aria-selected="false">Semestre II</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-three" data-bs-toggle="tab" href="#three" role="tab"
                                aria-controls="three" aria-selected="false">Exames Finais</a>
                            </li>
                        <?endif;?>
                    </ul>
                    <div class="tab-content" id="customTabContent">
                        <?if($periodos[0]->periodo >= '2'):?>
                            <div class="tab-pane fade show active" id="one" role="tabpanel">
                                <h5 class="ms-4">Lista de Recuperação</h5>
                                <div class="card mb-3">                                    
                                    <div class="card-header">
                                        <div class="card-title">Ações</div>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                        <table id="customButtons" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Estudantes</th>
                                                    <th>Disciplinas reprovadas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <? foreach($semester_1 as $semester):?>                                      
                                                    <tr>
                                                        <td><?=$semester->estudante_nome?></td>
                                                        <td><?=$semester->disciplinas_reprovadas?></td>
                                                    </tr>
                                                <? endforeach;?>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?endif;?>
                        <?if($periodos[0]->periodo == '4'):?>
                            <div class="tab-pane fade" id="two" role="tabpanel">
                                <div class="p-5">
                                <h1 class="display-5 fw-bold text-success">
                                    Tab Two
                                </h1>
                                <div class="col-lg-6">
                                    <p class="lead mb-4">
                                    Quickly design and customize responsive
                                    mobile-first sites with Bootstrap, the world’s
                                    most popular front-end open source toolkit,
                                    featuring Sass variables and mixins, responsive
                                    grid system, extensive prebuilt components, and
                                    powerful JavaScript plugins.
                                    </p>
                                    <div class="d-grid gap-2 d-sm-flex">
                                    <button type="button" class="btn btn-success btn-lg">
                                        Button
                                    </button>
                                    <button type="button" class="btn btn-info btn-lg">
                                        Button
                                    </button>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="three" role="tabpanel">
                                <div class="p-5 text-end">
                                <h1 class="display-5 fw-bold text-success">
                                    Tab Three
                                </h1>
                                <div class="col-lg-6 ms-auto">
                                    <p class="lead mb-4">
                                    Quickly design and customize responsive
                                    mobile-first sites with Bootstrap, the world’s
                                    most popular front-end open source toolkit,
                                    featuring Sass variables and mixins, responsive
                                    grid system, extensive prebuilt components, and
                                    powerful JavaScript plugins.
                                    </p>
                                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                    <button type="button" class="btn btn-success btn-lg">
                                        Button
                                    </button>
                                    <button type="button" class="btn btn-info btn-lg">
                                        Button
                                    </button>
                                    </div>
                                </div>
                                </div>
                            </div>
                        <?endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/bottom.php'; ?>

    <!-- Overlay Scroll JS -->
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/overlay-scroll/custom-scrollbar.js"></script>

<!-- Data Tables -->
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/dataTables.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/dataTables.bootstrap.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/custom/custom-datatables.js"></script>
<!-- DataTable Buttons -->
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/dataTables.buttons.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/jszip.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/dataTables.buttons.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/pdfmake.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/vfs_fonts.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/buttons.html5.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/buttons.print.min.js"></script>
<script src="<?=URL_PREFIX_APP?>/Public/assets/vendor/datatables/buttons/buttons.colVis.min.js"></script>


<script>
    $(document).ready(function() {
        $('#example').DataTable({
            pageLength: 5,
            lengthMenu: [ [5, 10, 25, -1], [5, 10, 25, "Todos"] ]
        });
    });
</script>