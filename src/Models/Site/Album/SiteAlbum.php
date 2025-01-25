<?php

namespace App\Models\Site\Album;

use App\Models\Traits\UuidTrait;

class SiteAlbum{

    use UuidTrait;

    public $id;
    public $uuid;
    public $nome;
    public $arquivo;
    public $site_arquivo_id;
    public $descricao;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct(){}

    public function create(
        array $data
    ) : SiteAlbum {
        $site_album = new SiteAlbum();
        $site_album->id = $data['id'] ?? null;
        $site_album->uuid = $data['uuid'] ?? $this->generateUUID();
        $site_album->nome = $data['name'] ?? null;
        $site_album->arquivo = $data['arquivo'] ?? null;
        $site_album->site_arquivo_id = $data['site_arquivo_id'];
        $site_album->descricao = $data['description'] ?? null;
        $site_album->ativo = $data['active'] ?? null;
        $site_album->created_at = $data['created_at'] ?? null;
        $site_album->updated_at = $data['updated_at'] ?? null;
        return $site_album;
    }

}