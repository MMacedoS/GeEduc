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
            <li class="breadcrumb-item">
                <i class="icon-dollar-sign lh-1"></i>
                <a href="\mensalidades" class="text-decoration-none">Mensalidades</a>
            </li>
            <li class="breadcrumb-item">Atualizar</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <? if (hasPermission('cadastrar mensalidades')) {?>
        <div class="col-4 col-xl-6">
            <div class="float-end">
                <a href="\mensalidade" class="btn btn-outline-primary" > + </a>
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
    <form action="\mensalidade\<?=$mensalidade->uuid?>" method="POST">
        <div class="row gx-3">
            <? include_once('_forms.php');?>
        </div>
    </form>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>