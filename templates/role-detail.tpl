<div class="detail">
	<div class="item">
		<div class="image">
			{image src=$role.imagePath alt=$role.title width='300' height='460'}
		</div>
		<div class="title">
			<h2>{$role.localizedTitle}</h2>
		</div>
		<div class="description">
			{$role.localizedDescription}
		</div>
		{if $role.relatedRoles}
			<div class="related">
				{foreach from=$role.relatedRoles item='relatedRole'}
					<div class="item">
						<div class="image">
							<a href="{$relatedRole.url}">
								{image src=$relatedRole.imagePath alt=$relatedRole.title width='150' height='230'}
							</a>
						</div>
						<div class="text">
							<p><a href="{$relatedRole.url}">{$relatedRole.title}</a></p>
						</div>
					</div>
				{/foreach}
			</div>
		{/if}
	</div>
</div>