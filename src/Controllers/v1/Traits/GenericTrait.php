<?php
 
namespace App\Controllers\v1\Traits;

trait GenericTrait {
    public function sumAbsences($frequencias)
    {
        return array_reduce($frequencias, function ($carry, $item) {
            return $carry + $item->faltas;
        }, 0);
    }

    public function sumMonthlyFees($monthlyfees, $situation = null)
    {
        return array_reduce($monthlyfees, function ($monthly, $item) use ($situation) {
            if (is_null($situation) || $item->situacao === $situation) {
                return $monthly + $item->valor;
            }
            return $monthly; // Não soma se a situação não corresponde
        }, 0);
    }    

    public function calculatePercentage($partial, $total) {
        return $total > 0 ? round(($partial / $total) * 100, 2) : 0;
    }

    public function extractItemOfObject($dados, string $param) 
    {   
        return array_map(function($item) use ($param) {
            return $item->$param;
        }, $dados);
    }

    public function responseJson(int $code = 200, string $message = '') {
        http_response_code($code);
        return json_encode([
            'status' => $code,
            'message' => $message
        ]);
    }

    public function extractWeekDays($aulas) 
    {
        if (is_null($aulas)) {
            return [];
        }

        $diasPermitidos = [];
        $mapaDias = [
            "domingo" => 6,
            "segunda-feira" => 0,
            "terça-feira" => 1,
            "quarta-feira" => 2,
            "quinta-feira" => 3,
            "sexta-feira" => 4,
            "sábado" => 5,
        ];

        foreach ($aulas as $aula) {
            $dia = json_decode($aula->dia);
            $nome = strtolower($dia->nome);
            if (isset($mapaDias[$nome])) {
                $diasPermitidos[] = $mapaDias[$nome];
            }
        }

        return array_unique($diasPermitidos);
    }

    public function checkSelect($value) 
    {
        return $value == 'on' ? 1:0;       
    }

}