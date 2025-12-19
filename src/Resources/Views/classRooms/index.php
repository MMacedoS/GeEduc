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
            <li class="breadcrumb-item">Turmas</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>

    <div class="col-4 col-xl-6">
        <div class="float-end">
            <? if (hasPermission('cadastrar_turma')) { ?>
                <a href="\turma" class="btn btn-outline-primary"> + </a>
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
        <form id="classrooms-form" action="/turmas" method="GET">
            <div class="accordion mt-2" id="accordionSpecialTitle">
                <div class="accordion-item bg-transparent">
                    <h2 class="accordion-header" id="headingSpecialTitleTwo">
                        <button
                            class="bg-transparent accordion-button <?= isset($situation) || isset($searchFilter) ? '' : 'collapsed' ?>"
                            type="button" data-bs-toggle="collapse"
                            data-bs-target="#filters-classrooms"
                            aria-expanded="false"
                            aria-controls="collapseSpecialTitleTwo">
                            <h5 class="m-0">Filtros</h5>
                        </button>
                    </h2>
                    <div id="filters-classrooms"
                        class="accordion-collapse <?= isset($situation) || isset($searchFilter) ? '' : 'collapse' ?>"
                        aria-labelledby="headingSpecialTitleTwo"
                        data-bs-parent="#accordionSpecialTitle">
                        <div class="accordion-body">
                            <div class="row justify-content-start">
                                <div class="col-sm-6 col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="m-0">
                                                <label class="form-label">Busca por nome</label>
                                                <input
                                                    class="form-input form-control"
                                                    type="text"
                                                    name="classroom"
                                                    id="classroom"
                                                    value="<?= isset($searchFilter) ? $searchFilter : null ?>"
                                                    placeholder="Digite o nome da turma">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-2 mb-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="m-0">
                                                <label class="form-label">Turno</label>
                                                <select class="form-select form-control" name="shift" id="shift">
                                                    <option <?= (isset($shift) && $shift == '') ? 'selected' : '' ?> value="">Ambos</option>
                                                    <option value="matutino" <?= (isset($shift) && $shift == "matutino") ? 'selected' : '' ?>>Matutino</option>
                                                    <option value="vespertino" <?= (isset($shift) && $shift == "vespertino") ? 'selected' : '' ?>>Vespertino</option>
                                                    <option value="noturno" <?= (isset($shift) && $shift == "noturno") ? 'selected' : '' ?>>Noturno</option>
                                                    <option value="integral" <?= (isset($shift) && $shift == "integral") ? 'selected' : '' ?>>Integral</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="m-0">
                                                <label class="form-label">Busca por coordenador</label>
                                                <input
                                                    class="form-input form-control"
                                                    type="text"
                                                    name="coordinator"
                                                    id="coordinator"
                                                    value="<?= isset($coordinator) ? $coordinator : null ?>"
                                                    placeholder="Digite o nome do coordenador">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-2 mb-2">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="m-0">
                                                <label class="form-label">Situação</label>
                                                <select class="form-select form-control" name="situation" id="situation">
                                                    <option <?= (isset($situation) && $situation == '') ? 'selected' : '' ?> value="">Ambas</option>
                                                    <option value="1" <?= (isset($situation) && $situation == 1) ? 'selected' : '' ?>>Disponível</option>
                                                    <option value="0" <?= (isset($situation) && $situation == 0) ? 'selected' : '' ?>>Impedido</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xxl-12">
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                                <a href="\turmas" class="btn btn-secondary <?= isset($situation) || isset($searchFilter) || isset($shift) || isset($coordinator) ? 'd-block' : 'd-none' ?>">Limpar</a>
                                                <button type="submit" class="btn btn-primary">Buscar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="accordion" id="accordionTurmas">
                    <? foreach ($data['turmas'] as $index => $turma) {
                        $turma = (object)$turma;
                        $collapseId = "collapse_" . $turma->code;
                    ?>
                        <div class="accordion-item mb-3 border rounded">
                            <h2 class="accordion-header" id="heading_<?= $turma->code ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#<?= $collapseId ?>" aria-expanded="false"
                                    aria-controls="<?= $collapseId ?>">
                                    <div class="d-flex w-100 justify-content-between align-items-center pe-3">
                                        <div>
                                            <span class="badge bg-primary me-2">#<?= $turma->code ?></span>
                                            <strong><?= $turma->name ?? 'não identificado' ?></strong>
                                            <span class="text-muted ms-2">| <?= ucfirst($turma->shift ?? 'não identificado') ?></span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <? if ($turma->active == 0) { ?>
                                                <span class="badge bg-danger">Impedido</span>
                                            <? } else { ?>
                                                <span class="badge bg-success">Disponível</span>
                                            <? } ?>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="<?= $collapseId ?>" class="accordion-collapse collapse"
                                aria-labelledby="heading_<?= $turma->code ?>" data-bs-parent="#accordionTurmas">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <h6 class="mb-2"><i class="icon-user me-2"></i>Coordenador(es):</h6>
                                            <p class="text-muted mb-3"><?= getCustomers($turma->coordinators) ?></p>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0"><i class="icon-book me-2"></i>Disciplinas Vinculadas</h6>
                                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                                    <? if (!empty($turma->disciplines)) {
                                                        // Extrair anos letivos únicos
                                                        $disciplinesArray = is_object($turma->disciplines) ? (array)$turma->disciplines : $turma->disciplines;
                                                        $anosLetivos = [];
                                                        foreach ($disciplinesArray as $disc) {
                                                            $discObj = is_array($disc) ? (object)$disc : $disc;
                                                            if (isset($discObj->ano_letivo) && !in_array($discObj->ano_letivo, $anosLetivos)) {
                                                                $anosLetivos[] = $discObj->ano_letivo;
                                                            }
                                                        }
                                                        sort($anosLetivos);
                                                        $currentYear = date('Y');
                                                        if (count($anosLetivos) >= 1) {
                                                    ?>
                                                            <select class="form-select form-select-sm"
                                                                id="filter_year_<?= $turma->code ?>"
                                                                onchange="filterDisciplinesByYear(<?= $turma->code ?>)"
                                                                style="min-width: 150px;">
                                                                <option value="">Todos os anos</option>
                                                                <? foreach ($anosLetivos as $ano) { ?>
                                                                    <option value="<?= $ano ?>" <?= ($ano == $currentYear) ? 'selected' : '' ?>><?= $ano ?></option>
                                                                <? } ?>
                                                            </select>
                                                    <? }
                                                    } ?>
                                                    <? if (hasPermission('visualizar_turmas_estudantes')) {
                                                        $currentYear = date('Y');
                                                    ?>
                                                        <a href="/turmas/<?= $turma->id ?>/disciplinas?school_year=<?= $currentYear ?>"
                                                            id="btn_gerenciar_<?= $turma->code ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="icon-link me-1"></i> Gerenciar Disciplinas
                                                        </a>
                                                    <? } ?>
                                                </div>
                                            </div>

                                            <? if (!empty($turma->disciplines)) { ?>
                                                <div class="row g-2" id="disciplines_container_<?= $turma->code ?>">
                                                    <? foreach ($turma->disciplines as $disciplina) {
                                                        $disciplina = (object)$disciplina;
                                                    ?>
                                                        <div class="col-md-6 col-lg-4 discipline-card" data-year="<?= $disciplina->ano_letivo ?>" data-turma="<?= $turma->code ?>">
                                                            <div class="card border h-100">
                                                                <div class="card-body p-3">
                                                                    <h6 class="card-title text-primary mb-2">
                                                                        <?= $disciplina->disciplina_nome ?>
                                                                    </h6>
                                                                    <div class="small text-muted">
                                                                        <div class="mb-1">
                                                                            <i class="icon-user me-1"></i>
                                                                            <strong>Professor:</strong> <?= $disciplina->professor_nome ?>
                                                                        </div>
                                                                        <div class="mb-1">
                                                                            <i class="icon-clock me-1"></i>
                                                                            <strong>Carga Horária:</strong> <?= $disciplina->carga_horaria ?>h
                                                                        </div>
                                                                        <div>
                                                                            <i class="icon-calendar me-1"></i>
                                                                            <strong>Ano Letivo:</strong> <?= $disciplina->ano_letivo ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                            <? } else { ?>
                                                <div class="alert alert-info mb-0">
                                                    <i class="icon-info me-2"></i>
                                                    Nenhuma disciplina vinculada a esta turma ainda.
                                                </div>
                                            <? } ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-2">
                                                <? if (hasPermission('editar_turma')) { ?>
                                                    <a href="/turma/<?= $turma->id ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="icon-edit me-1"></i> Editar
                                                    </a>
                                                <? } ?>
                                                <? if (hasPermission('deletar_turma')) { ?>
                                                    <button class="btn btn-sm btn-outline-danger" type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal_<?= $turma->id ?>">
                                                        <i class="icon-delete1 me-1"></i> Excluir
                                                    </button>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal de confirmação de exclusão -->
                        <div class="modal fade" id="exampleModal_<?= $turma->id ?>" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Confirmação de Exclusão</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Tem certeza que deseja excluir este registro?
                                        <p>Turma <?= $turma->name ?? 'não identificado' ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancelar</button>
                                        <button type="button" onclick="deleteData('/turma/<?= $turma->id ?>')"
                                            class="btn btn-danger">Confirmar Exclusão</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? } ?>
                </div>
                <div class="text-end mt-3">
                    Total <b><?= count($data['turmas']) ?></b> registros
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="float-end">
        <?= $data['links'] ?>
    </div>
</div>

<script>
    function filterDisciplinesByYear(turmaCode) {
        const selectElement = document.getElementById(`filter_year_${turmaCode}`);
        const selectedYear = selectElement.value;
        const disciplineCards = document.querySelectorAll(`.discipline-card[data-turma="${turmaCode}"]`);

        // Atualizar a URL do botão "Gerenciar Disciplinas"
        const btnGerenciar = document.getElementById(`btn_gerenciar_${turmaCode}`);
        if (btnGerenciar) {
            const baseUrl = btnGerenciar.getAttribute('href').split('?')[0];
            if (selectedYear) {
                btnGerenciar.setAttribute('href', `${baseUrl}?school_year=${selectedYear}`);
            } else {
                btnGerenciar.setAttribute('href', baseUrl);
            }
        }

        disciplineCards.forEach(card => {
            if (selectedYear === '' || card.getAttribute('data-year') === selectedYear) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });

        // Verificar se há cards visíveis
        const visibleCards = Array.from(disciplineCards).filter(card => card.style.display !== 'none');
        const container = document.getElementById(`disciplines_container_${turmaCode}`);
        const noResultsAlert = document.getElementById(`no_results_${turmaCode}`);

        if (visibleCards.length === 0) {
            if (!noResultsAlert) {
                const alert = document.createElement('div');
                alert.id = `no_results_${turmaCode}`;
                alert.className = 'col-12';
                alert.innerHTML = `
                <div class="alert alert-warning mb-0">
                    <i class="icon-info me-2"></i>
                    Nenhuma disciplina encontrada para o ano letivo selecionado.
                </div>
            `;
                container.appendChild(alert);
            }
        } else {
            if (noResultsAlert) {
                noResultsAlert.remove();
            }
        }
    }

    // Aplicar filtro do ano atual automaticamente ao carregar
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('[id^="filter_year_"]');
        selects.forEach(select => {
            const turmaCode = select.id.replace('filter_year_', '');
            if (select.value) {
                filterDisciplinesByYear(turmaCode);
            }
        });
    });
</script><?php require_once __DIR__ . '/../layout/bottom.php'; ?>