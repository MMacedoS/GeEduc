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
            <? if (hasPermission('vincular_turmas_disciplina')) {
                $selectedYear = isset($school_year) ? $school_year : date('Y');
            ?>
                <a href="\turmas\<?= $turma->id ?>\disciplina?school_year=<?= $selectedYear ?>" class="btn btn-outline-primary"> + </a>
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

<!-- Filtro de Ano Letivo -->
<div class="row gx-3 mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="/turmas/<?= $turma->id ?>/disciplinas" class="d-flex align-items-center gap-3">
                    <label class="form-label mb-0"><strong>Ano Letivo:</strong></label>
                    <select name="school_year" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                        <?
                        $currentYear = date('Y');
                        $startYear = $currentYear - 5;
                        $endYear = $currentYear + 1;
                        for ($year = $endYear; $year >= $startYear; $year--) {
                        ?>
                            <option value="<?= $year ?>" <?= (isset($school_year) && $school_year == $year) ? 'selected' : ($year == $currentYear && !isset($school_year) ? 'selected' : '') ?>>
                                <?= $year ?>
                            </option>
                        <? } ?>
                    </select>
                    <small class="text-muted">Exibindo disciplinas do ano <?= isset($school_year) ? $school_year : $currentYear ?></small>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Row start -->


<div class="row gx-3">
    <? if (!empty($turmas_disciplinas)) { ?>
        <? foreach ($turmas_disciplinas as $turma_disciplina) { ?>
            <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-3">
                <div class="card h-100 border">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <span class="badge bg-primary">#<?= $turma_disciplina->code ?></span>
                        <? if ($turma_disciplina->active == 0) { ?>
                            <span class="badge bg-danger">Impedido</span>
                        <? } else { ?>
                            <span class="badge bg-success">Disponível</span>
                        <? } ?>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">
                            <?= $turma_disciplina->subject_name ?? 'não identificado' ?>
                        </h5>

                        <div class="mb-2">
                            <small class="text-muted"><i class="icon-user me-1"></i> <strong>Professor:</strong></small>
                            <p class="mb-0"><?= $turma_disciplina->teacher_name ?? 'não identificado' ?></p>
                        </div>

                        <div class="mb-2">
                            <small class="text-muted"><i class="icon-clock me-1"></i> <strong>Carga Horária:</strong></small>
                            <p class="mb-0"><?= $turma_disciplina->workload ?? 'não identificado' ?> Horas</p>
                        </div>
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-around align-items-center flex-wrap gap-2">
                            <? if (hasPermission('editar_turmas_disciplinas')) { ?>
                                <a href="/turmas/<?= $turma->id ?>/disciplina/<?= $turma_disciplina->id ?>"
                                    class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="icon-edit"></i>
                                </a>
                            <? } ?>

                            <? if (hasPermission('visualizar_atividades')) { ?>
                                <a href="/turmas/<?= $turma->id ?>/disciplinas/<?= $turma_disciplina->id ?>/atividades"
                                    class="btn btn-sm btn-outline-info" title="Atividades">
                                    <i class="icon-link"></i>
                                </a>
                            <? } ?>

                            <? if (hasPermission('visualizar_atividades')) { ?>
                                <a href="/turmas/<?= $turma->id ?>/disciplinas/<?= $turma_disciplina->id ?>/aulas"
                                    class="btn btn-sm btn-outline-secondary" title="Aulas">
                                    <i class="icon-library_books"></i>
                                </a>
                            <? } ?>

                            <? if (hasPermission('deletar_turmas_disciplinas')) { ?>
                                <button class="btn btn-sm btn-outline-danger" type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#exampleModal_<?= $turma_disciplina->id ?>"
                                    title="Excluir">
                                    <i class="icon-delete1"></i>
                                </button>
                            <? } ?>
                        </div>
                    </div>
                </div>

                <!-- Modal de confirmação de exclusão -->
                <div class="modal fade" id="exampleModal_<?= $turma_disciplina->id ?>" tabindex="-1"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Tem certeza que deseja excluir este registro da Turma <?= $turma->name ?? 'não identificado' ?>?
                                <p class="fw-bold mt-2">Disciplina: <?= $turma_disciplina->subject_name ?? 'não identificado' ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Cancelar</button>
                                <button type="button"
                                    onclick="deleteData('/turmas/<?= $turma->id ?>/disciplina/<?= $turma_disciplina->id ?>')"
                                    class="btn btn-danger">Confirmar Exclusão</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    <? } else { ?>
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="icon-info me-2"></i>
                        Nenhuma disciplina vinculada a esta turma ainda.
                    </div>
                </div>
            </div>
        </div>
    <? } ?>
</div>

<div class="row">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Total <b><?= count($turmas_disciplinas) ?></b> registros</span>
                    <div>
                        <?= $links ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/bottom.php'; ?>