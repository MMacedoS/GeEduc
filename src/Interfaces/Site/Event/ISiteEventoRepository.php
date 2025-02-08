<?php

namespace App\Interfaces\Site\Event;

interface ISiteEventoRepository {

    public function allSiteEvents(array $params  = []);

    public function saveAll(array $data, string $dir);

    public function create(array $data);

    public function updateAll(array $data, string $dir);

    public function update(array $data, int $id);

    public function deleteAll($site_evento);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}