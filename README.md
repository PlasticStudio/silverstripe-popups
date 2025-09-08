# Silverstripe Popups

A flexible popup module for SilverStripe CMS with three display modes: modal, strip, and edge. Features scheduling, page targeting, minimize functionality, and mobile responsiveness.

## Installation

Install via Composer:

```bash
composer require plasticstudio/silverstripe-popups
```

Add the template include to your `Page.ss` file before the closing `</body>` tag:

```html
<% include PopupProvider %>
```

Run `dev/build` to create the database tables and visit `/admin/popups` to start creating popups.

## Features

- **Display modes**: Modal (center), Strip (bottom bar), Edge (right side)
- **Scheduling**: Show popups between specific dates/times
- **Page targeting**: Display on all pages, specific pages, or page types
- **Minimize functionality**: Allow users to minimize strip/edge popups
- **Mobile responsive**: Optional mobile collapse for better UX
- **Cookie management**: Respects user dismissal preferences
- **Content flexibility**: Support for images, HTML content, raw embeds, and multiple links

## Customization

### CSS Variables

Override default styling by defining CSS custom properties in your theme:

```css
:root {
  /* Content styling */
  --popup-content-background-colour: #ffffff;
  --popup-minimised-background-colour: #ffffff; 
  --popup-max-width: 640px;
  
  /* Spacing */
  --popup-padding: 16px;
  --popup-margin: 0;
  --popup-border-radius: 8px;
  
  /* Shadows */
  --popup-modal-shadow: 0 2px 32px rgba(0,0,0,0.16);
  --popup-strip-shadow: 0 -2px 16px rgba(0,0,0,0.08);
  --popup-edge-shadow: -2px 0 16px rgba(0,0,0,0.08);
  
  /* Backdrop & controls */
  --popup-backdrop-color: rgba(0, 0, 0, 0.5);
  --popup-close-color: #373737;
  --popup-z-index: 1000;

  /* Buttons */
  --popup-button-text-color: #ffffff;
  --popup-button-color: #007bff;
  --popup-button-hover-text-color: #ffffff;
  --popup-button-hover-color: #0056b3;
}
```

### Configuration

Configure cookie expiry time in your project's YAML config:

```yaml
PlasticStudio\SilverstripePopups\DataObjects\Popup:
  cookie_expiry_time: 2628000000 # 30 days in milliseconds
```

### Custom CSS Classes

Add custom CSS classes to popups, links, and buttons through the CMS interface for tracking or additional styling.

### Template Overrides

Copy `templates/Includes/PopupProvider.ss` to your theme to customize the popup HTML structure.