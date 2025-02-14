<?php

namespace App\Interfaces\Site\Carousel;

interface ISiteCarrosselRepository {

    public function allSiteCarousel(array $params = []);

    public function saveAll(array $data, string $dir);

    public function create(array $data);

    public function updateAll(array $data, string $dir);

    public function update(array $data, int $id);

    public function deleteAll($site_carousel);

    public function delete(int $id);

    public function findByUuid(string $uuid);

    public function findById(string $id);
}