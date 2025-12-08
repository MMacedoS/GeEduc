<?php require_once __DIR__ . '/../layout/top.php'; ?>

<style>
  .chart-loader {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 8px;
    margin: 10px 0;
  }

  .chart-loader .spinner-border {
    width: 3rem;
    height: 3rem;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .chart-loader .loading-text {
    margin-top: 1rem;
    font-weight: 500;
    color: #495057;
    animation: fadeInOut 2s ease-in-out infinite;
  }

  @keyframes fadeInOut {

    0%,
    100% {
      opacity: 0.7;
    }

    50% {
      opacity: 1;
    }
  }

  .chart-loader .loading-progress {
    width: 200px;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    margin-top: 1rem;
    overflow: hidden;
  }

  .chart-loader .loading-progress::before {
    content: '';
    display: block;
    height: 100%;
    background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545);
    background-size: 200% 100%;
    animation: progressBar 2s ease-in-out infinite;
  }

  @keyframes progressBar {
    0% {
      transform: translateX(-100%);
    }

    100% {
      transform: translateX(100%);
    }
  }

  @keyframes progressBar {
    0% {
      transform: translateX(-100%);
    }

    100% {
      transform: translateX(100%);
    }
  }

  .error-state {
    padding: 2rem;
    text-align: center;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    color: #721c24;
  }
</style>

<? if (isset($_GET['error'])): ?>
  <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
    <b>Sem permissão!</b>.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<? endif; ?>

<!-- Row start -->
<?php
$userPanel = $_SESSION['user']->painel ?? 'guest';

// Dashboard para Administrativo e Secretaria
if ($userPanel == 'administrativo' || $userPanel == 'secretaria'):
?>
  <div class="row gx-3">
    <div class="col-12 mb-3">
      <h5 class="fw-bold">
        <i class="icon-admin_panel_settings me-2"></i>
        Painel <?= $userPanel == 'administrativo' ? 'Administrativo' : 'da Secretaria' ?>
      </h5>
    </div>

    <div class="col-sm-3 col-12">
      <a href="\estudantes">
        <div class="card mb-3 border-0 shadow-sm hover-card">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-success rounded-5 me-3">
                <i class="icon-people fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($estudantes) ? count($estudantes) : 0 ?></h2>
                <p class="m-0 opacity-50">Estudantes</p>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <div class="col-sm-3 col-12">
      <a href="\turmas">
        <div class="card mb-3 border-0 shadow-sm hover-card">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-info rounded-5 me-3">
                <i class="icon-groups fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($turmas) ? count($turmas) : 0 ?></h2>
                <p class="m-0 opacity-50">Turmas</p>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <div class="col-sm-3 col-12">
      <a href="\disciplinas">
        <div class="card mb-3 border-0 shadow-sm hover-card">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-danger rounded-5 me-3">
                <i class="icon-book fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($discipline) ? count($discipline) : 0 ?></h2>
                <p class="m-0 opacity-50">Disciplinas</p>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <div class="col-sm-3 col-12">
      <a href="\professores">
        <div class="card mb-3 border-0 shadow-sm hover-card">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-warning rounded-5 me-3">
                <i class="icon-school fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($teachers) ? count($teachers) : 0 ?></h2>
                <p class="m-0 opacity-50">Professores</p>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>

    <? if ($userPanel == 'administrativo'): ?>
      <div class="col-xl-6">
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Reprovações por Disciplina</h5>
            <div class="mb-3">
              <label for="classSelect" class="form-label">Selecionar Turma:</label>
              <select id="classSelect" class="form-select" onchange="loadFailedStudentsChart()">
                <option value="">Todas as turmas</option>
              </select>
            </div>
            <div id="failedStudentsChart" class="auto-align-graph">
              <div id="failedStudentsLoading" class="chart-loader" style="height: 250px;">
                <div class="spinner-border text-warning" role="status">
                  <span class="visually-hidden">Carregando...</span>
                </div>
                <div class="loading-text">Calculando soma dos 4 bimestres...</div>
                <div class="loading-progress"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <? endif; ?>
  </div>

  <style>
    .hover-card {
      transition: all 0.3s ease;
    }

    .hover-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
  </style>
<? endif; ?>

<?php
// Dashboard para Coordenador
if ($userPanel == 'coordenador'):
?>
  <!-- Cards de Estatísticas do Coordenador -->
  <div class="row gx-3 mb-3">
    <div class="col-12 mb-3">
      <h5 class="fw-bold"><i class="icon-admin_panel_settings me-2"></i>Painel de Coordenação</h5>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-primary rounded-circle">
              <i class="icon-groups text-white fs-4"></i>
            </div>
            <div id="coordTurmasLoader" class="spinner-border spinner-border-sm text-primary" role="status"></div>
          </div>
          <h3 id="totalTurmasCoord" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Turmas Ativas</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-info rounded-circle">
              <i class="icon-people text-white fs-4"></i>
            </div>
            <div id="coordAlunosLoader" class="spinner-border spinner-border-sm text-info" role="status"></div>
          </div>
          <h3 id="totalAlunosCoord" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Total de Alunos</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-warning rounded-circle">
              <i class="icon-warning text-white fs-4"></i>
            </div>
            <div id="coordRiscoLoader" class="spinner-border spinner-border-sm text-warning" role="status"></div>
          </div>
          <h3 id="alunosRisco" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Alunos em Risco</p>
        </div>
      </div>
    </div>

    <!-- Card de Visão Geral -->
    <div class="col-lg-6">
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold"><i class="icon-pie_chart me-2"></i>Visão Geral dos Alunos</h6>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-6 mb-3">
              <div class="p-3 bg-light rounded">
                <h2 id="alunosAprovadosCoord" class="mb-1 fw-bold text-success">-</h2>
                <p class="mb-0 small text-muted">Alunos Aprovados</p>
                <small class="text-muted">(Média ≥ 28)</small>
              </div>
            </div>
            <div class="col-6 mb-3">
              <div class="p-3 bg-light rounded">
                <h2 id="alunosRiscoCoord" class="mb-1 fw-bold text-danger">-</h2>
                <p class="mb-0 small text-muted">Alunos em Risco</p>
                <small class="text-muted">(Média &lt; 28)</small>
              </div>
            </div>
          </div>
          <div class="progress" style="height: 20px;">
            <div id="approvalProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
            <div id="riskProgress" class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card de Disciplinas em Risco -->
    <div class="col-lg-6">
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold"><i class="icon-priority_high me-2 text-danger"></i>Componentes com Maior Número de Alunos em Risco</h6>
        </div>
        <div class="card-body" style="max-height: 250px; overflow-y: auto;">
          <div id="disciplinasRiscoList">
            <div class="text-center py-3">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Carregando...</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    fetch('/dashboard/my-coordinator-stats')
      .then(response => {
        if (!response.ok) {
          throw new Error('Erro na resposta do servidor: ' + response.status);
        }
        return response.text();
      })
      .then(text => {
        try {
          const data = JSON.parse(text);

          if (data.success) {
            const stats = data.data;

            document.getElementById('totalTurmasCoord').textContent = stats.total_turmas;
            document.getElementById('totalAlunosCoord').textContent = stats.total_alunos;
            document.getElementById('alunosRisco').textContent = stats.alunos_risco;

            document.getElementById('alunosAprovadosCoord').textContent = stats.alunos_aprovados;
            document.getElementById('alunosRiscoCoord').textContent = stats.alunos_risco;

            const approvalPercent = stats.total_alunos > 0 ? (stats.alunos_aprovados / stats.total_alunos * 100) : 0;
            const riskPercent = stats.total_alunos > 0 ? (stats.alunos_risco / stats.total_alunos * 100) : 0;
            document.getElementById('approvalProgress').style.width = approvalPercent + '%';
            document.getElementById('riskProgress').style.width = riskPercent + '%';

            const disciplinasRiscoList = document.getElementById('disciplinasRiscoList');
            disciplinasRiscoList.innerHTML = '';

            if (stats.disciplinas_risco && stats.disciplinas_risco.length > 0) {
              const totalReprovacoes = stats.disciplinas_risco.reduce((sum, disc) => sum + disc.quantidade_reprovados, 0);

              if (totalReprovacoes > stats.alunos_risco) {
                const nota = document.createElement('div');
                nota.className = 'alert alert-info mb-3 py-2';
                nota.innerHTML = `
                  <small>
                    <i class="icon-info-circle me-1"></i>
                    <strong>${stats.alunos_risco} alunos</strong> estão em risco, com <strong>${totalReprovacoes} reprovações</strong> 
                    distribuídas em ${stats.total_componentes_com_reprovados} disciplinas. 
                    Alguns alunos estão reprovados em múltiplas disciplinas.
                  </small>
                `;
                disciplinasRiscoList.appendChild(nota);
              }

              stats.disciplinas_risco.forEach((disc, index) => {
                const item = document.createElement('div');
                item.className = 'mb-2 pb-2 border-bottom';
                item.innerHTML = `
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center flex-grow-1">
                      <span class="badge bg-danger me-2">${index + 1}º</span>
                      <div>
                        <strong>${disc.disciplina}</strong>
                        <br>
                        <small class="text-muted"><i class="icon-school me-1"></i>${disc.turma}</small>
                      </div>
                    </div>
                    <div class="text-end">
                      <h5 class="mb-0 text-danger">${disc.quantidade_reprovados}</h5>
                      <small class="text-muted">${disc.quantidade_reprovados === 1 ? 'aluno' : 'alunos'}</small>
                    </div>
                  </div>
                `;
                disciplinasRiscoList.appendChild(item);
              });
            } else {
              disciplinasRiscoList.innerHTML = '<p class="text-muted text-center mb-0">Nenhum aluno em situação de risco (média < 27.6)</p>';
            }

            document.getElementById('coordTurmasLoader').style.display = 'none';
            document.getElementById('coordAlunosLoader').style.display = 'none';
            document.getElementById('coordRiscoLoader').style.display = 'none';
          } else {
            showCoordError(data.message || 'Erro ao carregar dados');
          }
        } catch (e) {
          showCoordError('Erro ao processar resposta do servidor');
        }
      })
      .catch(error => {
        showCoordError('Erro de conexão com o servidor');
      });

    function showCoordError(message) {
      document.getElementById('coordTurmasLoader').style.display = 'none';
      document.getElementById('coordAlunosLoader').style.display = 'none';
      document.getElementById('coordRiscoLoader').style.display = 'none';

      document.getElementById('totalTurmasCoord').textContent = '0';
      document.getElementById('totalAlunosCoord').textContent = '0';
      document.getElementById('alunosRisco').textContent = '0';
      document.getElementById('alunosAprovadosCoord').textContent = '0';
      document.getElementById('alunosRiscoCoord').textContent = '0';

      const disciplinasRiscoList = document.getElementById('disciplinasRiscoList');
      disciplinasRiscoList.innerHTML = `<div class="alert alert-warning mb-0" role="alert">
        <i class="icon-warning me-2"></i>${message}
      </div>`;
    }
  </script>

<?php elseif ($userPanel == 'professor'): ?>
  <div class="row gx-3">
    <!-- Cards de Estatísticas do Professor -->
    <div class="col-12 mb-3">
      <h5 class="fw-bold"><i class="icon-school me-2"></i>Painel do Professor</h5>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-info rounded-circle">
              <i class="icon-book text-white fs-4"></i>
            </div>
            <div id="teacherDisciplinasCount" class="spinner-border spinner-border-sm text-info" role="status"></div>
          </div>
          <h3 id="totalDisciplinasProf" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Componentes Ativos</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-success rounded-circle">
              <i class="icon-people text-white fs-4"></i>
            </div>
            <div id="teacherAlunosCount" class="spinner-border spinner-border-sm text-success" role="status"></div>
          </div>
          <h3 id="totalAlunosProf" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Total de Alunos</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-warning rounded-circle">
              <i class="icon-calendar text-white fs-4"></i>
            </div>
            <div id="teacherAulasCount" class="spinner-border spinner-border-sm text-warning" role="status"></div>
          </div>
          <h3 id="aulasMinistradas" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Aulas neste Mês</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-primary rounded-circle">
              <i class="icon-assessment text-white fs-4"></i>
            </div>
            <div id="teacherFreqCount" class="spinner-border spinner-border-sm text-primary" role="status"></div>
          </div>
          <h3 id="frequenciaMedia" class="mb-1 fw-bold">-%</h3>
          <p class="text-muted mb-0 small">Frequência Média</p>
        </div>
      </div>
    </div>

    <!-- Card de Turmas do Professor -->
    <div class="col-12">
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold"><i class="icon-groups me-2"></i>Minhas Turmas e Componentes</h6>
        </div>
        <div class="card-body">
          <div class="d-flex flex-wrap gap-2" id="turmasList">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .bg-gradient-info {
      background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
    }

    .bg-gradient-success {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-warning {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .bg-gradient-primary {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .hover-card {
      transition: all 0.3s ease;
    }

    .hover-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
  </style>

  <script>
    // Carregar estatísticas do professor
    fetch('/dashboard/my-teacher-stats')
      .then(response => {
        if (!response.ok) {
          throw new Error('Erro na resposta do servidor: ' + response.status);
        }
        return response.text();
      })
      .then(text => {
        try {
          const data = JSON.parse(text);
          if (data.success) {
            document.getElementById('totalDisciplinasProf').textContent = data.data.total_disciplinas;
            document.getElementById('totalAlunosProf').textContent = data.data.total_alunos;
            document.getElementById('aulasMinistradas').textContent = data.data.aulas_ministradas_mes;
            document.getElementById('frequenciaMedia').textContent = data.data.frequencia_media + '%';

            // Ocultar spinners
            document.getElementById('teacherDisciplinasCount').style.display = 'none';
            document.getElementById('teacherAlunosCount').style.display = 'none';
            document.getElementById('teacherAulasCount').style.display = 'none';
            document.getElementById('teacherFreqCount').style.display = 'none';

            // Renderizar turmas
            const turmasList = document.getElementById('turmasList');
            turmasList.innerHTML = '';
            if (data.data.turmas && data.data.turmas.length > 0) {
              data.data.turmas.forEach(turma => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-primary';
                badge.textContent = turma;
                turmasList.appendChild(badge);
              });
            } else {
              turmasList.innerHTML = '<p class="text-muted mb-0">Nenhuma turma encontrada</p>';
            }
          } else {
            console.error('Erro no servidor:', data.message);
            showTeacherError(data.message || 'Erro ao carregar dados');
          }
        } catch (e) {
          console.error('Erro ao fazer parse do JSON:', e);
          console.error('Resposta recebida:', text);
          showTeacherError('Erro ao processar resposta do servidor');
        }
      })
      .catch(error => {
        console.error('Erro ao carregar estatísticas do professor:', error);
        showTeacherError('Erro de conexão com o servidor');
      });

    function showTeacherError(message) {
      document.getElementById('teacherDisciplinasCount').style.display = 'none';
      document.getElementById('teacherAlunosCount').style.display = 'none';
      document.getElementById('teacherAulasCount').style.display = 'none';
      document.getElementById('teacherFreqCount').style.display = 'none';

      document.getElementById('totalDisciplinasProf').textContent = '0';
      document.getElementById('totalAlunosProf').textContent = '0';
      document.getElementById('aulasMinistradas').textContent = '0';
      document.getElementById('frequenciaMedia').textContent = '0%';

      const turmasList = document.getElementById('turmasList');
      turmasList.innerHTML = `<div class="alert alert-warning mb-0" role="alert">
        <i class="icon-warning me-2"></i>${message}
      </div>`;
    }
  </script>

<?php elseif ($userPanel == 'estudante'): ?>
  <!-- Cards de Estatísticas do Estudante -->
  <div class="row gx-3">
    <div class="col-12 mb-3">
      <h5 class="fw-bold"><i class="icon-school me-2"></i>Painel do Estudante</h5>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-success rounded-circle">
              <i class="icon-assessment text-white fs-4"></i>
            </div>
            <div id="studentMediaLoader" class="spinner-border spinner-border-sm text-success" role="status"></div>
          </div>
          <h3 id="mediaGeral" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Média Geral</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-info rounded-circle">
              <i class="icon-check_circle text-white fs-4"></i>
            </div>
            <div id="studentFreqLoader" class="spinner-border spinner-border-sm text-info" role="status"></div>
          </div>
          <h3 id="frequenciaPercentual" class="mb-1 fw-bold">-%</h3>
          <p class="text-muted mb-0 small">Frequência</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="icons-box md bg-gradient-warning rounded-circle">
              <i class="icon-book text-white fs-4"></i>
            </div>
            <div id="studentDiscLoader" class="spinner-border spinner-border-sm text-warning" role="status"></div>
          </div>
          <h3 id="totalDisciplinasEst" class="mb-1 fw-bold">-</h3>
          <p class="text-muted mb-0 small">Componentes</p>
        </div>
      </div>
    </div>

    <div class="col-sm-6 col-lg-3">
      <div class="card mb-3 border-0 shadow-sm hover-card">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div id="situacaoIcon" class="icons-box md bg-gradient-primary rounded-circle">
              <i class="icon-info text-white fs-4"></i>
            </div>
            <div id="studentSitLoader" class="spinner-border spinner-border-sm text-primary" role="status"></div>
          </div>
          <h5 id="situacaoAcademica" class="mb-1 fw-bold">-</h5>
          <p class="text-muted mb-0 small">Situação</p>
        </div>
      </div>
    </div>

    <!-- Cards de Resumo Acadêmico -->
    <div class="col-md-6 col-lg-4">
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold"><i class="icon-emoji_events me-2 text-success"></i>Aprovações</h6>
        </div>
        <div class="card-body text-center">
          <div class="position-relative d-inline-block">
            <svg width="120" height="120" class="circular-progress">
              <circle cx="60" cy="60" r="50" stroke="#e9ecef" stroke-width="8" fill="none"></circle>
              <circle id="approvedCircle" cx="60" cy="60" r="50" stroke="#28a745" stroke-width="8" fill="none"
                stroke-dasharray="314" stroke-dashoffset="314"
                style="transition: stroke-dashoffset 1s ease; transform: rotate(-90deg); transform-origin: center;"></circle>
            </svg>
            <div class="position-absolute top-50 start-50 translate-middle">
              <h4 id="disciplinasAprovadas" class="mb-0 fw-bold text-success">-</h4>
            </div>
          </div>
          <p class="mt-2 mb-0 text-muted small">Componentes Aprovados</p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-4">
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold"><i class="icon-sync_problem me-2 text-warning"></i>Recuperação</h6>
        </div>
        <div class="card-body text-center">
          <div class="position-relative d-inline-block">
            <svg width="120" height="120" class="circular-progress">
              <circle cx="60" cy="60" r="50" stroke="#e9ecef" stroke-width="8" fill="none"></circle>
              <circle id="recoveryCircle" cx="60" cy="60" r="50" stroke="#ffc107" stroke-width="8" fill="none"
                stroke-dasharray="314" stroke-dashoffset="314"
                style="transition: stroke-dashoffset 1s ease; transform: rotate(-90deg); transform-origin: center;"></circle>
            </svg>
            <div class="position-absolute top-50 start-50 translate-middle">
              <h4 id="disciplinasRecuperacao" class="mb-0 fw-bold text-warning">-</h4>
            </div>
          </div>
          <p class="mt-2 mb-0 text-muted small">Em Recuperação</p>
        </div>
      </div>
    </div>

    <div class="col-md-12 col-lg-4">
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold"><i class="icon-cancel me-2 text-danger"></i>Faltas</h6>
        </div>
        <div class="card-body text-center">
          <div class="mb-3">
            <h2 id="totalFaltas" class="mb-0 fw-bold text-danger">-</h2>
            <p class="text-muted small mb-0">Total de Ausências</p>
          </div>
          <div class="progress" style="height: 10px;">
            <div id="faltasProgress" class="progress-bar bg-danger" role="progressbar" style="width: 0%"></div>
          </div>
          <p class="mt-2 mb-0 text-muted small">Limite: 25% de faltas</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Carregar estatísticas do estudante
    fetch('/dashboard/my-student-stats')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const stats = data.data;

          // Atualizar cards principais
          document.getElementById('mediaGeral').textContent = stats.media_geral.toFixed(1);
          document.getElementById('frequenciaPercentual').textContent = stats.frequencia_percentual.toFixed(1) + '%';
          document.getElementById('totalDisciplinasEst').textContent = stats.total_disciplinas;
          document.getElementById('situacaoAcademica').textContent = stats.situacao;

          // Atualizar ícone de situação
          const situacaoIcon = document.getElementById('situacaoIcon');
          const situacaoEl = document.getElementById('situacaoAcademica');
          if (stats.situacao === 'Aprovado') {
            situacaoIcon.className = 'icons-box md bg-success rounded-circle';
            situacaoEl.className = 'mb-1 fw-bold text-success';
          } else if (stats.situacao === 'Reprovado') {
            situacaoIcon.className = 'icons-box md bg-danger rounded-circle';
            situacaoEl.className = 'mb-1 fw-bold text-danger';
          } else if (stats.situacao === 'Recuperação') {
            situacaoIcon.className = 'icons-box md bg-warning rounded-circle';
            situacaoEl.className = 'mb-1 fw-bold text-warning';
          }

          // Atualizar aprovações com animação circular
          document.getElementById('disciplinasAprovadas').textContent = stats.disciplinas_aprovadas;
          const approvedPercent = stats.total_disciplinas > 0 ? (stats.disciplinas_aprovadas / stats.total_disciplinas) : 0;
          const approvedCircle = document.getElementById('approvedCircle');
          approvedCircle.style.strokeDashoffset = 314 - (314 * approvedPercent);

          // Atualizar recuperação com animação circular
          document.getElementById('disciplinasRecuperacao').textContent = stats.disciplinas_recuperacao;
          const recoveryPercent = stats.total_disciplinas > 0 ? (stats.disciplinas_recuperacao / stats.total_disciplinas) : 0;
          const recoveryCircle = document.getElementById('recoveryCircle');
          recoveryCircle.style.strokeDashoffset = 314 - (314 * recoveryPercent);

          // Atualizar faltas
          document.getElementById('totalFaltas').textContent = stats.total_faltas;
          const faltasPercent = 100 - stats.frequencia_percentual;
          document.getElementById('faltasProgress').style.width = faltasPercent + '%';

          // Ocultar loaders
          document.getElementById('studentMediaLoader').style.display = 'none';
          document.getElementById('studentFreqLoader').style.display = 'none';
          document.getElementById('studentDiscLoader').style.display = 'none';
          document.getElementById('studentSitLoader').style.display = 'none';
        }
      })
      .catch(error => {
        console.error('Erro ao carregar estatísticas do estudante:', error);
        document.getElementById('studentMediaLoader').style.display = 'none';
        document.getElementById('studentFreqLoader').style.display = 'none';
        document.getElementById('studentDiscLoader').style.display = 'none';
        document.getElementById('studentSitLoader').style.display = 'none';
      });
  </script>

  <div class="row">
    <div class="col-xl-6">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="card-title">Gráficos de Faltas</h5>
        </div>
        <div class="card-body">
          <div id="attendanceChart" class="chart-height-xl">
            <div id="attendanceLoading" class="chart-loader" style="height: 350px;">
              <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Carregando...</span>
              </div>
              <div class="loading-text">Calculando frequência escolar...</div>
              <div class="loading-progress"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Desempenho</h5>
          <div id="scoresByDiscipline" class="auto-align-graph">
            <div id="scoresByDisciplineLoading" class="chart-loader" style="height: 250px;">
              <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Carregando...</span>
              </div>
              <div class="loading-text">Organizando notas por disciplina...</div>
              <div class="loading-progress"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>

<script>
  // Função global para ocultar loading
  function hideLoading(loadingId) {
    const loadingElement = document.getElementById(loadingId);
    if (loadingElement) {
      loadingElement.style.display = 'none';
    }
  }

  // Função global para mostrar erro quando o gráfico falha
  function showError(chartId, message) {
    const chartElement = document.getElementById(chartId);
    if (chartElement) {
      chartElement.innerHTML = `
        <div class="error-state">
          <i class="fas fa-exclamation-triangle fs-2 mb-3"></i>
          <h6>Ops! Algo deu errado</h6>
          <p class="mb-3">${message}</p>
          <button class="btn btn-sm btn-outline-danger" onclick="location.reload()">
            <i class="fas fa-redo me-1"></i>
            Tentar novamente
          </button>
        </div>
      `;
    }
  }

  // Funções globais para o gráfico de reprovações por disciplina
  function loadCoordinatorClasses() {
    fetch('/dashboard/coordinator-classes')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const select = document.getElementById('classSelect');
          if (select) {
            select.innerHTML = '<option value="">Todas as turmas</option>';
            data.data.forEach(turma => {
              const option = document.createElement('option');
              option.value = turma.id;
              option.textContent = `${turma.nome} (${turma.turno}) - ${turma.total_estudantes} alunos`;
              select.appendChild(option);
            });
          }
        }
      })
      .catch(error => {
        console.error('Erro ao carregar turmas:', error);
      });
  }

  function loadFailedStudentsChart() {
    const classSelect = document.getElementById('classSelect');
    if (!classSelect) return;

    const classId = classSelect.value;
    const params = new URLSearchParams();
    if (classId) {
      params.append('turma_id', classId);
    }

    fetch(`/dashboard/failed-students-by-discipline?${params}`)
      .then(response => response.json())
      .then(data => {
        hideLoading('failedStudentsLoading');
        if (data.success) {
          renderFailedStudentsChart(data.data);
        } else {
          console.error('Erro ao carregar dados do gráfico:', data.message);
          showError('failedStudentsChart', 'Erro ao carregar dados do gráfico');
        }
      })
      .catch(error => {
        hideLoading('failedStudentsLoading');
        console.error('Erro na requisição:', error);
        showError('failedStudentsChart', 'Erro de conexão');
      });
  }

  function renderFailedStudentsChart(chartData) {
    // Limpar gráfico existente se houver
    const chartElement = document.querySelector("#failedStudentsChart");
    if (chartElement && chartElement._chart) {
      chartElement._chart.destroy();
    }

    var options = {
      series: [{
        name: 'Alunos Reprovados',
        data: chartData.map(item => parseInt(item.quantidade_reprovados))
      }],
      chart: {
        type: 'bar',
        height: 350,
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          horizontal: true,
          columnWidth: '55%',
          endingShape: 'rounded',
          borderRadius: 4,
          dataLabels: {
            position: 'top',
          }
        },
      },
      dataLabels: {
        enabled: true,
        style: {
          fontSize: '12px',
          colors: ["#304758"]
        }
      },
      stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
      },
      xaxis: {
        categories: chartData.map(item => item.disciplina),
        title: {
          text: 'Quantidade de Alunos'
        }
      },
      yaxis: {
        title: {
          text: 'Disciplinas'
        }
      },
      fill: {
        opacity: 1,
        colors: ['#ffc107']
      },
      tooltip: {
        y: {
          formatter: function(val) {
            return val + " alunos reprovados"
          }
        }
      },
      title: {
        text: 'Reprovações por Disciplina (Soma dos 4 Bimestres < 27.6)',
        align: 'center',
        style: {
          fontSize: '14px',
          fontWeight: 'bold',
          color: '#333'
        }
      },
      grid: {
        borderColor: '#e7e7e7',
        row: {
          colors: ['#f3f3f3', 'transparent'],
          opacity: 0.5
        },
      }
    };

    var failedStudentsChart = new ApexCharts(document.querySelector("#failedStudentsChart"), options);
    chartElement._chart = failedStudentsChart; // Salvar referência para destruir depois
    failedStudentsChart.render();
  }

  $(document).ready(function() {
    // Sistema de mensagens rotativas para loading
    const loadingMessages = {
      'scoresByClassLoading': [
        'Compilando dados de desempenho...',
        'Calculando médias por turma...',
        'Gerando estatísticas...'
      ],
      'monthlyFeesLoading': [
        'Processando dados financeiros...',
        'Verificando status de pagamentos...',
        'Calculando percentuais...'
      ],
      'attendanceLoading': [
        'Calculando frequência escolar...',
        'Analisando dados de presença...',
        'Processando faltas...'
      ],
      'scoresByDisciplineLoading': [
        'Organizando notas por disciplina...',
        'Calculando médias individuais...',
        'Preparando visualização...'
      ],
      'failedStudentsLoading': [
        'Analisando reprovações por disciplina...',
        'Calculando soma dos 4 bimestres...',
        'Identificando alunos com soma < 27.6...'
      ]
    };

    // Função para rotacionar mensagens de loading
    function rotateLoadingMessages() {
      Object.keys(loadingMessages).forEach(loadingId => {
        const element = document.getElementById(loadingId);
        if (element && element.style.display !== 'none') {
          const textElement = element.querySelector('.loading-text');
          if (textElement) {
            const messages = loadingMessages[loadingId];
            let currentIndex = parseInt(textElement.dataset.index || '0');
            currentIndex = (currentIndex + 1) % messages.length;
            textElement.textContent = messages[currentIndex];
            textElement.dataset.index = currentIndex;
          }
        }
      });
    }

    // Rotacionar mensagens a cada 1.5 segundos
    const messageRotationInterval = setInterval(rotateLoadingMessages, 1500);

    // Parar rotação após 5 segundos
    setTimeout(() => {
      clearInterval(messageRotationInterval);
    }, 5000);

    <?php if (hasPermission("financeiro")): ?>
      // Carregar gráfico de mensalidades
      setTimeout(function() {
        hideLoading('monthlyFeesLoading');

        Morris.Donut({
          element: "donutFormatter",
          data: [{
              value: "<?= $pending_monthly ?? 0; ?>",
              label: "Pendentes",
              formatted: "<?= $percentual_pending ?? 0; ?>%"
            },
            {
              value: "<?= $late_monthly ?? 0; ?>",
              label: "Atrasado",
              formatted: "<?= $percentual_late ?? 0; ?>%"
            },
            {
              value: "<?= $paid_monthly ?? 0; ?>",
              label: "Pago",
              formatted: "<?= $percentual_paid ?? 0; ?>%"
            },
            {
              value: "<?= $canceled_monthly ?? 0; ?>",
              label: "Cancelado",
              formatted: "<?= $percentual_canceled ?? 0; ?>%"
            }
          ],
          resize: true,
          hideHover: "auto",
          formatter: function(x, data) {
            return data.formatted;
          },
          labelColor: "#507D0C",
          colors: ["#00abf1", "#e66100", "#34a853", "#e94235"]
        });
      }, 1500);
    <?php endif; ?>

    <?php if ($userPanel == 'estudante'): ?>
      // Carregar gráfico de frequência
      setTimeout(function() {
        hideLoading('attendanceLoading');

        Morris.Donut({
          element: "attendanceChart",
          data: [{
              value: "<?= $total_faltas ?? 0 ?>",
              label: "Faltas",
              formatted: "<?= $percentual_faltas ?? 0 ?>%"
            },
            {
              value: "<?= $presenca ?? 1 ?>",
              label: "Presença",
              formatted: "<?= $percentual_presenca ?? 100 ?>%"
            }
          ],
          resize: true,
          hideHover: "auto",
          formatter: function(x, data) {
            return data.formatted;
          },
          labelColor: "#507D0C",
          colors: ["#e94235", "#34a853"]
        });
      }, 1200);

      // Carregar gráfico de desempenho por disciplina
      setTimeout(function() {
        hideLoading('scoresByDisciplineLoading');

        var notas = JSON.parse('<?= json_encode($notas ?? []) ?>');
        var options = {
          chart: {
            height: 350,
            width: "100%",
            type: "bar",
            toolbar: {
              show: false,
            },
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: "60%",
              borderRadius: 8,
            },
          },
          dataLabels: {
            enabled: false,
          },
          stroke: {
            show: true,
            width: 0,
            colors: ["#ec5757"],
          },
          series: [{
            name: "Pontos",
            data: notas.map((item) => item.total),
          }],
          legend: {
            show: false,
          },
          xaxis: {
            categories: notas.map((item) => item.nome),
          },
          yaxis: {
            show: false,
          },
          fill: {
            colors: ["#e73737"],
          },
          tooltip: {
            y: {
              formatter: function(val) {
                return +val;
              },
            },
          },
          grid: {
            borderColor: "#c8cfcc",
            strokeDashArray: 5,
            xaxis: {
              lines: {
                show: true,
              },
            },
            yaxis: {
              lines: {
                show: false,
              },
            },
            padding: {
              top: 0,
              right: 0,
              bottom: -10,
              left: 0,
            },
          },
        };
        var chart = new ApexCharts(document.querySelector("#scoresByDiscipline"), options);
        chart.render();
      }, 1800);
    <?php endif; ?>

    // Carregar dados do gráfico de reprovações quando a página carregar
    if (document.getElementById('classSelect')) {
      loadCoordinatorClasses();
      loadFailedStudentsChart();
    }
  });
</script>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>