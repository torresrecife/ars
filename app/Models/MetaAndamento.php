<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaAndamento extends Model
{
    protected $table = 'metas_andamentos';
    protected $primaryKey = 'meta_id';
    public $timestamps = false;

    protected $fillable = array(
        'banco_id',
        'meta_mes',
        'meta_ano',
        'anda_id',
        'meta_valor',
        'def_sem',
        'sem_1',
        'sem_2',
        'sem_3',
        'sem_4',
        'sem_5',
        'regiao_id',
        'sort_order',
    );

    protected $casts = array(
        'meta_id' => 'int',
        'banco_id' => 'int',
        'meta_mes' => 'int',
        'meta_ano' => 'int',
        'anda_id' => 'int',
        'meta_valor' => 'decimal:2',
        'sem_1' => 'decimal:2',
        'sem_2' => 'decimal:2',
        'sem_3' => 'decimal:2',
        'sem_4' => 'decimal:2',
        'sem_5' => 'decimal:2',
        'regiao_id' => 'int',
        'sort_order' => 'int',
    );

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'banco_id', 'banco_id');
    }

    public function andamento()
    {
        return $this->belongsTo(Andamento::class, 'anda_id', 'anda_id');
    }

    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'regiao_id', 'regiao_id');
    }
}
