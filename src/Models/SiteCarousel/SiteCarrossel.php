<?php

namespace App\Models\SiteCarousel;

use App\Models\Traits\UuidTrait;

class SiteCarrossel {

    use UuidTrait;

    public $id;
    public $uuid;
    public $nome;
    public $descricao;
    public $arquivo;
    public $site_arquivo_id;
    public $local;
    public $ativo;
    public $link;
    public $created_at;
    public $updated_at;

    public function __construct(){}

    public function create(
        array $data
    ) : SiteCarrossel {
        $site_carousel = new SiteCarrossel();
        $site_carousel->id = $data['id'] ?? null;
        $site_carousel->uuid = $data['uuid'] ?? $this->generateUUID();
        $site_carousel->nome = $data['name'] ?? null;
        $site_carousel->descricao = $data['description'] ?? null;
        $site_carousel->arquivo = $data['arquivo'] ?? null;
        $site_carousel->site_arquivo_id = $data['site_arquivo_id'];
        $site_carousel->local = $data['local'] ?? null;
        $site_carousel->ativo = $data['active'] ?? null;
        $site_carousel->link = $data['link'] ?? null;
        $site_carousel->created_at = $data['created_at'] ?? null;
        $site_carousel->updated_at = $data['updated_at'] ?? null;
        return $site_carousel;
    }

}