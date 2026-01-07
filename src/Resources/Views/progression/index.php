<?php require_once __DIR__ . '/../layout/top.php'; ?>

<div class="row gx-3">
    <div class="col-12">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item">
                <i class="icon-house_siding lh-1"></i>
                <a href="\dashboard" class="text-decoration-none">Início</a>
            </li>
            <li class="breadcrumb-item">Progressão de Estudantes</li>
        </ol>
    </div>
</div>

<div class="row gx-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Progressão de Estudantes - 2025 para 2026</h5>
                <div class="btn-group" role="group" id="actionButtons" style="display: none;">
                    <button type="button" class="btn btn-success" id="btnMaintainSelected">
                        Manter Selecionados
                    </button>
                    <button type="button" class="btn btn-primary" id="btnProgressSelected">
                        Progredir Selecionados
                    </button>
                    <button type="button" class="btn btn-danger" id="btnDropoutSelected">
                        Desistente/Formado
                    </button>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($turmasGrouped)): ?>
                    <div class="alert alert-info">
                        Nenhum estudante encontrado para progressão no ano letivo de 2025.
                    </div>
                    <!-- Debug -->
                    <div class="alert alert-warning">
                        <strong>Debug:</strong> Verifique se há estudantes cadastrados com ano_letivo = 2025 e ativo = 1 na tabela estudante_turma.
                    </div>
                <?php else: ?>
                    <div class="accordion" id="accordionTurmas">
                        <?php foreach ($turmasGrouped as $index => $grupo): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $index ?>">
                                    <button class="accordion-button <?= $index === array_key_first($turmasGrouped) ? '' : 'collapsed' ?>"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $index ?>"
                                        aria-expanded="<?= $index === array_key_first($turmasGrouped) ? 'true' : 'false' ?>"
                                        aria-controls="collapse<?= $index ?>">
                                        <?= htmlspecialchars($grupo['turma']['nome'] ?? 'Turma sem nome') ?> -
                                        <?= count($grupo['estudantes']) ?> estudante(s)
                                    </button>
                                </h2>
                                <div id="collapse<?= $index ?>"
                                    class="accordion-collapse collapse "
                                    aria-labelledby=" heading<?= $index ?>"
                                    data-bs-parent="#accordionTurmas">
                                    <div class="accordion-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped m-0">
                                                <thead>
                                                    <tr>
                                                        <th width="50">
                                                            <input type="checkbox" class="form-check-input select-all-turma" data-turma-id="<?= $index ?>">
                                                        </th>
                                                        <th>Estudante</th>
                                                        <th>Matrícula</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($grupo['estudantes'] as $estudanteTurma): ?>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox"
                                                                    class="form-check-input student-checkbox"
                                                                    data-uuid="<?= $estudanteTurma['uuid'] ?>"
                                                                    data-order="<?= $grupo['turma']['ordem'] ?? 0 ?>"
                                                                    data-student-name="<?= htmlspecialchars($estudanteTurma['estudante']['nome'] ?? 'Estudante') ?>"
                                                                    data-turma-id="<?= $index ?>">
                                                            </td>
                                                            <td><?= htmlspecialchars($estudanteTurma['estudante']['nome'] ?? 'Nome não disponível') ?></td>
                                                            <td><?= htmlspecialchars($estudanteTurma['estudante']['matricula'] ?? 'N/A') ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="classSelectionModal" tabindex="-1" aria-labelledby="classSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="classSelectionModalLabel">Selecionar Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="modalStudentInfo"></p>
                <div class="form-group">
                    <label for="selectClass" class="form-label">Turma de Destino</label>
                    <select class="form-select" id="selectClass">
                        <option value="">Selecione uma turma...</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmProgression">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentAction = null;
        const selectedStudents = new Set();

        document.querySelectorAll('.student-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedStudents);
        });

        document.querySelectorAll('.select-all-turma').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const turmaId = this.dataset.turmaId;
                const isChecked = this.checked;
                document.querySelectorAll(`.student-checkbox[data-turma-id="${turmaId}"]`).forEach(cb => {
                    cb.checked = isChecked;
                });
                updateSelectedStudents();
            });
        });

        function updateSelectedStudents() {
            selectedStudents.clear();
            document.querySelectorAll('.student-checkbox:checked').forEach(checkbox => {
                selectedStudents.add({
                    uuid: checkbox.dataset.uuid,
                    order: checkbox.dataset.order,
                    name: checkbox.dataset.studentName
                });
            });

            const actionButtons = document.getElementById('actionButtons');
            actionButtons.style.display = selectedStudents.size > 0 ? 'block' : 'none';
        }

        document.getElementById('btnMaintainSelected').addEventListener('click', function() {
            if (selectedStudents.size === 0) return;
            currentAction = 'maintain';
            handleBulkAction('maintain');
        });

        document.getElementById('btnProgressSelected').addEventListener('click', function() {
            if (selectedStudents.size === 0) return;
            currentAction = 'progress';
            handleBulkAction('progress');
        });

        document.getElementById('btnDropoutSelected').addEventListener('click', async function() {
            if (selectedStudents.size === 0) return;

            const studentNames = Array.from(selectedStudents).map(s => s.name).join(', ');
            if (!confirm(`Tem certeza que deseja marcar os seguintes estudantes como desistente/formado?\n\n${studentNames}\n\nO acesso destes estudantes será desativado.`)) {
                return;
            }

            for (const student of selectedStudents) {
                await processProgression(student.uuid, null, 'dropout');
            }

            alert('Estudantes processados com sucesso!');
            window.location.reload();
        });

        async function handleBulkAction(action) {
            const studentsArray = Array.from(selectedStudents);
            const firstStudent = studentsArray[0];
            const allSameOrder = studentsArray.every(s => s.order === firstStudent.order);

            if (!allSameOrder) {
                alert('Selecione apenas estudantes da mesma turma para progressão em lote');
                return;
            }

            const actionText = action === 'maintain' ? 'Manter' : 'Progredir';
            const studentNames = studentsArray.map(s => s.name).join(', ');
            document.getElementById('modalStudentInfo').textContent =
                `Estudantes (${studentsArray.length}): ${studentNames}\nAção: ${actionText}`;

            const response = await fetch(`/progression/available-classes?current_order=${firstStudent.order}&action=${action}`);
            const data = await response.json();

            const selectClass = document.getElementById('selectClass');
            selectClass.innerHTML = '<option value="">Selecione uma turma...</option>';

            data.classes.forEach(classe => {
                const option = document.createElement('option');
                option.value = classe.id;
                option.textContent = classe.name;
                selectClass.appendChild(option);
            });

            const modal = new bootstrap.Modal(document.getElementById('classSelectionModal'));
            modal.show();
        }

        document.getElementById('confirmProgression').addEventListener('click', async function() {
            const selectedClass = document.getElementById('selectClass').value;

            if (!selectedClass) {
                alert('Por favor, selecione uma turma');
                return;
            }

            for (const student of selectedStudents) {
                await processProgression(student.uuid, selectedClass, currentAction);
            }

            alert('Estudantes processados com sucesso!');
            window.location.reload();
        });

        async function processProgression(uuid, newClassId, action) {
            const formData = new FormData();
            formData.append('estudante_turma_uuid', uuid);
            formData.append('new_class_id', newClassId);
            formData.append('action', action);

            try {
                const response = await fetch('/progression/process', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Erro ao processar progressão');
                }

                return data;
            } catch (error) {
                console.error(error);
                throw error;
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>