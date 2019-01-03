<div class="cms-content fill-height flexbox-area-grow cms-tabset center $BaseCSSClasses" data-layout-type="border" data-pjax-fragment="Content">

	<div class="cms-content-header north">
		<div class="cms-content-header-info vertical-align-items flexbox-area-grow">
			<div class="breadcrumbs-wrapper">
				<span class="cms-panel-link crumb last">
					<% if $SectionTitle %>
						$SectionTitle
					<% else %>
						<%t SilverStripe\Admin\ModelAdmin.Title 'Data Models' %>
					<% end_if %>
				</span>
			</div>
		</div>

		<div class="cms-content-header-tabs cms-tabset-nav-primary ss-ui-tabs-nav">
			<ul class="cms-tabset-nav-primary">
				<% loop $ManagedModelTabs %>
				<li class="tab-$ClassName $LinkOrCurrent<% if $LinkOrCurrent == 'current' %> ui-tabs-active<% end_if %>">
					<a href="$Link" class="cms-panel-link" title="$Title.ATT">$Title</a>
				</li>
				<% end_loop %>
			</ul>
		</div>
	</div>

	<div class="cms-content-fields center ui-widget-content cms-panel-padded fill-height flexbox-area-grow" data-layout-type="border">
		$Tools

		<div class="cms-content-view">
			<% if $Snippets.exists %>
				<div class="row mb-4">
					<% loop $Snippets %>
						<div class="col <% if $Last %>pr-3<% else %>pr-0<% end_if %>">
							{$Snippet}
						</div>
					<% end_loop %>
				</div>
			<% end_if %>

			$EditForm
		</div>
	</div>

</div>
