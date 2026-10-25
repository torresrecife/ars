<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Tests\TestCase;

class SubpathHttpAuthorizationTest extends TestCase
{
    public function test_generated_urls_keep_ars_subpath()
    {
        $this->assertStringContainsString('/ars/usuarios', route('usuarios'));
        $this->assertStringContainsString('/ars/admin/usuarios/1', route('admin.usuarios.show', array('id' => 1)));
        $this->assertStringContainsString('/ars/_test/http/admin-protegido', route('test.http.admin'));
    }
}
