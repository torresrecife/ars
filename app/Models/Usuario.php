<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usu';
    public $timestamps = false;

    protected $fillable = array(
        'id_neo',
        'nome_usu',
        'login_usu',
        'senha_usu',
        'email_usu',
        'nivel_usu',
        'acesso_usu',
        'data_cad',
        'id_setor',
        'id_cliente',
        'regiao_modo',
        'status_usu',
        'estados_usu',
        'comarca_usu',
    );

    protected $casts = array(
        'id_usu' => 'int',
        'id_neo' => 'int',
        'id_setor' => 'int',
        'acesso_usu' => 'datetime',
        'data_cad' => 'datetime',
    );

    protected $hidden = array(
        'senha_usu',
    );

    public function getAuthPassword()
    {
        return (string) $this->senha_usu;
    }

    public function getRememberTokenName()
    {
        return null;
    }

    public function setor()
    {
        return $this->belongsTo(Area::class, 'id_setor', 'area_id');
    }

    public function vinculosRegiao()
    {
        return $this->hasMany(UsuarioRegiao::class, 'usuario_id', 'id_usu');
    }

    public function regioes()
    {
        return $this->belongsToMany(Regiao::class, 'usuarios_regioes', 'usuario_id', 'regiao_id');
    }
}
