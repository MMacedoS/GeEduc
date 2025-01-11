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
                    <i class="icon-archive lh-1"></i>
                    <a href="/turmas" class="text-decoration-none">Turmas</a>
            </li>
            <li class="breadcrumb-item">
                    <i class="icon-content_copy lh-1"></i>
                    <a href="/turmas/<?=$turma->uuid?>/disciplinas/" class="text-decoration-none"><?=$turma->nome?></a>
            </li>
            <li class="breadcrumb-item">Adicionar Componentes Curricular</li>
        </ol>
       <!-- Breadcrumb end -->
       <? if(isset($danger)){?>
        <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
        <b><?=$message?>!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <? }?>  
    </div>  
    
    <div class="col-2 col-xl-6">
        <div class="float-end">
            <a href="/turmas/<?=$turma->uuid?>/disciplinas/" class="btn btn-outline-primary" > Voltar </a>
        </div>
    </div>
</div>

 <!-- Row end -->
 <form action="/turmas/<?=$turma->uuid?>/disciplina" method="post">
    <div class="row gx-3">
        <? include_once('_forms.php');?>
    </div>
</form>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>
