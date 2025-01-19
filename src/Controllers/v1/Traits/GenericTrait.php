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
}