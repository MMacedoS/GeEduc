<?php

use App\Config\AppServiceProvider;
use App\Config\Auth;
use App\Config\Container;
use App\Config\Router;
use App\Controllers\v1\Activitie\AtividadeController;
use App\Controllers\v1\Bank_account\ContaBancariaController;
use App\Controllers\v1\Calendar\DiaLetivoController;
use App\Controllers\v1\ClassRooms\AulaController;
use App\Controllers\v1\Contracts\ContratoController;
use App\Controllers\v1\GradeReport\GradeReportController;
use App\Controllers\v1\ClassRooms\TurmaController;
use App\Controllers\v1\Dashboard\DashboardController;
use App\Controllers\v1\Permission\PermissaoController;
use App\Controllers\v1\Plan\PlanoController;
use App\Controllers\v1\Profile\UsuarioController;
use App\Controllers\v1\Teacher\ProfessorController;
use App\Controllers\v1\Teacher\ProfessorDisciplinaController;
use App\Controllers\v1\Discipline\DisciplinaController;
use App\Controllers\v1\ClassRooms\TurmaDisciplinaController;
use App\Controllers\v1\MonthlyFees\MensalidadeController;
use App\Controllers\v1\Student\EstudanteController;
use App\Controllers\v1\Work_Load\CargaHorariaController;
use App\Controllers\v1\Student\EstudanteMensalidadeController;
use App\Controllers\v1\Student\EstudanteTurmaController;
use App\Controllers\v1\Site\Event\SiteEventoController;
use App\Controllers\v1\Site\Carousel\SiteCarrosselController;
use App\Controllers\v1\Coordination\CoordenadorController;
use App\Controllers\v1\Frequencies\FrequenciaController;
use App\Controllers\v1\Period\PeriodoController;
use App\Controllers\v1\Person\PessoaContatoController;
use App\Controllers\v1\Profile\RecuperarSenhaController;
use App\Controllers\v1\Scores\NotaController;
use App\Controllers\v1\Site\Album\SiteAlbumController;

$router = new Router();
$auth = new Auth();

$container = new Container();
$appServiceProvider = new AppServiceProvider($container);
$appServiceProvider->registerDependencies();

/////created container active
$atividadeController = $container->get(AtividadeController::class);

$aulaController = $container->get(AulaController::class);

/////created container coodenador
$coordenadorController = $container->get(CoordenadorController::class);
$cargaHorariaController = $container->get(CargaHorariaController::class);
$contaBancariaController = $container->get(ContaBancariaController::class);

$contratoController = $container->get(ContratoController::class);

/////created container dash
$dashboardController = $container->get(DashboardController::class);
$disciplinaController = $container->get(DisciplinaController::class);
$disciplinaController = $container->get(DisciplinaController::class);
$diaLetivoController = $container->get(DiaLetivoController::class);

/////created container studants
$estudanteController = $container->get(EstudanteController::class);
$estudanteTurmaController = $container->get(EstudanteTurmaController::class);
$estudanteMensalidadeController = $container->get(EstudanteMensalidadeController::class);

$frequenciaController = $container->get(FrequenciaController::class);

$gradeReportController = $container->get(GradeReportController::class);

$mensalidadeController = $container->get(MensalidadeController::class);

$notaController = $container->get(NotaController::class);

$planoController = $container->get(PlanoController::class);
$periodoController = $container->get(PeriodoController::class);
$professorController = $container->get(ProfessorController::class);
$permissaoController = $container->get(PermissaoController::class);
$pessoaContatoController = $container->get(PessoaContatoController::class);
$professorDisciplinaController = $container->get(ProfessorDisciplinaController::class);

$recuperarSenhaController = $container->get(RecuperarSenhaController::class);

$siteAlbumController = $container->get(SiteAlbumController::class);
$siteEventoController = $container->get(SiteEventoController::class);
$siteCarrosselController = $container->get(SiteCarrosselController::class);

$turmaController = $container->get(TurmaController::class);
$turmaDisciplinaController = $container->get(TurmaDisciplinaController::class);

$usuarioController = $container->get(UsuarioController::class);

/////routes
require_once "login/loginRouters.php";

require_once "dashboard/dashboardRouters.php";

//users
require_once "user/usersRouters.php";

//permissions
require_once "permission/permissionsRouters.php";

require_once "calendar/diaLetivoRouter.php";

//teachers
require_once "teacher/teacherRouters.php";

//students
require_once "student/studentsRouters.php";

//plans
require_once "plans/plansRouters.php";

//classrooms
require_once "classRoom/classRoomRouters.php";

//work_load
require_once "workload/workloadsRouters.php";

//banks
require_once "bank/banksRouters.php";

//disciplines
require_once "discipline/disciplinesRouters.php";

//student-class
require_once "student/studentsClassRouter.php";

//student-monthlyfees
require_once "student/studentsMonthylessRouters.php";

// Coordenação
require_once "coordination/coordinationsRouters.php";

//periods
require_once "period/periodsRouters.php";

//activities-teacher
require_once "myComponent/myComponents.php";

//site
require_once "site/siteEvents.php";
require_once "site/siteCarousel.php";

//minha-turma-estudantes
require_once "myClassRoom/myClassRoomsRouters.php";

//mensalidades
require_once "monthylees/monthyleesRouters.php";

// pessoa_contato
require_once "person/personRouters.php";

require_once "reports/reportsRouters.php";

require_once "profile/profileRouters.php";

require_once "contract/contractsRouters.php";

require_once "integrations/integrationAutentique.php";

return $router;

