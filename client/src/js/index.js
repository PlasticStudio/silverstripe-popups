function showNextPopup() {
  var popups = $(".wtk-popup");
  for (var i = 0; i < popups.length; i++) {
    var popup = $(popups[i]);
    var shown = shouldShowPopup(popup);
    if (shown) {
      break;
    }
  }
}

// Function to close popup
function closePopup(popupId) {
  var popup = $(`.wtk-popup[data-popup-id='${popupId}']`);
  var showAgainAfter = parseInt(popup.data("show-again-after"), 10);
  var now = new Date();
  var expiryDate = showAgainAfter
    ? new Date(now.getTime() + showAgainAfter * 24 * 60 * 60 * 1000)
    : new Date(now.getTime() + 10 * 365 * 24 * 60 * 60 * 1000); // Default 10 years
  var expiryDateString = expiryDate.toUTCString();
  document.cookie = `popup-hidden-${popupId}=true; expires=${expiryDateString}; path=/`;
  document.cookie = `popup-last-shown-${popupId}=${now.toUTCString()}; expires=${expiryDateString}; path=/`;
  document.cookie = `popup-minimized-${popupId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
  popup.addClass("wtk-popup--hidden");
  setTimeout(showNextPopup, 3000);
}

// Function to minimize popup
function minimizePopup(popupId, animate) {
  var popup = $(`.wtk-popup[data-popup-id='${popupId}']`);
  if (animate) {
    popup.addClass("wtk-popup--animating");
    setTimeout(function () {
      popup.removeClass("wtk-popup--animating");
    }, 300);
  }

  popup.addClass("wtk-popup--minimized-state");

  // Hide the full popup content
  popup.find(".wtk-popup__full").addClass("wtk-popup--hidden");

  // Show the minimized version
  popup.find(".wtk-popup__minimized").removeClass("wtk-popup--hidden");

  // Ensure the MAIN popup container is visible
  popup.removeClass("wtk-popup--hidden");

  document.cookie = `popup-minimized-${popupId}=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/`;
}

// Function to show the full popup
function showFullPopup(popupId) {
  var popup = $(`.wtk-popup[data-popup-id='${popupId}']`);

  popup.removeClass("wtk-popup--minimized-state");

  // Hide the minimized version
  popup.find(".wtk-popup__minimized").addClass("wtk-popup--hidden");

  // Show the full content
  popup.find(".wtk-popup__full").removeClass("wtk-popup--hidden");

  // Clear the minimized cookie
  document.cookie = `popup-minimized-${popupId}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
}

// Function to get cookie value
function getCookie(name) {
  var cookieArr = document.cookie.split("; ");
  for (var i = 0; i < cookieArr.length; i++) {
    var cookiePair = cookieArr[i].split("=");
    if (name === cookiePair[0].trim()) {
      return decodeURIComponent(cookiePair[1]);
    }
  }
  return null;
}

function shouldShowPopup(popup) {
  var now = new Date();
  var popupId = popup.data("popup-id");
  var popupHidden = getCookie(`popup-hidden-${popupId}`);
  var showAgainAfter = parseInt(popup.data("show-again-after"), 10);
  if (popupHidden) {
    return false;
  }
  if (!isNaN(showAgainAfter) && showAgainAfter > 0) {
    var lastShown = new Date(getCookie(`popup-last-shown-${popupId}`));
    if ((now - lastShown) / (1000 * 3600 * 24) < showAgainAfter) {
      return false;
    }
  }

  // Check the minimized cookie and conditionally minimize
  if (getCookie(`popup-minimized-${popupId}`) === "true") {
    minimizePopup(popupId, false);
  } else {
    // Only show the full popup if it's not minimized
    popup.removeClass("wtk-popup--hidden");
  }
  return true;
}

function setupPopup(popup) {
  var popupId = popup.data("popup-id");
  // Check the minimized cookie and conditionally minimize
  if (getCookie(`popup-minimized-${popupId}`) === "true") {
    minimizePopup(popupId, false);
  } else {
    // If not minimized, show the full popup
    popup.removeClass("wtk-popup--hidden");
  }
}

// Main Load

$(function () {
  console.log("[popup.js] loaded");

  // Call setupPopup AFTER the DOM is ready
  $(".wtk-popup").each(function () {
    var popup = $(this);
    setupPopup(popup);
  });

  setTimeout(showNextPopup, 3000); // Delay showing the next popup
});

// Click Handlers
$(".c-popup").on("click", function () {
  var popupId = $(this).data("popup-id");
  closePopup(popupId);
});

$(".c-popup__inner").on("click", function (e) {
  e.stopPropagation();
});

// Handle clicks on the minimize button
$(".wtk-popup__minimize").on("click", function () {
  var popupId = $(this).data("popup-id");
  minimizePopup(popupId, true);
});

$(".wtk-popup__minimized").on("click", function () {
  var popupId = $(this).data("popup-id");
  showFullPopup(popupId);
});

// Handle clicks on the close button (in both minimized and full versions)
$(".wtk-popup__close").on("click", function () {
  var popupId = $(this).data("popup-id");
  closePopup(popupId);
});

$(".wtk-popup__backdrop").on("click", function () {
  var popupId = $(this).data("popup-id");
  var popup = $(`.wtk-popup[data-popup-id='${popupId}']`);

  // Check if the popup has a minimize button (minimization enabled)
  if (popup.find(".wtk-popup__minimize").length > 0) {
    minimizePopup(popupId, true); // Minimize if the button exists
  } else {
    closePopup(popupId); // Otherwise, close the popup
  }
});
