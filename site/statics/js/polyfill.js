// 兼容性处理
if (!Array.prototype.forEach) {
    Array.prototype.forEach = function forEach(callback) {
        // 获取数组长度
        var len = this.length;
        if (typeof callback != "function") {
            throw new TypeError();
        }
        // thisArg为callback 函数的执行上下文环境
        var thisArg = arguments[1];
        for (var i = 0; i < len; i++) {
            if (i in this) {
                // callback函数接收三个参数：当前项的值、当前项的索引和数组本身
                callback.call(thisArg, this[i], i, this);
            }
        }
    }
}
if (!Array.prototype.filter) {
    Array.prototype.filter = function (func, thisArg) {
        'use strict';
        if (!((typeof func === 'Function' || typeof func === 'function') && this))
            throw new TypeError();

        var len = this.length >>> 0,
            res = new Array(len), // preallocate array
            t = this, c = 0, i = -1;
        if (thisArg === undefined) {
            while (++i !== len) {
                // checks to see if the key was set
                if (i in this) {
                    if (func(t[i], i, t)) {
                        res[c++] = t[i];
                    }
                }
            }
        } else {
            while (++i !== len) {
                // checks to see if the key was set
                if (i in this) {
                    if (func.call(thisArg, t[i], i, t)) {
                        res[c++] = t[i];
                    }
                }
            }
        }

        res.length = c; // shrink down array to proper size
        return res;
    };
}
if (!Array.prototype.includes) {
    Object.defineProperty(Array.prototype, 'includes', {
        value: function (valueToFind, fromIndex) {
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }
            var o = Object(this);
            var len = o.length >>> 0;
            if (len === 0) {
                return false;
            }
            var n = fromIndex | 0;
            var k = Math.max(n >= 0 ? n : len - Math.abs(n), 0);

            function sameValueZero(x, y) {
                return x === y || (typeof x === 'number' && typeof y === 'number' && isNaN(x) && isNaN(y));
            }

            while (k < len) {
                if (sameValueZero(o[k], valueToFind)) {
                    return true;
                }
                k++;
            }
            return false;
        }
    });
}
if (!String.prototype.includes) {
    String.prototype.includes = function (search, start) {
        'use strict';
        if (typeof start !== 'number') {
            start = 0;
        }
        if (start + search.length > this.length) {
            return false;
        } else {
            return this.indexOf(search, start) !== -1;
        }
    };
}

/**
 * Object.prototype.forEach() polyfill
 * https://gomakethings.com/looping-through-objects-with-es6/
 * @author Chris Ferdinandi
 * @license MIT
 */
if (!Object.prototype.forEach) {
    Object.defineProperty(Object.prototype, 'forEach', {
        value: function (callback, thisArg) {
            if (this == null) {
                throw new TypeError('Not an object');
            }
            thisArg = thisArg || window;
            // 增加一个计数 2020-4-26
            var index = 0;
            for (var key in this) {
                if (this.hasOwnProperty(key)) {
                    callback.call(thisArg, this[key], key, index, this);
                    index++;
                }
            }
        }
    });
}

/**
 * Object.prototype.filter
 * https://blog.csdn.net/github_38885296/article/details/89528139
 */
if (!Object.prototype.filter) {
    Object.defineProperty(Object.prototype, 'filter', {
        enumerable: false,
        writable: true,
        value: function (check = ((v, k, o) => undefined), this4check = undefined) {
            const oldObj = this;
            const newObj = {};
            Object.entries(oldObj).forEach(([key, value]) => {
                if (check.bind(this4check)(value, key, oldObj))
                    newObj[key] = value;
            })
            return newObj;
        },
    });
}