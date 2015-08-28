var PrisnaGWTCommon = {

	$: function() {
		var _elements = new Array();
		for (var i = 0; i < arguments.length; i++) {
			var _element = arguments[i];
			if (typeof _element == "string")
				_element = document.getElementById(_element);
			if (arguments.length == 1)
				return _element;
			_elements.push(_element);
		}
		return _elements;
	},
	
	addEvent: function(element, evType, fn, useCapture) {
		if(element.addEventListener) {
			element.addEventListener(evType, fn, useCapture);
			return true;
		}
		else if(element.attachEvent) {
			var r = element.attachEvent("on" + evType, fn);
			return r;
		}
		else {
			element["on" + evType] = fn;
		}
	},

	mergeText: function(message, newValuesArray) {
		result = message;
		var i = 0;
		for(var i=0; i<newValuesArray.length; i++) result = result.replace("["+i+"]", newValuesArray[i]);
		return result;
	},

	unserialize: function(_url) {

		var result = {};
		var full_option;
		var option;
		var value;
		var params = _url;
		var single = arguments[1] || false;

		params = params.slice(params.indexOf('?') + 1).split("&");

		for (var i=0; i<params.length; i++) {
			
			full_option = params[i].split("=");
			option = full_option[0];
			value = full_option[1];

			if (single == option)
				return value;
			
			result[option] = value;
				
		}

		if (single !== false)
			return false;

		return result;
	
	},

	cleanId: function(_string, _separator) {
		
		var separator = _separator || "-";
		
		return _string.replace(/[^a-zA-Z0-9]+|\s+/g, separator).toLowerCase();
		
	},

	getOwnerParent: function(_element) {
		
		if (_element._owner)
			return _element;

		var result = _element.parentNode;
		try {
			while (!result._owner) 
				result = result.parentNode;
			return result;
		}
		catch(e) {
			return null;
		}

	},

	clickSelected: function(_container) {
		
		var fields = jQuery(_container).find("input[type=radio]");
		
		if (fields.length > 0)
			for (var i=0; i<fields.length; i++)
				if (fields[i].checked)
					fields.eq(i).click();
		
	},
	
	getFieldValue: function(_container) {
		
		var fields = jQuery(_container).find("input[type=radio]");
		
		if (fields.length > 0) {
			for (var i=0; i<fields.length; i++)
				if (fields[i].checked)
					return fields[i].value;
		}
		else {
			
			fields = jQuery(_container).find("select");
		
			if (fields.length > 0)
				return fields[0].value;

		}

		return false;
		
	},
	
	getOwner: function(e) {

		return e.srcElement || e.target;

	},
	
	trim: function(_string) {
		
		return this != window ? _string.replace(/^\s*|\s*$/g,"") : null;

	},
	
	startsWith: function(_string, substr) {
		if (this == window) return null;
		return _string.substring(0, substr.length) == substr;
	},

	endsWith: function(_string, substr) {
		if (this == window) return null;
		return _string.length >= substr.length && _string.substring(_string.length - substr.length) == substr;
	},
	
	inArray: function(_value, _array, _property) {
	
		if (!(_array instanceof Array))
			_array = _array.split(",");

		for (var i=0; i<_array.length; i++) {
			if (_array[i] instanceof Object) {
				if (_array[i][_property] == _value)
					return i;
			}				
			else if (_array[i] == _value)
				return i;
		}
				
		return false;
		
	},
	
	getHeadingContainer: function(_item) {

		var section = jQuery(_item).parents(".prisna_gwt_section");
		var heading = section.prevUntil(".prisna_gwt_heading");
		var result = heading.length > 0 ? jQuery(heading[heading.length-1]) : section;
			
		return result.prev();
		
	},
	
	getHeadingObject: function(_item, _headings) {

		var heading_container = this.getHeadingContainer(_item);
		var heading_id = heading_container.attr("id");
		return _headings[heading_id];
		
	}
	
};

PrisnaGWTCommon.Dependencies = {
	
	_targets: {},

	_record: function(_target) {
		
		var foo = _target[0];
		var section = jQuery(foo).parents(".prisna_gwt_section");
		
		this._targets[section.attr("id")] = _target;
		
	},	
	
	simulate: function(_section) {
		
		var section_id = _section.id;
		var type;

		if (typeof this._targets[section_id] != "object")
			return false;
		
		if (PrisnaGWTCommon.CSS.hasClass(_section, "prisna_gwt_toggle"))
			type = "toggle";

		switch (type) {
			case "toggle": {
				for (var i=0; i<this._targets[section_id].length; i++)
					if (this._targets[section_id][i].checked)
						jQuery(this._targets[section_id][i]).click();
				break;
			}
		}
		
	},
	
	add: function(_target, _event, _function) {
		
		this._record(_target);
		
		_target.bind(_event, _function);
		
	}
	
};

PrisnaGWTCommon.Heading = function(_target) {
	
	this._id = _target.id;
	this._target = _target;
	this._items = [];

	this._initialize_items();
	this._initialize_events();
	this._set_styles();	
	
};
	
PrisnaGWTCommon.Heading.prototype._initialize_events = function() {
	
	PrisnaGWTCommon.addEvent(this._target, "click", function(e) {

		var owner = PrisnaGWTCommon.getOwnerParent(PrisnaGWTCommon.getOwner(e));
		owner._owner._click();
		
	}, false);
	
};

PrisnaGWTCommon.Heading.prototype.isShowing = function() {
	
	return !PrisnaGWTCommon.CSS.hasClass(this._target, "prisna_gwt_heading_hiding");
	
};

PrisnaGWTCommon.Heading.prototype._click = function() {
	
	this._show(!this.isShowing());
	
};

PrisnaGWTCommon.Heading.prototype._show = function(_state, _now) {

	PrisnaGWTCommon.CSS.chooseClass(this._target, _state, "prisna_gwt_heading_showing", "prisna_gwt_heading_hiding");

	var items = jQuery(this._items);
	items = items.not(".prisna_gwt_no_display.prisna_gwt_section_tabbed_2");

	if (_now === true) {
		if (_state)
			items.show();
		else
			items.hide();
	}
	else {
		if (_state)
			items.slideDown("fast");
		else
			items.slideUp("fast");

		PrisnaGWTCommon.Cookie.set("prisna_gwt_heading_" + this._id, _state ? "true" : "false", 10, false, false, false);

	}
	
	this._click_items(items, _now === true); // to satisfy dependencies

};

PrisnaGWTCommon.Heading.prototype._click_items = function(_items, _delayed) {

	if (_delayed)
		setTimeout(function() {
			for (var i=0; i<_items.length; i++)
				PrisnaGWTCommon.Dependencies.simulate(_items[i]);
		}, 200);
	else
		for (var i=0; i<_items.length; i++)
			PrisnaGWTCommon.Dependencies.simulate(_items[i]);
	
};

PrisnaGWTCommon.Heading.prototype._click_item = function(_item, _name) {
	
	var type;
	
	if (PrisnaGWTCommon.CSS.hasClass(_item, "prisna_gwt_toggle"))
		type = "toggle";
	
	switch (type) {
		case "toggle": {
			
			break;
		}
	}
	
};

PrisnaGWTCommon.Heading.prototype._initialize_items = function() {

	this._target._owner = this;
	
	var item = this._get_next_sibling(this._target);

	while (item != null && item.className.match(/\bprisna_gwt_section_tabbed_\d+\b/)) {
		this._items.push(item);
		item = this._get_next_sibling(item);
	}
	
	var cookie = PrisnaGWTCommon.Cookie.get("prisna_gwt_heading_" + this._id);
	if (cookie == "true" || cookie == "false")
		this._show(cookie == "true", true);
	
};
	
PrisnaGWTCommon.Heading.prototype._get_next_sibling = function(_reference) {

	var result = _reference.nextSibling;
	while (result != null && result.nodeType != 1)
		result = result.nextSibling;
	
	return result;
	
};
	
PrisnaGWTCommon.Heading.prototype._set_styles = function() {
		
	PrisnaGWTCommon.CSS.addClass(this._target, "prisna_gwt_heading_enabled");
	
};

PrisnaGWTCommon.Cookie = {

	get: function(name) {
		var start = document.cookie.indexOf(name+"=");
		var len = start + name.length + 1;
		if ((!start) && (name != document.cookie.substring(0, name.length))) {
			return null;
		}
		if (start == -1) 
			return null;
		var end = document.cookie.indexOf( ';', len );
		if (end == -1) 
			end = document.cookie.length;
		return decodeURIComponent(document.cookie.substring(len, end));
	},

	set: function(name, value, expires, path, domain, secure) {
		var today = new Date();
		today.setTime(today.getTime());

		if (expires)
			expires = expires * 1000 * 60 * 60 * 24;
		
		var expires_date = new Date(today.getTime() + (expires));
		document.cookie = name + '=' + escape(value) +
			((expires) ? ';expires=' + expires_date.toGMTString() : '') + //expires.toGMTString()
			((path) ? ';path=' + path : '') +
			((domain) ? ';domain=' + domain : '') +
			((secure) ? ';secure' : '');
	},

	remove: function(name, path, domain) {
		if (PrisnaGWTCommon.Cookie.get(name)) document.cookie = name + '=' +
				((path) ? ';path=' + path : '') +
				((domain) ? ';domain=' + domain : '') +
				';expires=Thu, 01-Jan-1970 00:00:01 GMT';
	}
	
};

PrisnaGWTCommon.initializeTooltip = function(_target) {

	jQuery(_target).tooltip({
		effect: "slide",
		relative: true,
		direction: "left",
		position: "center left"
	});

};

PrisnaGWTCommon.CSS = {

	hasClass: function(_element, className) { 
		_element = PrisnaGWTCommon.$(_element);
		if (!_element)
			return;
		if(_element&&className&&_element.className) { 
			return new RegExp('\\b'+PrisnaGWTCommon.trim(className)+'\\b').test(_element.className);
		}
		return false;
	},
	
	addClass: function(_element, className) {
		_element = PrisnaGWTCommon.$(_element);
		if (!_element)
			return;
		if(_element&&className) {
			if(!PrisnaGWTCommon.CSS.hasClass(_element, className)) {
				className = PrisnaGWTCommon.trim(className);
				if(_element.className) { 
					_element.className += " " + className;
				}
				else { 
					_element.className = className; 
				}
			}
		}
		return this;
	},
	
	removeClass: function(_element, className) {
		_element = PrisnaGWTCommon.$(_element);
		if (!_element)
			return;
		if(_element&&className&&_element.className) {
			className = PrisnaGWTCommon.trim(className);
			var regexp = new RegExp("\\b" + className + "\\b","g");
			_element.className = _element.className.replace(regexp,"");
		}
		return this;
	},
		
	conditionClass: function(_element, className, shouldShow) { 
		if(shouldShow) { 
			PrisnaGWTCommon.CSS.addClass(_element, className);
		}
		else { 
			PrisnaGWTCommon.CSS.removeClass(_element, className);
		}
	},
		
	chooseClass: function(_element, expression, trueClass, falseClass) { 
		PrisnaGWTCommon.CSS.conditionClass(_element, trueClass, expression);
		PrisnaGWTCommon.CSS.conditionClass(_element, falseClass, !expression);
	},
		
	setClass: function(_element, className) { 
		_element = PrisnaGWTCommon.$(_element);
		if (!_element)
			return;
		_element.className = className;
		return this;
	},
		
	toggleClass: function(_element, className) {
		_element = PrisnaGWTCommon.$(_element);
		if(PrisnaGWTCommon.CSS.hasClass(_element, className)) {
			return PrisnaGWTCommon.CSS.removeClass(_element, className);
		}
		else {
			return PrisnaGWTCommon.CSS.addClass(_element, className);
		}
	},
		
	setStyle: function(_element, name, value) {
		if (!_element)
			return;
		_element.style[name] = value;
		return _element;
	}

};

PrisnaGWTCommon.Tabs = function(level) {
	
	this.tabs = [];
	this.level = typeof level == "undefined" ? 0 : level;

};

PrisnaGWTCommon.Tabs.prototype.registerTab = function(param, callback) {
	
	var target = PrisnaGWTCommon.$(param + "_menu");
	
	if (!target)
		return;
	
	target._owner = this;
	
	PrisnaGWTCommon.addEvent(PrisnaGWTCommon.$(param + "_menu"), "click", function(e) {

		var owner = PrisnaGWTCommon.getOwnerParent(PrisnaGWTCommon.getOwner(e));
		owner._owner.selectTab(param);
		
		if (typeof callback != "undefined")
			callback(param);
		
	}, false);
	
	this.tabs.push(param);

};

PrisnaGWTCommon.Tabs.prototype.selectTab = function(name) {

	for(var i=0; i<this.tabs.length; i++) {
		this.displayTab(this.tabs[i], name == this.tabs[i]);
		this.selectMenu(this.tabs[i], false);
	}

	this.selectMenu(name, true);

	this.setField(name);

};

PrisnaGWTCommon.Tabs.prototype.setField = function(name) {
	
	var aux = this.level !== 0 ? "_" + this.level : "";
	PrisnaGWTCommon.$("prisna_tab" + aux).value = name;
	
};

PrisnaGWTCommon.Tabs.prototype.displayTab = function(name, state) {

	PrisnaGWTCommon.CSS.chooseClass(PrisnaGWTCommon.$(name + "_tab"), state, "prisna_gwt_display", "prisna_gwt_no_display");

};

PrisnaGWTCommon.Tabs.prototype.getSelected = function() {
	
	for(var i=0; i<this.tabs.length; i++) 
		if(PrisnaGWTCommon.CSS.hasClass(this.tabs[i] + "_menu", "prisna_wp_translate_ui_tab_selected")) 
			return this.tabs[i];
	
	return "";
};

PrisnaGWTCommon.Tabs.prototype.selectMenu = function(name, state) {

	PrisnaGWTCommon.CSS.chooseClass(PrisnaGWTCommon.$(name + "_menu"), state, "prisna_gwt_ui_tab_selected", "prisna_gwt_ui_tab_unselected");

};

/*!
 * 
 * tooltip/tooltip.js
 * tooltip/tooltip.dynamic.js
 * tooltip/tooltip.slide.js
 * 
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 * 
 * http://flowplayer.org/tools/
 * 
 */
(function(a) {
	a.tools = a.tools || {
		version: "dev"
	}, a.tools.tooltip = {
		conf: {
			effect: "toggle",
			fadeOutSpeed: "fast",
			predelay: 0,
			delay: 30,
			opacity: 1,
			tip: 0,
			fadeIE: !1,
			position: ["top", "center"],
			offset: [0, 0],
			relative: !1,
			cancelDefault: !0,
			events: {
				def: "mouseenter,mouseleave",
				input: "focus,blur",
				widget: "focus mouseenter,blur mouseleave",
				tooltip: "mouseenter,mouseleave"
			},
			layout: "<div/>",
			tipClass: "tooltip"
		},
		addEffect: function(a, c, d) {
			b[a] = [c, d]
		}
	};
	var b = {
		toggle: [function(a) {
			var b = this.getConf(),
				c = this.getTip(),
				d = b.opacity;
			d < 1 && c.css({
				opacity: d
			}), c.show(), a.call()
		}, function(a) {
			this.getTip().hide(), a.call()
		}],
		fade: [function(b) {
			var c = this.getConf();
			!a.browser.msie || c.fadeIE ? this.getTip().fadeTo(c.fadeInSpeed, c.opacity, b) : (this.getTip().show(), b())
		}, function(b) {
			var c = this.getConf();
			!a.browser.msie || c.fadeIE ? this.getTip().fadeOut(c.fadeOutSpeed, b) : (this.getTip().hide(), b())
		}]
	};

	function c(b, c, d) {
		var e = d.relative ? b.position().top : b.offset().top,
			f = d.relative ? b.position().left : b.offset().left,
			g = d.position[0];
		e -= c.outerHeight() - d.offset[0], f += b.outerWidth() + d.offset[1], /iPad/i.test(navigator.userAgent) && (e -= a(window).scrollTop());
		var h = c.outerHeight() + b.outerHeight();
		g == "center" && (e += h / 2), g == "bottom" && (e += h), g = d.position[1];
		var i = c.outerWidth() + b.outerWidth();
		g == "center" && (f -= i / 2), g == "left" && (f -= i);
		return {
			top: e,
			left: f
		}
	}

	function d(d, e) {
		var f = this,
			g = d.add(f),
			h, i = 0,
			j = 0,
			k = d.attr("title"),
			l = d.attr("data-tooltip"),
			m = b[e.effect],
			n, o = d.is(":input"),
			p = o && d.is(":checkbox, :radio, select, :button, :submit"),
			q = d.attr("type"),
			r = e.events[q] || e.events[o ? p ? "widget" : "input" : "def"];
		if (!m) throw "Nonexistent effect \"" + e.effect + "\"";
		r = r.split(/,\s*/);
		if (r.length != 2) throw "Tooltip: bad events configuration for " + q;
		d.bind(r[0], function(a) {
			clearTimeout(i), e.predelay ? j = setTimeout(function() {
				f.show(a)
			}, e.predelay) : f.show(a)
		}).bind(r[1], function(a) {
			clearTimeout(j), e.delay ? i = setTimeout(function() {
				f.hide(a)
			}, e.delay) : f.hide(a)
		}), k && e.cancelDefault && (d.removeAttr("title"), d.data("title", k)), a.extend(f, {
			show: function(b) {
				if (!h) {
					l ? h = a(l) : e.tip ? h = a(e.tip).eq(0) : k ? h = a(e.layout).addClass(e.tipClass).appendTo(document.body).hide().append(k) : (h = d.next(), h.length || (h = d.parent().next()));
					if (!h.length) throw "Cannot find tooltip for " + d
				}
				if (f.isShown()) return f;
				h.stop(!0, !0);
				var o = c(d, h, e);
				e.tip && h.html(d.data("title")), b = a.Event(), b.type = "onBeforeShow", g.trigger(b, [o]);
				if (b.isDefaultPrevented()) return f;
				o = c(d, h, e), h.css({
					position: "absolute",
					top: o.top,
					left: o.left
				}), n = !0, m[0].call(f, function() {
					b.type = "onShow", n = "full", g.trigger(b)
				});
				var p = e.events.tooltip.split(/,\s*/);
				h.data("__set") || (h.unbind(p[0]).bind(p[0], function() {
					clearTimeout(i), clearTimeout(j)
				}), p[1] && !d.is("input:not(:checkbox, :radio), textarea") && h.unbind(p[1]).bind(p[1], function(a) {
					a.relatedTarget != d[0] && d.trigger(r[1].split(" ")[0])
				}), e.tip || h.data("__set", !0));
				return f
			},
			hide: function(c) {
				if (!h || !f.isShown()) return f;
				c = a.Event(), c.type = "onBeforeHide", g.trigger(c);
				if (!c.isDefaultPrevented()) {
					n = !1, b[e.effect][1].call(f, function() {
						c.type = "onHide", g.trigger(c)
					});
					return f
				}
			},
			isShown: function(a) {
				return a ? n == "full" : n
			},
			getConf: function() {
				return e
			},
			getTip: function() {
				return h
			},
			getTrigger: function() {
				return d
			}
		}), a.each("onHide,onBeforeShow,onShow,onBeforeHide".split(","), function(b, c) {
			a.isFunction(e[c]) && a(f).bind(c, e[c]), f[c] = function(b) {
				b && a(f).bind(c, b);
				return f
			}
		})
	}
	a.fn.tooltip = function(b) {
		var c = this.data("tooltip");
		if (c) return c;
		b = a.extend(!0, {}, a.tools.tooltip.conf, b), typeof b.position == "string" && (b.position = b.position.split(/,?\s/)), this.each(function() {
			c = new d(a(this), b), a(this).data("tooltip", c)
		});
		return b.api ? c : this
	}
})(jQuery);
(function(a) {
	var b = a.tools.tooltip;
	b.dynamic = {
		conf: {
			classNames: "top right bottom left"
		}
	};

	function c(b) {
		var c = a(window),
			d = c.width() + c.scrollLeft(),
			e = c.height() + c.scrollTop();
		return [b.offset().top <= c.scrollTop(), d <= b.offset().left + b.width(), e <= b.offset().top + b.height(), c.scrollLeft() >= b.offset().left]
	}

	function d(a) {
		var b = a.length;
		while (b--)
			if (a[b]) return !1;
		return !0
	}
	a.fn.dynamic = function(e) {
		typeof e == "number" && (e = {
			speed: e
		}), e = a.extend({}, b.dynamic.conf, e);
		var f = e.classNames.split(/\s/),
			g;
		this.each(function() {
			var b = a(this).tooltip().onBeforeShow(function(b, h) {
				var i = this.getTip(),
					j = this.getConf();
				g || (g = [j.position[0], j.position[1], j.offset[0], j.offset[1], a.extend({}, j)]), a.extend(j, g[4]), j.position = [g[0], g[1]], j.offset = [g[2], g[3]], i.css({
					visibility: "hidden",
					position: "absolute",
					top: h.top,
					left: h.left
				}).show();
				var k = c(i);
				if (!d(k)) {
					k[2] && (a.extend(j, e.top), j.position[0] = "top", i.addClass(f[0])), k[3] && (a.extend(j, e.right), j.position[1] = "right", i.addClass(f[1])), k[0] && (a.extend(j, e.bottom), j.position[0] = "bottom", i.addClass(f[2])), k[1] && (a.extend(j, e.left), j.position[1] = "left", i.addClass(f[3]));
					if (k[0] || k[2]) j.offset[0] *= -1;
					if (k[1] || k[3]) j.offset[1] *= -1
				}
				i.css({
					visibility: "visible"
				}).hide()
			});
			b.onBeforeShow(function() {
				var a = this.getConf(),
					b = this.getTip();
				setTimeout(function() {
					a.position = [g[0], g[1]], a.offset = [g[2], g[3]]
				}, 0)
			}), b.onHide(function() {
				var a = this.getTip();
				a.removeClass(e.classNames)
			}), ret = b
		});
		return e.api ? ret : this
	}
})(jQuery);
(function(a) {
	var b = a.tools.tooltip;
	a.extend(b.conf, {
		direction: "up",
		bounce: !1,
		slideOffset: 10,
		slideInSpeed: 200,
		slideOutSpeed: 200,
		slideFade: !a.browser.msie
	});
	var c = {
		up: ["-", "top"],
		down: ["+", "top"],
		left: ["-", "left"],
		right: ["+", "left"]
	};
	b.addEffect("slide", function(a) {
		var b = this.getConf(),
			d = this.getTip(),
			e = b.slideFade ? {
				opacity: b.opacity
			} : {},
			f = c[b.direction] || c.up;
		e[f[1]] = f[0] + "=" + b.slideOffset, b.slideFade && d.css({
			opacity: 0
		}), d.show().animate(e, b.slideInSpeed, a)
	}, function(b) {
		var d = this.getConf(),
			e = d.slideOffset,
			f = d.slideFade ? {
				opacity: 0
			} : {},
			g = c[d.direction] || c.up,
			h = "" + g[0];
		d.bounce && (h = h == "+" ? "-" : "+"), f[g[1]] = h + "=" + e, this.getTip().animate(f, d.slideOutSpeed, function() {
			a(this).hide(), b.call()
		})
	})
})(jQuery);

// jQuery List DragSort v0.4.3
// License: http://dragsort.codeplex.com/license
(function(b) {
	b.fn.dragsort = function(k) {
		var d = b.extend({}, b.fn.dragsort.defaults, k),
			g = [],
			a = null,
			j = null;
		this.selector && b("head").append("<style type='text/css'>" + (this.selector.split(",").join(" " + d.dragSelector + ",") + " " + d.dragSelector) + " { cursor: move; }</style>");
		this.each(function(k, i) {
			b(i).is("table") && b(i).children().size() == 1 && b(i).children().is("tbody") && (i = b(i).children().get(0));
			var m = {
				draggedItem: null,
				placeHolderItem: null,
				pos: null,
				offset: null,
				offsetLimit: null,
				scroll: null,
				container: i,
				init: function() {
					b(this.container).attr("data-listIdx", k).mousedown(this.grabItem).find(d.dragSelector).css("cursor", "move");
					b(this.container).children(d.itemSelector).each(function(a) {
						b(this).attr("data-itemIdx", a)
					})
				},
				grabItem: function(e) {
					if (!(e.which != 1 || b(e.target).is(d.dragSelectorExclude))) {
						for (var c = e.target; !b(c).is("[data-listIdx='" + b(this).attr("data-listIdx") + "'] " + d.dragSelector);) {
							if (c == this) return;
							c = c.parentNode
						}
						a != null && a.draggedItem != null && a.dropItem();
						b(e.target).css("cursor", "move");
						a = g[b(this).attr("data-listIdx")];
						a.draggedItem = b(c).closest(d.itemSelector);
						var c = parseInt(a.draggedItem.css("marginTop")),
							f = parseInt(a.draggedItem.css("marginLeft"));
						a.offset = a.draggedItem.offset();
						a.offset.top = e.pageY - a.offset.top + (isNaN(c) ? 0 : c) - 1;
						a.offset.left = e.pageX - a.offset.left + (isNaN(f) ? 0 : f) - 1;
						if (!d.dragBetween) c = b(a.container).outerHeight() == 0 ? Math.max(1, Math.round(0.5 + b(a.container).children(d.itemSelector).size() * a.draggedItem.outerWidth() / b(a.container).outerWidth())) * a.draggedItem.outerHeight() : b(a.container).outerHeight(), a.offsetLimit = b(a.container).offset(), a.offsetLimit.right = a.offsetLimit.left + b(a.container).outerWidth() - a.draggedItem.outerWidth(), a.offsetLimit.bottom = a.offsetLimit.top + c - a.draggedItem.outerHeight();
						var c = a.draggedItem.height(),
							f = a.draggedItem.width(),
							h = a.draggedItem.attr("style");
						if (jQuery.browser.msie) f = f + 2;
						a.draggedItem.attr("data-origStyle", h ? h : "");
						d.itemSelector == "tr" ? (a.draggedItem.children().each(function() {
							b(this).width(b(this).width())
						}), a.placeHolderItem = a.draggedItem.clone().attr("data-placeHolder", !0), a.draggedItem.after(a.placeHolderItem), a.placeHolderItem.children().each(function() {
							b(this).css({
								borderWidth: 0,
								width: b(this).width() + 1,
								height: b(this).height() + 1
							}).html("&nbsp;")
						})) : (a.draggedItem.after(d.placeHolderTemplate), a.placeHolderItem = a.draggedItem.next().css({
							height: c,
							width: f
						}).attr("data-placeHolder", !0));
						a.draggedItem.css({
							position: "absolute",
							opacity: 0.8,
							"z-index": 999,
							height: c,
							width: f
						});
						b(g).each(function(a, b) {
							b.createDropTargets();
							b.buildPositionTable()
						});
						a.scroll = {
							moveX: 0,
							moveY: 0,
							maxX: b(document).width() - b(window).width(),
							maxY: b(document).height() - b(window).height()
						};
						a.scroll.scrollY = window.setInterval(function() {
							if (d.scrollContainer != window) b(d.scrollContainer).scrollTop(b(d.scrollContainer).scrollTop() + a.scroll.moveY);
							else {
								var c = b(d.scrollContainer).scrollTop();
								if (a.scroll.moveY > 0 && c < a.scroll.maxY || a.scroll.moveY < 0 && c > 0) b(d.scrollContainer).scrollTop(c + a.scroll.moveY), a.draggedItem.css("top", a.draggedItem.offset().top + a.scroll.moveY + 1)
							}
						}, 10);
						a.scroll.scrollX = window.setInterval(function() {
							if (d.scrollContainer != window) b(d.scrollContainer).scrollLeft(b(d.scrollContainer).scrollLeft() + a.scroll.moveX);
							else {
								var c = b(d.scrollContainer).scrollLeft();
								if (a.scroll.moveX > 0 && c < a.scroll.maxX || a.scroll.moveX < 0 && c > 0) b(d.scrollContainer).scrollLeft(c + a.scroll.moveX), a.draggedItem.css("left", a.draggedItem.offset().left + a.scroll.moveX + 1)
							}
						}, 10);
						a.setPos(e.pageX, e.pageY);
						b(document).bind("selectstart", a.stopBubble);
						b(document).bind("mousemove", a.swapItems);
						b(document).bind("mouseup", a.dropItem);
						d.scrollContainer != window && b(window).bind("DOMMouseScroll mousewheel", a.wheel);
						return !1
					}
				},
				setPos: function(e, c) {
					var f = c - this.offset.top,
						h = e - this.offset.left;
					d.dragBetween || (f = Math.min(this.offsetLimit.bottom, Math.max(f, this.offsetLimit.top)), h = Math.min(this.offsetLimit.right, Math.max(h, this.offsetLimit.left)));
					this.draggedItem.parents().each(function() {
						if (b(this).css("position") != "static" && (!b.browser.mozilla || b(this).css("display") != "table")) {
							var a = b(this).offset();
							f -= a.top;
							h -= a.left;
							return !1
						}
					});
					if (d.scrollContainer == window) c -= b(window).scrollTop(), e -= b(window).scrollLeft(), c = Math.max(0, c - b(window).height() + 5) + Math.min(0, c - 5), e = Math.max(0, e - b(window).width() + 5) + Math.min(0, e - 5);
					else var l = b(d.scrollContainer),
						g = l.offset(),
						c = Math.max(0, c - l.height() - g.top) + Math.min(0, c - g.top),
						e = Math.max(0, e - l.width() - g.left) + Math.min(0, e - g.left);
					a.scroll.moveX = e == 0 ? 0 : e * d.scrollSpeed / Math.abs(e);
					a.scroll.moveY = c == 0 ? 0 : c * d.scrollSpeed / Math.abs(c);
					this.draggedItem.css({
						top: f,
						left: h
					})
				},
				wheel: function(e) {
					if ((b.browser.safari || b.browser.mozilla) && a && d.scrollContainer != window) {
						var c = b(d.scrollContainer),
							f = c.offset();
						e.pageX > f.left && e.pageX < f.left + c.width() && e.pageY > f.top && e.pageY < f.top + c.height() && (f = e.detail ? e.detail * 5 : e.wheelDelta / -2, c.scrollTop(c.scrollTop() + f), e.preventDefault())
					}
				},
				buildPositionTable: function() {
					var a = this.draggedItem == null ? null : this.draggedItem.get(0),
						c = [];
					b(this.container).children(d.itemSelector).each(function(d, h) {
						if (h != a) {
							var g = b(h).offset();
							g.right = g.left + b(h).width();
							g.bottom = g.top + b(h).height();
							g.elm = h;
							c.push(g)
						}
					});
					this.pos = c
				},
				dropItem: function() {
					if (a.draggedItem != null) {
						b(a.container).find(d.dragSelector).css("cursor", "move");
						a.placeHolderItem.before(a.draggedItem);
						var e = a.draggedItem.attr("data-origStyle");
						a.draggedItem.attr("style", e);
						e == "" && a.draggedItem.removeAttr("style");
						a.draggedItem.removeAttr("data-origStyle");
						a.placeHolderItem.remove();
						b("[data-dropTarget]").remove();
						window.clearInterval(a.scroll.scrollY);
						window.clearInterval(a.scroll.scrollX);
						var c = !1;
						b(g).each(function() {
							b(this.container).children(d.itemSelector).each(function(a) {
								parseInt(b(this).attr("data-itemIdx")) != a && (c = !0, b(this).attr("data-itemIdx", a))
							})
						});
						c && d.dragEnd.apply(a.draggedItem);
						a.draggedItem = null;
						b(document).unbind("selectstart", a.stopBubble);
						b(document).unbind("mousemove", a.swapItems);
						b(document).unbind("mouseup", a.dropItem);
						d.scrollContainer != window && b(window).unbind("DOMMouseScroll mousewheel", a.wheel);
						return !1
					}
				},
				stopBubble: function() {
					return !1
				},
				swapItems: function(e) {
					if (a.draggedItem == null) return !1;
					a.setPos(e.pageX, e.pageY);
					for (var c = a.findPos(e.pageX, e.pageY), f = a, h = 0; c == -1 && d.dragBetween && h < g.length; h++) c = g[h].findPos(e.pageX, e.pageY), f = g[h];
					if (c == -1 || b(f.pos[c].elm).attr("data-placeHolder")) return !1;
					j == null || j.top > a.draggedItem.offset().top || j.left > a.draggedItem.offset().left ? b(f.pos[c].elm).before(a.placeHolderItem) : b(f.pos[c].elm).after(a.placeHolderItem);
					b(g).each(function(a, b) {
						b.createDropTargets();
						b.buildPositionTable()
					});
					j = a.draggedItem.offset();
					return !1
				},
				findPos: function(a, b) {
					for (var d = 0; d < this.pos.length; d++)
						if (this.pos[d].left < a && this.pos[d].right > a && this.pos[d].top < b && this.pos[d].bottom > b) return d;
					return -1
				},
				createDropTargets: function() {
					d.dragBetween && b(g).each(function() {
						var d = b(this.container).find("[data-placeHolder]"),
							c = b(this.container).find("[data-dropTarget]");
						d.size() > 0 && c.size() > 0 ? c.remove() : d.size() == 0 && c.size() == 0 && (b(this.container).append(a.placeHolderItem.removeAttr("data-placeHolder").clone().attr("data-dropTarget", !0)), a.placeHolderItem.attr("data-placeHolder", !0))
					})
				}
			};
			m.init();
			g.push(m)
		});
		return this
	};
	b.fn.dragsort.defaults = {
		itemSelector: "li",
		dragSelector: "li",
		dragSelectorExclude: "input, textarea, a[href]",
		dragEnd: function() {},
		dragBetween: !1,
		placeHolderTemplate: "<li>&nbsp;</li>",
		scrollContainer: window,
		scrollSpeed: 5
	}
})(jQuery);