module.exports =  function (_a, e, t, n) {
  function r(e) {
    return t.update ? t.update(e, "utf8") : t.write(e, "utf8");
  }
  return (
    (n = n || []),
    {
      dispatch: function (t) {
        e.replacer && (t = e.replacer(t));
        var n = typeof t;
        return null === t && (n = "null"), this["_" + n](t);
      },
      _object: function (t) {
        var o,
          i = Object.prototype.toString.call(t),
          s = /\[object (.*)\]/i.exec(i);
        if (
          ((s = (s = s ? s[1] : "unknown:[" + i + "]").toLowerCase()),
          0 <= (o = n.indexOf(t)))
        )
          return this.dispatch("[CIRCULAR:" + o + "]");
        if ((n.push(t), void 0 !== _a && _a.isBuffer && _a.isBuffer(t)))
          return r("buffer:"), r(t);
        if ("object" === s || "function" === s) {
          var c = Object.keys(t);
          e.unorderedObjects && (c = c.sort()),
            !1 === e.respectType ||
              g(t) ||
              c.splice(0, 0, "prototype", "__proto__", "constructor"),
            e.excludeKeys &&
              (c = c.filter(function (t) {
                return !e.excludeKeys(t);
              })),
            r("object:" + c.length + ":");
          var l = this;
          return c.forEach(function (n) {
            l.dispatch(n), r(":"), e.excludeValues || l.dispatch(t[n]), r(",");
          });
        }
        if (!this["_" + s]) {
          if (e.ignoreUnknown) return r("[" + s + "]");
          throw new Error('Unknown object type "' + s + '"');
        }
        this["_" + s](t);
      },
      _array: function (t, o) {
        o = void 0 !== o ? o : !1 !== e.unorderedArrays;
        var a = this;
        if ((r("array:" + t.length + ":"), !o || t.length <= 1))
          return t.forEach(function (e) {
            return a.dispatch(e);
          });
        var i = [],
          s = t.map(function (t) {
            var r = new b(),
              o = n.slice();
            return (
              v(e, r, o).dispatch(t),
              (i = i.concat(o.slice(n.length))),
              r.read().toString()
            );
          });
        return (n = n.concat(i)), s.sort(), this._array(s, !1);
      },
      _date: function (e) {
        return r("date:" + e.toJSON());
      },
      _symbol: function (e) {
        return r("symbol:" + e.toString());
      },
      _error: function (e) {
        return r("error:" + e.toString());
      },
      _boolean: function (e) {
        return r("bool:" + e.toString());
      },
      _string: function (e) {
        r("string:" + e.length + ":"), r(e.toString());
      },
      _function: function (t) {
        r("fn:"),
          g(t) ? this.dispatch("[native]") : this.dispatch(t.toString()),
          !1 !== e.respectFunctionNames &&
            this.dispatch("function-name:" + String(t.name)),
          e.respectFunctionProperties && this._object(t);
      },
      _number: function (e) {
        return r("number:" + e.toString());
      },
      _xml: function (e) {
        return r("xml:" + e.toString());
      },
      _null: function () {
        return r("Null");
      },
      _undefined: function () {
        return r("Undefined");
      },
      _regexp: function (e) {
        return r("regex:" + e.toString());
      },
      _uint8array: function (e) {
        return r("uint8array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _uint8clampedarray: function (e) {
        return (
          r("uint8clampedarray:"), this.dispatch(Array.prototype.slice.call(e))
        );
      },
      _int8array: function (e) {
        return r("uint8array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _uint16array: function (e) {
        return r("uint16array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _int16array: function (e) {
        return r("uint16array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _uint32array: function (e) {
        return r("uint32array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _int32array: function (e) {
        return r("uint32array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _float32array: function (e) {
        return r("float32array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _float64array: function (e) {
        return r("float64array:"), this.dispatch(Array.prototype.slice.call(e));
      },
      _arraybuffer: function (e) {
        return r("arraybuffer:"), this.dispatch(new Uint8Array(e));
      },
      _url: function (e) {
        return r("url:" + e.toString());
      },
      _map: function (t) {
        r("map:");
        var n = Array.from(t);
        return this._array(n, !1 !== e.unorderedSets);
      },
      _set: function (t) {
        r("set:");
        var n = Array.from(t);
        return this._array(n, !1 !== e.unorderedSets);
      },
      _blob: function () {
        if (e.ignoreUnknown) return r("[blob]");
        throw Error(
          'Hashing Blob objects is currently not supported\n(see https://github.com/puleos/object-hash/issues/26)\nUse "options.replacer" or "options.ignoreUnknown"\n'
        );
      },
      _domwindow: function () {
        return r("domwindow");
      },
      _process: function () {
        return r("process");
      },
      _timer: function () {
        return r("timer");
      },
      _pipe: function () {
        return r("pipe");
      },
      _tcp: function () {
        return r("tcp");
      },
      _udp: function () {
        return r("udp");
      },
      _tty: function () {
        return r("tty");
      },
      _statwatcher: function () {
        return r("statwatcher");
      },
      _securecontext: function () {
        return r("securecontext");
      },
      _connection: function () {
        return r("connection");
      },
      _zlib: function () {
        return r("zlib");
      },
      _context: function () {
        return r("context");
      },
      _nodescript: function () {
        return r("nodescript");
      },
      _httpparser: function () {
        return r("httpparser");
      },
      _dataview: function () {
        return r("dataview");
      },
      _signal: function () {
        return r("signal");
      },
      _fsevent: function () {
        return r("fsevent");
      },
      _tlswrap: function () {
        return r("tlswrap");
      },
    }
  );
};
