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
                <i class="icon-person lh-1"></i>
                <a href="\estudantes" class="text-decoration-none">Estudantes</a>
            </li>
            <li class="breadcrumb-item">Estudante & Mensalidade</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
    <div class="col-2 col-xl-6">
        <div class="float-end">
           <a href="/estudantes/<?=$estudante->uuid?>/mensalidades" class="btn btn-outline-primary" > Voltar </a>
        </div>
    </div>
</div>

<form action="/estudantes/<?=$estudante->uuid?>/mensalidade" method="POST">
    <div class="row gx-3">
        <? include_once('_forms.php');?>
    </div>
</form>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>