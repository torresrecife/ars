<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegiaoUf extends Model
{
    protected $table = 'regioes_ufs';
    protected $primaryKey = 'regiao_uf_id';
    public $timestamps = false;

    protected $fillable = array(
        'regiao_id',
        'uf',
    );

    protected $casts = array(
        'regiao_uf_id' => 'int',
        'regiao_id' => 'int',
    );

    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'regiao_id', 'regiao_id');
    }
}
