<?php
 
namespace App\Controllers\v1\Traits;

trait GenericTrait {
    private function sumAbsences($frequencias)
    {
        return array_reduce($frequencias, function ($carry, $item) {
            return $carry + $item->faltas;
        }, 0);
    }
}