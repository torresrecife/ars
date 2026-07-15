<?php

require __DIR__ . '/bootstrap/module_entry.php';

ars_run_module_entry(function ($request) {
	$input = $request->all();
	return isset($input['geral']) && (string) $input['geral'] === '1' ? 'relatorio-semanal' : 'relatorio-mensal';
});
