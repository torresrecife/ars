<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BancoAndamento extends Model
{
    protected $table = 'banco_andamentos';
    protected $primaryKey = 'ba_id';
    public $timestamps = false;

    protected $fillable = array(
        'banco_id',
        'anda_id',
        'stt',
    );

    protected $casts = array(
        'ba_id' => 'int',
        'banco_id' => 'int',
        'anda_id' => 'int',
    );

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'banco_id', 'banco_id');
    }

    public function andamento()
    {
        return $this->belongsTo(Andamento::class, 'anda_id', 'anda_id');
    }
}
