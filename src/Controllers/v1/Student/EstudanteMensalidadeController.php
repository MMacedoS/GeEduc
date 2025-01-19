<?php

namespace App\Controllers\v1\Student;

use App\Controllers\Controller;
use App\Repositories\MonthlyFees\MensalidadeRepository;
use App\Repositories\Plan\PlanoRepository;
use App\Repositories\Student\EstudanteMensalidadeRepository;
use App\Repositories\Student\EstudanteRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;

class EstudanteMensalidadeController extends Controller
{
    protected $mensalidadeRepository;
    protected $estudanteRepository;
    protected $estudanteMensalidadeRepository;
    protected $planosRepository;

    public function __construct()
    {
        parent::__construct();
        $this->estudanteRepository = new EstudanteRepository();
        $this->mensalidadeRepository = new MensalidadeRepository();
        $this->estudanteMensalidadeRepository = new EstudanteMensalidadeRepository();
        $this->planosRepository = new PlanoRepository();
    }

    public function index(Request $request, $student_id)
    {
        if (!hasPermission("cadastrar mensalidades")) {
            return $this->router->redirect("estudantes?error=442");
        }

        $student = $this->estudanteRepository->findByUuid((string) $student_id);

        if (is_null($student)) {
            return $this->router->redirect("estudantes?error=442");
        }

        $students_monthlylees = $this->mensalidadeRepository->allMonthlyfees([
            "student_id" => (int) $student->id,
        ]);

        $perPage = 10;
        $currentPage = $request->getParam("page")
            ? (int) $request->getParam("page")
            : 1;

        $paginator = new Paginator(
            $students_monthlylees,
            $perPage,
            $currentPage
        );

        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view("/student/student-monthly/index", [
            "active" => "pedagogico",
            "estudante" => $student,
            "estudante_mensalidades" => $paginatedBoards,
            "links" => $paginator->links(),
        ]);
    }

    public function create(Request $request, string $student_id)
    {
        $student = $this->estudanteRepository->findByUuid((string) $student_id);

        if (is_null($student)) {
            return $this->router->redirect("estudantes?error=442");
        }

        $student_mensalidade = $this->estudanteMensalidadeRepository->getMonthlyFee(
            [
                "student_id" => $student->id,
                "active" => 1,
            ]
        );

        $planos = $this->planosRepository->allPlans(["active" => 1]);

        return $this->router->view("/student/student-monthly/create", [
            "active" => "pedagogico",
            "estudante" => $student,
            "estudante_mensalidade" => $student_mensalidade,
            "planos" => $planos,
        ]);
    }

    public function store(Request $request, string $student_id)
    {
        $data = $request->getBodyParams();

        $student = $this->estudanteRepository->findByUuid((string)$student_id);

        if (is_null($student)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        $student_mensalidade = $this->estudanteMensalidadeRepository->getMonthlyFee(
            [
                "student_id" => (int)$student->id
            ]
        );

        if (is_null($student_mensalidade)) {
            $planos = $this->planosRepository->planByAmmount($data['plan_amount']);

            $data['plan_id'] = is_null($planos) ? 1 : $planos->id;
            
            $data['student_id'] = (int)$student->id;

            $student_mensalidade = $this->estudanteMensalidadeRepository->create($data);

            if (is_null($student_mensalidade)) {
                return $this->router->redirect(
                    "estudantes/$student_id/mensalidades?error=442"
                );
            }
        }

        $validator = new Validator($data);

        $rules = [
            "expiration_date" => "required",
            "monthly_day" => "required",
            "amount" => "required",
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view("/student/student-monthly/create", [
                "active" => "register",
                "errors" => $validator->getErrors(),
            ]);
        }

        $data["studante_monthly_id"] = $student_mensalidade->id;

        $created = $this->mensalidadeRepository->create($data);

        if (is_null($created)) {
            return $this->router->view("/student/student-monthly/create", [
                "active" => "register",
                "errors" => $validator->getErrors(),
            ]);
        }

        return $this->router->redirect("estudantes/$student_id/mensalidades");
    }

    public function edit(
        Request $request,
        string $student_id,
        string $monthlyfees_id
    ) {
        $student = $this->estudanteRepository->findByUuid((string) $student_id);

        if (is_null($student)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        $student_mensalidade = $this->estudanteMensalidadeRepository->getMonthlyFee(
            [
                "student_id" => $student->id,
                "active" => 1,
            ]
        );

        $planos = $this->planosRepository->allPlans(["active" => 1]);

        $monthlyfees = $this->mensalidadeRepository->findByUuid(
            (string) $monthlyfees_id
        );

        if (is_null($monthlyfees)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        return $this->router->view("/student/student-monthly/edit", [
            "active" => "pedagogico",
            "estudante" => $student,
            "estudante_mensalidade" => $student_mensalidade,
            "planos" => $planos,
            "mensalidade" => $monthlyfees,
        ]);
    }

    public function update(
        Request $request,
        string $student_id,
        string $monthlyfees_id
    ) {
        $data = $request->getBodyParams();

        $student = $this->estudanteRepository->findByUuid((string) $student_id);

        if (is_null($student)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        $student_mensalidade = $this->estudanteMensalidadeRepository->getMonthlyFee(
            [
                "student_id" => $student->id,
                "active" => 1,
            ]
        );

        if (is_null($student_mensalidade)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        $planos = $this->planosRepository->allPlans(["active" => 1]);

        $monthlyfees = $this->mensalidadeRepository->findByUuid(
            (string) $monthlyfees_id
        );

        if (is_null($monthlyfees)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        $validator = new Validator($data);

        $rules = [
            "expiration_date" => "required",
            "monthly_day" => "required",
            "amount" => "required",
        ];

        if (!$validator->validate($rules)) {
            return $this->router->view("/student/student-monthly/edit", [
                "active" => "register",
                "errors" => $validator->getErrors(),"estudante" => $student,
                "estudante_mensalidade" => $student_mensalidade,
                "planos" => $planos,
                "mensalidade" => $monthlyfees,
            ]);
        }

        $data["studante_monthly_id"] = $student_mensalidade->id;

        $created = $this->mensalidadeRepository->update(
            $data,
            $monthlyfees->id
        );

        if (is_null($created)) {
            return $this->router->view("/student/student-monthly/edit", [
                "active" => "register",
                "errors" => $validator->getErrors(),
                "estudante_mensalidade" => $student_mensalidade,
                "planos" => $planos,
                "mensalidade" => $monthlyfees,
            ]);
        }

        return $this->router->redirect("estudantes/$student_id/mensalidades");
    }

    public function destroy(
        Request $request,
        string $student_id,
        string $monthlyfee_id
    ) {
        $student = $this->estudanteRepository->findByUuid((string) $student_id);

        if (is_null($student)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        $monthlyfees = $this->mensalidadeRepository->findByUuid(
            (string) $monthlyfee_id
        );

        if (is_null($monthlyfees)) {
            return $this->router->redirect(
                "estudantes/$student_id/mensalidades?error=442"
            );
        }

        $this->mensalidadeRepository->delete((int)$monthlyfees->id);

        echo json_encode(
            ['status'=> 200, 'message' => 'deleted with success']
        );

        exit();
    }
}
