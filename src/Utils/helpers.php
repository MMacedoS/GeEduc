<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}  

if (!function_exists('isPermissionChecked')) {
    function isPermissionChecked($permissionId, $userPermissoes) {
        foreach ($userPermissoes as $userPermissao) {
            if ($userPermissao['permissao_id'] === $permissionId) {
                return true;
           }
        }
        return false;
    }
}

if (!function_exists('reservaFilteredById')) {
    function reservaFilteredById($reservaId, $reservas) {
        foreach ($reservas as $reserva) {
            if ($reserva->id === $reservaId) {
                return $reserva;
           }
        }
        return null;
    }
}

if (!function_exists('hasPermission')) {    
    function hasPermission($permissio_name) {
        $my_permissions = $_SESSION['my_permissions'];
        foreach ($my_permissions as $permission) {
            if ($permission->name === $permissio_name) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('getCustomers')) {    
    function getCustomers($data) {
        if (is_array($data)) {
            $names = '';
            foreach ($data as $customer) {
                $names.= $customer->name; 
                if(count($data) > 1) {
                    $names .= ' | ';
                }
            }
            return $names;
        }
        return "Não identificado";
    }
}

if (!function_exists('getParamsToJson')) {    
    function getParamsToJson($data) {
        return json_decode($data);
    }
}

if (!function_exists('brDate')) {    
    function brDate($date) {
        if (!is_null($date)) {
            $date = implode('/', array_reverse(explode('-', $date)));
            return $date;
        }
        return "Não identificado";
    }
}

if (!function_exists('brCurrency')) {    
    function brCurrency($value) {
        if (!is_null($value) && is_numeric($value)) {
            return 'R$ ' . number_format($value, 2, ',', '.');
        }
        return null;
    }
}

if (!function_exists('filterAvailableToursWithYear')) { 
    function filterAvailableToursWithYear($turmas, $estudanteTurma, $year): array {
        return array_filter($turmas, function($turma) use ($estudanteTurma, $year) {
            foreach ($estudanteTurma as $est_tur) {
                if ($est_tur->turma_id === $turma->id && $est_tur->ano_letivo == $year) {
                    return false;
                }
            }
            return true;
        });
    }
}
