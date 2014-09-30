<div class="prisna_gwt_section prisna_gwt_{{ type }}{{ dependence.show.false:begin }} prisna_gwt_no_display{{ dependence.show.false:end }}{{ has_dependence.true:begin }} prisna_gwt_section_tabbed_{{ dependence_count }}{{ has_dependence.true:end }}" id="section_{{ id }}">
	
	<div class="prisna_gwt_tooltip"></div>
	<div class="prisna_gwt_description prisna_gwt_no_display">{{ description_message }}</div>
		
	<div class="prisna_gwt_title_container prisna_gwt_icon prisna_gwt_icon_grid2"><h3 class="prisna_gwt_title">{{ title_message }}</h3></div>
	<div class="prisna_gwt_setting">
		<div class="prisna_gwt_field" id="{{ id }}">
			<div class="prisna_gwt_toggle_container">
				<input type="radio" name="{{ name }}" value="{{ value_true }}"{{ value_true.checked.true:begin }} checked="checked"{{ value_true.checked.true:end }} id="{{ id }}_true" class="prisna_gwt_radio_option" />
				<label for="{{ id }}_true">{{ option_true }}</label>
			</div>
			<div class="prisna_gwt_toggle_container">
				<input type="radio" name="{{ name }}" value="{{ value_false }}"{{ value_false.checked.true:begin }} checked="checked"{{ value_false.checked.true:end }} id="{{ id }}_false" class="prisna_gwt_radio_option" />
				<label for="{{ id }}_false">{{ option_false }}</label>
			</div>
		</div>
		{{ has_dependence.true:begin }}
		<input type="hidden" name="{{ id }}_dependence" id="{{ id }}_dependence" value="{{ formatted_dependence }}" />
		<input type="hidden" name="{{ id }}_dependence_show_value" id="{{ id }}_dependence_show_value" value="{{ formatted_dependence_show_value }}" />
		{{ has_dependence.true:end }}
		<div class="prisna_gwt_clear"></div>
	
	</div>
</div>
