/**
 * Browser Compatibility Polyfills and Helpers
 * Ensures JavaScript functionality works across different browsers
 */

(function() {
    'use strict';

    // Element.matches polyfill for older browsers
    if (!Element.prototype.matches) {
        Element.prototype.matches = 
            Element.prototype.matchesSelector || 
            Element.prototype.mozMatchesSelector ||
            Element.prototype.msMatchesSelector || 
            Element.prototype.oMatchesSelector || 
            Element.prototype.webkitMatchesSelector ||
            function(s) {
                var matches = (this.document || this.ownerDocument).querySelectorAll(s),
                    i = matches.length;
                while (--i >= 0 && matches.item(i) !== this) {}
                return i > -1;            
            };
    }

    // Element.closest polyfill for older browsers
    if (!Element.prototype.closest) {
        Element.prototype.closest = function(s) {
            var el = this;
            do {
                if (el.matches(s)) return el;
                el = el.parentElement || el.parentNode;
            } while (el !== null && el.nodeType === 1);
            return null;
        };
    }

    // Array.from polyfill for older browsers
    if (!Array.from) {
        Array.from = (function () {
            var toStr = Object.prototype.toString;
            var isCallable = function (fn) {
                return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
            };
            var toInteger = function (value) {
                var number = Number(value);
                if (isNaN(number)) { return 0; }
                if (number === 0 || !isFinite(number)) { return number; }
                return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
            };
            var maxSafeInteger = Math.pow(2, 53) - 1;
            var toLength = function (value) {
                var len = toInteger(value);
                return Math.min(Math.max(len, 0), maxSafeInteger);
            };

            return function from(arrayLike/*, mapFn, thisArg */) {
                var C = this;
                var items = Object(arrayLike);
                if (arrayLike == null) {
                    throw new TypeError('Array.from requires an array-like object - not null or undefined');
                }

                var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
                var T;
                if (typeof mapFn !== 'undefined') {
                    if (!isCallable(mapFn)) {
                        throw new TypeError('Array.from: when provided, the second argument must be a function');
                    }
                    if (arguments.length > 2) {
                        T = arguments[2];
                    }
                }

                var len = toLength(items.length);
                var A = isCallable(C) ? Object(new C(len)) : new Array(len);
                var k = 0;
                var kValue;
                while (k < len) {
                    kValue = items[k];
                    if (mapFn) {
                        A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
                    } else {
                        A[k] = kValue;
                    }
                    k += 1;
                }
                A.length = len;
                return A;
            };
        }());
    }

    // Object.assign polyfill for older browsers
    if (typeof Object.assign !== 'function') {
        Object.assign = function(target) {
            'use strict';
            if (target == null) {
                throw new TypeError('Cannot convert undefined or null to object');
            }

            var to = Object(target);

            for (var index = 1; index < arguments.length; index++) {
                var nextSource = arguments[index];

                if (nextSource != null) {
                    for (var nextKey in nextSource) {
                        if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                            to[nextKey] = nextSource[nextKey];
                        }
                    }
                }
            }
            return to;
        };
    }

    // String.includes polyfill for older browsers
    if (!String.prototype.includes) {
        String.prototype.includes = function(search, start) {
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

    // Array.includes polyfill for older browsers
    if (!Array.prototype.includes) {
        Array.prototype.includes = function(searchElement /*, fromIndex*/) {
            'use strict';
            var O = Object(this);
            var len = parseInt(O.length) || 0;
            if (len === 0) {
                return false;
            }
            var n = parseInt(arguments[1]) || 0;
            var k;
            if (n >= 0) {
                k = n;
            } else {
                k = len + n;
                if (k < 0) {k = 0;}
            }
            var currentElement;
            while (k < len) {
                currentElement = O[k];
                if (searchElement === currentElement ||
                   (searchElement !== searchElement && currentElement !== currentElement)) {
                    return true;
                }
                k++;
            }
            return false;
        };
    }

    // CustomEvent polyfill for older browsers
    if (typeof window.CustomEvent !== 'function') {
        function CustomEvent(event, params) {
            params = params || { bubbles: false, cancelable: false, detail: undefined };
            var evt = document.createEvent('CustomEvent');
            evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
            return evt;
        }
        CustomEvent.prototype = window.Event.prototype;
        window.CustomEvent = CustomEvent;
    }

    // Console polyfill for older browsers
    if (!window.console) {
        window.console = {
            log: function() {},
            error: function() {},
            warn: function() {},
            info: function() {},
            debug: function() {}
        };
    }

    // Helper function to safely get element matches
    window.safeMatches = function(element, selector) {
        if (!element || !selector) return false;
        
        try {
            return element.matches(selector);
        } catch (e) {
            console.warn('Error in safeMatches:', e);
            return false;
        }
    };

    // Helper function to safely get closest element
    window.safeClosest = function(element, selector) {
        if (!element || !selector) return null;
        
        try {
            return element.closest(selector);
        } catch (e) {
            console.warn('Error in safeClosest:', e);
            return null;
        }
    };

    // Helper function to safely convert to array
    window.safeArrayFrom = function(arrayLike) {
        try {
            return Array.from(arrayLike);
        } catch (e) {
            console.warn('Error in safeArrayFrom:', e);
            // Fallback for very old browsers
            var result = [];
            for (var i = 0; i < arrayLike.length; i++) {
                result.push(arrayLike[i]);
            }
            return result;
        }
    };

    // Helper function to safely dispatch events
    window.safeDispatchEvent = function(element, eventType, detail) {
        if (!element) return false;
        
        try {
            var event;
            if (detail !== undefined) {
                event = new CustomEvent(eventType, { detail: detail, bubbles: true });
            } else {
                event = new Event(eventType, { bubbles: true });
            }
            return element.dispatchEvent(event);
        } catch (e) {
            console.warn('Error in safeDispatchEvent:', e);
            // Fallback for older browsers
            try {
                var fallbackEvent = document.createEvent('Event');
                fallbackEvent.initEvent(eventType, true, true);
                return element.dispatchEvent(fallbackEvent);
            } catch (e2) {
                console.warn('Fallback event dispatch also failed:', e2);
                return false;
            }
        }
    };

    // Helper function to safely add event listeners with options
    window.safeAddEventListener = function(element, type, listener, options) {
        if (!element || !type || !listener) return;
        
        try {
            // Check if browser supports options parameter
            var supportsOptions = false;
            try {
                var opts = Object.defineProperty({}, 'passive', {
                    get: function() {
                        supportsOptions = true;
                    }
                });
                window.addEventListener('test', null, opts);
                window.removeEventListener('test', null, opts);
            } catch (e) {}
            
            if (supportsOptions && options) {
                element.addEventListener(type, listener, options);
            } else {
                // Fallback for older browsers
                var useCapture = options && (options.capture || options === true);
                element.addEventListener(type, listener, useCapture);
            }
        } catch (e) {
            console.warn('Error in safeAddEventListener:', e);
        }
    };

    console.log('Browser compatibility polyfills loaded successfully');

})();
