<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioRegiao extends Model
{
    protected $table = 'usuarios_regioes';
    protected $primaryKey = 'usuario_regiao_id';
    public $timestamps = false;

    protected $fillable = array(
        'usuario_id',
        'regiao_id',
    );

    protected $casts = array(
        'usuario_regiao_id' => 'int',
        'usuario_id' => 'int',
        'regiao_id' => 'int',
    );

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id_usu');
    }

    public function regiao()
    {
        return $this->belongsTo(Regiao::class, 'regiao_id', 'regiao_id');
    }
}
