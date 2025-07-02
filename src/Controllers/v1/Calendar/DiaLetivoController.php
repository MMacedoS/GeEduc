<?php

namespace App\Controllers\v1\Calendar;

use App\Controllers\Controller;
use App\Controllers\v1\Traits\GenericTrait;
use App\Interfaces\Calendar\IDiaLetivoRepository;
use App\Request\Request;
use App\Utils\Paginator;
use App\Utils\Validator;
use DateInterval;
use DateTime;

class DiaLetivoController extends Controller {    
    use GenericTrait;

    private $diaLetivoRepository;
    
    public function __construct(IDiaLetivoRepository $diaLetivoRepository)
    {
        parent::__construct();   
        $this->diaLetivoRepository = $diaLetivoRepository;
    }

    public function index(Request $request) 
    {
        $dias = $this->diaLetivoRepository->all();

        $perPage = 365;
        $currentPage = $request->getParam('page') ? (int)$request->getParam('page') : 1;
        $paginator = new Paginator($dias, $perPage, $currentPage);
        $paginatedBoards = $paginator->getPaginatedItems();

        return $this->router->view('calendar/index', 
            [
                'active' => 'Calendar', 
                'dias' => $paginatedBoards, 
                'searchFilter' => $params['classroom'] ?? null,
                'situation' => $params['situation'] ?? null
            ]
        );
    }

    public function store(Request $request) 
    {
        $data = $request->getBodyParams();

        $validator = new Validator($data);

        $rules = [
            'title' => 'required',
            'start' => 'required',
        ];

        if (!$validator->validate($rules)) {
            echo $this->responseJson(422, "Erro ao validar dados!");
            exit();
        }

        if (isset($data['end'])) {
            $dataInicio = new DateTime($data['start']);
            $dataFim = new DateTime($data['end']);
            $titulo = $data['title'] ?? 'Dia Letivo';
            
            $eventosCriados = [];
        
            while ($dataInicio <= $dataFim) {
                $dataFormatada = $dataInicio->format('Y-m-d');
        
                $eventosCriados = $this->diaLetivoRepository->create([
                    'title' => $titulo,
                    'start' => $dataFormatada,
                ]);
                $dataInicio->add(new DateInterval('P1D'));
            }
        }

        if(empty($eventosCriados)) {
            echo $this->responseJson(422, "dados não inseridos");
            exit();
        }

        echo $this->responseJson(202, "Inserido com sucesso!");
        exit();
    }

    public function destroy(Request $request, $id) 
    {
        $event = $this->diaLetivoRepository->findByUuid($id);

        if (is_null($event)) {
            echo $this->responseJson(422, "evento não encontrado");
            exit();
        }

        $deleted = $this->diaLetivoRepository->delete($event->id);

        if(!$deleted) {
            echo $this->responseJson(422, "não pode ser deletado");
            exit();
        }

        echo $this->responseJson(202, "deletado com sucesso");
        exit();
    }
}