<?php

namespace App\Models\SiteEvent;

use App\Models\Traits\UuidTrait;

class SiteEvento {
    
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

    public function __construct() {}

    public function create(
        array $data
    ): SiteEvento {
        $site_evento = new SiteEvento();
        $site_evento->id = $data['id'] ?? null;
        $site_evento->uuid = $data['uuid'] ?? $this->generateUUID();
        $site_evento->nome = $data['name'] ?? null;
        $site_evento->arquivo = $data['arquivo'] ?? null;
        $site_evento->site_arquivo_id = $data['site_arquivo_id'];
        $site_evento->descricao = $data['description'] ?? null;
        $site_evento->ativo = $data['active'] ?? null;
        $site_evento->created_at = $data['created_at'] ?? null;
        $site_evento->updated_at = $data['updated_at'] ?? null;
        return $site_evento;
    }
}