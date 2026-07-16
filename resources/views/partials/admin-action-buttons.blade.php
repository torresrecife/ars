<div id="module-status" style="display:{{ isset($display) ? e((string) $display) : 'block' }};">
	<span class="editar"><a href="javascript:{!! $editAction !!}" class="button_del" title="{{ e((string) $editTitle) }}">Editar</a></span>
	<span class="excluir"><a href="javascript:{!! $deleteAction !!}" class="button_del" title="{{ e((string) $deleteTitle) }}">Excluir</a></span>
</div>
