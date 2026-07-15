<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteiraNome extends Model
{
    protected $table = 'carteira_nome';
    protected $primaryKey = 'carteira_cod';
    public $timestamps = false;

    protected $fillable = array(
        'carteira_condicao',
    );

    protected $casts = array(
        'carteira_cod' => 'int',
    );
}
