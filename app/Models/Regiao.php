<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regiao extends Model
{
    protected $table = 'regioes';
    protected $primaryKey = 'regiao_id';
    public $timestamps = false;

    protected $fillable = array(
        'regiao_nome',
        'regiao_slug',
        'regiao_status',
        'data_cad',
        'data_alt',
    );

    protected $casts = array(
        'regiao_id' => 'int',
        'data_cad' => 'datetime',
        'data_alt' => 'datetime',
    );

    public function ufs()
    {
        return $this->hasMany(RegiaoUf::class, 'regiao_id', 'regiao_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_regioes', 'regiao_id', 'usuario_id');
    }

    public function metas()
    {
        return $this->hasMany(MetaAndamento::class, 'regiao_id', 'regiao_id');
    }
}
