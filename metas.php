<?php

require __DIR__ . '/bootstrap/module_entry.php';

ars_run_module_entry(function ($request) {
	$input = $request->all();
	return (isset($input['startBanco']) || isset($input['banco_id']) || isset($input['meta_mes']) || isset($input['meta_ano'])) ? 'metas-***REMOVED***' : 'metas-select';
});
