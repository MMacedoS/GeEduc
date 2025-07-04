<?php require_once __DIR__ . '/../../../layout/top.php'; ?>

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
                <i class="icon-house_siding lh-1"></i>
                <a href="\minha-coordenacao/turma/<?=$turma->uuid?>/disciplinas" class="text-decoration-none">Disciplinas Turma: <?=getJsonToObject($turmas_disciplinas[0]->turma)->nome ?? 'não identificado'?></a>
            </li>
            <li class="breadcrumb-item">
                <i class="icon-archive lh-1"></i>
                <a href="/minha-coordenacao/turma/<?=$turma->uuid?>/disciplinas" class="text-decoration-none">Atividades dos Componentes</a>
            </li>
            <li class="breadcrumb-item">Cadastrar Atividade</li>
        </ol>
       <!-- Breadcrumb end -->
       <? if(isset($_GET['created']) && $_GET['created'] == 0){?>
        <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
        <b>Esta Atividade já está inserida!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <? }?>  

       <? if(isset($_GET['created']) && $_GET['created'] > 0){?>
        <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
        <b><?=$_GET['created']?> Foram inseridas!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <? }?> 
         

       <? if(isset($_GET['completed'])){?>
        <div class="alert border border-warning alert-dismissible fade show text-warning" role="alert">
        <b><?=$_GET['completed']?> é o limite de pontuação somado!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <? }?>  
    </div>  
    
    <div class="col-4 col-xl-6">
        <div class="float-end">
            <a href="/minha-coordenacao/turma/<?=$turma->uuid?>/disciplinas" class="btn btn-outline-primary" > Voltar </a>
        </div>
    </div>
</div>

 <!-- Row end -->
 <form action="/minha-coordenacao/turma/<?=$turma->uuid?>/atividades" method="POST">
    <div class="row gx-3">
        <div class="col-lg-4 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <div class="m-0">
                    <label class="form-label">Atividade</label>
                    <select class="form-control" name="type" id="type">
                        <option value="">Selecione um componente</option>
                        <option value="A-1" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-1') { echo 'selected'; } ?>>Atividade 1</option>
                        <option value="A-2" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-2') { echo 'selected'; } ?>>Atividade 2</option>
                        <option value="A-3" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-3') { echo 'selected'; } ?>>Atividade 3</option>
                        <option value="A-4" <?php if(isset($atividade->tipo)  && $atividade->tipo == 'A-4') { echo 'selected'; } ?>>Atividade 4</option>
                    </select>
                </div>
                </div>
            </div>
            </div>

            <div class="col-lg-4 col-sm-6 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <div class="m-0">
                    <label class="form-label">Nota Maxima</label>
                    <input type="number" class="form-control" name="value" step="0.1" min="0" id="">
                </div>
                </div>
            </div>
            </div>

            <div class="col-lg-4 col-sm-3 col-12">
            <div class="card mb-3">
                <div class="card-body">
                <div class="m-0">
                    <label class="form-label">Situação</label>
                    <select name="active" class="form-control" id="">
                        <option value="0" <?php if(isset($atividade->ativo)  && $atividade->ativo == '0') { echo 'selected'; } ?>>Impedido</option>
                        <option value="1" selected <?php if(isset($atividade->ativo)  && $atividade->ativo == '1') { echo 'selected'; } ?>>Disponivel</option>
                    </select>
                </div>
            </div>
            </div>
            </div>

            <div class="col-xxl-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <a href="/minha-coordenacao/turma/<?=$turma->uuid?>/disciplinas" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</form>

<?php require_once __DIR__ . '/../../../layout/bottom.php'; ?>
