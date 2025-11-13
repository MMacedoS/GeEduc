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
                <i class="icon-archive lh-1"></i>
                <a href="/turmas/<?= $turma->id ?>/disciplinas" class="text-decoration-none">Turma: <?= $turma->name ?></a>
            </li>
            <li class="breadcrumb-item">Componentes Curriculares</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>

    <div class="col-4 col-xl-6">
        <div class="float-end">
            <? if (hasPermission('vincular_turmas_disciplina')) { ?>
                <a href="\turmas\<?= $turma->id ?>\disciplina" class="btn btn-outline-primary"> + </a>
            <? } ?>
        </div>
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
                                    <th class="text-center">Componente Curicular</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Professor</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Carga Horária</th>
                                    <th class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">Situação</th>
                                    <? if (hasPermission('editar_turmas_disciplinas') || hasPermission('deletar_turmas_disciplinas')) { ?>
                                        <th>Ação</th>
                                    <? } ?>
                                </tr>
                            </thead>

                            <tbody>
                                <? foreach ($turmas_disciplinas as $turma_disciplina) {
                                ?>
                                    <tr>
                                        <td><?= $turma_disciplina->code ?></td>
                                        <td class="text-center">
                                            <?= $turma_disciplina->subject_name ?? 'não identificado' ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <?= $turma_disciplina->teacher_name ?? 'não identificado' ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <?= $turma_disciplina->workload ?? 'não identificado' ?> Horas
                                        </td>
                                        <td class="d-none d-xl-table-cell d-lg-table-cell d-md-table-cell">
                                            <div class="d-flex align-items-center">
                                                <? if ($turma_disciplina->active == 0) { ?>
                                                    <i class="icon-circle1 me-2 text-danger fs-5"></i>
                                                    Impedido
                                                <? } ?>
                                                <? if ($turma_disciplina->active == 1) { ?>
                                                    <i class="icon-circle1 me-2 text-success fs-5"></i>
                                                    Disponivel
                                                <? } ?>
                                            </div>
                                        </td>
                                        <? if (hasPermission('editar_turmas_disciplinas') || hasPermission('deletar_turmas_disciplinas')) { ?>
                                            <td>
                                                <div class="d-none d-xl-flex d-lg-flex d-md-flex">
                                                    <? if (hasPermission('editar_turmas_disciplinas')) { ?>
                                                        <a class="mb-1 me-2 mt-1" href="/turmas/<?= $turma->id ?>/disciplina/<?= $turma_disciplina->id ?>">
                                                            <div class="border p-2 rounded-3">
                                                                <i class="icon-edit fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>
                                                    <? if (hasPermission('visualizar_atividades')) { ?>
                                                        <a class="mb-1 me-2 mt-1" href="/turmas/<?= $turma->id ?>/disciplinas/<?= $turma_disciplina->id ?>/atividades">
                                                            <div class="border p-2 rounded-3">
                                                                <i class="icon-link fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>

                                                    <? if (hasPermission('visualizar_atividades')) { ?>
                                                        <a class="mb-1 me-2 mt-1" href="/turmas/<?= $turma->id ?>/disciplinas/<?= $turma_disciplina->id ?>/aulas">
                                                            <div class="border p-2 rounded-3">
                                                                <i class="icon-library_books fs-5"></i>
                                                            </div>
                                                        </a>
                                                    <? } ?>

                                                    <? if (hasPermission('deletar_turmas_disciplinas')) { ?>
                                                        <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?= $turma_disciplina->id ?>">
                                                            <div class="border p-2 rounded-3">
                                                                <span class="fs-5 text-danger icon-delete1"></span>
                                                            </div>
                                                        </button>
                                                    <? } ?>
                                                </div>
                                                <div class="d-block d-xl-none d-lg-none d-md-none dropdown ms-3">
                                                    <a class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none"
                                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="icon-menu"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <div class="header-action-links float-end">
                                                            <? if (hasPermission('editar_turmas_disciplinas')) { ?>
                                                                <a class="mb-1 me-2 mt-1" href="/turmas/<?= $turma->id ?>/disciplina/<?= $turma_disciplina->id ?>">
                                                                    <div class="border p-2 rounded-3">
                                                                        <i class="icon-edit fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('visualizar_atividades')) { ?>
                                                                <a class="mb-1 me-2 mt-1" href="/turmas/<?= $turma->id ?>/disciplinas/<?= $turma_disciplina->id ?>/atividades">
                                                                    <div class="border p-2 rounded-3">
                                                                        <i class="icon-link fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('visualizar_atividades')) { ?>
                                                                <a class="mb-1 me-2 mt-1" href="/turmas/<?= $turma->id ?>/disciplinas/<?= $turma_disciplina->id ?>/atividades">
                                                                    <div class="border p-2 rounded-3">
                                                                        <i class="icon-edit fs-5"></i>
                                                                    </div>
                                                                </a>
                                                            <? } ?>
                                                            <? if (hasPermission('deletar_turmas_disciplinas')) { ?>
                                                                <button class="btn btn-outline btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_<?= $turma_disciplina->id ?>">
                                                                    <div class="border p-2 rounded-3">
                                                                        <span class="fs-5 text-danger icon-delete1"></span>
                                                                    </div>
                                                                </button>
                                                            <? } ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal fade" id="exampleModal_<?= $turma_disciplina->id ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Tem certeza que deseja excluir este registro da Turma <?= $turma->nome ?? 'não identificado' ?>?
                                                                <p>Disciplina <?= $turma_disciplina->subject_name ?? 'não identificado' ?></p>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="button" onclick="deleteData('/turmas/<?= $turma->id ?>/disciplina/<?= $turma_disciplina->id ?>')" class="btn btn-danger">Confirmar Exclusão</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        <? } ?>
                                    </tr>
                                <? } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end ">
                        Total <b><?= count($turmas_disciplinas) ?></b> registros
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        <?= $links ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>