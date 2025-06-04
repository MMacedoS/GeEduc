<?php

namespace App\Interfaces\Site\Album;

interface ISiteAlbumRepository{

    public function allSiteAlbuns(array $params = []);

    public function saveAll(array $data, string $dir);

    public function create(array $data);

    public function updateAll(array $data, string $dir);

    public function update(array $data, int $id);

    public function deleteAll($site_album);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}