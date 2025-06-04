<?php

namespace App\Interfaces\File;

interface IArquivoRepository {

    public function allFiles(array $params = []);

    public function create(array $data, string $dir);

    public function update(array $data, string $dir, int $id);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}