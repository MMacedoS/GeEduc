<?php

namespace App\Interfaces\Ticket;

interface IBoletoRepository 
{
    public function create(array $params);

    public function ticketByMonthlyId(int $monthly_id);
}