<?php

use App\Config\Auth;
use App\Config\Router;
use App\Controllers\v1\Activitie\AtividadeController;
use App\Controllers\v1\Bank_account\ContaBancariaController;
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
use App\Controllers\v1\Scores\NotaController;
use App\Controllers\v1\Site\Album\SiteAlbumController;

$router = new Router();
$auth = new Auth();
$dashboardController = new DashboardController();
$usuarioController = new UsuarioController();
$professorController = new ProfessorController();
$professorDisciplinaController = new ProfessorDisciplinaController();
$estudanteController = new EstudanteController();
$permissaoController = new PermissaoController();
$planoController = new PlanoController();
$turmaController = new TurmaController();
$contaBancariaController = new ContaBancariaController();
$cargaHorariaController = new CargaHorariaController();
$disciplinaController = new DisciplinaController();
$periodoController = new PeriodoController();
$estudanteTurmaController = new EstudanteTurmaController();
$mensalidadeController = new MensalidadeController();
$estudanteMensalidadeController = new EstudanteMensalidadeController();
$turmaDisciplinaController = new TurmaDisciplinaController();
$coordenadorController = new CoordenadorController();
$atividadeController = new AtividadeController();
$siteEventoController = new SiteEventoController();
$siteCarrosselController = new SiteCarrosselController();
$frequenciaController = new FrequenciaController();
$pessoaContatoController = new PessoaContatoController();
$notaController = new NotaController();
$gradeReportController = new GradeReportController();
$siteAlbumController = new SiteAlbumController();

$router->create("GET", "/", [$usuarioController, "login"], null);
$router->create("POST", "/login", [$usuarioController, "auth"]);
$router->create("GET", "/logout", [$usuarioController, "logout"], $auth);

$router->create("GET", "/dashboard", [$dashboardController, "index"], $auth);

//users
$router->create("POST", "/usuario/{id}/deletar",[$usuarioController, "delete"], $auth);
$router->create("GET", "/usuario/{id}/editar",[$usuarioController, "edit"], $auth);
$router->create("POST", "/usuario/{id}/upt", [$usuarioController, "update"], $auth);
$router->create("GET", "/usuario/{id}/permissao", [$usuarioController, "permissao"], $auth);
$router->create("POST", "/usuario/{id}/permissao", [$usuarioController, "add_permissao"], $auth);
$router->create("POST", "/usuario/add/", [$usuarioController, "store"], $auth);
$router->create("GET", "/usuario/criar", [$usuarioController, "create"], $auth);
$router->create("GET", "/usuario/{request}", [$usuarioController, "index"], $auth);
$router->create("GET", "/usuario/", [$usuarioController, "index"], $auth);

//permissions
$router->create("POST", "/permissao/{id}/deletar", [$permissaoController, "delete"], $auth);
$router->create("GET", "/permissao/{id}/editar",[$permissaoController, "edit"], $auth);
$router->create("POST", "/permissao/{id}/upt", [$permissaoController, "update"], $auth);
$router->create("POST", "/permissao/add/", [$permissaoController, "store"], $auth);
$router->create("GET", "/permissao/criar", [$permissaoController, "create"], $auth);
$router->create("GET", "/permissao/{request}", [$permissaoController, "index"], $auth);
$router->create("GET", "/permissao/", [$permissaoController, "index"], $auth);

//teachers
$router->create("GET", "/professores", [$professorController, "index"], $auth);
$router->create("GET", "/professores/criar", [$professorController, "create"], $auth);
$router->create("POST","/professores/criar", [$professorController, "store"], $auth);
$router->create("GET", "/professores/{id}/editar", [$professorController, "edit"], $auth);
$router->create("POST", "/professores/{id}/editar", [$professorController, "update"], $auth);
$router->create("DELETE", "/professores/{id}", [$professorController, "destroy"], $auth);

//students
$router->create("GET", "/estudantes", [$estudanteController, "index"], $auth);
$router->create('GET', "/estudante/excel", [$estudanteController, 'createExcel'], $auth);
$router->create("GET", "/estudante", [$estudanteController, "create"], $auth);
$router->create("POST", "/estudante",[$estudanteController, "store"], $auth);
$router->create("POST", "/estudante/excel",[$estudanteController, "storeExcel"], $auth);
$router->create("GET","/estudante/{id}", [$estudanteController, "edit"], $auth);
$router->create("POST", "/estudante/{id}", [$estudanteController, "update"], $auth);
$router->create("DELETE", "/estudante/{id}", [$estudanteController, "destroy"], $auth);

//plans
$router->create("GET", "/planos", [$planoController, "index"], $auth);
$router->create("GET", "/planos/criar", [$planoController, "create"], $auth);
$router->create("POST", "/planos/criar", [$planoController, "store"], $auth);
$router->create("GET", "/planos/{id}/editar", [$planoController, "edit"], $auth);
$router->create( "POST", "/planos/{id}/editar", [$planoController, "update"], $auth);
$router->create("DELETE", "/planos/{id}", [$planoController, "destroy"], $auth);

//classrooms
$router->create("GET", "/turmas", [$turmaController, "index"], $auth);
$router->create("GET", "/turma", [$turmaController, "create"], $auth);
$router->create("POST", "/turma", [$turmaController, "store"], $auth);
$router->create( "GET", "/turma/{id}", [$turmaController, "edit"], $auth);
$router->create( "POST", "/turma/{id}", [$turmaController, "update"], $auth);
$router->create("DELETE", "/turma/{id}", [$turmaController, "destroy"], $auth);

//work_load
$router->create( "GET", "/carga-horaria", [$cargaHorariaController, "index"], $auth);
$router->create( "GET", "/carga-horaria/criar", [$cargaHorariaController, "create"], $auth);
$router->create( "POST", "/carga-horaria/criar", [$cargaHorariaController, "store"], $auth);
$router->create( "GET", "/carga-horaria/{id}/editar", [$cargaHorariaController, "edit"], $auth);
$router->create( "POST", "/carga-horaria/{id}/editar", [$cargaHorariaController, "update"], $auth);
$router->create( "DELETE", "/carga-horaria/{id}", [$cargaHorariaController, "destroy"], $auth);

//banks
$router->create("GET", "/bancos", [$contaBancariaController, "index"], $auth);
$router->create( "GET", "/bancos/criar", [$contaBancariaController, "create"], $auth);
$router->create( "POST", "/bancos/criar", [$contaBancariaController, "store"], $auth);
$router->create( "GET", "/bancos/{id}/editar", [$contaBancariaController, "edit"], $auth);
$router->create( "POST", "/bancos/{id}/editar", [$contaBancariaController, "update"], $auth);
$router->create( "DELETE", "/bancos/{id}", [$contaBancariaController, "destroy"], $auth);

//disciplines
$router->create("GET", "/disciplinas", [$disciplinaController, "index"], $auth);
$router->create( "GET", "/disciplinas/criar", [$disciplinaController, "create"], $auth);
$router->create( "POST", "/disciplinas/criar", [$disciplinaController, "store"], $auth);
$router->create( "GET", "/disciplinas/{id}/editar", [$disciplinaController, "edit"], $auth);
$router->create( "POST", "/disciplinas/{id}/editar", [$disciplinaController, "update"], $auth);
$router->create( "DELETE", "/disciplinas/{id}", [$disciplinaController, "destroy"], $auth);

//student-class
$router->create( "GET", "/estudantes/{id}/turma", [$estudanteTurmaController, "studentLinkClass"], $auth);
$router->create( "POST", "/estudantes/{id}/turma/{id}", [$estudanteTurmaController, "store"], $auth);
$router->create( "PUT", "/estudantes-class/{id}", [$estudanteTurmaController, "updateStatus"], $auth);

//student-monthlyfees
$router->create( "GET", "/estudantes/{id}/mensalidades", [$estudanteMensalidadeController, "index"], $auth);
$router->create( "GET", "/estudantes/{id}/mensalidade/", [$estudanteMensalidadeController, "create"], $auth);
$router->create( "POST", "/estudantes/{id}/mensalidade/", [$estudanteMensalidadeController, "store"], $auth);
$router->create( "GET", "/estudantes/{id}/mensalidade/{mensalidade_id}/", [$estudanteMensalidadeController, "edit"], $auth);
$router->create( "POST", "/estudantes/{id}/mensalidade/{mensalidade_id}/", [$estudanteMensalidadeController, "update"], $auth);

$router->create( "DELETE", "/estudantes/{id}/mensalidade/{mensalidade_id}/", [$estudanteMensalidadeController, "destroy"], $auth);

// Coordenação
$router->create('GET', '/coordenadores', [$coordenadorController, 'index'], $auth);
$router->create('GET', '/coordenador', [$coordenadorController, 'create'], $auth);
$router->create('POST', '/coordenador', [$coordenadorController, 'store'], $auth);
$router->create('GET', '/coordenador/{id}/', [$coordenadorController, 'edit'], $auth);
$router->create('POST', '/coordenador/{id}/', [$coordenadorController, 'update'], $auth);
$router->create('DELETE', '/coordenador/{id}', [$coordenadorController, 'destroy'], $auth);

//bimesters
$router->create( "GET", "/periodos", [$periodoController, "index"], $auth);
$router->create( "GET", "/periodos/criar", [$periodoController, "create"], $auth);
$router->create( "POST", "/periodos/criar", [$periodoController, "store"], $auth);
$router->create( "GET", "/periodos/{id}/editar", [$periodoController, "edit"], $auth);
$router->create( "POST", "/periodos/{id}/editar", [$periodoController, "update"], $auth);

//teacher-disciplines
$router->create( "GET", "/professores/{id}/disciplina", [$professorDisciplinaController, "teacherLinkDiscipline"], $auth);
$router->create( "POST", "/professores/{id}/disciplina/{id}", [$professorDisciplinaController, "store"], $auth);
$router->create( "PUT", "/professores-disciplina/{id}", [$professorDisciplinaController, "updateStatus"], $auth);

//classRooms
$router->create( "GET", "/turmas/{id}/disciplinas/", [$turmaDisciplinaController, "index"], $auth);
$router->create( "GET", "/turmas/{id}/disciplina/", [$turmaDisciplinaController, "create"], $auth);
$router->create( "POST", "/turmas/{id}/disciplina/", [$turmaDisciplinaController, "store"], $auth);
$router->create( "GET", "/turmas/{id}/disciplina/{turma_disciplina}", [$turmaDisciplinaController, "edit"], $auth);
$router->create( "POST", "/turmas/{id}/disciplina/{turma_disciplina}", [$turmaDisciplinaController, "update"], $auth);
$router->create( "DELETE", "/turmas/{id}/disciplina/{turma_disciplina}", [$turmaDisciplinaController, "destroy"], $auth);

//activities
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/atividades", [$atividadeController, "index"], $auth);
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade", [$atividadeController, "create"], $auth);
$router->create( "POST", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade", [$atividadeController, "store"], $auth);
$router->create( "GET", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "edit"], $auth);
$router->create( "POST", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "update"], $auth);
$router->create( "DELETE", "/turmas/{id}/disciplinas/{turma_disciplina}/atividade/{atividade_id}", [$atividadeController, "destroy"], $auth);

//siteEvents
$router->create('GET', '/site-eventos', [$siteEventoController, 'index'], $auth);
$router->create('GET', '/site-eventos/criar', [$siteEventoController, 'create'], $auth);
$router->create('POST', '/site-eventos/criar', [$siteEventoController, 'store'], $auth);
$router->create('GET', '/site-eventos/{id}/editar', [$siteEventoController, 'edit'], $auth);
$router->create('POST', '/site-eventos/{id}/editar', [$siteEventoController, 'update'], $auth);
$router->create('DELETE', '/site-eventos/{id}', [$siteEventoController, 'destroy'], $auth);

//minha-turma-estudantes
$router->create("GET", "/minhas-turmas/", [$estudanteController, "indexStudents"], $auth);
$router->create('GET', "/minhas-turmas/{id}/frequencia", [$frequenciaController, 'indexStudents'], $auth);

//minha-turma-professor
$router->create("GET", "/meus-componentes/", [$professorController, "indexTeacher"], $auth);
$router->create('GET', "/meus-componentes/{id}/frequencia", [$frequenciaController, 'indexTeacher'], $auth);
$router->create('POST', "/meus-componentes/{id}/frequencia", [$frequenciaController, 'store'], $auth);
$router->create('GET', "/meus-componentes/{id}/notas", [$notaController, 'indexTeacher'], $auth);
$router->create('POST', "/meus-componentes/{id}/notas", [$notaController, 'store'], $auth);

//mensalidades
$router->create("GET", "/mensalidades", [$mensalidadeController, 'index'], $auth);
$router->create("GET", "/mensalidade", [$mensalidadeController, 'create'], $auth);
$router->create("POST", "/mensalidade", [$mensalidadeController, 'store'], $auth);
$router->create("GET", "/mensalidade/{id}", [$mensalidadeController, 'edit'], $auth);
$router->create("POST", "/mensalidade/{id}", [$mensalidadeController, 'update'], $auth);
$router->create("DELETE", "/mensalidade/{id}", [$mensalidadeController, 'destroy'], $auth);

// pessoa_contato
$router->create('GET', '/pessoas', [$pessoaContatoController, 'index'], $auth);
$router->create('GET', '/pessoa', [$pessoaContatoController, 'create'], $auth);
$router->create('POST', '/pessoa', [$pessoaContatoController, 'store'], $auth);
$router->create('GET', '/pessoa/{id}/', [$pessoaContatoController, 'edit'], $auth);
$router->create('POST', '/pessoa/{id}/', [$pessoaContatoController, 'update'], $auth);
$router->create('DELETE', '/pessoa/{id}', [$pessoaContatoController, 'destroy'], $auth);

$router->create('GET', '/pessoas-lista', [$pessoaContatoController, 'indexWithoutPagination'], $auth);
$router->create('GET', '/minha-galerinha', [$pessoaContatoController, 'indexMyLittleGroup'], $auth);
$router->create('GET', '/minha-galerinha/estudante/{id}', [$estudanteTurmaController, 'indexHistory'], $auth);
$router->create('GET', "/minha-galerinha/estudante/{id}/turma/{class_student_id}/frequencia", [$frequenciaController, 'indexResponsibleStudents'], $auth);
$router->create('GET', "/minha-galerinha/estudante/{id}/turma/{class_student_id}/notas", [$notaController, 'indexResponsibleStudents'], $auth);

//SiteCarrossel
$router->create('GET', '/site-carrossel', [$siteCarrosselController, 'index'], $auth);
$router->create('GET', '/site-carrossel/criar', [$siteCarrosselController, 'create'], $auth);
$router->create('POST', '/site-carrossel/criar', [$siteCarrosselController, 'store'], $auth);
$router->create('GET', '/site-carrossel/{id}/editar', [$siteCarrosselController, 'edit'], $auth);
$router->create('POST', '/site-carrossel/{id}/editar', [$siteCarrosselController, 'update'], $auth);
$router->create('DELETE', '/site-carrossel/{id}', [$siteCarrosselController, 'destroy'], $auth);

$router->create('GET', "/relatorios/{id}/gerar-grade", [$gradeReportController, 'indexTeacher'], $auth);

$router->create('GET', '/perfil', [$usuarioController, 'profile'], $auth);
$router->create('POST', '/upload', [$usuarioController, 'profileUploadPhoto'], $auth);

$router->create('GET', '/site-albuns', [$siteAlbumController, 'index'], $auth);
$router->create('GET', '/site-albuns/criar', [$siteAlbumController, 'create'], $auth);
$router->create('POST', '/site-albuns/criar', [$siteAlbumController, 'store'], $auth);
$router->create('GET', '/site-albuns/{id}/editar', [$siteAlbumController, 'edit'], $auth);
$router->create('POST', '/site-albuns/{id}/editar', [$siteAlbumController, 'update'], $auth);
$router->create('DELETE', '/site-albuns/{id}', [$siteAlbumController, 'destroy'], $auth);

$router->create('GET', '/perfil', [$usuarioController, 'profile'], $auth);
$router->create('POST', '/upload', [$usuarioController, 'profileUploadPhoto'], $auth);
$router->create('POST', '/perfil', [$usuarioController, 'profileUpdate'], $auth);
$router->create('POST', '/perfil-senha', [$usuarioController, 'profilePasswordUpdate'], $auth);

$router->create('GET', "/relatorios/{id}/grade-notas", [$gradeReportController, 'indexStudents'], $auth);
$router->create('GET', "/relatorios/{id}/gerar-grade", [$gradeReportController, 'indexTeacher'], $auth);



