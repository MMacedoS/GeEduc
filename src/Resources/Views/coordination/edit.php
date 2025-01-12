<?php require_once __DIR__ . '/../layout/top.php'; ?>


<!-- Row start -->
<div class="row gx-3">
        <div class="col-8 col-xl-6">
            <!-- Breadcrumb start -->
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item">
                    <i class="icon-house_siding lh-1"></i>
                    <a href="/" class="text-decoration-none">Início</a>
                </li>
                <li class="breadcrumb-item">
                    <i class="icon-archive lh-1"></i>
                    <a href="/coordenadores/" class="text-decoration-none">Coordenadores</a>
                </li>
                <li class="breadcrumb-item">Atualizar</li>
            </ol>
            <!-- Breadcrumb end -->
        </div>
        <div class="col-2 col-xl-6">
            <div class="float-end">
                <a href="/coordenadores/" class="btn btn-outline-primary" > Voltar </a>
            </div>
        </div>
    </div>
    <!-- Row end -->
    <?php 
    ?>
    <form action="/coordenador/<?=$coordenador->uuid?>" method="post" enctype="multipart/form-data">   
        <div class="row gx-3">
            <?php include_once '_forms.php';?>
        </div>
    </form>


<?php require_once __DIR__ . '/../layout/bottom.php'; ?>
