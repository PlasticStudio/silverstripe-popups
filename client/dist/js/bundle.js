/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "jquery":
/*!*************************!*\
  !*** external "jQuery" ***!
  \*************************/
/***/ (function(module) {

module.exports = jQuery;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!********************************!*\
  !*** ./client/src/js/index.js ***!
  \********************************/
/* provided dependency */ var $ = __webpack_require__(/*! jquery */ "jquery");


function showNextPopup() {
  const popups = $('.wtk-popup');
  for (let i = 0; i < popups.length; i++) {
    const popup = $(popups[i]);
    const shown = shouldShowPopup(popup);
    if (shown) {
      break;
    }
  }
}
function closePopup(popupId) {
  const popup = $(`.wtk-popup[data-popup-id='${popupId}']`);
  const showAgainAfter = parseInt(popup.data('show-again-after'), 10);
  const now = new Date();
  const expiryDate = showAgainAfter ? new Date(now.getTime() + showAgainAfter * 24 * 60 * 60 * 1000) : new Date(now.getTime() + 10 * 365 * 24 * 60 * 60 * 1000);
  const expiryDateString = expiryDate.toUTCString();
  document.cookie = `popup-hidden-${popupId}=true; expires=${expiryDateString}; path=/`;
  document.cookie = `popup-last-shown-${popupId}=${now.toUTCString()}; expires=${expiryDateString}; path=/`;
  document.cookie = `popup-minimized-${popupId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
  popup.addClass('wtk-popup--hidden');
  setTimeout(showNextPopup, 3000);
}
function minimizePopup(popupId, animate) {
  const popup = $(`.wtk-popup[data-popup-id='${popupId}']`);
  if (animate) {
    popup.addClass('wtk-popup--animating');
    setTimeout(() => {
      popup.removeClass('wtk-popup--animating');
    }, 300);
  }
  popup.addClass('wtk-popup--minimized-state');
  popup.find('.wtk-popup__full').addClass('wtk-popup--hidden');
  popup.find('.wtk-popup__minimized').removeClass('wtk-popup--hidden');
  popup.removeClass('wtk-popup--hidden');
  document.cookie = `popup-minimized-${popupId}=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/`;
}
function showFullPopup(popupId) {
  const popup = $(`.wtk-popup[data-popup-id='${popupId}']`);
  popup.removeClass('wtk-popup--minimized-state');
  popup.find('.wtk-popup__minimized').addClass('wtk-popup--hidden');
  popup.find('.wtk-popup__full').removeClass('wtk-popup--hidden');
  document.cookie = `popup-minimized-${popupId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
}
function getCookie(name) {
  const cookieArr = document.cookie.split('; ');
  for (let i = 0; i < cookieArr.length; i++) {
    const cookiePair = cookieArr[i].split('=');
    if (name === cookiePair[0].trim()) {
      return decodeURIComponent(cookiePair[1]);
    }
  }
  return null;
}
function shouldShowPopup(popup) {
  const now = new Date();
  const popupId = popup.data('popup-id');
  const popupHidden = getCookie(`popup-hidden-${popupId}`);
  const showAgainAfter = parseInt(popup.data('show-again-after'), 10);
  if (popupHidden) {
    return false;
  }
  if (!isNaN(showAgainAfter) && showAgainAfter > 0) {
    const lastShown = new Date(getCookie(`popup-last-shown-${popupId}`));
    if ((now - lastShown) / (1000 * 3600 * 24) < showAgainAfter) {
      return false;
    }
  }
  if (getCookie(`popup-minimized-${popupId}`) === 'true') {
    minimizePopup(popupId, false);
  } else {
    popup.removeClass('wtk-popup--hidden');
  }
  return true;
}
function setupPopup(popup) {
  const popupId = popup.data('popup-id');
  if (getCookie(`popup-minimized-${popupId}`) === 'true') {
    minimizePopup(popupId, false);
  } else {
    popup.removeClass('wtk-popup--hidden');
  }
}
$(() => {
  console.log('[silverstripe-popups] loaded');
  $('.wtk-popup').each(function () {
    const popup = $(this);
    setupPopup(popup);
  });
  setTimeout(showNextPopup, 3000);
});
$('.c-popup').on('click', function () {
  const popupId = $(this).data('popup-id');
  closePopup(popupId);
});
$('.c-popup__inner').on('click', e => {
  e.stopPropagation();
});
$('.wtk-popup__minimize').on('click', function () {
  const popupId = $(this).data('popup-id');
  minimizePopup(popupId, true);
});
$('.wtk-popup__minimized').on('click', function () {
  const popupId = $(this).data('popup-id');
  showFullPopup(popupId);
});
$('.wtk-popup__close').on('click', function () {
  const popupId = $(this).data('popup-id');
  closePopup(popupId);
});
$('.wtk-popup__backdrop').on('click', function () {
  const popupId = $(this).data('popup-id');
  const popup = $(`.wtk-popup[data-popup-id='${popupId}']`);
  if (popup.find('.wtk-popup__minimize').length > 0) {
    minimizePopup(popupId, true);
  } else {
    closePopup(popupId);
  }
});
}();
/******/ })()
;
//# sourceMappingURL=bundle.js.map