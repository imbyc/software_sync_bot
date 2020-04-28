;(function ($) {

var _v = {};
_v.keys = function(object){
    var keys = [];
    for(var prop in object){
        if(object.hasOwnProperty(prop)){
            keys.push(prop);
        }
    }
    return keys;
};

_v.invert = function(obj) {
    var result = {};
    var keys = _v.keys(obj);
    for (var i = 0, length = keys.length; i < length; i++) {
        result[obj[keys[i]]] = keys[i];
    }
    return result;
};

//extend from other object
_v.extend = function(obj){
    var hasOwnProperty   = ({}).hasOwnProperty, source, prop;
    for(var i = 1, length = arguments.length; i < length; i++){
        source = arguments[i];
        for (prop in source) {
            if (hasOwnProperty.call(source, prop)) {
                obj[prop] = source[prop];
            }
        }
    }
    return obj;
};

_v.templateSettings = {
    evaluate    : /<%([\s\S]+?)%>/g,
    interpolate : /<%=([\s\S]+?)%>/g,
    escape      : /<%-([\s\S]+?)%>/g
};
var noMatch = /(.)^/;
var escapes = {
    "'":      "'",
    '\\':     '\\',
    '\r':     'r',
    '\n':     'n',
    '\u2028': 'u2028',
    '\u2029': 'u2029'
};

var escaper = /\\|'|\r|\n|\u2028|\u2029/g;
var escapeChar = function(match) {
    return '\\' + escapes[match];
};

// List of HTML entities for escaping.
var escapeMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#x27;',
    '`': '&#x60;'
};
var unescapeMap = _v.invert(escapeMap);

// Functions for escaping and unescaping strings to/from HTML interpolation.
var createEscaper = function(map) {
    var escaper = function(match) {
        return map[match];
    };
    // Regexes for identifying a key that needs to be escaped
    var source = '(?:' + _v.keys(map).join('|') + ')';
    var testRegexp = RegExp(source);
    var replaceRegexp = RegExp(source, 'g');
    return function(string) {
        string = string == null ? '' : '' + string;
        return testRegexp.test(string) ? string.replace(replaceRegexp, escaper) : string;
    };
};
_v.escape = createEscaper(escapeMap);
_v.unescape = createEscaper(unescapeMap);

_v.template = function(text, data, settings) {
    settings = _v.extend({}, settings, _v.templateSettings);
    var matcher = RegExp([
        (settings.escape || noMatch).source,
        (settings.interpolate || noMatch).source,
        (settings.evaluate || noMatch).source
    ].join('|') + '|$', 'g');

    var index = 0;
    var source = "__p+='";
    text.replace(matcher, function(match, escape, interpolate, evaluate, offset){
        source += text.slice(index, offset).replace(escaper, escapeChar);
        index = offset + match.length;
        if (escape) {
            source += "'+\n((__t=(" + escape + "))==null?'':_v.escape(__t))+\n'";
        } else if (interpolate) {
            source += "'+\n((__t=(" + interpolate + "))==null?'':__t)+\n'";
        } else if (evaluate) {
            source += "';\n" + evaluate + "\n__p+='";
        }
        return match;
    });
    source += "';\n";
    if (!settings.variable){
        source = 'with(obj||{}){\n' + source + '}\n';
    }

    source = "var __t,__p='',__j=Array.prototype.join," +
        "print=function(){__p+=__j.call(arguments,'');};\n" +
        source + 'return __p;\n';

    var render = '';
    try {
        render = Function(settings.variable || 'obj', '_v', source);
    } catch (e) {
        e.source = source;
        throw e;
    }

    if(data){
        return render(data, _v);
    }
    var template = function(data) {
      return render.call(this, data, _v);
    };

    var argument = settings.variable || 'obj';
    template.source = 'function(' + argument + '){\n' + source + '}';

    return template;
};

$.fn.render = function(tempHtml, data, settings){
    return _v.template(tempHtml, data, settings);
};

})(jQuery);
