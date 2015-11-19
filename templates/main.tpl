
<style type="text/css">
<!--
{{ is_inline.true:begin }}
.prisna-gwt-align-left {
	text-align: left !important;
}
.prisna-gwt-align-right {
	text-align: right !important;
}
{{ is_inline.true:end }}
{{ has_flags.true:begin }}
.prisna-gwt-flags-container {
	list-style: none !important;
	margin: 0 !important;
	padding: 0 !important;
	border: none !important;
	clear: both !important;
}
.prisna-gwt-flag-container {
	list-style: none !important;
	display: inline-block;
	margin: 0 2px 0 0 !important;
	padding: 0 !important;
	border: none !important;
}
.prisna-gwt-flag-container a {
	display: inline-block;
	margin: 0 !important;
	padding: 0 !important;
	border: none !important;
	background-repeat: no-repeat !important;
	background-image: url({{ flags_image_path }}/all.png) !important;
	width: 22px !important;
	height: 16px !important;
}
{{ flags_css }}
{{ has_flags.true:end }}
{{ hide_banner.true:begin }}
body {
	top: 0 !important;
}
.goog-te-banner-frame {
	display: none !important;
	visibility: hidden !important;
}
{{ hide_banner.true:end }}
.goog-tooltip,
.goog-tooltip:hover {
	display: none !important;
}
.goog-text-highlight {
	background-color: transparent !important;
	border: none !important;
	box-shadow: none !important;
}
{{ custom_css }}
-->
</style>
{{ has_flags.true:begin }}
<script type="text/javascript">
/*<![CDATA[*/
var PrisnaGWT = {

	_fire_event: function(_element, _event) {
		
		try {
			if (document.createEvent) {
				var ev = document.createEvent("HTMLEvents");
				ev.initEvent(_event, true, true);
				_element.dispatchEvent(ev);
			} 
			else {
				var ev = document.createEventObject();
				_element.fireEvent("on" + _event, ev);
			}
		} 
		catch (e) {
			console.log("Prisna GWT: Browser not supported!");
		}
		
	},

	translate: function(_language) {
	
		var element;
		var combos = document.getElementsByTagName("select"); // IE8 doesn't support getElementsByClassName
		
		for (var i=0; i<combos.length; i++)
			if (combos[i].className == "goog-te-combo")
				element = combos[i];
		
		if (!element)
			return;
		
		element.value = _language;
		this._fire_event(element, "change");

	}
	
};
/*]]>*/
</script>
{{ flags_formatted }}
{{ has_flags.true:end }}
{{ has_container.true:begin }}<div id="google_translate_element" class="prisna-gwt-align-{{ align_mode }}"></div>{{ has_container.true:end }}
<script type="text/javascript">
/*<![CDATA[*/
function initializeGoogleTranslateElement() {
	new google.translate.TranslateElement({
{{ options_formatted }}	}{{ has_container.true:begin }}, "google_translate_element"{{ has_container.true:end }});
}
/*]]>*/
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=initializeGoogleTranslateElement"></script>