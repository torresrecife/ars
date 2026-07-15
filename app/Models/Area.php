<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'area_id';
    public $timestamps = false;

    protected $fillable = array(
        'area_nome',
        'area_date',
        'area_status',
    );

    protected $casts = array(
        'area_id' => 'int',
        'area_date' => 'datetime',
    );

    public function bancos()
    {
        return $this->hasMany(Banco::class, 'banco_area', 'area_id');
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_setor', 'area_id');
    }
}
