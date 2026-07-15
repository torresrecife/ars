<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carteira extends Model
{
    protected $table = 'carteira';
    protected $primaryKey = 'carteira_id';
    public $timestamps = false;

    protected $fillable = array(
        'banco_id',
        'carteira_condicao',
        'carteira_cod',
        'carteira_vinc',
        'carteira_date',
        'carteira_status',
    );

    protected $casts = array(
        'carteira_id' => 'int',
        'banco_id' => 'int',
        'carteira_cod' => 'int',
        'carteira_date' => 'datetime',
    );

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'banco_id', 'banco_id');
    }

    public function dados()
    {
        return $this->hasMany(Dado::class, 'carteira_id', 'carteira_id');
    }
}
