<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Andamento extends Model
{
    protected $table = 'andamentos';
    protected $primaryKey = 'anda_id';
    public $timestamps = false;

    protected $fillable = array(
        'nome',
        'chave',
        'anda_neo',
        'especie',
        'painel',
        'titulo',
        'ordem',
        'repete',
    );

    protected $casts = array(
        'anda_id' => 'int',
        'especie' => 'int',
        'ordem' => 'int',
    );

    public function metas()
    {
        return $this->hasMany(MetaAndamento::class, 'anda_id', 'anda_id');
    }

    public function bancos()
    {
        return $this->hasMany(BancoAndamento::class, 'anda_id', 'anda_id');
    }
}
