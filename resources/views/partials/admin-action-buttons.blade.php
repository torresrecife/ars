<div id="module-status" style="display:{{ isset($display) ? e((string) $display) : 'block' }};">
	<span class="editar"><a href="#" onclick="{{ $editAction }}; return false;" class="button_del" title="{{ e((string) $editTitle) }}">Editar</a></span>
	<span class="excluir"><a href="#" onclick="{{ $deleteAction }}; return false;" class="button_del" title="{{ e((string) $deleteTitle) }}">Excluir</a></span>
</div>
