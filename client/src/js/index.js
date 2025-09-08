function showNextPopup() {
  // Get popups sorted by data-sort-order attribute
  const popups = $('.sp-popup').toArray().sort((a, b) => {
    const sortA = parseInt($(a).data('sort-order') || 0, 10);
    const sortB = parseInt($(b).data('sort-order') || 0, 10);
    return sortA - sortB;
  });
  
  // Show the first eligible popup (respecting cookies and minimized state)
  for (let i = 0; i < popups.length; i++) {
    const popup = $(popups[i]);
    const popupId = popup.data('popup-id');

    // Skip if cookie says hidden
    if (getCookie(`popup-hidden-${popupId}`)) {
      continue;
    }

    const showAfter = parseInt(popup.data('show-after') || 0, 10);

    // Schedule showing taking into account the configured delay
    setTimeout(() => {
      // If still eligible at show time, run setup/show
      if (!getCookie(`popup-hidden-${popupId}`)) {
        setupPopup(popup);
      }
    }, showAfter * 1000);

    // Only schedule the first eligible popup in order
    break;
  }
}

// Function to close popup
function closePopup(popupId) {
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
  const now = new Date();
  // Get configurable expiry time from data attribute or default to 1 month
  const expiryTime = parseInt($('.sp-popups').data('cookie-expiry-time'), 10) || 2628000000; // 1 month in milliseconds
  const expiryDate = new Date(now.getTime() + expiryTime);
  const expiryDateString = expiryDate.toUTCString();
  document.cookie = `popup-hidden-${popupId}=true; expires=${expiryDateString}; path=/`;
  document.cookie = `popup-minimized-${popupId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
  popup.addClass('sp-popup--hidden');
  setTimeout(showNextPopup, 3000);
}

// Function to minimize popup
function minimizePopup(popupId, animate) {
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
  if (animate) {
    popup.addClass('sp-popup--animating');
    setTimeout(() => {
      popup.removeClass('sp-popup--animating');
    }, 300);
  }

  popup.addClass('sp-popup--minimized-state');

  // Hide the full popup content
  popup.find('.sp-popup__full').addClass('sp-popup--hidden');

  // Show the minimized version
  popup.find('.sp-popup__minimized').removeClass('sp-popup--hidden');

  // Ensure the MAIN popup container is visible
  popup.removeClass('sp-popup--hidden');

  document.cookie = `popup-minimized-${popupId}=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/`;
}

// Function to show the full popup
function showFullPopup(popupId) {
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);

  popup.removeClass('sp-popup--minimized-state');

  // Hide the minimized version
  popup.find('.sp-popup__minimized').addClass('sp-popup--hidden');

  // Show the full content
  popup.find('.sp-popup__full').removeClass('sp-popup--hidden');

  // Clear the minimized cookie
  document.cookie = `popup-minimized-${popupId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
}

// Function to get cookie value
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
  // Check the minimized cookie and conditionally minimize
  if (getCookie(`popup-minimized-${popupId}`) === 'true') {
    minimizePopup(popupId, false);
  } else {
    // Only show the full popup if it's not minimized
    popup.removeClass('sp-popup--hidden');
  }
  return true;
}

function setupPopup(popup) {
  const popupId = popup.data('popup-id');
  // CollapseOnMobile: if set, always minimize on mobile
  const collapseOnMobile = popup.data('collapse-on-mobile') === 1 || popup.data('collapse-on-mobile') === '1';
  const isMobile = window.matchMedia('(max-width: 600px)').matches;
  if (collapseOnMobile && isMobile) {
    minimizePopup(popupId, false);
    return;
  }
  // Check the minimized cookie and conditionally minimize
  if (getCookie(`popup-minimized-${popupId}`) === 'true') {
    minimizePopup(popupId, false);
  } else {
    // If not minimized, show the full popup
    popup.removeClass('sp-popup--hidden');
  }
}

// Main Load

$(() => {
  console.log('[silverstripe-popups] loaded');

  // Call setupPopup AFTER the DOM is ready
  // We still want to initialize minimized state and similar for all popups immediately
  $('.sp-popup').each(function () {
    const popup = $(this);
    const popupId = popup.data('popup-id');

    // If popup is minimized via cookie, reflect that immediately
    if (getCookie(`popup-minimized-${popupId}`) === 'true') {
      minimizePopup(popupId, false);
    }
  });

  // Start the showNextPopup flow after a short initial delay (gives the page time to settle)
  setTimeout(showNextPopup, 500);
});

// Click Handlers
$('.c-popup').on('click', function () {
  const popupId = $(this).data('popup-id');
  closePopup(popupId);
});

$('.c-popup__inner').on('click', (e) => {
  e.stopPropagation();
});

// Handle clicks on the close button (in both minimized and full versions)
$('.sp-popup__close').on('click', function () {
  const popupId = $(this).data('popup-id');
  
  // Check if this is a close button that should minimize instead
  if ($(this).hasClass('sp-popup__close--minimize')) {
    minimizePopup(popupId, true);
  } else {
    closePopup(popupId);
  }
});

// Update the minimized click handler to handle clicking anywhere on the minimized bar
$('.sp-popup__minimized').on('click', function (e) {
  const popupId = $(this).data('popup-id');
  
  // If clicking on the close button within minimized state, close the popup
  if ($(e.target).hasClass('sp-popup__close')) {
    closePopup(popupId);
  } else {
    // Otherwise, clicking anywhere else on the minimized bar should reopen
    showFullPopup(popupId);
  }
});

// Add click handler for strip/edge popup content areas to minimize when minimize is available
$(document).on('click', '.sp-popup--mode-strip .sp-popup__content, .sp-popup--mode-edge .sp-popup__content', function (e) {
  // Only trigger if clicking on the content wrapper itself, not inner elements
  if (e.target === this) {
    const popupId = $(this).closest('.sp-popup').data('popup-id');
    const popup = $(`.sp-popup[data-popup-id='${popupId}']`);
    const hasMinimize = popup.find('.sp-popup__close--minimize').length > 0;

    if (hasMinimize) {
      minimizePopup(popupId, true);
    }
  }
});

$('.sp-popup__backdrop').on('click', function () {
  const popupId = $(this).data('popup-id');
  const popup = $(`.sp-popup[data-popup-id='${popupId}']`);

  // For modal popups: check if minimize is available, otherwise close
  const mode = popup.data('mode');
  const hasMinimize = popup.find('.sp-popup__close--minimize').length > 0;

  if (hasMinimize && mode === 'modal') {
    minimizePopup(popupId, true); // Modal with minimize available
  } else {
    closePopup(popupId); // No minimize available, close the popup
  }
});
