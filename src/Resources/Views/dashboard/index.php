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
<?php if (hasPermission("visualizar_cards_dashboard")): ?>
  <div class="row gx-3">
    <div class="col-sm-3 col-12">
      <a href="\estudantes">
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-success rounded-5 me-3">
                <i class="icon-add_task fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($estudante_turmas) ? count($estudante_turmas) : 0 ?></h2>
                <p class="m-0 opacity-50">Estudantes</p>
              </div>
            </div>
            <div class="m-0 d-none">
              <div class="progress thin mb-2">
                <div class="progress-bar bg-info" role="progressbar" style="width: 70%" aria-valuenow="70"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="m-0 small fw-light opacity-75">70 percent completed.</p>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-3 col-12">
      <a href="\turmas">
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-info rounded-5 me-3">
                <i class="icon-add_task fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($turmas) ? count($turmas) : 0 ?></h2>
                <p class="m-0 opacity-50">Turmas</p>
              </div>
            </div>
            <div class="m-0 d-none">
              <div class="progress thin mb-2">
                <div class="progress-bar bg-info" role="progressbar" style="width: 70%" aria-valuenow="70"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="m-0 small fw-light opacity-75">70 percent completed.</p>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-3 col-12">
      <a href="\disciplinas">
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-danger rounded-5 me-3">
                <i class="icon-add_task fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($discipline) ? count($discipline) : 0 ?></h2>
                <p class="m-0 opacity-50">Disciplinas</p>
              </div>
            </div>
            <div class="m-0 d-none">
              <div class="progress thin mb-2">
                <div class="progress-bar bg-danger" role="progressbar" style="width: 80%" aria-valuenow="80"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="m-0 small fw-light opacity-75">80 percent completed.</p>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-3 col-12">
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex mb-2">
            <div class="icons-box md bg-warning rounded-5 me-3">
              <i class="icon-add_task fs-4 text-white"></i>
            </div>
            <div class="d-flex flex-column">
              <h2 class="m-0 lh-1"><?= isset($teachers) ? count($teachers) : 0 ?></h2>
              <p class="m-0 opacity-50">Professores</p>
            </div>
          </div>
          <div class="m-0 d-none">
            <div class="progress thin mb-2">
              <div class="progress-bar bg-success" role="progressbar" style="width: 90%" aria-valuenow="90"
                aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="m-0 small fw-light opacity-75">90 percent completed.</p>
          </div>
        </div>
      </div>
    </div>


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
  </div>
<? endif; ?>

<?
if (hasPermission('coordenacao')):
?>
  <div class="col-xl-6">
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title">Desempenho</h5>
        <div id="scoresByClass" class="auto-align-graph">
          <div id="scoresByClassLoading" class="chart-loader" style="height: 250px;">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Carregando...</span>
            </div>
            <div class="loading-text">Compilando dados de desempenho...</div>
            <div class="loading-progress"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?
endif;
?>

<?
if (hasPermission('financeiro')):
?>
  <div class="row">
    <div class="col-xl-6">
      <div class="card mb-3">
        <div class="card-header">
          <h5 class="card-title">Gráficos de Mensalidades</h5>
        </div>
        <div class="card-body">
          <div id="donutFormatter" class="chart-height-xl">
            <div id="monthlyFeesLoading" class="chart-loader" style="height: 350px;">
              <div class="spinner-border text-info" role="status">
                <span class="visually-hidden">Carregando...</span>
              </div>
              <div class="loading-text">Processando dados financeiros...</div>
              <div class="loading-progress"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?
endif;
?>

<?php if (hasPermission("professor") && !hasPermission("coordenador")): ?>
  <div class="row gx-3">
    <div class="col-sm-3 col-12">
      <a href="\meus-componentes">
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-info rounded-5 me-3">
                <i class="icon-add_task fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($turmas) ? count($turmas) : 0 ?></h2>
                <p class="m-0 opacity-50">Turmas</p>
              </div>
            </div>
            <div class="m-0 d-none">
              <div class="progress thin mb-2">
                <div class="progress-bar bg-info" role="progressbar" style="width: 70%" aria-valuenow="70"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="m-0 small fw-light opacity-75">70 percent completed.</p>
            </div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-sm-3 col-12">
      <a href="\meus-componentes">
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-danger rounded-5 me-3">
                <i class="icon-add_task fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">
                <h2 class="m-0 lh-1"><?= isset($discipline) ? count($discipline) : 0 ?></h2>
                <p class="m-0 opacity-50">Disciplinas</p>
              </div>
            </div>
            <div class="m-0 d-none">
              <div class="progress thin mb-2">
                <div class="progress-bar bg-danger" role="progressbar" style="width: 80%" aria-valuenow="80"
                  aria-valuemin="0" aria-valuemax="100"></div>
              </div>
              <p class="m-0 small fw-light opacity-75">80 percent completed.</p>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
<? endif; ?>

<?php if (hasPermission("estudante") || hasPermission("responsavel_legal")): ?>
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
<? endif; ?>

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

    <?php if (hasPermission("estudante") || hasPermission("responsavel_legal")): ?>
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