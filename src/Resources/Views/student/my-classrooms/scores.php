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
                <a href="\minhas-turmas" class="text-decoration-none">Minha Galerinha</a>
            </li>
            <li class="breadcrumb-item">
                <i class="icon-person_outline lh-1"></i>
                <a href="\minhas-turmas" class="text-decoration-none"><?=getJsonToObject($estudante->pessoa_fisica)->nome?></a>
            </li>
            <li class="breadcrumb-item">Componentes Curriculares</li>
        </ol>
       <!-- Breadcrumb end -->
    </div>
</div>
<!-- Row end -->

<div class="row gx-3">
<? foreach ($notas as $index => $nota): ?>       
    <div class="col-xl-4 col-sm-6">
        <div class="card mb-2 text-center">
            <div class="card-header">
                <h5 class="card-title"><?=$nota->disciplina?></h5>
            </div>
            <div class="card-body">
                <p><?=$nota->professor?></p>
                <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalDetalhes<?=$index?>">
                    Ver detalhes
                </a>
            </div>
            <div class="card-footer">
                <span class="badge border border-success text-success fs-5">
                    Pontuação: <?=$nota->media?>
                </span>
            </div>
        </div>
    </div>

    <!-- Modal Detalhes -->
    <div class="modal fade" id="modalDetalhes<?=$index?>" tabindex="-1" aria-labelledby="modalDetalhesLabel<?=$index?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDetalhesLabel<?=$index?>">
                        Detalhes da Disciplina: <?=$nota->disciplina?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-start">
                    <p><strong>Professor:</strong> <?=$nota->professor?></p>                    
                    <?php if (!empty($nota->notas_bimestre1)) : ?>
                        <p><strong>Detalhamento: </strong></p>
                        <div class="row">
                            <div class="col-sm-3">
                                <?=$nota->notas_bimestre1?>
                            </div>
                            <div class="col-sm-3">
                                <?=$nota->notas_bimestre2?>
                            </div>
                            <div class="col-sm-3">
                                <?=$nota->notas_bimestre3?>
                            </div>
                            <div class="col-sm-3">
                                <?=$nota->notas_bimestre4?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <br>
                    <p><strong>Total:</strong> <?=$nota->media?></p>
                    <!-- Adicione outros campos se necessário -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
<? endforeach; ?>

</div>

<div class="row">
    <div class="float-end">
        <?=$data['links']?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>
