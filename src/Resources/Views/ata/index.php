<?php require_once __DIR__ . '/../layout/top.php'; ?>

<style>
    .filter-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-bottom: 25px;
    }

    .ata-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        border-left: 4px solid #4e73df;
    }

    .ata-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .ata-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ata-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .ata-card-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .ata-card-body {
        color: #6c757d;
    }

    .ata-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .ata-info-item i {
        margin-right: 8px;
        color: #4e73df;
        width: 20px;
    }

    .ata-card-footer {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .filter-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .ata-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-card {
            padding: 15px;
        }

        .ata-card-footer {
            flex-direction: column;
        }

        .ata-card-footer .btn {
            width: 100%;
        }
    }
</style>

<!-- Row start -->
<div class="row gx-3">
    <div class="col-12 col-xl-8">
        <!-- Breadcrumb start -->
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">Atas</li>
        </ol>
        <!-- Breadcrumb end -->
    </div>

    <div class="col-12 col-xl-4">
        <div class="float-xl-end float-start mb-3">
            <a href="/ata" class="btn btn-primary">
                <i class="icon-refresh"></i> Atualizar
            </a>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="row gx-3">
    <div class="col-12">
        <div class="filter-card">
            <h5 class="mb-3"><i class="icon-filter_alt"></i> Filtros</h5>
            <form method="GET" action="/ata" id="filterForm">
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label for="academic_year" class="filter-label">Ano Letivo</label>
                        <select name="academic_year" id="academic_year" class="form-select">
                            <?php
                            $currentYear = date('Y');
                            $selectedYear = $data['filtros']['academic_year'] ?? $currentYear;
                            for ($year = $currentYear - 2; $year <= $currentYear + 1; $year++):
                            ?>
                                <option value="<?= $year ?>" <?= $selectedYear == $year ? 'selected' : '' ?>>
                                    <?= $year ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-12 col-lg-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="icon-search"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Lista de Atas -->
<div class="row gx-3">
    <div class="col-12">
        <?php if (isset($data['turmas']) && is_array($data['turmas']) && count($data['turmas']) > 0): ?>
            <?php foreach ($data['turmas'] as $turma): ?>
                <div class="ata-card">
                    <div class="ata-card-header">
                        <h5 class="ata-card-title">
                            <i class="icon-school"></i> <?= $turma['name'] ?? 'Sem nome' ?>
                        </h5>
                        <span class="ata-card-badge bg-primary text-white">
                            <?= $data['filtros']['academic_year'] ?? date('Y') ?>
                        </span>
                    </div>

                    <div class="ata-card-body">
                        <div class="row mb-3">
                            <div class="col-12 col-md-6">
                                <div class="ata-info-item">
                                    <i class="icon-wb_twilight"></i>
                                    <span><strong>Turno:</strong> <?= $turma['shift'] ?? 'Não informado' ?></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="ata-info-item">
                                    <i class="icon-book"></i>
                                    <span><strong>Disciplinas:</strong> <?= $turma['total_disciplinas'] ?? 0 ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($turma['disciplinas']) && count($turma['disciplinas']) > 0): ?>
                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <h6 class="mb-0"><i class="icon-list"></i> Disciplinas da Turma:</h6>
                                    <div class="d-flex gap-2">
                                        <a href="/relatorios/turma/<?= $turma['id'] ?>/boletins"
                                            class="btn btn-sm btn-info"
                                            title="Ver Boletins da Turma"
                                            target="_blank">
                                            <i class="icon-description"></i> Boletins da Turma
                                        </a>
                                        <a href="/ata/export/turma/<?= $turma['id'] ?>?academic_year=<?= $data['filtros']['academic_year'] ?? date('Y') ?>"
                                            class="btn btn-sm btn-success"
                                            title="Exportar Ata Completa da Turma"
                                            target="_blank">
                                            <i class="icon-file_download"></i> Exportar Ata
                                        </a>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Disciplina</th>
                                                <th>Professor</th>
                                                <th>Carga Horária</th>
                                                <th class="text-center">Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($turma['disciplinas'] as $disciplina): ?>
                                                <tr>
                                                    <td><?= $disciplina->subject_name ?? 'N/A' ?></td>
                                                    <td><?= $disciplina->teacher_name ?? 'N/A' ?></td>
                                                    <td><?= $disciplina->workload ?? 'N/A' ?>h</td>
                                                    <td class="text-center">
                                                        <a href="/relatorios/<?= $disciplina->id ?? '' ?>/gerar-grade"
                                                            class="btn btn-sm btn-outline-primary"
                                                            title="Ver Grade da Disciplina"
                                                            target="_blank">
                                                            <i class="icon-grid_on"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="icon-info"></i> Esta turma não possui disciplinas cadastradas.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="icon-description"></i>
                <h4>Nenhuma turma encontrada</h4>
                <p>Não há turmas disponíveis para os filtros selecionados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // Auto-submit no change dos filtros
    document.getElementById('academic_year').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
</script>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>