var PrisnaGWTAdmin = {

	_tabs: {
		general: null,
		advanced: null
	},
	
	_form: null,
	_action: null,
	_buttons: {},
	
	_visual: {},
	
	_headings: {},
	
	_fields: {
		general: {},
		advanced: {}
	},
	
	initialize: function() {
		
		if (typeof PrisnaGWTCommon == "undefined") {
			setTimeout(function() {
				PrisnaGWTAdmin.initialize();
			}, 200);
			return;
		}

		PrisnaGWTAdmin._initialize_elements();
		PrisnaGWTAdmin._initialize_tooltips();
		PrisnaGWTAdmin._initialize_headings();
		PrisnaGWTAdmin._initialize_visual_fields();
		PrisnaGWTAdmin._initialize_languages();
		PrisnaGWTAdmin._initialize_tabs();

		PrisnaGWTAdmin._initialize_dependences();
		
		PrisnaGWTCommon.clickSelected("#section_prisna_style_inline");
		
	}, 
	
	_initialize_tabs: function() {
		
		jQuery(".prisna_gwt_ui_tab_unselected").removeClass("prisna_gwt_hidden_important");
		
		this._tabs.general = new PrisnaGWTCommon.Tabs();

		this._tabs.general.registerTab("general", PrisnaGWTAdmin._on_tab_change);
		this._tabs.general.registerTab("advanced", PrisnaGWTAdmin._on_tab_change);
		this._tabs.general.registerTab("premium", PrisnaGWTAdmin._on_tab_change);

		this._on_tab_change(this._tabs.general.getSelected());
		
		this._tabs.advanced = new PrisnaGWTCommon.Tabs(2);

		this._tabs.advanced.registerTab("advanced_general");
		this._tabs.advanced.registerTab("advanced_import_export");

	},

	_on_tab_change: function(_param) {
		
		PrisnaGWTAdmin._show_buttons(_param != "premium");
		
	},

	_show_buttons: function(_state) {
		
		if (_state) {
			this._buttons.save.show();
			this._buttons.reset.show();
		}
		else {
			this._buttons.save.hide();
			this._buttons.reset.hide();
		}
		
	},

	_initialize_elements: function() {
	
		this._form = PrisnaGWTCommon.$("prisna_admin");
		this._action = PrisnaGWTCommon.$("prisna_gwt_admin_action");

		this._fields.general.all_languages = jQuery("#section_prisna_all_languages input");
		this._fields.general.display_mode = jQuery("#prisna_display_mode");
		this._fields.general.style_inline = jQuery("#section_prisna_style_inline input");
		this._fields.general.show_flags = jQuery("#section_prisna_show_flags input");
		this._fields.general.languages = jQuery("#section_prisna_languages input");

		this._fields.advanced.google_analytics = jQuery("#prisna_google_analytics input");

		this._buttons.save = jQuery(".button-primary");
		this._buttons.reset = jQuery(".reset-settings");

	},
	
	_initialize_dependences: function() {	

		PrisnaGWTCommon.Dependencies.add(this._fields.general.all_languages, "click", function() {

			PrisnaGWTAdmin.showSection("section_prisna_available_languages", this.value != "true");
			
		});

		PrisnaGWTCommon.Dependencies.add(this._fields.general.display_mode, "change", function() {

			PrisnaGWTAdmin.showSection("section_prisna_style_inline", this.value == "inline");
			PrisnaGWTAdmin.showSection("section_prisna_style_tabbed", this.value == "tabbed");
			PrisnaGWTAdmin.showSection("section_prisna_align_mode", this.value == "inline");
			PrisnaGWTAdmin.showSection("section_prisna_show_flags", this.value == "inline");
			
			var show_flags = this.value == "inline" && PrisnaGWTCommon.getFieldValue("#section_prisna_show_flags") == "true" && PrisnaGWTCommon.getFieldValue("#section_prisna_style_inline") != "dropdown";
			
			PrisnaGWTAdmin.showSection("section_prisna_languages", show_flags);
			PrisnaGWTAdmin.showSection("section_prisna_languages_order", show_flags);
			
		});	
	
		PrisnaGWTCommon.Dependencies.add(this._fields.general.style_inline, "click", function() {

			var display_mode = PrisnaGWTCommon.getFieldValue("#section_prisna_display_mode");
			var show_flags = PrisnaGWTCommon.getFieldValue("#section_prisna_show_flags");
			
			PrisnaGWTAdmin.showSection("section_prisna_show_flags", display_mode == "inline" && this.value != "dropdown");
			PrisnaGWTAdmin.showSection("section_prisna_languages", display_mode == "inline" && this.value != "dropdown" && show_flags == "true");
			PrisnaGWTAdmin.showSection("section_prisna_languages_order", display_mode == "inline" && this.value != "dropdown" && show_flags == "true");
			
		});
		
		PrisnaGWTCommon.Dependencies.add(this._fields.general.show_flags, "click", function() {

			var show_languages = PrisnaGWTCommon.getFieldValue("#section_prisna_display_mode") == "inline" && this.value == "true";

			PrisnaGWTAdmin.showSection("section_prisna_languages", show_languages);
			PrisnaGWTAdmin.showSection("section_prisna_languages_order", show_languages);
			
		});
	
		PrisnaGWTCommon.Dependencies.add(this._fields.advanced.google_analytics, "click", function() {

			var heading = PrisnaGWTCommon.getHeadingObject(this, PrisnaGWTAdmin._headings);

			PrisnaGWTAdmin.showSection("section_prisna_google_analytics_code", this.value == "true" && heading.isShowing());
			
		});

	},
	
	_initialize_headings: function() {
		
		var headings = jQuery(".prisna_gwt_heading");
		for (var i=0; i<headings.length; i++)
			PrisnaGWTAdmin._headings[headings[i].id] = new PrisnaGWTCommon.Heading(headings[i]);

	},
	
	_initialize_visual_fields: function() {
		
		var fields = jQuery(".prisna_gwt_visual input");
		for (var i=0; i<fields.length; i++)
			if (fields[i].checked)
				this._visual[fields.eq(i).attr("name")] = fields[i].value;
		
		jQuery(".prisna_gwt_visual input").click(function() {

			var checkbox = jQuery(this);
			
			checkbox.parents(".prisna_gwt_visual").find(".prisna_gwt_field").removeClass("prisna_gwt_visual_checked");
			checkbox.parents(".prisna_gwt_field").addClass("prisna_gwt_visual_checked");

		});
		
	},
		
	_initialize_languages: function() {

		var sorter = jQuery("#section_prisna_languages_order ul.prisna_gwt_language_order_group");
	
		sorter.dragsort({
			dragBetween: true,
			placeHolderTemplate: '<li class="prisna_gwt_language_order_item prisna_gwt_language_place_holder"></li>',
			dragEnd: this._languages_order_update
		});
	
		this._fields.general.languages.click(function() {
			
			PrisnaGWTAdmin._languages_update(this);
			
		});
		
	},
	
	adjustPost: function(_event, _element) {
		
		if (event.which == 13 || event.keyCode == 13)
			return false;
		
		if (!_element)
			return true;
		
		var target = PrisnaGWTCommon.$(_element.id + "_post");
		var ini = _element.value.indexOf("?") != -1 ? "&" : "?";
		
		target.innerHTML = target.innerHTML.replace(/^(\?|\&amp\;)/, ini);
		
		return true;
		
	},
	
	setLanguageLite: function(_data) {
	
		var select = this._fields.translations.languages.get(0);
		select.options.length = 0;
				
		var data = [{ 
			text: '',
			value: '' 
		}];

		for (var i in _data)
			data.push({
				text: i,
				value: _data[i].file
			});

		for(var i=0; i < data.length; i++)
			select.options.add(new Option(data[i].text, data[i].value));
		
	},
	
	_languages_update: function(_checkbox) {

		if (_checkbox.checked) {
			
			var container = jQuery(_checkbox).parents(".prisna_gwt_language_item");
			var item = container.find(".prisna_gwt_language_order_item").clone(false);
			item.attr("id", "prisna_gwt_language_order_item_" + _checkbox.value);
		
			var sorter = jQuery("#section_prisna_languages_order ul.prisna_gwt_language_order_group");
			sorter.append(item);
			
		}
		else
			jQuery("#prisna_gwt_language_order_item_" + _checkbox.value).remove();
		
		this._languages_order_update();
		
	},
	
	_languages_order_update: function() {
		
		var result = [];
		var target = jQuery("#prisna_languages_order");
		var items = jQuery("#section_prisna_languages_order ul.prisna_gwt_language_order_group input");
		for (var i=0; i<items.length; i++)
			result.push(items[i].value);
		target.val(result.join(","));
		
	},
	
	_initialize_tooltips: function() {
		
		PrisnaGWTCommon.initializeTooltip(".prisna_gwt_tooltip");
		
	},
	
	showSection: function(_section_id, _state, _now) {
		
		if (_now === true) {
			if (_state)
				jQuery("#" + _section_id).show();
			else
				jQuery("#" + _section_id).hide();
		}
		else {
			if (_state)
				jQuery("#" + _section_id).slideDown("fast");
			else
				jQuery("#" + _section_id).slideUp("fast");
		}
		
	},
	
	submitSettings: function() {
		
		this._form.submit();
		
	},
	
	resetSettings: function(_message) {
	
		if (confirm(_message)) {
			this._action.value = "prisna_gwt_reset_settings";
			this._form.submit();
			return true;
		} 

		return false;
		
	},
	
	hideMessage: function(_selector, _delay) {
		
		setTimeout(function() {
			jQuery(_selector).animate({
				opacity: "toggle",
				height: "toggle"
			}, "fast")
		}, _delay);
		
	}

};