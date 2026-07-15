<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dado extends Model
{
    protected $table = 'dados';
    protected $primaryKey = 'dados_id';
    public $timestamps = false;

    protected $fillable = array(
        'carteira_id',
        'banco_id',
        'dados_cod',
        'dados_date',
        'dados_status',
    );

    protected $casts = array(
        'dados_id' => 'int',
        'carteira_id' => 'int',
        'banco_id' => 'int',
        'dados_date' => 'datetime',
    );

    public function carteira()
    {
        return $this->belongsTo(Carteira::class, 'carteira_id', 'carteira_id');
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'banco_id', 'banco_id');
    }
}
