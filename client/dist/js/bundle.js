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
  const popups = $('.sp-popup').toArray().sort((a, b) => {
    const sortA = parseInt($(a).data('sort-order') || 0, 10);
    const sortB = parseInt($(b).data('sort-order') || 0, 10);
    return sortA - sortB;
  });
  for (let i = 0; i < popups.length; i++) {
    const popup = $(popups[i]);
    const popupId = popup.data('popup-id');
    if (getCookie(`popup-hidden-${popupId}`)) {
      continue;
    }
    const showAfter = parseInt(popup.data('show-after') || 0, 10);
    setTimeout(() => {
      if (!getCookie(`popup-hidden-${popupId}`)) {
        setupPopup(popup);
      }
    }, showAfter * 1000);
    break;
  }
}
function closePopup(popupId) {
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
  const now = new Date();
  const expiryTime = parseInt($('.sp-popups').data('cookie-expiry-time'), 10) || 2628000000;
  const expiryDate = new Date(now.getTime() + expiryTime);
  const expiryDateString = expiryDate.toUTCString();
  document.cookie = `popup-hidden-${popupId}=true; expires=${expiryDateString}; path=/`;
  document.cookie = `popup-minimized-${popupId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
  popup.addClass('sp-popup--hidden');
  setTimeout(showNextPopup, 3000);
}
function minimizePopup(popupId, animate) {
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
  if (animate) {
    popup.addClass('sp-popup--animating');
    setTimeout(() => {
      popup.removeClass('sp-popup--animating');
    }, 300);
  }
  popup.addClass('sp-popup--minimized-state');
  popup.find('.sp-popup__full').addClass('sp-popup--hidden');
  popup.find('.sp-popup__minimized').removeClass('sp-popup--hidden');
  popup.removeClass('sp-popup--hidden');
  document.cookie = `popup-minimized-${popupId}=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/`;
}
function showFullPopup(popupId) {
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
  popup.removeClass('sp-popup--minimized-state');
  popup.find('.sp-popup__minimized').addClass('sp-popup--hidden');
  popup.find('.sp-popup__full').removeClass('sp-popup--hidden');
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
  const popupId = popup.data('popup-id');
  const popupHidden = getCookie(`popup-hidden-${popupId}`);
  if (popupHidden) {
    return false;
  }
  if (getCookie(`popup-minimized-${popupId}`) === 'true') {
    minimizePopup(popupId, false);
  } else {
    popup.removeClass('sp-popup--hidden');
  }
  return true;
}
function setupPopup(popup) {
  const popupId = popup.data('popup-id');
  const collapseOnMobile = popup.data('collapse-on-mobile') === 1 || popup.data('collapse-on-mobile') === '1';
  const isMobile = window.matchMedia('(max-width: 600px)').matches;
  if (collapseOnMobile && isMobile) {
    minimizePopup(popupId, false);
    return;
  }
  if (getCookie(`popup-minimized-${popupId}`) === 'true') {
    minimizePopup(popupId, false);
  } else {
    popup.removeClass('sp-popup--hidden');
  }
}
$(() => {
  console.log('[silverstripe-popups] loaded');
  $('.sp-popup').each(function () {
    const popup = $(this);
    const popupId = popup.data('popup-id');
    if (getCookie(`popup-minimized-${popupId}`) === 'true') {
      minimizePopup(popupId, false);
    }
  });
  setTimeout(showNextPopup, 500);
});
$('.c-popup').on('click', function () {
  const popupId = $(this).data('popup-id');
  closePopup(popupId);
});
$('.c-popup__inner').on('click', e => {
  e.stopPropagation();
});
$('.sp-popup__minimize').on('click', function () {
  const popupId = $(this).data('popup-id');
  minimizePopup(popupId, true);
});
$('.sp-popup__minimized').on('click', function () {
  const popupId = $(this).data('popup-id');
  showFullPopup(popupId);
});
$('.sp-popup__close').on('click', function () {
  const popupId = $(this).data('popup-id');
  closePopup(popupId);
});
$(document).on('click', '.sp-popup--mode-strip .sp-popup__content, .sp-popup--mode-edge .sp-popup__content', function (e) {
  if (e.target === this) {
    const popupId = $(this).closest('.sp-popup').data('popup-id');
    const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
    const hasMinimize = popup.find('.sp-popup__minimize').length > 0;
    if (hasMinimize) {
      minimizePopup(popupId, true);
    }
  }
});
$('.sp-popup__backdrop').on('click', function () {
  const popupId = $(this).data('popup-id');
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
  const mode = popup.data('mode');
  const hasMinimize = popup.find('.sp-popup__minimize').length > 0;
  if (hasMinimize && mode === 'modal') {
    minimizePopup(popupId, true);
  } else {
    closePopup(popupId);
  }
});
}();
/******/ })()
;
//# sourceMappingURL=bundle.js.map