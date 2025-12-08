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
                <a href="/meus-componentes/turma/<?= $turma->id ?>/disciplina/<?= $turmas_disciplinas->id ?>/recuperacoes"
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
    <div class="alert border border-success alert-dismissible fade show text-success d-flex align-items-center" role="alert">
        <i class="icon-check_circle me-2"></i>
        <div>
            <strong>Sucesso!</strong> A nota foi lançada com sucesso.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? endif; ?>
<? if (isset($danger)): ?>
    <div class="alert border border-danger alert-dismissible fade show text-danger d-flex align-items-center" role="alert">
        <i class="icon-error me-2"></i>
        <div>
            <strong>Erro!</strong> Ocorreu um problema ao lançar a nota. Tente novamente.
        </div>
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
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="icon-school me-2"></i>
                                        Lista de Recuperação - I Semestre
                                    </h5>
                                    <p class="text-muted">Estudantes com média inferior a 13,8 no I Semestre</p>
                                </div>
                                <div class="row g-3">
                                    <? foreach ($semester_1 as $index => $value) : ?>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm hover-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h6 class="card-title mb-0">
                                                            <i class="icon-person me-1"></i>
                                                            <?= getJsonToObject($value->estudante)->nome ?>
                                                        </h6>
                                                        <? if (!is_null($value->nota)) : ?>
                                                            <span class="badge bg-success">
                                                                <i class="icon-check_circle"></i> Lançada
                                                            </span>
                                                        <? else : ?>
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="icon-warning"></i> Pendente
                                                            </span>
                                                        <? endif; ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted d-block">Média do Semestre</small>
                                                        <h4 class="mb-0 text-primary"><?= number_format($value->media, 2, ',', '.') ?></h4>
                                                    </div>
                                                    <? if (!is_null($value->nota)) : ?>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Nota de Recuperação</small>
                                                            <h5 class="mb-0 text-success"><?= number_format($value->nota, 2, ',', '.') ?></h5>
                                                        </div>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Total Alcançado</small>
                                                            <h5 class="mb-0"><?= number_format($value->media + $value->nota, 2, ',', '.') ?></h5>
                                                        </div>
                                                    <? endif; ?>
                                                    <button type="button"
                                                        class="btn btn-primary w-100 mt-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalNota<?= $index ?>">
                                                        <i class="icon-edit me-1"></i>
                                                        <?= !is_null($value->nota) ? 'Editar Nota' : 'Lançar Nota' ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modalNota<?= $index ?>" tabindex="-1" aria-labelledby="modalNotaLabel<?= $index ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form class="modal-content" method="post" action="/meus-componentes/turma/<?= $turma->id ?>/disciplina/<?= $turmas_disciplinas->id ?>/recuperacao">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="modalNotaLabel<?= $index ?>">
                                                            <i class="icon-edit me-2"></i>
                                                            Lançar Recuperação I Semestre
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info mb-3">
                                                            <strong><i class="icon-person me-1"></i> Estudante:</strong> <?= getJsonToObject($value->estudante)->nome ?><br>
                                                            <strong><i class="icon-trending_down me-1"></i> Média do Semestre:</strong> <?= number_format($value->media, 2, ',', '.') ?> pontos
                                                        </div>
                                                        <input type="hidden" name="student_class_id" value="<?= $value->estudante_turma_id ?>">
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-6">
                                                                <label for="nota<?= $index ?>" class="form-label fw-bold">
                                                                    <i class="icon-star me-1"></i> Nota da Recuperação
                                                                </label>
                                                                <input type="number"
                                                                    name="score"
                                                                    id="nota<?= $index ?>"
                                                                    class="form-control form-control-lg"
                                                                    value="<?= $value->nota ?>"
                                                                    required
                                                                    min="0"
                                                                    max="3"
                                                                    step="0.1"
                                                                    placeholder="0.0">
                                                                <small class="form-text text-muted">
                                                                    <i class="icon-info me-1"></i> Máximo: 3,0 pontos
                                                                </small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="" class="form-label fw-bold">
                                                                    <i class="icon-calendar_today me-1"></i> Período
                                                                </label>
                                                                <input type="text" name="period" class="form-control form-control-lg" value="I Semestre" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="detalhamento<?= $index ?>" class="form-label fw-bold">
                                                                <i class="icon-description me-1"></i> Detalhamento da Avaliação
                                                            </label>
                                                            <textarea name="obs"
                                                                id="detalhamento<?= $index ?>"
                                                                class="form-control"
                                                                rows="4"
                                                                required
                                                                placeholder="Descreva os critérios de avaliação e observações relevantes..."><?= $value->obs ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="icon-close me-1"></i> Cancelar
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="icon-save me-1"></i> Salvar Nota
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        <? endif; ?>
                        <? if ($periodos[0]->periodo == '4'): ?>
                            <div class="tab-pane fade" id="two" role="tabpanel">
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="icon-school me-2"></i>
                                        Lista de Recuperação - II Semestre
                                    </h5>
                                    <p class="text-muted">Estudantes com média inferior a 13,8 no II Semestre</p>
                                </div>
                                <? if (empty($semester_2)): ?>
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <i class="icon-check_circle me-3 fs-3"></i>
                                        <div>
                                            <h5 class="alert-heading mb-1">Parabéns!</h5>
                                            <p class="mb-0">Não há estudantes para recuperação neste semestre.</p>
                                        </div>
                                    </div>
                                <? endif; ?>
                                <div class="row g-3">
                                    <? foreach ($semester_2 as $index => $value) : ?>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="card h-100 shadow-sm hover-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h6 class="card-title mb-0">
                                                            <i class="icon-person me-1"></i>
                                                            <?= getJsonToObject($value->estudante)->nome ?>
                                                        </h6>
                                                        <? if (!is_null($value->nota)) : ?>
                                                            <span class="badge bg-success">
                                                                <i class="icon-check_circle"></i> Lançada
                                                            </span>
                                                        <? else : ?>
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="icon-warning"></i> Pendente
                                                            </span>
                                                        <? endif; ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted d-block">Média do Semestre</small>
                                                        <h4 class="mb-0 text-primary"><?= number_format($value->media, 2, ',', '.') ?></h4>
                                                    </div>
                                                    <? if (!is_null($value->nota)) : ?>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Nota de Recuperação</small>
                                                            <h5 class="mb-0 text-success"><?= number_format($value->nota, 2, ',', '.') ?></h5>
                                                        </div>
                                                        <div class="mb-3">
                                                            <small class="text-muted d-block">Total Alcançado</small>
                                                            <h5 class="mb-0"><?= number_format($value->media + $value->nota, 2, ',', '.') ?></h5>
                                                        </div>
                                                    <? endif; ?>
                                                    <button type="button"
                                                        class="btn btn-primary w-100 mt-2"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalNota2_<?= $index ?>">
                                                        <i class="icon-edit me-1"></i>
                                                        <?= !is_null($value->nota) ? 'Editar Nota' : 'Lançar Nota' ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modalNota2_<?= $index ?>" tabindex="-1" aria-labelledby="modalNotaLabel2_<?= $index ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form class="modal-content" method="post" action="/meus-componentes/turma/<?= $turma->id ?>/disciplina/<?= $turmas_disciplinas->id ?>/recuperacao">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="modalNotaLabel2_<?= $index ?>">
                                                            <i class="icon-edit me-2"></i>
                                                            Lançar Recuperação II Semestre
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info mb-3">
                                                            <strong><i class="icon-person me-1"></i> Estudante:</strong> <?= getJsonToObject($value->estudante)->nome ?><br>
                                                            <strong><i class="icon-trending_down me-1"></i> Média do Semestre:</strong> <?= number_format($value->media, 2, ',', '.') ?> pontos
                                                        </div>
                                                        <input type="hidden" name="student_class_id" value="<?= $value->estudante_turma_id ?>">
                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-6">
                                                                <label for="nota2_<?= $index ?>" class="form-label fw-bold">
                                                                    <i class="icon-star me-1"></i> Nota da Recuperação
                                                                </label>
                                                                <input type="number"
                                                                    name="score"
                                                                    id="nota2_<?= $index ?>"
                                                                    class="form-control form-control-lg"
                                                                    value="<?= $value->nota ?>"
                                                                    required
                                                                    min="0"
                                                                    max="3"
                                                                    step="0.1"
                                                                    placeholder="0.0">
                                                                <small class="form-text text-muted">
                                                                    <i class="icon-info me-1"></i> Máximo: 3,0 pontos
                                                                </small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="" class="form-label fw-bold">
                                                                    <i class="icon-calendar_today me-1"></i> Período
                                                                </label>
                                                                <input type="text" name="period" class="form-control form-control-lg" value="II Semestre" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="detalhamento2_<?= $index ?>" class="form-label fw-bold">
                                                                <i class="icon-description me-1"></i> Detalhamento da Avaliação
                                                            </label>
                                                            <textarea name="obs"
                                                                id="detalhamento2_<?= $index ?>"
                                                                class="form-control"
                                                                rows="4"
                                                                required
                                                                placeholder="Descreva os critérios de avaliação e observações relevantes..."><?= $value->obs ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="icon-close me-1"></i> Cancelar
                                                        </button>
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="icon-save me-1"></i> Salvar Nota
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="three" role="tabpanel">
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="icon-assignment me-2"></i>
                                        Lista de Exames Finais
                                    </h5>
                                    <p class="text-muted">Estudantes que necessitam realizar o exame final (média anual + recuperações < 27,9)</p>
                                </div>
                                <? if (empty($final)): ?>
                                    <div class="alert alert-success d-flex align-items-center" role="alert">
                                        <i class="icon-check_circle me-3 fs-3"></i>
                                        <div>
                                            <h5 class="alert-heading mb-1">Parabéns!</h5>
                                            <p class="mb-0">Não há estudantes para exame final neste período.</p>
                                        </div>
                                    </div>
                                <? endif; ?>
                                <div class="row g-3">
                                    <? foreach ($final as $index => $value) : ?>
                                        <div class="col-12 col-lg-6">
                                            <div class="card h-100 shadow-sm hover-shadow">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h6 class="card-title mb-0">
                                                            <i class="icon-person me-1"></i>
                                                            <?= getJsonToObject($value->estudante)->nome ?>
                                                        </h6>
                                                        <? if ($value->situacao == 'Aprovado com Exame Final') : ?>
                                                            <span class="badge bg-success">
                                                                <i class="icon-check_circle"></i> Aprovado
                                                            </span>
                                                        <? elseif ($value->situacao == 'Aprovado com Recuperação') : ?>
                                                            <span class="badge bg-success">
                                                                <i class="icon-check_circle"></i> Aprovado
                                                            </span>
                                                        <? elseif ($value->situacao == 'Aguardando Exame Final') : ?>
                                                            <span class="badge bg-info text-white">
                                                                <i class="icon-schedule"></i> Aguardando Exame
                                                            </span>
                                                        <? elseif ($value->situacao == 'Reprovado no Exame Final') : ?>
                                                            <span class="badge bg-danger">
                                                                <i class="icon-close"></i> Reprovado
                                                            </span>
                                                        <? else : ?>
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="icon-warning"></i> Lançar Exame
                                                            </span>
                                                        <? endif; ?>
                                                    </div>

                                                    <div class="row g-2 mb-3">
                                                        <div class="col-6">
                                                            <div class="p-2 bg-light rounded">
                                                                <small class="text-muted d-block">Média do Ano</small>
                                                                <strong class="text-primary"><?= number_format($value->media_notas, 2, ',', '.') ?></strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-2 bg-light rounded">
                                                                <small class="text-muted d-block">Recup. Semestrais</small>
                                                                <strong class="text-info"><?= number_format($value->recuperacoes_semestrais, 2, ',', '.') ?></strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-2 bg-light rounded">
                                                                <small class="text-muted d-block">Total (sem exame)</small>
                                                                <strong class="text-secondary"><?= number_format($value->media_total, 2, ',', '.') ?></strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-2 <?= $value->nota_final >= 7 ? 'bg-success' : ($value->nota_final ? 'bg-danger' : 'bg-light') ?> bg-opacity-10 rounded">
                                                                <small class="text-muted d-block">Exame Final</small>
                                                                <strong class="<?= $value->nota_final >= 7 ? 'text-success' : ($value->nota_final ? 'text-danger' : 'text-muted') ?>">
                                                                    <?= $value->nota_final ? number_format($value->nota_final, 2, ',', '.') : '0,00' ?>
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <small class="text-muted d-block mb-1">
                                                            <i class="icon-info me-1"></i> Situação
                                                        </small>
                                                        <span class="fw-bold"><?= $value->situacao ?></span>
                                                    </div>

                                                    <button type="button"
                                                        class="btn btn-primary w-100"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalNotaFinal_<?= $value->estudante_turma_id ?>">
                                                        <i class="icon-edit me-1"></i>
                                                        <?= !is_null($value->nota_final) ? 'Ver Detalhes' : 'Lançar Exame Final' ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modalNotaFinal_<?= $value->estudante_turma_id ?>" tabindex="-1" aria-labelledby="modalNotaFinalLabel_<?= $value->estudante_turma_id ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <form class="modal-content" method="post" action="/meus-componentes/turma/<?= $turma->id ?>/disciplina/<?= $turmas_disciplinas->id ?>/exame-final">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title" id="modalNotaFinalLabel_<?= $value->estudante_turma_id ?>">
                                                            <i class="icon-assignment me-2"></i>
                                                            Exame Final - <?= getJsonToObject($value->estudante)->nome ?>
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-info mb-4">
                                                            <h6 class="alert-heading">
                                                                <i class="icon-calculate me-2"></i>
                                                                Resumo de Pontuação
                                                            </h6>
                                                            <hr>
                                                            <div class="row g-3">
                                                                <div class="col-md-6">
                                                                    <strong><i class="icon-trending_up me-1"></i> Média do Ano:</strong><br>
                                                                    <span class="fs-5 text-primary"><?= number_format($value->media_notas, 2, ',', '.') ?> pontos</span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <strong><i class="icon-autorenew me-1"></i> Recuperações Semestrais:</strong><br>
                                                                    <ul class="mb-0 mt-2">
                                                                        <li>I Semestre: <strong><?= $value->recup_sem1_nota ? number_format($value->recup_sem1_nota, 2, ',', '.') : '0,00' ?></strong></li>
                                                                        <li>II Semestre: <strong><?= $value->recup_sem2_nota ? number_format($value->recup_sem2_nota, 2, ',', '.') : '0,00' ?></strong></li>
                                                                        <li class="text-info"><strong>Subtotal: <?= number_format($value->recuperacoes_semestrais, 2, ',', '.') ?> pontos</strong></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <strong><i class="icon-functions me-1"></i> Total (sem exame):</strong><br>
                                                                    <span class="fs-5 fw-bold text-secondary">
                                                                        <?= number_format($value->media_total, 2, ',', '.') ?> pontos
                                                                    </span>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <? if ($value->nota_final): ?>
                                                                        <strong><i class="icon-assignment_turned_in me-1"></i> Exame Final:</strong><br>
                                                                        <span class="fs-5 fw-bold <?= $value->nota_final >= 7 ? 'text-success' : 'text-danger' ?>">
                                                                            <?= number_format($value->nota_final, 2, ',', '.') ?> pontos
                                                                        </span>
                                                                    <? else: ?>
                                                                        <strong><i class="icon-flag me-1"></i> Necessário no Exame:</strong><br>
                                                                        <span class="fs-5 fw-bold text-warning">7,00 pontos</span>
                                                                    <? endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" name="student_class_id" value="<?= $value->estudante_turma_id ?>">

                                                        <div class="row g-3 mb-3">
                                                            <div class="col-md-6">
                                                                <label for="notaFinal_<?= $value->estudante_turma_id ?>" class="form-label fw-bold">
                                                                    <i class="icon-star me-1"></i> Nota do Exame Final
                                                                </label>
                                                                <input type="number"
                                                                    name="score"
                                                                    id="notaFinal_<?= $value->estudante_turma_id ?>"
                                                                    class="form-control form-control-lg"
                                                                    value="<?= $value->nota_final ?>"
                                                                    required
                                                                    min="0"
                                                                    max="10"
                                                                    step="0.1"
                                                                    placeholder="0.0"
                                                                    <? if ($value->situacao == 'Aprovado com Exame Final'): ?>disabled<? endif; ?>>
                                                                <small class="form-text text-muted">
                                                                    <i class="icon-info me-1"></i> Máximo: 10,0 pontos
                                                                </small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label for="" class="form-label fw-bold">
                                                                    <i class="icon-calendar_today me-1"></i> Período
                                                                </label>
                                                                <input type="text" name="period" class="form-control form-control-lg" value="Exames Finais" readonly>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="detalhamentoFinal_<?= $value->estudante_turma_id ?>" class="form-label fw-bold">
                                                                <i class="icon-description me-1"></i> Detalhamento da Avaliação
                                                            </label>
                                                            <textarea name="obs"
                                                                id="detalhamentoFinal_<?= $value->estudante_turma_id ?>"
                                                                class="form-control"
                                                                rows="4"
                                                                required
                                                                placeholder="Descreva os critérios de avaliação do exame final e observações relevantes..."
                                                                <? if ($value->situacao == 'Aprovado com Exame Final'): ?>disabled<? endif; ?>><?= $value->nota_final_obs ?></textarea>
                                                        </div> <? if ($value->nota_final): ?>
                                                            <div class="alert <?= $value->nota_final >= 7 ? 'alert-success' : 'alert-danger' ?> mb-0">
                                                                <strong><i class="icon-info me-1"></i> Resultado do Exame Final:</strong>
                                                                <?= $value->situacao ?>
                                                                <? if ($value->nota_final >= 7): ?>
                                                                    - O estudante obteve <?= number_format($value->nota_final, 2, ',', '.') ?> pontos e foi aprovado no exame final!
                                                                <? else: ?>
                                                                    - O estudante obteve <?= number_format($value->nota_final, 2, ',', '.') ?> pontos, abaixo da nota mínima necessária (7,00).
                                                                <? endif; ?>
                                                            </div>
                                                        <? endif; ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="icon-close me-1"></i> Fechar
                                                        </button>
                                                        <? if ($value->situacao != 'Aprovado com Exame Final'): ?>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="icon-save me-1"></i> Salvar Exame Final
                                                            </button>
                                                        <? endif; ?>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    <? endforeach; ?>
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

<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
    }

    .card-title {
        font-size: 1rem;
        color: #2c3e50;
    }

    .badge {
        font-weight: 600;
        padding: 0.4rem 0.8rem;
    }

    .form-control-lg {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .modal-header.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .alert {
        border-radius: 8px;
    }

    .nav-tabs .nav-link {
        font-weight: 500;
        color: #6c757d;
    }

    .nav-tabs .nav-link.active {
        font-weight: 700;
        color: #667eea;
    }
</style>

<?php require_once __DIR__ . '/../../../layout/bottom.php'; ?>