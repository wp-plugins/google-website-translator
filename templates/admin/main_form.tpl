<form method="post" action="" name="prisna_admin" id="prisna_admin">

<div class="prisna_gwt_header">
	<div class="prisna_gwt_header_icon">
		<div class="prisna_gwt_header_title"><a href="http://www.prisna.net/?d=96bf1f652e7648e6a8163cdd0a8fba41" target="_blank">Prisna</a>: {{ title_message }}</div>
	</div>
</div>

{{ wp_version_check.false:begin }}
<div class="prisna_gwt_wp_version_check_fail prisna_gwt_message">
	<p>{{ wp_version_check_fail_message }}</p>
</div>
{{ wp_version_check.false:end }}

{{ just_saved.true:begin }}
<div class="prisna_gwt_saved prisna_gwt_message">
	<p>{{ saved_message }}</p>
</div>
<script type="text/javascript">
PrisnaGWTAdmin.hideMessage(".prisna_gwt_saved", 1000);
</script>
{{ just_saved.true:end }}

{{ just_imported_success.true:begin }}
<div class="prisna_gwt_imported_success prisna_gwt_message">
	<p>{{ advanced_import_success_message }}</p>
</div>
<script type="text/javascript">
PrisnaGWTAdmin.hideMessage(".prisna_gwt_imported_success", 3000);
</script>
{{ just_imported_success.true:end }}

{{ just_imported_fail.true:begin }}
<div class="prisna_gwt_imported_fail prisna_gwt_message">
	<p>{{ advanced_import_fail_message }}</p>
</div>
<script type="text/javascript">
PrisnaGWTAdmin.hideMessage(".prisna_gwt_imported_fail", 10000);
</script>
{{ just_imported_fail.true:end }}

{{ just_reseted.true:begin }}
<div class="prisna_gwt_reseted prisna_gwt_message">
	<p>{{ reseted_message }}</p>
</div>
<script type="text/javascript">
PrisnaGWTAdmin.hideMessage(".prisna_gwt_reseted", 1000);
</script>
{{ just_reseted.true:end }}

<div class="prisna_gwt_admin_container">

	<div class="prisna_gwt_submit_top_container">
		<input class="button-primary" type="submit" name="save_top" value="{{ save_button_message }}" />
	</div>

	<div class="prisna_gwt_ui_tabs_container">
		<ul>
			<li class="prisna_gwt_ui_tab prisna_gwt_ui_tab_{{ general.show.false:begin }}un{{ general.show.false:end }}selected{{ general.show.false:begin }} prisna_gwt_hidden_important{{ general.show.false:end }}" id="general_menu"><span><span>{{ general_message }}</span></span></li> 
			<li class="prisna_gwt_ui_tab prisna_gwt_ui_tab_{{ advanced.show.false:begin }}un{{ advanced.show.false:end }}selected{{ advanced.show.false:begin }} prisna_gwt_hidden_important{{ advanced.show.false:end }}" id="advanced_menu"><span><span>{{ advanced_message }}</span></span></li> 
			<li class="prisna_gwt_ui_tab prisna_gwt_ui_tab_{{ premium.show.false:begin }}un{{ premium.show.false:end }}selected{{ premium.show.false:begin }} prisna_gwt_hidden_important{{ premium.show.false:end }}" id="premium_menu"><span><span>{{ premium_message }}</span></span></li> 
		</ul>
	</div>

	<div class="prisna_gwt_main_form_container">
	
		<div class="prisna_gwt_ui_tabs_main_container">

			<div class="prisna_gwt_ui_tab_container prisna_gwt_{{ general.show.false:begin }}no_{{ general.show.false:end }}display" id="general_tab">
				<div class="prisna_gwt_ui_tab_content">

					{{ group_1 }}

				</div>
			</div>

			<div class="prisna_gwt_ui_tab_container prisna_gwt_{{ advanced.show.false:begin }}no_{{ advanced.show.false:end }}display" id="advanced_tab">
				<div class="prisna_gwt_ui_tab_content">

					<div class="prisna_gwt_ui_tabs_container prisna_gwt_ui_tabs_container_alt">
						<ul>
						   <li class="prisna_gwt_ui_tab prisna_gwt_ui_tab_{{ advanced_general.show.false:begin }}un{{ advanced_general.show.false:end }}selected{{ advanced_general.show.false:begin }} prisna_gwt_hidden_important{{ advanced_general.show.false:end }}" id="advanced_general_menu"><span><span>{{ advanced_general_message }}</span></span></li> 
						   <li class="prisna_gwt_ui_tab prisna_gwt_ui_tab_{{ advanced_import_export.show.false:begin }}un{{ advanced_import_export.show.false:end }}selected{{ advanced_import_export.show.false:begin }} prisna_gwt_hidden_important{{ advanced_import_export.show.false:end }}" id="advanced_import_export_menu"><span><span>{{ advanced_import_export_message }}</span></span></li> 
						</ul>
					</div>

					<div class="prisna_gwt_main_form_container">
			
						<div class="prisna_gwt_ui_tabs_main_container">

							<div class="prisna_gwt_ui_tab_container prisna_gwt_{{ advanced_general.show.false:begin }}no_{{ advanced_general.show.false:end }}display" id="advanced_general_tab">

								<div class="prisna_gwt_ui_tab_content">
									
										{{ group_2 }}

								</div>

							</div>

							<div class="prisna_gwt_ui_tab_container prisna_gwt_{{ advanced_import_export.show.false:begin }}no_{{ advanced_import_export.show.false:end }}display" id="advanced_import_export_tab">

								<div class="prisna_gwt_ui_tab_content">
									
									{{ group_3 }}
		
								</div>
									
							</div>
						
						</div>
						
					</div>

				</div>
			</div>

			<div class="prisna_gwt_ui_tab_container prisna_gwt_{{ premium.show.false:begin }}no_{{ premium.show.false:end }}display" id="premium_tab">
				<div class="prisna_gwt_ui_tab_content">

					{{ group_4 }}

				</div>
			</div>

		</div>

		<div class="prisna_gwt_submit_container">

			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<input name="reset" type="button" value="{{ reset_button_message }}" class="button submit-button reset-button reset-settings" onclick="return PrisnaGWTAdmin.resetSettings('{{ reset_message }}');" >
					</td>
					<td>
						<input class="button-primary" type="submit" name="save" value="{{ save_button_message }}" />
					</td>
				</tr>
			</table>			

			<input type="hidden" name="prisna_gwt_admin_action" id="prisna_gwt_admin_action" value="prisna_gwt_save_settings" />
			<input type="hidden" name="prisna_tab" id="prisna_tab" value="{{ tab }}" />
			<input type="hidden" name="prisna_tab_2" id="prisna_tab_2" value="{{ tab_2 }}" />

		</div>
			
	</div>
	
</div>

{{ nonce }}

</form>

<script type="text/javascript">
/*<![CDATA[*/
PrisnaGWTAdmin.initialize();
/*]]>*/
</script>