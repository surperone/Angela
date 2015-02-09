!
function(j) {
	var d = {
		rclass: /[\n\t]/g,
		rspaces: /\s+/,
		arraify: function(a) {
			var b = [];
			try {
				b = Array.prototype.slice.call(a, 0)
			} catch(c) {
				for (var d, b = [], e = 0; d = a[e++];) b.push(d)
			}
			return b
		},
		hasClass: function(a, b) {
			return 0 <= (" " + a.className + " ").replace(this.rclass, " ").indexOf(" " + b + " ") ? !0 : !1
		},
		addClass: function(a, b) {
			if (b && "string" == typeof b) {
				var c = (b || "").split(this.rspaces);
				if (1 === a.nodeType) if (a.className) {
					for (var d = " " + a.className + " ",
					e = a.className,
					f = 0,
					g = c.length; g > f; f++) 0 > d.indexOf(" " + c[f] + " ") && (e += " " + c[f]);
					a.className = this.trim(e)
				} else a.className = b
			}
		},
		removeClass: function(a, b) {
			if (b && "string" == typeof b || void 0 === b) {
				var c = (b || "").split(this.rspaces);
				if (1 === a.nodeType && a.className) if (b) {
					for (var d = (" " + a.className + " ").replace(this.rclass, " "), e = 0, f = c.length; f > e; e++) d = d.replace(" " + c[e] + " ", " ");
					a.className = this.trim(d)
				} else a.className = ""
			}
		},
		_id: function(a) {
			return document.getElementById(a)
		},
		_tag: function(a, b) {
			return b = b || document,
			this.arraify(b.getElementsByTagName(a))
		},
		_class: function(a, b, c) {
			if (b = b || document, b.getElementsByClassName) return this.arraify(b.getElementsByClassName(a));
			for (var a = a.replace(/\-/g, "\\-"), d = [], b = this._tag(c || "*", b), c = b.length, a = RegExp("(^|\\s)" + a + "(\\s|$)"); c--;) a.test(b[c].className) && d.push(b[c]);
			return d
		},
		_selector: function(a, b) {
			return b = b || document,
			this.arraify(b.querySelectorAll(a))
		},
		after: function(a, b) {
			b && b.parentNode && b.parentNode.insertBefore(a, b.nextSibling)
		},
		remove: function(a) {
			a && a.parentNode && a.parentNode.removeChild(a)
		},
		each: function(a, b) {
			if ("undefined" !== a.length) for (var c = 0,
			d = a.length; d > c && !1 !== b.call(a[c], c, a[c]); c++);
			else for (name in a) b.call(a[name], name, a[name])
		},
		trim: function(a) {
			return a && a.replace(/^(\s|\u00A0)+/, "").replace(/(\s|\u00A0)+$/, "")
		},
		hide: function(a) {
			a.style.display = "none"
		},
		show: function(a) {
			a.style.display = "block"
		},
		ready: function(a) {
			document.addEventListener("DOMContentLoaded",
			function() {
				a.call(this)
			},
			!1)
		},
		text: function(a, b) {
			return b ? void(a.textContent = b) : this.trim(a.textContent)
		},
		ajax: function() {},
		css3support: function() {
			for (var a = document.createElement("div"), b = ["Webkit", "Moz", "O", "ms", "w3c"], c = !1, d = b.length - 1; d >= 0; d--) {
				if (void 0 !== a.style[b[d] + "AnimationName"]) {
					c = b[d];
					break
				}
				if ("w3c" == b[d] && void 0 !== a.style.animationName) {
					c = b[d];
					break
				}
			}
			return c
		},
		randomInteger: function(a, b) {
			return a + Math.floor(Math.random() * (b - a))
		},
		randomFloat: function(a, b) {
			return a + Math.random() * (b - a)
		},
		getStyle: function(a, b) {
			return document.defaultView.getComputedStyle(b, null).getPropertyValue(a)
		},
		ajax: function(a) {
			this.init(a)
		}
	};
	d.ajax.prototype = {
		init: function(a) {
			this.url = a.url || j.location.href,
			this.method = a.method || "GET",
			this.before = a.before || new Function,
			this.dataType = a.dataType || "json",
			this.send = a.send || null,
			this.after = a.after || new Function,
			this.delay = a.delay || 30,
			this.header = a.header || "",
			this.success = a.success || new Function,
			this.error = a.error || new Function,
			this.timeoutCallback = a.timeoutCallback || !1,
			this.hasDo = !1,
			this.sendxmlHttp()
		},
		createxmlHttp: function() {
			return new XMLHttpRequest
		},
		sendxmlHttp: function() {
			this.isTimeout = !1,
			this.xmlHttp = this.createxmlHttp(),
			this.before(),
			this.xmlHttp.open(this.method, this.url, !0),
			this.timer = setTimeout(this.bind(this.checkTimeout, this), 1e3 * this.delay),
			this.setHeader(this.header),
			this.requestStatus = 0,
			this.xmlHttp.onreadystatechange = this.bind(function() {
				switch (this.xmlHttp.readyState) {
				case 4:
					if (!this.hasDo) {
						this.hasDo = !0;
						try {
							if (clearTimeout(this.timer), this.xmlHttp.status && 200 == this.xmlHttp.status) {
								if ("" === this.xmlHttp.responseText.trim()) {
									this.error(),
									this.after();
									break
								}
								switch (this.contentType = this.xmlHttp.getResponseHeader("Content-Type").replace(/\s|;.*/gi, ""), this.contentType) {
								case "text/javascript":
								case "application/json":
									try {
										this.data = "json" != this.dataType ? this.xmlHttp.responseText: eval("(" + this.xmlHttp.responseText + ")")
									} catch(a) {
										console.log(a)
									}
									break;
								default:
									this.data = this.xmlHttp.responseText
								}
								this.success()
							} else this.isTimeout ? (console.log("Timeout"), this.timeoutCallback ? this.timeoutCallback() : this.error()) : this.isError || (this.data = this.xmlHttp.responseText, this.error());
							this.after()
						} catch(c) {
							console.log(c)
						}
					}
				}
			},
			this),
			this.xmlHttp.send(this.send)
		},
		checkTimeout: function() {
			4 !== this.requestStatus && (this.isTimeout = !0, this.xmlHttp.abort())
		},
		setHeader: function() {
			null == this.send ? this.xmlHttp.setRequestHeader("Content-type", "charset=UTF-8") : this.xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
			var a = this.header.split("&");
			if (a[0].length) for (var c = 0; c < a.length; c++) 2 == b.length && this.xmlHttp.setRequestHeader(b[0], b[1])
		},
		bind: function(a, b) {
			return function() {
				return a.apply(b, arguments)
			}
		}
	},
	j.angela = d;
	var get_script = function(a) {
		var b = document.createElement("script"),
		c = d._tag("head")[0];
		b.src = a,
		c.appendChild(b)
	};
	d.ready(function() {
		var a = d._id("<header></header>"),
		c = d._selector("i.icon-paragraphleft", a)[0],
		m = d._selector("span.icon-list", c)[0],
		e = document.body,
		f = d._id("canvas-menu"),
		h = d._id("surface-content");
		h.addEventListener("touchstart",
		function(a) {
			if (d.hasClass(e, "is-canvas-show")) a.preventDefault(),
			a.stopPropagation(),
			d.removeClass(e, "is-canvas-show"),
			setTimeout(function() {
				d.removeClass(e, "menu-activing")
			},
			300);
			else if (a.target === c || a.target === m) d.addClass(e, "is-canvas-show"),
			d.addClass(e, "menu-activing");
			return ! 1
		},
		!1);
		c.addEventListener("touchend",
		function(a) {
			a.preventDefault();
			return ! 1
		},
		!1);
		d._id("respond") && get_script(Crystal.comment)
	})
} (window);