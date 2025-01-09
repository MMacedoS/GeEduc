<?php

namespace App\Models\SiteArchive;

use App\Models\Traits\UuidTrait;

class SiteArquivo {

    use UuidTrait;

    public $id;
    public $uuid;
    public $nome_original;
    public $ext_arquivo;
    public $arquivo;
    public $created_at;
    public $updated_at;

    public function __construct(){}

    public function create(
        array $data
    ): SiteArquivo {
        $site_arquivo = new SiteArquivo();
        $site_arquivo->id = $data['id'] ?? null;
        $site_arquivo->uuid = $data['uuid'] ?? $this->generateUUID();
        $site_arquivo->nome_original = $data['original_name'] ?? null;
        $site_arquivo->ext_arquivo = $data['ext_archive'] ?? null;
        $site_arquivo->arquivo = $data['archive'] ?? null;
        $site_arquivo->created_at = $data['created_at'] ?? null;
        $site_arquivo->updated_at = $data['updated_at'] ?? null;
        return $site_arquivo;
    }

}