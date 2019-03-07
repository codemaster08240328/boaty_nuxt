
! function(t) {
    "use strict";
    var e = [
        ["#000000", "#424242", "#636363", "#9C9C94", "#CEC6CE", "#EFEFEF", "#F7F7F7", "#FFFFFF"],
        ["#FF0000", "#FF9C00", "#FFFF00", "#00FF00", "#00FFFF", "#0000FF", "#9C00FF", "#FF00FF"],
        ["#F7C6CE", "#FFE7CE", "#FFEFC6", "#D6EFD6", "#CEDEE7", "#CEE7F7", "#D6D6E7", "#E7D6DE"],
        ["#E79C9C", "#FFC69C", "#FFE79C", "#B5D6A5", "#A5C6CE", "#9CC6EF", "#B5A5D6", "#D6A5BD"],
        ["#E76363", "#F7AD6B", "#FFD663", "#94BD7B", "#73A5AD", "#6BADDE", "#8C7BC6", "#C67BA5"],
        ["#CE0000", "#E79439", "#EFC631", "#6BA54A", "#4A7B8C", "#3984C6", "#634AA5", "#A54A7B"],
        ["#9C0000", "#B56308", "#BD9400", "#397B21", "#104A5A", "#085294", "#311873", "#731842"],
        ["#630000", "#7B3900", "#846300", "#295218", "#083139", "#003163", "#21104A", "#4A1031"]
    ],
        i = function(e, i) {
            e.addClass("bootstrap-colorpalette");
            var n = [];
            t.each(i, function(e, i) {
                n.push("<div>"), t.each(i, function(t, e) {
                    var i = ['<button type="button" class="btn-color" style="background-color:', e, '" data-value="', e, '" title="', e, '"></button>'].join("");
                    n.push(i)
                }), n.push("</div>")
            }), e.html(n.join(""))
        }, n = function(e) {
            e.element.on("click", function(i) {
                var n = t(i.target),
                    s = n.closest(".btn-color");
                if (s[0]) {
                    var a = s.attr("data-value");
                    e.value = a, e.element.trigger({
                        type: "selectColor",
                        color: a,
                        element: e.element
                    })
                }
            })
        }, s = function(t, s) {
            this.element = t, i(t, s && s.colors || e), n(this)
        };
    t.fn.extend({
        colorPalette: function(e) {
            return this.each(function() {
                var i = t(this),
                    n = i.data("colorpalette");
                n || i.data("colorpalette", new s(i, e))
            }), this
        }
    })
}(jQuery),
function(t) {
    "use strict";
    var e = function(t) {
        this.value = {
            h: 0,
            s: 0,
            b: 0,
            a: 1
        }, this.origFormat = null, t && (void 0 !== t.toLowerCase ? this.setColor(t) : void 0 !== t.h && (this.value = t))
    };
    e.prototype = {
        constructor: e,
        _sanitizeNumber: function(t) {
            return "number" == typeof t ? t : isNaN(t) || null === t || "" === t || void 0 === t ? 1 : void 0 !== t.toLowerCase ? parseFloat(t) : 1
        },
        setColor: function(t) {
            t = t.toLowerCase(), this.value = this.stringToHSB(t) || {
                h: 0,
                s: 0,
                b: 0,
                a: 1
            }
        },
        stringToHSB: function(e) {
            e = e.toLowerCase();
            var i = this,
                n = !1;
            return t.each(this.stringParsers, function(t, s) {
                var a = s.re.exec(e),
                    o = a && s.parse.apply(i, [a]),
                    r = s.format || "rgba";
                return o ? (n = r.match(/hsla?/) ? i.RGBtoHSB.apply(i, i.HSLtoRGB.apply(i, o)) : i.RGBtoHSB.apply(i, o), i.origFormat = r, !1) : !0
            }), n
        },
        setHue: function(t) {
            this.value.h = 1 - t
        },
        setSaturation: function(t) {
            this.value.s = t
        },
        setBrightness: function(t) {
            this.value.b = 1 - t
        },
        setAlpha: function(t) {
            this.value.a = parseInt(100 * (1 - t), 10) / 100
        },
        toRGB: function(t, e, i, n) {
            t = t || this.value.h, e = e || this.value.s, i = i || this.value.b, n = n || this.value.a;
            var s, a, o, r, l, c, d, u;
            switch (t && void 0 === e && void 0 === i && (e = t.s, i = t.v, t = t.h), r = Math.floor(6 * t), l = 6 * t - r, c = i * (1 - e), d = i * (1 - l * e), u = i * (1 - (1 - l) * e), r % 6) {
                case 0:
                    s = i, a = u, o = c;
                    break;
                case 1:
                    s = d, a = i, o = c;
                    break;
                case 2:
                    s = c, a = i, o = u;
                    break;
                case 3:
                    s = c, a = d, o = i;
                    break;
                case 4:
                    s = u, a = c, o = i;
                    break;
                case 5:
                    s = i, a = c, o = d
            }
            return {
                r: Math.floor(255 * s),
                g: Math.floor(255 * a),
                b: Math.floor(255 * o),
                a: n
            }
        },
        toHex: function(t, e, i, n) {
            var s = this.toRGB(t, e, i, n);
            return "#" + (1 << 24 | parseInt(s.r) << 16 | parseInt(s.g) << 8 | parseInt(s.b)).toString(16).substr(1)
        },
        toHSL: function(t, e, i, n) {
            t = t || this.value.h, e = e || this.value.s, i = i || this.value.b, n = n || this.value.a;
            var s = t,
                a = (2 - e) * i,
                o = e * i;
            return o /= a > 0 && 1 >= a ? a : 2 - a, a /= 2, o > 1 && (o = 1), {
                h: isNaN(s) ? 0 : s,
                s: isNaN(o) ? 0 : o,
                l: isNaN(a) ? 0 : a,
                a: isNaN(n) ? 0 : n
            }
        },
        RGBtoHSB: function(t, e, i, n) {
            t /= 255, e /= 255, i /= 255;
            var s, a, o, r;
            return o = Math.max(t, e, i), r = o - Math.min(t, e, i), s = 0 === r ? null : o === t ? (e - i) / r : o === e ? (i - t) / r + 2 : (t - e) / r + 4, s = (s + 360) % 6 * 60 / 360, a = 0 === r ? 0 : r / o, {
                h: this._sanitizeNumber(s),
                s: a,
                b: o,
                a: this._sanitizeNumber(n)
            }
        },
        HueToRGB: function(t, e, i) {
            return 0 > i ? i += 1 : i > 1 && (i -= 1), 1 > 6 * i ? t + (e - t) * i * 6 : 1 > 2 * i ? e : 2 > 3 * i ? t + (e - t) * (2 / 3 - i) * 6 : t
        },
        HSLtoRGB: function(t, e, i, n) {
            0 > e && (e = 0);
            var s;
            s = .5 >= i ? i * (1 + e) : i + e - i * e;
            var a = 2 * i - s,
                o = t + 1 / 3,
                r = t,
                l = t - 1 / 3,
                c = Math.round(255 * this.HueToRGB(a, s, o)),
                d = Math.round(255 * this.HueToRGB(a, s, r)),
                u = Math.round(255 * this.HueToRGB(a, s, l));
            return [c, d, u, this._sanitizeNumber(n)]
        },
        toString: function(t) {
            switch (t = t || "rgba") {
                case "rgb":
                    var e = this.toRGB();
                    return "rgb(" + e.r + "," + e.g + "," + e.b + ")";
                case "rgba":
                    var e = this.toRGB();
                    return "rgba(" + e.r + "," + e.g + "," + e.b + "," + e.a + ")";
                case "hsl":
                    var i = this.toHSL();
                    return "hsl(" + Math.round(360 * i.h) + "," + Math.round(100 * i.s) + "%," + Math.round(100 * i.l) + "%)";
                case "hsla":
                    var i = this.toHSL();
                    return "hsla(" + Math.round(360 * i.h) + "," + Math.round(100 * i.s) + "%," + Math.round(100 * i.l) + "%," + i.a + ")";
                case "hex":
                    return this.toHex();
                default:
                    return !1
            }
        },
        stringParsers: [{
            re: /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/,
            format: "hex",
            parse: function(t) {
                return [parseInt(t[1], 16), parseInt(t[2], 16), parseInt(t[3], 16), 1]
            }
        }, {
            re: /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/,
            format: "hex",
            parse: function(t) {
                return [parseInt(t[1] + t[1], 16), parseInt(t[2] + t[2], 16), parseInt(t[3] + t[3], 16), 1]
            }
        }, {
            re: /rgb\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*?\)/,
            format: "rgb",
            parse: function(t) {
                return [t[1], t[2], t[3], 1]
            }
        }, {
            re: /rgb\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*?\)/,
            format: "rgb",
            parse: function(t) {
                return [2.55 * t[1], 2.55 * t[2], 2.55 * t[3], 1]
            }
        }, {
            re: /rgba\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*(?:,\s*(\d+(?:\.\d+)?)\s*)?\)/,
            format: "rgba",
            parse: function(t) {
                return [t[1], t[2], t[3], t[4]]
            }
        }, {
            re: /rgba\(\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d+(?:\.\d+)?)\s*)?\)/,
            format: "rgba",
            parse: function(t) {
                return [2.55 * t[1], 2.55 * t[2], 2.55 * t[3], t[4]]
            }
        }, {
            re: /hsl\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*?\)/,
            format: "hsl",
            parse: function(t) {
                return [t[1] / 360, t[2] / 100, t[3] / 100, t[4]]
            }
        }, {
            re: /hsla\(\s*(\d+(?:\.\d+)?)\s*,\s*(\d+(?:\.\d+)?)\%\s*,\s*(\d+(?:\.\d+)?)\%\s*(?:,\s*(\d+(?:\.\d+)?)\s*)?\)/,
            format: "hsla",
            parse: function(t) {
                return [t[1] / 360, t[2] / 100, t[3] / 100, t[4]]
            }
        }, {
            re: /^([a-z]{3,})$/,
            format: "alias",
            parse: function(t) {
                var e = this.colorNameToHex(t[0]) || "#000000",
                    i = this.stringParsers[0].re.exec(e),
                    n = i && this.stringParsers[0].parse.apply(this, [i]);
                return n
            }
        }],
        colorNameToHex: function(t) {
            var e = {
                aliceblue: "#f0f8ff",
                antiquewhite: "#faebd7",
                aqua: "#00ffff",
                aquamarine: "#7fffd4",
                azure: "#f0ffff",
                beige: "#f5f5dc",
                bisque: "#ffe4c4",
                black: "#000000",
                blanchedalmond: "#ffebcd",
                blue: "#0000ff",
                blueviolet: "#8a2be2",
                brown: "#a52a2a",
                burlywood: "#deb887",
                cadetblue: "#5f9ea0",
                chartreuse: "#7fff00",
                chocolate: "#d2691e",
                coral: "#ff7f50",
                cornflowerblue: "#6495ed",
                cornsilk: "#fff8dc",
                crimson: "#dc143c",
                cyan: "#00ffff",
                darkblue: "#00008b",
                darkcyan: "#008b8b",
                darkgoldenrod: "#b8860b",
                darkgray: "#a9a9a9",
                darkgreen: "#006400",
                darkkhaki: "#bdb76b",
                darkmagenta: "#8b008b",
                darkolivegreen: "#556b2f",
                darkorange: "#ff8c00",
                darkorchid: "#9932cc",
                darkred: "#8b0000",
                darksalmon: "#e9967a",
                darkseagreen: "#8fbc8f",
                darkslateblue: "#483d8b",
                darkslategray: "#2f4f4f",
                darkturquoise: "#00ced1",
                darkviolet: "#9400d3",
                deeppink: "#ff1493",
                deepskyblue: "#00bfff",
                dimgray: "#696969",
                dodgerblue: "#1e90ff",
                firebrick: "#b22222",
                floralwhite: "#fffaf0",
                forestgreen: "#228b22",
                fuchsia: "#ff00ff",
                gainsboro: "#dcdcdc",
                ghostwhite: "#f8f8ff",
                gold: "#ffd700",
                goldenrod: "#daa520",
                gray: "#808080",
                green: "#008000",
                greenyellow: "#adff2f",
                honeydew: "#f0fff0",
                hotpink: "#ff69b4",
                "indianred ": "#cd5c5c",
                "indigo ": "#4b0082",
                ivory: "#fffff0",
                khaki: "#f0e68c",
                lavender: "#e6e6fa",
                lavenderblush: "#fff0f5",
                lawngreen: "#7cfc00",
                lemonchiffon: "#fffacd",
                lightblue: "#add8e6",
                lightcoral: "#f08080",
                lightcyan: "#e0ffff",
                lightgoldenrodyellow: "#fafad2",
                lightgrey: "#d3d3d3",
                lightgreen: "#90ee90",
                lightpink: "#ffb6c1",
                lightsalmon: "#ffa07a",
                lightseagreen: "#20b2aa",
                lightskyblue: "#87cefa",
                lightslategray: "#778899",
                lightsteelblue: "#b0c4de",
                lightyellow: "#ffffe0",
                lime: "#00ff00",
                limegreen: "#32cd32",
                linen: "#faf0e6",
                magenta: "#ff00ff",
                maroon: "#800000",
                mediumaquamarine: "#66cdaa",
                mediumblue: "#0000cd",
                mediumorchid: "#ba55d3",
                mediumpurple: "#9370d8",
                mediumseagreen: "#3cb371",
                mediumslateblue: "#7b68ee",
                mediumspringgreen: "#00fa9a",
                mediumturquoise: "#48d1cc",
                mediumvioletred: "#c71585",
                midnightblue: "#191970",
                mintcream: "#f5fffa",
                mistyrose: "#ffe4e1",
                moccasin: "#ffe4b5",
                navajowhite: "#ffdead",
                navy: "#000080",
                oldlace: "#fdf5e6",
                olive: "#808000",
                olivedrab: "#6b8e23",
                orange: "#ffa500",
                orangered: "#ff4500",
                orchid: "#da70d6",
                palegoldenrod: "#eee8aa",
                palegreen: "#98fb98",
                paleturquoise: "#afeeee",
                palevioletred: "#d87093",
                papayawhip: "#ffefd5",
                peachpuff: "#ffdab9",
                peru: "#cd853f",
                pink: "#ffc0cb",
                plum: "#dda0dd",
                powderblue: "#b0e0e6",
                purple: "#800080",
                red: "#ff0000",
                rosybrown: "#bc8f8f",
                royalblue: "#4169e1",
                saddlebrown: "#8b4513",
                salmon: "#fa8072",
                sandybrown: "#f4a460",
                seagreen: "#2e8b57",
                seashell: "#fff5ee",
                sienna: "#a0522d",
                silver: "#c0c0c0",
                skyblue: "#87ceeb",
                slateblue: "#6a5acd",
                slategray: "#708090",
                snow: "#fffafa",
                springgreen: "#00ff7f",
                steelblue: "#4682b4",
                tan: "#d2b48c",
                teal: "#008080",
                thistle: "#d8bfd8",
                tomato: "#ff6347",
                turquoise: "#40e0d0",
                violet: "#ee82ee",
                wheat: "#f5deb3",
                white: "#ffffff",
                whitesmoke: "#f5f5f5",
                yellow: "#ffff00",
                yellowgreen: "#9acd32"
            };
            return "undefined" != typeof e[t.toLowerCase()] ? e[t.toLowerCase()] : !1
        }
    };
    var i = {
        horizontal: !1,
        inline: !1,
        color: !1,
        format: !1,
        input: "input",
        container: !1,
        component: ".add-on, .input-group-addon",
        sliders: {
            saturation: {
                maxLeft: 100,
                maxTop: 100,
                callLeft: "setSaturation",
                callTop: "setBrightness"
            },
            hue: {
                maxLeft: 0,
                maxTop: 100,
                callLeft: !1,
                callTop: "setHue"
            },
            alpha: {
                maxLeft: 0,
                maxTop: 100,
                callLeft: !1,
                callTop: "setAlpha"
            }
        },
        slidersHorz: {
            saturation: {
                maxLeft: 100,
                maxTop: 100,
                callLeft: "setSaturation",
                callTop: "setBrightness"
            },
            hue: {
                maxLeft: 100,
                maxTop: 0,
                callLeft: "setHue",
                callTop: !1
            },
            alpha: {
                maxLeft: 100,
                maxTop: 0,
                callLeft: "setAlpha",
                callTop: !1
            }
        },
        template: '<div class="colorpicker dropdown-menu"><div class="colorpicker-saturation"><i><b></b></i></div><div class="colorpicker-hue"><i></i></div><div class="colorpicker-alpha"><i></i></div><div class="colorpicker-color"><div /></div></div>'
    }, n = function(n, s) {
            this.element = t(n).addClass("colorpicker-element"), this.options = t.extend({}, i, this.element.data(), s), this.component = this.options.component, this.component = this.component !== !1 ? this.element.find(this.component) : !1, this.component && 0 === this.component.length && (this.component = !1), this.container = this.options.container === !0 ? this.element : this.options.container, this.container = this.container !== !1 ? t(this.container) : !1, this.input = this.element.is("input") ? this.element : this.options.input ? this.element.find(this.options.input) : !1, this.input && 0 === this.input.length && (this.input = !1), this.color = new e(this.options.color !== !1 ? this.options.color : this.getValue()), this.format = this.options.format !== !1 ? this.options.format : this.color.origFormat, this.picker = t(this.options.template), this.picker.addClass(this.options.inline ? "colorpicker-inline colorpicker-visible" : "colorpicker-hidden"), this.options.horizontal && this.picker.addClass("colorpicker-horizontal"), ("rgba" === this.format || "hsla" === this.format) && this.picker.addClass("colorpicker-with-alpha"), this.picker.on("mousedown.colorpicker", t.proxy(this.mousedown, this)), this.picker.appendTo(this.container ? this.container : t("body")), this.input !== !1 && (this.input.on({
                "keyup.colorpicker": t.proxy(this.keyup, this)
            }), this.component === !1 && this.element.on({
                "focus.colorpicker": t.proxy(this.show, this)
            }), this.options.inline === !1 && this.element.on({
                "focusout.colorpicker": t.proxy(this.hide, this)
            })), this.component !== !1 && this.component.on({
                "click.colorpicker": t.proxy(this.show, this)
            }), this.input === !1 && this.component === !1 && this.element.on({
                "click.colorpicker": t.proxy(this.show, this)
            }), this.update(), t(t.proxy(function() {
                this.element.trigger("create")
            }, this))
        };
    n.version = "2.0.0-beta", n.Color = e, n.prototype = {
        constructor: n,
        destroy: function() {
            this.picker.remove(), this.element.removeData("colorpicker").off(".colorpicker"), this.input !== !1 && this.input.off(".colorpicker"), this.component !== !1 && this.component.off(".colorpicker"), this.element.removeClass("colorpicker-element"), this.element.trigger({
                type: "destroy"
            })
        },
        reposition: function() {
            if (this.options.inline !== !1) return !1;
            var t = this.component ? this.component.offset() : this.element.offset();
            this.picker.css({
                top: t.top + (this.component ? this.component.outerHeight() : this.element.outerHeight()),
                left: t.left
            })
        },
        show: function(e) {
            return this.isDisabled() ? !1 : (this.picker.addClass("colorpicker-visible").removeClass("colorpicker-hidden"), this.reposition(), t(window).on("resize.colorpicker", t.proxy(this.reposition, this)), !this.hasInput() && e && e.stopPropagation && e.preventDefault && (e.stopPropagation(), e.preventDefault()), this.options.inline === !1 && t(window.document).on({
                "mousedown.colorpicker": t.proxy(this.hide, this)
            }), void this.element.trigger({
                type: "showPicker",
                color: this.color
            }))
        },
        hide: function() {
            this.picker.addClass("colorpicker-hidden").removeClass("colorpicker-visible"), t(window).off("resize.colorpicker", this.reposition), t(document).off({
                "mousedown.colorpicker": this.hide
            }), this.update(), this.element.trigger({
                type: "hidePicker",
                color: this.color
            })
        },
        updateData: function(t) {
            return t = t || this.color.toString(this.format), this.element.data("color", t), t
        },
        updateInput: function(t) {
            return t = t || this.color.toString(this.format), this.input !== !1 && this.input.prop("value", t), t
        },
        updatePicker: function(t) {
            void 0 !== t && (this.color = new e(t));
            var i = this.options.horizontal === !1 ? this.options.sliders : this.options.slidersHorz,
                n = this.picker.find("i");
            return 0 !== n.length ? (this.options.horizontal === !1 ? (i = this.options.sliders, n.eq(1).css("top", i.hue.maxTop * (1 - this.color.value.h)).end().eq(2).css("top", i.alpha.maxTop * (1 - this.color.value.a))) : (i = this.options.slidersHorz, n.eq(1).css("left", i.hue.maxLeft * (1 - this.color.value.h)).end().eq(2).css("left", i.alpha.maxLeft * (1 - this.color.value.a))), n.eq(0).css({
                top: i.saturation.maxTop - this.color.value.b * i.saturation.maxTop,
                left: this.color.value.s * i.saturation.maxLeft
            }), this.picker.find(".colorpicker-saturation").css("backgroundColor", this.color.toHex(this.color.value.h, 1, 1, 1)), this.picker.find(".colorpicker-alpha").css("backgroundColor", this.color.toHex()), this.picker.find(".colorpicker-color, .colorpicker-color div").css("backgroundColor", this.color.toString(this.format)), t) : void 0
        },
        updateComponent: function(t) {
            if (t = t || this.color.toString(this.format), this.component !== !1) {
                var e = this.component.find("i").eq(0);
                e.length > 0 ? e.css({
                    backgroundColor: t
                }) : this.component.css({
                    backgroundColor: t
                })
            }
            return t
        },
        update: function(t) {
            var e = this.updateComponent();
            return (this.getValue(!1) !== !1 || t === !0) && (this.updateInput(e), this.updateData(e)), this.updatePicker(), e
        },
        setValue: function(t) {
            this.color = new e(t), this.update(), this.element.trigger({
                type: "changeColor",
                color: this.color,
                value: t
            })
        },
        getValue: function(t) {
            t = void 0 === t ? "#000000" : t;
            var e;
            return e = this.hasInput() ? this.input.val() : this.element.data("color"), (void 0 === e || "" === e || null === e) && (e = t), e
        },
        hasInput: function() {
            return this.input !== !1
        },
        isDisabled: function() {
            return this.hasInput() ? this.input.prop("disabled") === !0 : !1
        },
        disable: function() {
            return this.hasInput() ? (this.input.prop("disabled", !0), !0) : !1
        },
        enable: function() {
            return this.hasInput() ? (this.input.prop("disabled", !1), !0) : !1
        },
        currentSlider: null,
        mousePointer: {
            left: 0,
            top: 0
        },
        mousedown: function(e) {
            e.stopPropagation(), e.preventDefault();
            var i = t(e.target),
                n = i.closest("div"),
                s = this.options.horizontal ? this.options.slidersHorz : this.options.sliders;
            if (!n.is(".colorpicker")) {
                if (n.is(".colorpicker-saturation")) this.currentSlider = t.extend({}, s.saturation);
                else if (n.is(".colorpicker-hue")) this.currentSlider = t.extend({}, s.hue);
                else {
                    if (!n.is(".colorpicker-alpha")) return !1;
                    this.currentSlider = t.extend({}, s.alpha)
                }
                var a = n.offset();
                this.currentSlider.guide = n.find("i")[0].style, this.currentSlider.left = e.pageX - a.left, this.currentSlider.top = e.pageY - a.top, this.mousePointer = {
                    left: e.pageX,
                    top: e.pageY
                }, t(document).on({
                    "mousemove.colorpicker": t.proxy(this.mousemove, this),
                    "mouseup.colorpicker": t.proxy(this.mouseup, this)
                }).trigger("mousemove")
            }
            return !1
        },
        mousemove: function(t) {
            t.stopPropagation(), t.preventDefault();
            var e = Math.max(0, Math.min(this.currentSlider.maxLeft, this.currentSlider.left + ((t.pageX || this.mousePointer.left) - this.mousePointer.left))),
                i = Math.max(0, Math.min(this.currentSlider.maxTop, this.currentSlider.top + ((t.pageY || this.mousePointer.top) - this.mousePointer.top)));
            return this.currentSlider.guide.left = e + "px", this.currentSlider.guide.top = i + "px", this.currentSlider.callLeft && this.color[this.currentSlider.callLeft].call(this.color, e / 100), this.currentSlider.callTop && this.color[this.currentSlider.callTop].call(this.color, i / 100), this.update(!0), this.element.trigger({
                type: "changeColor",
                color: this.color
            }), !1
        },
        mouseup: function(e) {
            return e.stopPropagation(), e.preventDefault(), t(document).off({
                "mousemove.colorpicker": this.mousemove,
                "mouseup.colorpicker": this.mouseup
            }), !1
        },
        keyup: function(t) {
            if (38 === t.keyCode) this.color.value.a < 1 && (this.color.value.a = Math.round(100 * (this.color.value.a + .01)) / 100), this.update(!0);
            else if (40 === t.keyCode) this.color.value.a > 0 && (this.color.value.a = Math.round(100 * (this.color.value.a - .01)) / 100), this.update(!0);
            else {
                var i = this.input.val();
                this.color = new e(i), this.getValue(!1) !== !1 && (this.updateData(), this.updateComponent(), this.updatePicker())
            }
            this.element.trigger({
                type: "changeColor",
                color: this.color,
                value: i
            })
        }
    }, t.bscolorpicker = n, t.fn.bscolorpicker = function(e) {
        var i = arguments;
        return this.each(function() {
            var s = t(this),
                a = s.data("colorpicker"),
                o = "object" == typeof e ? e : {};
            a || "string" == typeof e ? "string" == typeof e && a[e].apply(a, Array.prototype.slice.call(i, 1)) : s.data("colorpicker", new n(this, o))
        })
    }, t.fn.bscolorpicker.constructor = n
}(window.jQuery),

function(t) {
    "use strict";
    t.fn.extend({
        maxlength: function(e, i) {
            function n(t) {
                var i = t.val(),
                    n = i.match(/\n/g),
                    a = 0,
                    o = 0;
                return e.utf8 ? (a = n ? s(n) : 0, o = s(t.val())) : (a = n ? n.length : 0, o = t.val().length), o += e.ignoreBreaks ? 0 : a
            }

            function s(t) {
                for (var e = 0, i = 0; i < t.length; i++) {
                    var n = t.charCodeAt(i);
                    128 > n ? e++ : e += n > 127 && 2048 > n ? 2 : 3
                }
                return e
            }

            function a(t, i, s) {
                var a = !0;
                return !e.alwaysShow && s - n(t) > i && (a = !1), a
            }

            function o(t, e) {
                var i = e - n(t);
                return i
            }

            function r(t) {
                t.css({
                    display: "block"
                })
            }

            function l(t) {
                t.css({
                    display: "none"
                })
            }

            function c(t, i) {
                var n = "";
                return e.message ? n = e.message.replace("%charsTyped%", i).replace("%charsRemaining%", t - i).replace("%charsTotal%", t) : (e.preText && (n += e.preText), n += e.showCharsTyped ? i : t - i, e.showMaxLength && (n += e.separator + t), e.postText && (n += e.postText)), n
            }

            function d(t, i, n, s) {
                s.html(c(n, n - t)), t > 0 ? a(i, e.threshold, n) ? r(s.removeClass(e.limitReachedClass).addClass(e.warningClass)) : l(s) : r(s.removeClass(e.warningClass).addClass(e.limitReachedClass))
            }

            function u(e) {
                var i = e[0];
                return t.extend({}, "function" == typeof i.getBoundingClientRect ? i.getBoundingClientRect() : {
                    width: i.offsetWidth,
                    height: i.offsetHeight
                }, e.offset())
            }

            function h(t, i) {
                var n = u(t),
                    s = t.outerWidth(),
                    a = i.outerWidth(),
                    o = i.width(),
                    r = i.height();
                switch (e.placement) {
                    case "bottom":
                        i.css({
                            top: n.top + n.height,
                            left: n.left + n.width / 2 - o / 2
                        });
                        break;
                    case "top":
                        i.css({
                            top: n.top - r,
                            left: n.left + n.width / 2 - o / 2
                        });
                        break;
                    case "left":
                        i.css({
                            top: n.top + n.height / 2 - r / 2,
                            left: n.left - o
                        });
                        break;
                    case "right":
                        i.css({
                            top: n.top + n.height / 2 - r / 2,
                            left: n.left + n.width
                        });
                        break;
                    case "bottom-right":
                        i.css({
                            top: n.top + n.height,
                            left: n.left + n.width
                        });
                        break;
                    case "top-right":
                        i.css({
                            top: n.top - r,
                            left: n.left + s
                        });
                        break;
                    case "top-left":
                        i.css({
                            top: n.top - r,
                            left: n.left - a
                        });
                        break;
                    case "bottom-left":
                        i.css({
                            top: n.top + t.outerHeight(),
                            left: n.left - a
                        });
                        break;
                    case "centered-right":
                        i.css({
                            top: n.top + r / 2,
                            left: n.left + s - a - 3
                        })
                }
            }

            function p(t) {
                return t.attr("maxlength") || t.attr("size")
            }
            var f = t("body"),
                m = {
                    alwaysShow: !1,
                    threshold: 10,
                    warningClass: "label label-success",
                    limitReachedClass: "label label-important",
                    separator: " / ",
                    preText: "",
                    postText: "",
                    set_ID: "",
                    showMaxLength: !0,
                    placement: "bottom",
                    showCharsTyped: !0,
                    validate: !1,
                    utf8: !1,
                    ignoreBreaks: !1
                };
            return t.isFunction(e) && !i && (i = e, e = {}), e = t.extend(m, e), this.each(function() {
                var i, n, s = t(this);
                t(window).resize(function() {
                    h(s, n)
                }), s.focus(function() {
                    var a = c(i, "0");
                    i = p(s), t(".bootstrap-maxlength." + e.set_ID).remove(), n = t('<span class="' + e.set_ID + ' label bootstrap-maxlength"></span>').css({
                        display: "none",
                        position: "absolute",
                        whiteSpace: "nowrap",
                        zIndex: 1099
                    }).html(a), s.is("textarea") && (s.data("maxlenghtsizex", s.outerWidth()), s.data("maxlenghtsizey", s.outerHeight()), s.mouseup(function() {
                        (s.outerWidth() !== s.data("maxlenghtsizex") || s.outerHeight() !== s.data("maxlenghtsizey")) && h(s, n), s.data("maxlenghtsizex", s.outerWidth()), s.data("maxlenghtsizey", s.outerHeight())
                    })), f.append(n);
                    var r = o(s, p(s));
                    d(r, s, i, n), h(s, n)
                }), s.blur(function() {
                    n.remove()
                }), s.keyup(function() {
                    var t = o(s, p(s)),
                        a = !0;
                    return e.validate && 0 > t ? a = !1 : d(t, s, i, n), a
                })
            })
        }
    })
}(jQuery), ! function(t) {
    "use strict";
    t.expr[":"].icontains = function(e, i, n) {
        return t(e).text().toUpperCase().indexOf(n[3].toUpperCase()) >= 0
    };
    var e = function(i, n, s) {
        s && (s.stopPropagation(), s.preventDefault()), this.$element = t(i), this.$newElement = null, this.$button = null, this.$menu = null, this.$lis = null, this.options = t.extend({}, t.fn.selectpicker.defaults, this.$element.data(), "object" == typeof n && n), null === this.options.title && (this.options.title = this.$element.attr("title")), this.val = e.prototype.val, this.render = e.prototype.render, this.refresh = e.prototype.refresh, this.setStyle = e.prototype.setStyle, this.selectAll = e.prototype.selectAll, this.deselectAll = e.prototype.deselectAll, this.init()
    };
    e.prototype = {
        constructor: e,
        init: function() {
            var e = this,
                i = this.$element.attr("id");
            this.$element.hide(), this.multiple = this.$element.prop("multiple"), this.autofocus = this.$element.prop("autofocus"), this.$newElement = this.createView(), this.$element.after(this.$newElement), this.$menu = this.$newElement.find("> .dropdown-menu"), this.$button = this.$newElement.find("> button"), this.$searchbox = this.$newElement.find("input"), void 0 !== i && (this.$button.attr("data-id", i), t('label[for="' + i + '"]').click(function(t) {
                t.preventDefault(), e.$button.focus()
            })), this.checkDisabled(), this.clickListener(), this.options.liveSearch && this.liveSearchListener(), this.render(), this.liHeight(), this.setStyle(), this.setWidth(), this.options.container && this.selectPosition(), this.$menu.data("this", this), this.$newElement.data("this", this)
        },
        createDropdown: function() {
            var e = this.multiple ? " show-tick" : "",
                i = this.autofocus ? " autofocus" : "",
                n = this.options.header ? '<div class="popover-title"><button type="button" class="close" aria-hidden="true">&times;</button>' + this.options.header + "</div>" : "",
                s = this.options.liveSearch ? '<div class="bootstrap-select-searchbox"><input type="text" class="input-block-level form-control" /></div>' : "",
                a = '<div class="btn-group  bootstrap-select' + e + '"><button type="button" class="btn dropdown-toggle align_left the_input_element selectpicker" data-toggle="dropdown"' + i + '><span class="filter-option pull-left"></span>&nbsp;<span class="caret"></span></button><div class="dropdown-menu the_input_element open">' + n + s + '<ul class="dropdown-menu inner the_input_element selectpicker" role="menu"></ul></div></div>';
            return t(a)
        },
        createView: function() {
            var t = this.createDropdown(),
                e = this.createLi();
            return t.find("ul").append(e), t
        },
        reloadLi: function() {
            this.destroyLi();
            var t = this.createLi();
            this.$menu.find("ul").append(t)
        },
        destroyLi: function() {
            this.$menu.find("li").remove()
        },
        createLi: function() {
            var e = this,
                i = [],
                n = "";
            return this.$element.find("option").each(function() {
                var n = t(this),
                    s = n.attr("class") || "",
                    a = n.attr("style") || "",
                    o = n.data("content") ? n.data("content") : n.html(),
                    r = void 0 !== n.data("subtext") ? '<small class="muted text-muted">' + n.data("subtext") + "</small>" : "",
                    l = void 0 !== n.data("icon") ? '<i class="' + e.options.iconBase + " " + n.data("icon") + '"></i> ' : "";
                if ("" !== l && (n.is(":disabled") || n.parent().is(":disabled")) && (l = "<span>" + l + "</span>"), n.data("content") || (o = l + '<span class="text">' + o + r + "</span>"), e.options.hideDisabled && (n.is(":disabled") || n.parent().is(":disabled"))) i.push('<a style="min-height: 0; padding: 0"></a>');
                else if (n.parent().is("optgroup") && n.data("divider") !== !0)
                    if (0 === n.index()) {
                        var c = n.parent().attr("label"),
                            d = void 0 !== n.parent().data("subtext") ? '<small class="muted text-muted">' + n.parent().data("subtext") + "</small>" : "",
                            u = n.parent().data("icon") ? '<i class="' + n.parent().data("icon") + '"></i> ' : "";
                        c = u + '<span class="text">' + c + d + "</span>", i.push(0 !== n[0].index ? '<div class="div-contain"><div class="divider"></div></div><dt>' + c + "</dt>" + e.createA(o, "opt " + s, a) : "<dt>" + c + "</dt>" + e.createA(o, "opt " + s, a))
                    } else i.push(e.createA(o, "opt " + s, a));
                    else i.push(n.data("divider") === !0 ? '<div class="div-contain"><div class="divider"></div></div>' : t(this).data("hidden") === !0 ? "" : e.createA(o, s, a))
            }), t.each(i, function(t, e) {
                n += "<li rel=" + t + ">" + e + "</li>"
            }), this.multiple || 0 !== this.$element.find("option:selected").length || this.options.title || this.$element.find("option").eq(0).prop("selected", !0).attr("selected", "selected"), t(n)
        },
        createA: function(t, e, i) {
            return '<a tabindex="0" class="' + e + '" style="' + i + '">' + t + '<i class="' + this.options.iconBase + " " + this.options.tickIcon + ' icon-ok check-mark"></i></a>'
        },
        render: function(e) {
            var i = this;
            e !== !1 && this.$element.find("option").each(function(e) {
                i.setDisabled(e, t(this).is(":disabled") || t(this).parent().is(":disabled")), i.setSelected(e, t(this).is(":selected"))
            }), this.tabIndex();
            var n = this.$element.find("option:selected").map(function() {
                var e, n = t(this),
                    s = n.data("icon") && i.options.showIcon ? '<i class="' + i.options.iconBase + " " + n.data("icon") + '"></i> ' : "";
                return e = i.options.showSubtext && n.attr("data-subtext") && !i.multiple ? ' <small class="muted text-muted">' + n.data("subtext") + "</small>" : "", n.data("content") && i.options.showContent ? n.data("content") : void 0 !== n.attr("title") ? n.attr("title") : s + n.html() + e
            }).toArray(),
                s = this.multiple ? n.join(this.options.multipleSeparator) : n[0];
            if (this.multiple && this.options.selectedTextFormat.indexOf("count") > -1) {
                var a = this.options.selectedTextFormat.split(">"),
                    o = this.options.hideDisabled ? ":not([disabled])" : "";
                (a.length > 1 && n.length > a[1] || 1 == a.length && n.length >= 2) && (s = this.options.countSelectedText.replace("{0}", n.length).replace("{1}", this.$element.find('option:not([data-divider="true"]):not([data-hidden="true"])' + o).length))
            }
            s || (s = void 0 !== this.options.title ? this.options.title : this.options.noneSelectedText), this.$button.attr("title", t.trim(s)), this.$newElement.find(".filter-option").html(s)
        },
        setStyle: function(t, e) {
            this.$element.attr("class") && this.$newElement.addClass(this.$element.attr("class").replace(/selectpicker|mobile-device/gi, ""));
            var i = t ? t : this.options.style;
            "add" == e ? this.$button.addClass(i) : "remove" == e ? this.$button.removeClass(i) : (this.$button.removeClass(this.options.style), this.$button.addClass(i))
        },
        liHeight: function() {
            var t = this.$menu.parent().clone().find("> .dropdown-toggle").prop("autofocus", !1).end().appendTo("body"),
                e = t.addClass("open").find("> .dropdown-menu"),
                i = e.find("li > a").outerHeight(),
                n = this.options.header ? e.find(".popover-title").outerHeight() : 0,
                s = this.options.liveSearch ? e.find(".bootstrap-select-searchbox").outerHeight() : 0;
            t.remove(), this.$newElement.data("liHeight", i).data("headerHeight", n).data("searchHeight", s)
        },
        setSize: function() {
            var e, i, n, s = this,
                a = this.$menu,
                o = a.find(".inner"),
                r = this.$newElement.outerHeight(),
                l = this.$newElement.data("liHeight"),
                c = this.$newElement.data("headerHeight"),
                d = this.$newElement.data("searchHeight"),
                u = a.find("li .divider").outerHeight(!0),
                h = parseInt(a.css("padding-top")) + parseInt(a.css("padding-bottom")) + parseInt(a.css("border-top-width")) + parseInt(a.css("border-bottom-width")),
                p = this.options.hideDisabled ? ":not(.disabled)" : "",
                f = t(window),
                m = h + parseInt(a.css("margin-top")) + parseInt(a.css("margin-bottom")) + 2,
                g = function() {
                    i = s.$newElement.offset().top - f.scrollTop(), n = f.height() - i - r
                };
            if (g(), this.options.header && a.css("padding-top", 0), "auto" == this.options.size) {
                var v = function() {
                    var t;
                    g(), e = n - m, s.options.dropupAuto && s.$newElement.toggleClass("dropup", i > n && e - m < a.height()), s.$newElement.hasClass("dropup") && (e = i - m), t = a.find("li").length + a.find("dt").length > 3 ? 3 * l + m - 2 : 0, a.css({
                        "max-height": e + "px",
                        overflow: "hidden",
                        "min-height": t + "px"
                    }), o.css({
                        "max-height": e - c - d - h + "px",
                        "overflow-y": "auto",
                        "min-height": t - h + "px"
                    })
                };
                v(), t(window).resize(v), t(window).scroll(v)
            } else if (this.options.size && "auto" != this.options.size && a.find("li" + p).length > this.options.size) {
                var b = a.find("li" + p + " > *").filter(":not(.div-contain)").slice(0, this.options.size).last().parent().index(),
                    y = a.find("li").slice(0, b + 1).find(".div-contain").length;
                e = l * this.options.size + y * u + h, s.options.dropupAuto && this.$newElement.toggleClass("dropup", i > n && e < a.height()), a.css({
                    "max-height": e + c + d + "px",
                    overflow: "hidden"
                }), o.css({
                    "max-height": e - h + "px",
                    "overflow-y": "auto"
                })
            }
        },
        setWidth: function() {
            if ("auto" == this.options.width) {
                this.$menu.css("min-width", "0");
                var t = this.$newElement.clone().appendTo("body"),
                    e = t.find("> .dropdown-menu").css("width");
                t.remove(), this.$newElement.css("width", e)
            } else "fit" == this.options.width ? (this.$menu.css("min-width", ""), this.$newElement.css("width", "").addClass("fit-width")) : this.options.width ? (this.$menu.css("min-width", ""), this.$newElement.css("width", this.options.width)) : (this.$menu.css("min-width", ""), this.$newElement.css("width", ""));
            this.$newElement.hasClass("fit-width") && "fit" !== this.options.width && this.$newElement.removeClass("fit-width")
        },
        selectPosition: function() {
            var e, i, n = this,
                s = "<div />",
                a = t(s),
                o = function(t) {
                    a.addClass(t.attr("class")).toggleClass("dropup", t.hasClass("dropup")), e = t.offset(), i = t.hasClass("dropup") ? 0 : t[0].offsetHeight, a.css({
                        top: e.top + i,
                        left: e.left,
                        width: t[0].offsetWidth,
                        position: "absolute"
                    })
                };
            this.$newElement.on("click", function() {
                o(t(this)), a.appendTo(n.options.container), a.toggleClass("open", !t(this).hasClass("open")), a.append(n.$menu)
            }), t(window).resize(function() {
                o(n.$newElement)
            }), t(window).on("scroll", function() {
                o(n.$newElement)

            }), t("html").on("click", function(e) {
                t(e.target).closest(n.$newElement).length < 1 && a.removeClass("open")
            })
        },
        mobile: function() {
            this.$element.addClass("mobile-device").appendTo(this.$newElement), this.options.container && this.$menu.hide()
        },
        refresh: function() {
            this.$lis = null, this.reloadLi(), this.render(), this.setWidth(), this.setStyle(), this.checkDisabled(), this.liHeight()
        },
        update: function() {
            this.reloadLi(), this.setWidth(), this.setStyle(), this.checkDisabled(), this.liHeight()
        },
        setSelected: function(e, i) {
            null == this.$lis && (this.$lis = this.$menu.find("li")), t(this.$lis[e]).toggleClass("selected", i)
        },
        setDisabled: function(e, i) {
            null == this.$lis && (this.$lis = this.$menu.find("li")), i ? t(this.$lis[e]).addClass("disabled").find("a").attr("href", "#").attr("tabindex", -1) : t(this.$lis[e]).removeClass("disabled").find("a").removeAttr("href").attr("tabindex", 0)
        },
        isDisabled: function() {
            return this.$element.is(":disabled")
        },
        checkDisabled: function() {
            var t = this;
            this.isDisabled() ? this.$button.addClass("disabled").attr("tabindex", -1) : (this.$button.hasClass("disabled") && this.$button.removeClass("disabled"), -1 == this.$button.attr("tabindex") && (this.$element.data("tabindex") || this.$button.removeAttr("tabindex"))), this.$button.click(function() {
                return !t.isDisabled()
            })
        },
        tabIndex: function() {
            this.$element.is("[tabindex]") && (this.$element.data("tabindex", this.$element.attr("tabindex")), this.$button.attr("tabindex", this.$element.data("tabindex")))
        },
        clickListener: function() {
            var e = this;
            t("body").on("touchstart.dropdown", ".dropdown-menu", function(t) {
                t.stopPropagation()
            }), this.$newElement.on("click", function() {
                e.setSize(), e.options.liveSearch || e.multiple || setTimeout(function() {
                    e.$menu.find(".selected a").focus()
                }, 10)
            }), this.$menu.on("click", "li a", function(i) {
                var n = t(this).parent().index(),
                    s = e.$element.val(),
                    a = e.$element.prop("selectedIndex");
                if (e.multiple && i.stopPropagation(), i.preventDefault(), !e.isDisabled() && !t(this).parent().hasClass("disabled")) {
                    var o = e.$element.find("option"),
                        r = o.eq(n),
                        l = r.prop("selected");
                    e.multiple ? (r.prop("selected", !l), e.setSelected(n, !l)) : (o.prop("selected", !1), r.prop("selected", !0), e.$menu.find(".selected").removeClass("selected"), e.setSelected(n, !0)), e.multiple ? e.options.liveSearch && e.$searchbox.focus() : e.$button.focus(), (s != e.$element.val() && e.multiple || a != e.$element.prop("selectedIndex") && !e.multiple) && e.$element.change()
                }
            }), this.$menu.on("click", "li.disabled a, li dt, li .div-contain, .popover-title, .popover-title :not(.close)", function(t) {
                t.target == this && (t.preventDefault(), t.stopPropagation(), e.options.liveSearch ? e.$searchbox.focus() : e.$button.focus())
            }), this.$menu.on("click", ".popover-title .close", function() {
                e.$button.focus()
            }), this.$searchbox.on("click", function(t) {
                t.stopPropagation()
            }), this.$element.change(function() {
                e.render(!1)
            })
        },
        liveSearchListener: function() {
            var e = this,
                i = t('<li class="no-results"></li>');
            this.$newElement.on("click.dropdown.data-api", function() {
                e.$menu.find(".active").removeClass("active"), e.$searchbox.val() && (e.$searchbox.val(""), e.$menu.find("li").show(), i.parent().length && i.remove()), e.multiple || e.$menu.find(".selected").addClass("active"), setTimeout(function() {
                    e.$searchbox.focus()
                }, 10)
            }), this.$searchbox.on("input propertychange", function() {
                e.$searchbox.val() ? (e.$menu.find("li").show().not(":icontains(" + e.$searchbox.val() + ")").hide(), e.$menu.find("li").filter(":visible:not(.no-results)").length ? i.parent().length && i.remove() : (i.parent().length && i.remove(), i.html(e.options.noneResultsText + ' "' + e.$searchbox.val() + '"').show(), e.$menu.find("li").last().after(i))) : (e.$menu.find("li").show(), i.parent().length && i.remove()), e.$menu.find("li.active").removeClass("active"), e.$menu.find("li").filter(":visible:not(.divider)").eq(0).addClass("active").find("a").focus(), t(this).focus()
            }), this.$menu.on("mouseenter", "a", function(i) {
                e.$menu.find(".active").removeClass("active"), t(i.currentTarget).parent().not(".disabled").addClass("active")
            }), this.$menu.on("mouseleave", "a", function() {
                e.$menu.find(".active").removeClass("active")
            })
        },
        val: function(t) {
            return void 0 !== t ? (this.$element.val(t), this.$element.change(), this.$element) : this.$element.val()
        },
        selectAll: function() {
            this.$element.find("option").prop("selected", !0).attr("selected", "selected"), this.render()
        },
        deselectAll: function() {
            this.$element.find("option").prop("selected", !1).removeAttr("selected"), this.render()
        },
        keydown: function(e) {
            var i, n, s, a, o, r, l, c, d, u, h, p, f = {
                    32: " ",
                    48: "0",
                    49: "1",
                    50: "2",
                    51: "3",
                    52: "4",
                    53: "5",
                    54: "6",
                    55: "7",
                    56: "8",
                    57: "9",
                    59: ";",
                    65: "a",
                    66: "b",
                    67: "c",
                    68: "d",
                    69: "e",
                    70: "f",
                    71: "g",
                    72: "h",
                    73: "i",
                    74: "j",
                    75: "k",
                    76: "l",
                    77: "m",
                    78: "n",
                    79: "o",
                    80: "p",
                    81: "q",
                    82: "r",
                    83: "s",
                    84: "t",
                    85: "u",
                    86: "v",
                    87: "w",
                    88: "x",
                    89: "y",
                    90: "z",
                    96: "0",
                    97: "1",
                    98: "2",
                    99: "3",
                    100: "4",
                    101: "5",
                    102: "6",
                    103: "7",
                    104: "8",
                    105: "9"
                };
            if (i = t(this), s = i.parent(), i.is("input") && (s = i.parent().parent()), u = s.data("this"), u.options.liveSearch && (s = i.parent().parent()), u.options.container && (s = u.$menu), n = t("[role=menu] li:not(.divider) a", s), p = u.$menu.parent().hasClass("open"), !p && /([0-9]|[A-z])/.test(String.fromCharCode(e.keyCode)) && (u.setSize(), u.$menu.parent().addClass("open"), p = u.$menu.parent().hasClass("open"), u.$searchbox.focus()), u.options.liveSearch && (/(^9$|27)/.test(e.keyCode) && p && 0 === u.$menu.find(".active").length && (e.preventDefault(), u.$menu.parent().removeClass("open"), u.$button.focus()), n = t("[role=menu] li:not(.divider):visible", s), i.val() || /(38|40)/.test(e.keyCode) || 0 === n.filter(".active").length && (n = u.$newElement.find("li").filter(":icontains(" + f[e.keyCode] + ")"))), n.length) {
                if (/(38|40)/.test(e.keyCode)) a = n.index(n.filter(":focus")), r = n.parent(":not(.disabled):visible").first().index(), l = n.parent(":not(.disabled):visible").last().index(), o = n.eq(a).parent().nextAll(":not(.disabled):visible").eq(0).index(), c = n.eq(a).parent().prevAll(":not(.disabled):visible").eq(0).index(), d = n.eq(o).parent().prevAll(":not(.disabled):visible").eq(0).index(), u.options.liveSearch && (n.each(function(e) {
                    t(this).is(":not(.disabled)") && t(this).data("index", e)
                }), a = n.index(n.filter(".active")), r = n.filter(":not(.disabled):visible").first().data("index"), l = n.filter(":not(.disabled):visible").last().data("index"), o = n.eq(a).nextAll(":not(.disabled):visible").eq(0).data("index"), c = n.eq(a).prevAll(":not(.disabled):visible").eq(0).data("index"), d = n.eq(o).prevAll(":not(.disabled):visible").eq(0).data("index")), h = i.data("prevIndex"), 38 == e.keyCode && (u.options.liveSearch && (a -= 1), a != d && a > c && (a = c), r > a && (a = r), a == h && (a = l)), 40 == e.keyCode && (u.options.liveSearch && (a += 1), -1 == a && (a = 0), a != d && o > a && (a = o), a > l && (a = l), a == h && (a = r)), i.data("prevIndex", a), u.options.liveSearch ? (e.preventDefault(), i.is(".dropdown-toggle") || (n.removeClass("active"), n.eq(a).addClass("active").find("a").focus(), i.focus())) : n.eq(a).focus();
                else if (!i.is("input")) {
                    var m, g, v = [];
                    n.each(function() {
                        t(this).parent().is(":not(.disabled)") && t.trim(t(this).text().toLowerCase()).substring(0, 1) == f[e.keyCode] && v.push(t(this).parent().index())
                    }), m = t(document).data("keycount"), m++, t(document).data("keycount", m), g = t.trim(t(":focus").text().toLowerCase()).substring(0, 1), g != f[e.keyCode] ? (m = 1, t(document).data("keycount", m)) : m >= v.length && (t(document).data("keycount", 0), m > v.length && (m = 1)), n.eq(v[m - 1]).focus()
                }
                /(13|32|^9$)/.test(e.keyCode) && p && (/(32)/.test(e.keyCode) || e.preventDefault(), u.options.liveSearch ? /(32)/.test(e.keyCode) || (u.$menu.find(".active a").click(), i.focus()) : t(":focus").click(), t(document).data("keycount", 0)), (/(^9$|27)/.test(e.keyCode) && p && (u.multiple || u.options.liveSearch) || /(27)/.test(e.keyCode) && !p) && (u.$menu.parent().removeClass("open"), u.$button.focus())
            }
        },
        hide: function() {
            this.$newElement.hide()
        },
        show: function() {
            this.$newElement.show()
        },
        destroy: function() {
            this.$newElement.remove(), this.$element.remove()
        }
    }, t.fn.selectpicker = function(i, n) {
        var s, a = arguments,
            o = this.each(function() {
                if (t(this).is("select")) {
                    var o = t(this),
                        r = o.data("selectpicker"),
                        l = "object" == typeof i && i;
                    if (r) {
                        if (l)
                            for (var c in l) r.options[c] = l[c]
                    } else o.data("selectpicker", r = new e(this, l, n)); if ("string" == typeof i) {
                        var d = i;
                        r[d] instanceof Function ? ([].shift.apply(a), s = r[d].apply(r, a)) : s = r.options[d]
                    }
                }
            });
        return void 0 !== s ? s : o
    }, t.fn.selectpicker.defaults = {
        style: "btn-default",
        size: "auto",
        title: null,
        selectedTextFormat: "values",
        noneSelectedText: "Nothing selected",
        noneResultsText: "No results match",
        countSelectedText: "{0} of {1} selected",
        width: !1,
        container: !1,
        hideDisabled: !1,
        showSubtext: !1,
        showIcon: !0,
        showContent: !0,
        dropupAuto: !0,
        header: !1,
        liveSearch: !1,
        multipleSeparator: ", ",
        iconBase: "fa ",
        tickIcon: "fa-check"
    }, t(document).data("keycount", 0).on("keydown", ".bootstrap-select [data-toggle=dropdown], .bootstrap-select [role=menu], .bootstrap-select-searchbox input", e.prototype.keydown).on("focusin.modal", ".bootstrap-select [data-toggle=dropdown], .bootstrap-select [role=menu], .bootstrap-select-searchbox input", function(t) {
        t.stopPropagation()
    })
}(window.jQuery),
function(t) {
    "use strict";

    function e(e, i) {
        this.itemsArray = [], this.$element = t(e), this.$element.hide(), this.isSelect = "SELECT" === e.tagName, this.multiple = this.isSelect && e.hasAttribute("multiple"), this.objectItems = i && i.itemValue, this.placeholderText = e.hasAttribute("placeholder") ? this.$element.attr("placeholder") : "", this.inputSize = Math.max(1, this.placeholderText.length), this.$container = t('<div class="bootstrap-tagsinput form-control "></div>'), this.$input = t('<input  size="' + this.inputSize + '" type="text" placeholder="' + this.placeholderText + '"/>').appendTo(this.$container), this.$element.after(this.$container), this.build(i)
    }

    function i(t, e) {
        if ("function" != typeof t[e]) {
            var i = t[e];
            t[e] = function(t) {
                return t[i]
            }
        }
    }

    function n(t, e) {
        if ("function" != typeof t[e]) {
            var i = t[e];
            t[e] = function() {
                return i
            }
        }
    }

    function s(t) {
        return t ? r.text(t).html() : ""
    }

    function a(t) {
        var e = 0;
        if (document.selection) {
            t.focus();
            var i = document.selection.createRange();
            i.moveStart("character", -t.value.length), e = i.text.length
        } else(t.selectionStart || "0" == t.selectionStart) && (e = t.selectionStart);
        return e
    }
    var o = {
        tagClass: "label label-info",
        setTagClass: "label label-info",
        setTagIcon: "fa fa-tag",
        itemValue: function(t) {
            return t ? t.toString() : t
        },
        itemText: function(t) {
            return this.itemValue(t)
        },
        freeInput: !0,
        maxTags: void 0,
        confirmKeys: [13],
        onTagExists: function(t, e) {
            e.hide().fadeIn()
        }
    };
    e.prototype = {
        constructor: e,
        add: function(e, i) {
            var n = this;
            if (!(n.options.maxTags && n.itemsArray.length >= n.options.maxTags || e !== !1 && !e)) {
                if ("object" == typeof e && !n.objectItems) throw "Can't add objects when itemValue option is not set";
                if (!e.toString().match(/^\s*$/)) {
                    if (n.isSelect && !n.multiple && n.itemsArray.length > 0 && n.remove(n.itemsArray[0]), "string" == typeof e && "INPUT" === this.$element[0].tagName) {
                        var a = e.split(",");
                        if (a.length > 1) {
                            for (var o = 0; o < a.length; o++) this.add(a[o], !0);
                            return void(i || n.pushVal())
                        }
                    }
                    var r = n.options.itemValue(e),
                        l = n.options.itemText(e),
                        c = (n.options.tagClass, t.grep(n.itemsArray, function(t) {
                            return n.options.itemValue(t) === r
                        })[0]);
                    if (c) {
                        if (n.options.onTagExists) {
                            var d = t(".tag", n.$container).filter(function() {
                                return t(this).data("item") === c
                            });
                            n.options.onTagExists(e, d)
                        }
                    } else {
                        n.itemsArray.push(e);
                        var u = t('<span class="tag label ' + this.$element.attr("data-tag-class") + '"><span id="tag-icon" class="' + this.$element.attr("data-tag-icon") + '"></span> ' + s(l) + '<span data-role="remove"></span></span>');
                        if (u.data("item", e), n.findInputWrapper().before(u), u.after(" "), n.isSelect && !t('option[value="' + escape(r) + '"]', n.$element)[0]) {
                            var h = t("<option selected>" + s(l) + "</option>");
                            h.data("item", e), h.attr("value", r), n.$element.append(h)
                        }
                        i || n.pushVal(), n.options.maxTags === n.itemsArray.length && n.$container.addClass("bootstrap-tagsinput-max"), n.$element.trigger(t.Event("itemAdded", {
                            item: e
                        }))
                    }
                }
            }
        },
        remove: function(e, i) {
            var n = this;
            n.objectItems && (e = "object" == typeof e ? t.grep(n.itemsArray, function(t) {
                return n.options.itemValue(t) == n.options.itemValue(e)
            })[0] : t.grep(n.itemsArray, function(t) {
                return n.options.itemValue(t) == e
            })[0]), e && (t(".tag", n.$container).filter(function() {
                return t(this).data("item") === e
            }).remove(), t("option", n.$element).filter(function() {
                return t(this).data("item") === e
            }).remove(), n.itemsArray.splice(t.inArray(e, n.itemsArray), 1)), i || n.pushVal(), n.options.maxTags > n.itemsArray.length && n.$container.removeClass("bootstrap-tagsinput-max"), n.$element.trigger(t.Event("itemRemoved", {
                item: e
            }))
        },
        removeAll: function() {
            var e = this;
            for (t(".tag", e.$container).remove(), t("option", e.$element).remove(); e.itemsArray.length > 0;) e.itemsArray.pop();
            e.pushVal(), e.options.maxTags && !this.isEnabled() && this.enable()
        },
        refresh: function() {
            var e = this;
            t(".tag", e.$container).each(function() {
                var i = t(this),
                    n = i.data("item"),
                    a = e.options.itemValue(n),
                    o = e.options.itemText(n),
                    r = e.options.tagClass(n);
                if (i.attr("class", null), i.addClass("tag " + s(r)), i.contents().filter(function() {
                    return 3 == this.nodeType
                })[0].nodeValue = s(o), e.isSelect) {
                    var l = t("option", e.$element).filter(function() {
                        return t(this).data("item") === n
                    });
                    l.attr("value", a)
                }
            })
        },
        items: function() {
            return this.itemsArray
        },
        pushVal: function() {
            var e = this,
                i = t.map(e.items(), function(t) {
                    return e.options.itemValue(t).toString()
                });
            e.$element.val(i, !0).trigger("change")
        },
        build: function(e) {
            var s = this;
            s.options = t.extend({}, o, e);
            var r = s.options.typeahead || {};
            s.objectItems && (s.options.freeInput = !1), i(s.options, "itemValue"), i(s.options, "itemText"), i(s.options, "tagClass"), s.options.source && (r.source = s.options.source), r.source && t.fn.typeahead && (n(r, "source"), s.$input.typeahead({
                source: function(e, i) {
                    function n(t) {
                        for (var e = [], n = 0; n < t.length; n++) {
                            var o = s.options.itemText(t[n]);
                            a[o] = t[n], e.push(o)
                        }
                        i(e)
                    }
                    this.map = {};
                    var a = this.map,
                        o = r.source(e);
                    t.isFunction(o.success) ? o.success(n) : t.when(o).then(n)
                },
                updater: function(t) {
                    s.add(this.map[t])
                },
                matcher: function(t) {
                    return -1 !== t.toLowerCase().indexOf(this.query.trim().toLowerCase())
                },
                sorter: function(t) {
                    return t.sort()
                },
                highlighter: function(t) {
                    var e = new RegExp("(" + this.query + ")", "gi");
                    return t.replace(e, "<strong>$1</strong>")
                }
            })), s.$container.on("click", t.proxy(function() {
                s.$input.focus()
            }, s)), s.$container.on("keydown", "input", t.proxy(function(e) {
                var i = t(e.target),
                    n = s.findInputWrapper();
                switch (e.which) {
                    case 8:
                        if (0 === a(i[0])) {
                            var o = n.prev();
                            o && s.remove(o.data("item"))
                        }
                        break;
                    case 46:
                        if (0 === a(i[0])) {
                            var r = n.next();
                            r && s.remove(r.data("item"))
                        }
                        break;
                    case 37:
                        var l = n.prev();
                        0 === i.val().length && l[0] && (l.before(n), i.focus());
                        break;
                    case 39:
                        var c = n.next();
                        0 === i.val().length && c[0] && (c.after(n), i.focus());
                        break;
                    default:
                        s.options.freeInput && t.inArray(e.which, s.options.confirmKeys) >= 0 && (s.add(i.val()), i.val(""), e.preventDefault())
                }
                i.attr("size", Math.max(this.inputSize, i.val().length))
            }, s)), s.$container.on("click", "[data-role=remove]", t.proxy(function(e) {
                s.remove(t(e.target).closest(".tag").data("item"))
            }, s)), s.options.itemValue === o.itemValue && ("INPUT" === s.$element[0].tagName ? s.add(s.$element.val()) : t("option", s.$element).each(function() {
                s.add(t(this).attr("value"), !0)
            }))
        },
        destroy: function() {
            var t = this;
            t.$container.off("keypress", "input"), t.$container.off("click", "[role=remove]"), t.$container.remove(), t.$element.removeData("tagsinput"), t.$element.show()
        },
        focus: function() {
            this.$input.focus()
        },
        input: function() {
            return this.$input
        },
        findInputWrapper: function() {
            for (var e = this.$input[0], i = this.$container[0]; e && e.parentNode !== i;) e = e.parentNode;
            return t(e)
        }
    }, t.fn.tagsinput = function(i) {
        var n = [];
        return this.each(function() {
            var s = t(this).data("tagsinput");
            if (s) {
                var a = "";
                void 0 !== a && n.push(a)
            } else s = new e(this, i), t(this).data("tagsinput", s), n.push(s), "SELECT" === this.tagName && t("option", t(this)).attr("selected", "selected"), t(this).val(t(this).val())
        }), "string" == typeof i ? n.length > 1 ? n : n[0] : n
    }, t.fn.tagsinput.Constructor = e;
    var r = t("<div />");
    t(function() {
        t("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput()
    })
}(window.jQuery), + function(t) {
    "use strict";
    var e = "Microsoft Internet Explorer" == window.navigator.appName,
        i = function(e, i) {
            if (this.$element = t(e), this.$input = this.$element.find(":file"), 0 !== this.$input.length) {
                this.name = this.$input.attr("name") || i.name, this.$hidden = this.$element.find('input[type=hidden][name="' + this.name + '"]'), 0 === this.$hidden.length && (this.$hidden = t('<input type="hidden" />'), this.$element.prepend(this.$hidden)), this.$preview = this.$element.find(".fileinput-preview");
                var n = this.$preview.css("height");
                "inline" != this.$preview.css("display") && "0px" != n && "none" != n && this.$preview.css("line-height", n), this.original = {
                    exists: this.$element.hasClass("fileinput-exists"),
                    preview: this.$preview.html(),
                    hiddenVal: this.$hidden.val()
                }, this.listen()
            }
        };
    i.prototype.listen = function() {
        this.$input.on("change.bs.fileinput", t.proxy(this.change, this)), t(this.$input[0].form).on("reset.bs.fileinput", t.proxy(this.reset, this)), this.$element.find('[data-trigger="fileinput"]').on("click.bs.fileinput", t.proxy(this.trigger, this)), this.$element.find('[data-dismiss="fileinput"]').on("click.bs.fileinput", t.proxy(this.clear, this))
    }, i.prototype.change = function(e) {
        if (void 0 === e.target.files && (e.target.files = e.target && e.target.value ? [{
            name: e.target.value.replace(/^.+\\/, "")
        }] : []), 0 !== e.target.files.length) {
            this.$hidden.val(""), this.$hidden.attr("name", ""), this.$input.attr("name", this.name);
            var i = e.target.files[0];
            if (this.$preview.length > 0 && ("undefined" != typeof i.type ? i.type.match("image.*") : i.name.match(/\.(gif|png|jpe?g)$/i)) && "undefined" != typeof FileReader) {
                var n = new FileReader,
                    s = this.$preview,
                    a = this.$element;
                n.onload = function(n) {
                    var o = t("<img>").attr("src", n.target.result);
                    e.target.files[0].result = n.target.result, a.find(".fileinput-filename").text(i.name), "none" != s.css("max-height") && o.css("max-height", parseInt(s.css("max-height"), 10) - parseInt(s.css("padding-top"), 10) - parseInt(s.css("padding-bottom"), 10) - parseInt(s.css("border-top"), 10) - parseInt(s.css("border-bottom"), 10)), s.html(o), a.addClass("fileinput-exists").removeClass("fileinput-new"), a.trigger("change.bs.fileinput", e.target.files)
                }, n.readAsDataURL(i)
            } else this.$element.find(".fileinput-filename").text(i.name), this.$preview.text(i.name), this.$element.addClass("fileinput-exists").removeClass("fileinput-new"), this.$element.trigger("change.bs.fileinput")
        }
    }, i.prototype.clear = function(t) {
        if (t && t.preventDefault(), this.$hidden.val(""), this.$hidden.attr("name", this.name), this.$input.attr("name", ""), e) {
            var i = this.$input.clone(!0);
            this.$input.after(i), this.$input.remove(), this.$input = i
        } else this.$input.val("");
        this.$preview.html(""), this.$element.find(".fileinput-filename").text(""), this.$element.addClass("fileinput-new").removeClass("fileinput-exists"), t !== !1 && (this.$input.trigger("change"), this.$element.trigger("clear.bs.fileinput"))
    }, i.prototype.reset = function() {
        this.clear(!1), this.$hidden.val(this.original.hiddenVal), this.$preview.html(this.original.preview), this.$element.find(".fileinput-filename").text(""), this.original.exists ? this.$element.addClass("fileinput-exists").removeClass("fileinput-new") : this.$element.addClass("fileinput-new").removeClass("fileinput-exists"), this.$element.trigger("reset.bs.fileinput")
    }, i.prototype.trigger = function(t) {
        this.$input.trigger("click"), t.preventDefault()
    }, t.fn.fileinput = function(e) {
        return this.each(function() {
            var n = t(this),
                s = n.data("fileinput");
            s || n.data("fileinput", s = new i(this, e)), "string" == typeof e && s[e]()
        })
    }, t.fn.fileinput.Constructor = i, t(document).on("click.fileinput.data-api", '[data-provides="fileinput"]', function(e) {
        var i = t(this);
        if (!i.data("fileinput")) {
            i.fileinput(i.data());
            var n = t(e.target).closest('[data-dismiss="fileinput"],[data-trigger="fileinput"]');
            n.length > 0 && (e.preventDefault(), n.trigger("click.bs.fileinput"))
        }
    })
}(window.jQuery),
function(t) {
    "use strict";
    t.fn.TouchSpin = function(e) {
        var i = {
            min: 0,
            max: 100,
            initval: "",
            step: 1,
            decimals: 0,
            stepinterval: 100,
            forcestepdivisibility: "round",
            stepintervaldelay: 500,
            prefix: "",
            postfix: "",
            prefix_extraclass: "",
            postfix_extraclass: "",
            booster: !0,
            boostat: 10,
            maxboostedstep: !1,
            mousewheel: !0,
            buttondown_class: "btn btn-default",
            buttonup_class: "btn btn-default"
        };
        return this.each(function() {
            function n() {
                if (!A.data("alreadyinitialized")) {
                    if (A.data("alreadyinitialized", !0), !A.is("input")) return void console.log("Must be an input.");
                    o(), s(), g(), l(), u(), h(), p(), f(), D.input.css("display", "block")
                }
            }

            function s() {
                "" !== x.initval && "" === A.val() && A.val(x.initval)
            }

            function a(t) {
                r(t), g();
                var e = Number(D.input.val());
                D.input.val(e.toFixed(x.decimals))
            }

            function o() {
                x = t.extend({}, i, O, e)
            }

            function r(e) {
                x = t.extend({}, x, e)
            }

            function l() {
                var t = A.val(),
                    e = A.parent();
                "" !== t && (t = Number(t).toFixed(x.decimals)), A.data("initvalue", t).val(t), A.addClass("form-control"), e.hasClass("input-group") ? c(e) : d()
            }

            function c(e) {
                e.addClass("bootstrap-touchspin");
                var i, n, s = A.prev(),
                    a = A.next(),
                    o = '<span class="input-group-addon bootstrap-touchspin-prefix">' + x.prefix + "</span>",
                    r = '<span class="input-group-addon bootstrap-touchspin-postfix">' + x.postfix + "</span>";
                s.hasClass("input-group-btn") ? (i = '<button class="' + x.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button>', s.append(i)) : (i = '<span class="input-group-btn"><button class="' + x.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button></span>', t(i).insertBefore(A)), a.hasClass("input-group-btn") ? (n = '<button class="' + x.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button>', a.prepend(n)) : (n = '<span class="input-group-btn"><button class="' + x.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button></span>', t(n).insertAfter(A)), t(o).insertBefore(A), t(r).insertAfter(A), C = e
            }

            function d() {
                var e = '<div class="input-group bootstrap-touchspin"><span class="input-group-btn"><button class="' + x.buttondown_class + ' bootstrap-touchspin-down" type="button">-</button></span><span class="input-group-addon bootstrap-touchspin-prefix">' + x.prefix + '</span><span class="input-group-addon bootstrap-touchspin-postfix">' + x.postfix + '</span><span class="input-group-btn"><button class="' + x.buttonup_class + ' bootstrap-touchspin-up" type="button">+</button></span></div>';
                C = t(e).insertBefore(A), t(".bootstrap-touchspin-prefix", C).after(A), A.hasClass("input-sm") ? C.addClass("input-group-sm") : A.hasClass("input-lg") && C.addClass("input-group-lg")
            }

            function u() {
                D = {
                    down: t(".bootstrap-touchspin-down", C),
                    up: t(".bootstrap-touchspin-up", C),
                    input: t("input", C),
                    prefix: t(".bootstrap-touchspin-prefix", C).addClass(x.prefix_extraclass),
                    postfix: t(".bootstrap-touchspin-postfix", C).addClass(x.postfix_extraclass)
                }
            }

            function h() {
                "" == x.prefix && D.prefix.hide(), "" == x.postfix && D.postfix.hide()
            }

            function p() {
                A.on("keydown", function(t) {
                    var e = t.keyCode || t.which;
                    38 === e ? ("up" !== I && (b(), _()), t.preventDefault()) : 40 === e && ("down" !== I && (y(), w()), t.preventDefault())
                }), A.on("keyup", function(t) {
                    var e = t.keyCode || t.which;
                    38 === e ? k() : 40 === e && k()
                }), A.on("blur", function() {
                    g()
                }), D.down.on("keydown", function(t) {
                    var e = t.keyCode || t.which;
                    (32 === e || 13 === e) && ("down" !== I && (y(), w()), t.preventDefault())
                }), D.down.on("keyup", function(t) {
                    var e = t.keyCode || t.which;
                    (32 === e || 13 === e) && k()
                }), D.up.on("keydown", function(t) {
                    var e = t.keyCode || t.which;
                    (32 === e || 13 === e) && ("up" !== I && (b(), _()), t.preventDefault())
                }), D.up.on("keyup", function(t) {
                    var e = t.keyCode || t.which;
                    (32 === e || 13 === e) && k()
                }), D.down.on("mousedown touchstart", function(t) {
                    y(), w(), t.preventDefault(), t.stopPropagation()
                }), D.up.on("mousedown touchstart", function(t) {
                    b(), _(), t.preventDefault(), t.stopPropagation()
                }), D.up.on("mouseout touchleave touchend touchcancel", function(t) {
                    I && (t.stopPropagation(), k())
                }), D.down.on("mouseout touchleave touchend touchcancel", function(t) {
                    I && (t.stopPropagation(), k())
                }), D.down.on("mousemove touchmove", function(t) {
                    I && (t.stopPropagation(), t.preventDefault())
                }), D.up.on("mousemove touchmove", function(t) {
                    I && (t.stopPropagation(), t.preventDefault())
                }), t(document).on("mouseup touchend touchcancel", function(t) {
                    I && (t.preventDefault(), k())
                }), t(document).on("mousemove touchmove scroll scrollstart", function(t) {
                    I && (t.preventDefault(), k())
                }), x.mousewheel && A.on("mousewheel DOMMouseScroll", function(t) {
                    var e = t.originalEvent.wheelDelta || -t.originalEvent.detail;
                    t.stopPropagation(), t.preventDefault(), 0 > e ? y() : b()
                })
            }

            function f() {
                A.on("touchspin.uponce", function() {
                    k(), b()
                }), A.on("touchspin.downonce", function() {
                    k(), y()
                }), A.on("touchspin.startupspin", function() {
                    _()
                }), A.on("touchspin.startdownspin", function() {
                    w()
                }), A.on("touchspin.stopspin", function() {
                    k()
                }), A.on("touchspin.updatesettings", function(t, e) {
                    a(e)
                })
            }

            function m(t) {
                switch (x.forcestepdivisibility) {
                    case "round":
                        return (Math.round(t / x.step) * x.step).toFixed(x.decimals);
                    case "floor":
                        return (Math.floor(t / x.step) * x.step).toFixed(x.decimals);
                    case "ceil":
                        return (Math.ceil(t / x.step) * x.step).toFixed(x.decimals);
                    default:
                        return t
                }
            }

            function g() {
                var t, e, i;
                t = A.val(), "" !== t && (x.decimals > 0 && "." === t || (e = parseFloat(t), isNaN(e) && (e = 0), i = e, e.toString() !== t && (i = e), e < x.min && (i = x.min), e > x.max && (i = x.max), i = m(i), Number(t).toString() !== i.toString() && (A.val(i), A.trigger("change"))))
            }

            function v() {
                if (x.booster) {
                    var t = Math.pow(2, Math.floor(Y / x.boostat)) * x.step;
                    return x.maxboostedstep && t > x.maxboostedstep && (t = x.maxboostedstep, M = Math.round(M / t * t)), Math.max(x.step, t)
                }
                return x.step
            }

            function b() {
                g(), M = parseFloat(D.input.val()), isNaN(M) && (M = 0);
                var t = M,
                    e = v();
                M += e, M > x.max && (M = x.max, A.trigger("touchspin.on.max"), k()), D.input.val(Number(M).toFixed(x.decimals)), t !== M && A.trigger("change")
            }

            function y() {
                g(), M = parseFloat(D.input.val()), isNaN(M) && (M = 0);
                var t = M,
                    e = v();
                M -= e, M < x.min && (M = x.min, A.trigger("touchspin.on.min"), k()), D.input.val(M.toFixed(x.decimals)), t !== M && A.trigger("change")
            }

            function w() {
                k(), Y = 0, I = "down", A.trigger("touchspin.on.startspin"), A.trigger("touchspin.on.startdownspin"), T = setTimeout(function() {
                    $ = setInterval(function() {
                        Y++, y()
                    }, x.stepinterval)
                }, x.stepintervaldelay)
            }

            function _() {
                k(), Y = 0, I = "up", A.trigger("touchspin.on.startspin"), A.trigger("touchspin.on.startupspin"), F = setTimeout(function() {
                    S = setInterval(function() {
                        Y++, b()
                    }, x.stepinterval)
                }, x.stepintervaldelay)
            }

            function k() {
                switch (clearTimeout(T), clearTimeout(F), clearInterval($), clearInterval(S), I) {
                    case "up":
                        A.trigger("touchspin.on.stopupspin"), A.trigger("touchspin.on.stopspin");
                        break;
                    case "down":
                        A.trigger("touchspin.on.stopdownspin"), A.trigger("touchspin.on.stopspin")
                }
                Y = 0, I = !1
            }
            var x, C, D, M, $, S, T, F, A = t(this),
                O = A.data(),
                Y = 0,
                I = !1;
            n()
        })
    }
}(jQuery),
function(t, e, i, n) {
    "use strict";
    var s = "nexchecks",
        a = "plugin_" + s,
        o = {
            label: "",
            labelPosition: "right",
            customClass: "",
            color: "blue"
        };
    jQuery(".the-radios a, .the-radios .input-label").live("click", function(e) {
        e.preventDefault();
        var i = t(this).hasClass("input-label") ? t(this).parent().find(".clearfix") : t(this).closest(".clearfix"),
            n = i.find("input"),
            s = i.find("a:first");
        "radio" === n.prop("type") && t('input[name="' + n.attr("name") + '"]').each(function(e, i) {
            t(i).prop("checked", !1).parent().find("a:first").removeClass("checked").removeClass("ui-state-active").addClass("ui-state-default").removeClass(t(i).closest(".the-radios").attr("data-checked-class")), s.attr("class", "checked fa ui-state-active " + s.closest(".the-radios").attr("data-checked-class"))
        }), n.prop("checked") ? (n.prop("checked", !1), s.attr("class", "ui-state-default")) : (n.prop("checked", !0), s.attr("class", "checked ui-state-active fa " + s.closest(".the-radios").attr("data-checked-class")))
    });
    var r = function(e) {
        this.element = e, this.options = t.extend({}, o)
    };
    r.prototype = {
        init: function(e) {
            t.extend(this.options, e);
            var i = t(this.element);
            i.parent().addClass("has-pretty-child");
            var s = i.parent().find("a").length;
            if (!(s > 0)) {
                i.css("display", "none");
                var a = i.data("type") !== n ? i.data("type") : i.attr("type"),
                    o = (i.data("label") !== n ? i.data("label") : this.options.label, i.data("labelposition") !== n ? "label" + i.data("labelposition") : "label" + this.options.labelPosition),
                    r = i.data("customclass") !== n ? i.data("customclass") : this.options.customClass,
                    l = i.data("color") !== n ? i.data("color") : this.options.color,
                    c = i.prop("disabled") === !0 ? "disabled" : "",
                    d = ["pretty" + a, o, r, l].join(" ");
                i.wrap('<div class="clearfix ' + d + '"></div>').parent().html();
                var u = [],
                    h = i.prop("checked") ? "checked" : "";
                u.push('<a class="fa ui-state-default ' + h + " " + c + '"></a>'), i.parent().append(u.join("\n"))
            }
        },
        enable: function() {
            t(this.element).removeAttr("disabled").parent().find("a:first").removeClass("disabled")
        },
        disable: function() {
            t(this.element).attr("disabled", "disabled").parent().find("a:first").addClass("disabled")
        },
        destroy: function() {
            var e = t(this.element),
                i = e.clone();
            i.removeAttr("style").insertBefore(e.parent()), e.parent().remove()
        }
    }, t.fn[s] = function(e) {
        var i, n;
        if (this.data(a) instanceof r || this.data(a, new r(this)), n = this.data(a), n.element = this, "undefined" == typeof e || "object" == typeof e) "function" == typeof n.init && n.init(e);
        else {
            if ("string" == typeof e && "function" == typeof n[e]) return i = Array.prototype.slice.call(arguments, 1), n[e].apply(n, i);
            t.error("Method " + e + " does not exist on jQuery." + s)
        }
    }

}(jQuery);
