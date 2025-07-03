<?php require_once __DIR__ . '/../layout/top.php'; ?>

<? if(isset($_GET['error'])){?>
    <div class="alert border border-danger alert-dismissible fade show text-danger" role="alert">
       <b>Sem permissão!</b>.
       <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<? }?>
<!-- Row start -->  
<?php if (hasPermission("visualizar_cards_dashboard")) {?>
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
                <h2 class="m-0 lh-1"><?=isset($estudante_turmas) ? count($estudante_turmas) : 0?></h2>
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
                <h2 class="m-0 lh-1"><?=isset($turmas) ? count($turmas) : 0 ?></h2>
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
                <h2 class="m-0 lh-1"><?=isset($discipline) ? count($discipline) : 0?></h2>
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
              <h2 class="m-0 lh-1"><?=isset($teachers) ? count($teachers) : 0 ?></h2>
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
<?} ?>

<?php if (hasPermission("professor")) {?>
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
                <h2 class="m-0 lh-1"><?=isset($turmas) ? count($turmas) : 0 ?></h2>
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
                <h2 class="m-0 lh-1"><?=isset($discipline) ? count($discipline) : 0?></h2>
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
<?} ?>

<?php if (hasPermission("estudante") || hasPermission("responsavel_legal")) { ?>
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
      <a href="{{route('admin.news')}}">
        <div class="card mb-3">
          <div class="card-body">
            <div class="d-flex mb-2">
              <div class="icons-box md bg-primary rounded-5 me-3">
                <i class="icon-add_task fs-4 text-white"></i>
              </div>
              <div class="d-flex flex-column">                
                <h2 class="m-0 lh-1 d-block d-sm-none">Notificações</h2>
                <h2 class="m-0 lh-1 d-none d-xl-block d-lg-block d-md-block"> Disponiveis</h2>
                <p class="m-0 opacity-50"></p>
              </div>
            </div>
            <div class="m-0">
                <div class="progress thin mb-2">
                <div class="progress-bar bg-primary" role="progressbar" style="width: 0%" aria-valuenow="0"
                    aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="m-0 small fw-light opacity-75">0%.</p>
            </div>
          </div>
        </div>
      </a>
    </div>
  </div>
<?} ?>

<?php require_once __DIR__ . '/../layout/bottom.php'; ?>

<?php if (hasPermission("visualizar cards dashboard")) { ?>
    <script>
        Morris.Donut({
            element: "donutFormatter",
            data: [
                {
                    value: "<?=$pending_monthly; ?>",
                    label: "Pendentes",
                    formatted: "<?=$percentual_pending; ?>%"
                },
                {
                    value: "<?=$late_monthly; ?>",
                    label: "Atrasado",
                    formatted: "<?php echo $percentual_late; ?>%"
                },
                {
                    value: "<?=$paid_monthly; ?>",
                    label: "Pago",
                    formatted: "<?php echo $percentual_paid; ?>%"
                },
                {
                    value: "<?=$canceled_monthly; ?>",
                    label: "Cancelado",
                    formatted: "<?php echo $percentual_canceled; ?>%"
                }
            ],
            resize: true,
            hideHover: "auto",
            formatter: function (x, data) {
                return data.formatted;
            },
            labelColor: "#507D0C",
            colors: ["#00abf1", "#e66100", "#34a853", "#e94235"]
        });
    </script>
<? } ?>

<?php if (hasPermission("estudante")) { ?>
    <script>
        Morris.Donut({
            element: "donutFormatter",
            data: [
                {
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
            formatter: function (x, data) {
                return data.formatted;
            },
            labelColor: "#507D0C",
            colors: ["#e94235", "#34a853"]
        });
    </script>
<? } ?>