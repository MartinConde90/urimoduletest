<div class="container">
	<div class="panel">
		<div class="panel-heading">
			{l s='Textos personalizados' mod='urimoduletest'}
		</div>
		<div class="panel-body">
			{if isset($textos) && $textos|count}
				<table class="table">
					<thead>
						<tr>
							<th>{l s='ID' mod='urimoduletest'} </th>
							<th>{l s='Texto' mod='urimoduletest'} </th>
							<th>{l s='Acciones' mod='urimoduletest'} </th>
						</tr>
					</thead>
					<tbody>
						{foreach $textos as $t}
						<tr>
							<td>{$t['id']|intval}</td>
							<td>{$t.texto|escape:'html':'UTF-8'}</td>
							<td>
								<a href="{$urls.edit}{$t.id|intval}" class="btn btn-default"><i class="icon-edit"></i> {l s='Editar' mod='urimoduletest'}</a>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			{else}
				<p class="alert alert-info">{l s='Aun no has creado ningun texto' mod='urimoduletest'}</p>
			{/if}
		</div>
		<div class="panel-footer">
			<p class="text-right">
				<a href="{$urls.add|escape:'html':'UTF-8'}" class="btn btn-default"><i class="icon-plus"></i>{l s='Añadir texto'}</a>
			</p>
		</div>
	</div>
</div>
