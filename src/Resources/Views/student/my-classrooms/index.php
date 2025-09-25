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
            <li class="breadcrumb-item">Turmas</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>
</div>
<!-- Row end -->
<? if (isset($success)) { ?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
        <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? } ?>
<? if (isset($danger)) { ?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
        <b>Danger!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? } ?>

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
                                    <th>Turma</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Coordenador</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Ano</th>
                                    <? if (hasPermission('estudante')) { ?>
                                        <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>

                            <tbody>
                                <? foreach ($turmas as $turma_estudante) {
                                ?>
                                    <tr>
                                        <td><?= $turma_estudante->id ?></td>
                                        <td class="fw-bold"> <?= getJsonToObject($turma_estudante->turma)->nome ?? 'não identificado' ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <?= getCustomers(getJsonToObject($turma_estudante->turma)->coordenadores) ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <?= $turma_estudante->ano_letivo ?>
                                        </td>
                                        <? if (hasPermission('estudante') && getJsonToObject($turma_estudante->turma)->visivel == 1) { ?>
                                            <td class="d-flex">
                                                <? if (hasPermission('estudante')) { ?>
                                                    <a class="mb-1 me-2 mt-1" href="/minhas-turmas/<?= $turma_estudante->uuid ?>/estudante/<?= getJsonToObject($turma_estudante->estudante)->uuid ?>/notas">
                                                        <div class="border p-2 rounded-3" data-toggle="tooltip" title="Notas">
                                                            <i class="icon-edit fs-5"></i>
                                                        </div>
                                                    </a>
                                                <? } ?>
                                                <? if (hasPermission('estudante')) { ?>
                                                    <!-- <a class="mb-1 me-2 mt-1" href="/minhas-turmas/<?= $turma_estudante->uuid ?>/frequencia">
                                                        <div class="border p-2 rounded-3" data-toggle="tooltip" title="Frequência">
                                                            <i class="icon-calendar fs-5"></i>
                                                        </div>
                                                    </a>  -->
                                                <? } ?>
                                                <? if (hasPermission('estudante')) { ?>
                                                    <a class="mb-1 me-2 mt-1" href="/relatorios/<?= $turma_estudante->uuid ?>/grade-notas" target="_blank">
                                                        <div class="border p-2 rounded-3" data-toggle="tooltip" title="Grade de notas">
                                                            <i class="icon-file fs-5"></i>
                                                        </div>
                                                    </a>
                                                <? } ?>
                                            </td>
                                        <? } ?>
                                    </tr>
                                <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?= count($turmas) ?></b> registros
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>