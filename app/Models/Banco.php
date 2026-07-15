<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $table = 'bancos';
    protected $primaryKey = 'banco_id';
    public $timestamps = false;

    protected $fillable = array(
        'banco_name',
        'banco_cod',
        'banco_creator',
        'banco_area',
        'banco_status',
        'banco_class',
        'simulador',
        'banco_curto',
    );

    protected $casts = array(
        'banco_id' => 'int',
        'banco_creator' => 'datetime',
        'banco_area' => 'int',
        'simulador' => 'int',
    );

    public function area()
    {
        return $this->belongsTo(Area::class, 'banco_area', 'area_id');
    }

    public function carteiras()
    {
        return $this->hasMany(Carteira::class, 'banco_id', 'banco_id');
    }

    public function metas()
    {
        return $this->hasMany(MetaAndamento::class, 'banco_id', 'banco_id');
    }

    public function bancoAndamentos()
    {
        return $this->hasMany(BancoAndamento::class, 'banco_id', 'banco_id');
    }
}
