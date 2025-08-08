<?php require_once __DIR__ . '/../layout/top.php'; ?>

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
  </div>
<? endif; ?>

<?
if (hasPermission('coordenacao')):
?>
  <div class="col-xl-6">
    <div class="card mb-3">
      <div class="card-body">
        <h5 class="card-title">Desempenho</h5>
        <div id="scoresByClass" class="auto-align-graph"></div>
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
          <div id="donutFormatter" class="chart-height-xl"></div>
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
          <div id="donutFormatter" class="chart-height-xl"></div>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <div class="card mb-3">
        <div class="card-body">
          <h5 class="card-title">Desempenho</h5>
          <div id="scoresByDiscipline" class="auto-align-graph"></div>
        </div>
      </div>
    </div>
  </div>
<? endif; ?>

<?php require_once __DIR__ . '/../layout/bottom.php';
?>

<?php if (hasPermission("visualizar cards dashboard")): ?>
  <script>
    Morris.Donut({
      element: "donutFormatter",
      data: [{
          value: "<?= $pending_monthly; ?>",
          label: "Pendentes",
          formatted: "<?= $percentual_pending; ?>%"
        },
        {
          value: "<?= $late_monthly; ?>",
          label: "Atrasado",
          formatted: "<?php echo $percentual_late; ?>%"
        },
        {
          value: "<?= $paid_monthly; ?>",
          label: "Pago",
          formatted: "<?php echo $percentual_paid; ?>%"
        },
        {
          value: "<?= $canceled_monthly; ?>",
          label: "Cancelado",
          formatted: "<?php echo $percentual_canceled; ?>%"
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
  </script>
<? endif; ?>

<?php if (hasPermission("estudante")): ?>
  <script>
    Morris.Donut({
      element: "donutFormatter",
      data: [{
          value: "<?= $total_faltas ?? 0 ?>",
          label: "Faltas",
          formatted: "<?php echo $percentual_faltas ?? 0  ?>%"
        },
        {
          value: "<?= $presenca ?? 1 ?>",
          label: "Presença",
          formatted: "<?php echo $percentual_presenca ?? 100 ?>%"
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
  </script>
  <script>
    var notas = JSON.parse('<?= json_encode($notas) ?>');
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
      }, ],
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
  </script>
<? endif; ?>