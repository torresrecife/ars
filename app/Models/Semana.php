<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Semana extends Model
{
    protected $table = 'semanas';
    protected $primaryKey = 'semanas_id';
    public $timestamps = false;

    protected $fillable = array(
        'mes',
        'ano',
        'ini_1',
        'fim_1',
        'ini_2',
        'fim_2',
        'ini_3',
        'fim_3',
        'ini_4',
        'fim_4',
        'ini_5',
        'fim_5',
        'data_cad',
        'data_arlt',
    );

    protected $casts = array(
        'semanas_id' => 'int',
        'mes' => 'int',
        'ano' => 'int',
        'ini_1' => 'int',
        'fim_1' => 'int',
        'ini_2' => 'int',
        'fim_2' => 'int',
        'ini_3' => 'int',
        'fim_3' => 'int',
        'ini_4' => 'int',
        'fim_4' => 'int',
        'ini_5' => 'int',
        'fim_5' => 'int',
        'data_cad' => 'datetime',
        'data_arlt' => 'datetime',
    );
}
