<?php require_once __DIR__ . '/../../../layout/top.php';
?>

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
                <a href="\meus-componentes" class="text-decoration-none">Meus Componentes</a>
            </li>
            <li class="breadcrumb-item">
                <i class="icon-archive lh-1"></i>
                <a href="/meus-componentes/turma/<?= $turma->uuid ?>/disciplina/<?= $turmas_disciplinas->uuid ?>/recuperacoes"
                    class="text-decoration-none">Recuperação do Componente: <?= $turmas_disciplinas->teacher_name ?></a>
            </li>
        </ol>
        <!-- Breadcrumb end -->
    </div>

    <div class="col-4 col-xl-6">
        <div class="float-end">
            <? if (hasPermission('cadastrar_recuperacao') || hasPermission('professor')) : ?>
                <a href="/meus-componentes" class="btn btn-outline-primary"> Voltar </a>
            <? endif; ?>
        </div>
    </div>
</div>

<!-- Row end -->
<? if (isset($success)): ?>
    <div class="alert border border-success alert-dismissible fade show text-success" role="alert">
        <b>Success!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? endif; ?>
<? if (isset($danger)): ?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
        <b>Danger!</b>.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? endif; ?>
<!-- Row start -->

<div class="row gx-3">
    <div class="col-xxl-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="custom-tabs-container">
                    <ul class="nav nav-tabs" id="customTab" role="tablist">
                        <? if ($periodos[0]->periodo >= '2'): ?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab-one" data-bs-toggle="tab" href="#one" role="tab"
                                    aria-controls="one" aria-selected="true"><b>Semestre I</b></a>
                            </li>
                        <? endif; ?>
                        <? if ($periodos[0]->periodo == '4'): ?>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-two" data-bs-toggle="tab" href="#two" role="tab"
                                    aria-controls="two" aria-selected="false">Semestre II</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab-three" data-bs-toggle="tab" href="#three" role="tab"
                                    aria-controls="three" aria-selected="false">Exames Finais</a>
                            </li>
                        <? endif; ?>
                    </ul>
                    <div class="tab-content" id="customTabContent">
                        <? if ($periodos[0]->periodo >= '2'): ?>
                            <div class="tab-pane fade show active" id="one" role="tabpanel">
                                <h5 class="ms-4">Lista de Recuperação</h5>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="list-group w-auto">
                                            <? foreach ($semester_1 as $index => $value) : ?>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex gap-3 py-3"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalNota<?= $index ?>">
                                                    <div class="d-flex gap-2 w-100 justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-0"><?= getJsonToObject($value->estudante)->nome ?></h6>
                                                            <p class="mb-0 opacity-75">Total alcançado: <?= $value->media + $value->nota ?? 0 ?></p>
                                                        </div>
                                                        <? if (!is_null($value->nota)) : ?>
                                                            <small class="badge bg-primary border border-primary">Lançada</small>
                                                        <? else : ?>
                                                            <small class="badge bg-danger border border-danger">Lançar</small>
                                                        <? endif; ?>
                                                    </div>
                                                </a>

                                                <!-- Modal -->
                                                <div class="modal fade" id="modalNota<?= $index ?>" tabindex="-1" aria-labelledby="modalNotaLabel<?= $index ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form class="modal-content" method="post" action="/meus-componentes/turma/<?= $turma->uuid ?>/disciplina/<?= $turmas_disciplinas->uuid ?>/recuperacao">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalNotaLabel<?= $index ?>">
                                                                    Lançar Recuperação I Semestre - <?= getJsonToObject($value->estudante)->nome ?>
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="student_class_id" value="<?= $value->estudante_turma_id ?>">
                                                                <div class="mb-3 row">
                                                                    <div class="col-6">
                                                                        <label for="nota<?= $index ?>" class="form-label">Nota</label>
                                                                        <input type="number" name="score" id="nota<?= $index ?>" class="form-control" value="<?= $value->nota ?>" required min="0" max="3" step="0.1">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="" class="form-label">Período</label>
                                                                        <input type="text" name="period" id="" class="form-control" value="I Semestre" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="detalhamento<?= $index ?>" class="form-label">Detalhamento da nota</label>
                                                                    <textarea name="obs" id="detalhamento<?= $index ?>" class="form-control" rows="3" required><?= $value->obs ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer justify-content-end">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">Salvar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <? endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? endif; ?>
                        <? if ($periodos[0]->periodo == '4'): ?>
                            <div class="tab-pane fade" id="two" role="tabpanel">
                                <h5 class="ms-4">Lista de Recuperação</h5>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="list-group w-auto">
                                            <? if (empty($semester_2)): echo '<h1 class="display-9 fw-bold text-success">
                                                    Não há estudantes para recuperação neste semestre
                                                </h1>';
                                            endif; ?>
                                            <? foreach ($semester_2 as $index => $value) : ?>
                                                <a href="#"
                                                    class="list-group-item list-group-item-action d-flex gap-3 py-3"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalNota<?= $index ?>">
                                                    <div class="d-flex gap-2 w-100 justify-content-between align-items-start">
                                                        <div>
                                                            <h6 class="mb-0"><?= getJsonToObject($value->estudante)->nome ?></h6>
                                                            <p class="mb-0 opacity-75">Total alcançado: <?= $value->media + $value->nota ?? 0 ?></p>
                                                        </div>
                                                        <? if (!is_null($value->nota)) : ?>
                                                            <small class="badge bg-primary border border-primary">Lançada</small>
                                                        <? else : ?>
                                                            <small class="badge bg-danger border border-danger">Lançar</small>
                                                        <? endif; ?>
                                                    </div>
                                                </a>

                                                <!-- Modal -->
                                                <div class="modal fade" id="modalNota<?= $index ?>" tabindex="-1" aria-labelledby="modalNotaLabel<?= $index ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form class="modal-content" method="post" action="/meus-componentes/turma/<?= $turma->uuid ?>/disciplina/<?= $turmas_disciplinas->uuid ?>/recuperacao">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="modalNotaLabel<?= $index ?>">
                                                                    Lançar Recuperação II Semestre - <?= getJsonToObject($value->estudante)->nome ?>
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="student_class_id" value="<?= $value->estudante_turma_id ?>">
                                                                <div class="mb-3 row">
                                                                    <div class="col-6">
                                                                        <label for="nota<?= $index ?>" class="form-label">Nota</label>
                                                                        <input type="number" name="score" id="nota<?= $index ?>" class="form-control" value="<?= $value->nota ?>" required min="0" max="3" step="0.1">
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="" class="form-label">Período</label>
                                                                        <input type="text" name="period" id="" class="form-control" value="II Semestre" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="detalhamento<?= $index ?>" class="form-label">Detalhamento da nota</label>
                                                                    <textarea name="obs" id="detalhamento<?= $index ?>" class="form-control" rows="3" required><?= $value->obs ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer justify-content-end">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-primary">Salvar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <? endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="three" role="tabpanel">
                                <div class="p-5 text-end">
                                    <h1 class="display-5 fw-bold text-success">
                                        Não esta disponivel no momento
                                    </h1>
                                    <!-- <div class="col-lg-6 ms-auto">
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
                                    </div> -->
                                </div>
                            </div>
                        <? endif; ?>
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