# Silverstripe Popups

This module provides a simple way to create and manage popups on your Silverstripe website.

## Installation

Add the module to your project using Composer:

```bash
composer require plasticstudio/silverstripe-popups
```

Add the template to your `Page.ss` file:

```html
<% include PopupProvider %>
```

## Tracking

Track popup views using element visibility triggers- add custom class via ui

button interactions using standard click listeners- add custom class via ui

close via css selectors

