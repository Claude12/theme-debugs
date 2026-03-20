# Wedding Planner Functionality Plugin

A standalone WordPress plugin that lifts all wedding planner / book-now functionality out of the original theme and into a portable plugin. Works with Elementor — no Gutenberg required.

## Requirements

| Plugin | Notes |
|---|---|
| **Advanced Custom Fields PRO** | All ACF fields + option values should already be imported |
| **Gravity Forms** | The booking form should already be imported |

---

## Quick-Start Checklist

### 1. Install & Activate
Upload the `wedding-planner-plugin/` folder to `wp-content/plugins/` and activate via **Plugins → Installed Plugins**.

### 2. Copy the planner JavaScript
Copy the `planner.js` file from your **old theme's** `assets/` folder (or equivalent) into:
```
wp-content/plugins/wedding-planner-plugin/assets/planner.js
```
The placeholder file already contains the "Email these estimates" AJAX handler — merge your existing JS into it or replace the file entirely.

### 3. Create two Elementor pages

#### Page 1 — Book Now (the booking form page)
1. Create a new page, e.g. `/book-now/`
2. Edit with Elementor
3. Add a **Shortcode** widget and enter:
```
[wedding_booking_form]
```

#### Page 2 — Wedding Estimate (the results page)
1. Create a new page, e.g. `/wedding-estimate/`
2. Edit with Elementor
3. Add a **Shortcode** widget and enter:
```
[wedding_estimated_total]
```

### 4. Update the Gravity Forms confirmation redirect
In **Gravity Forms → Forms → your form → Settings → Confirmations**:
- Set type to **Redirect**
- Set URL to:
```
/wedding-estimate/?entry={entry_id}
```
Replace `/wedding-estimate/` with whatever slug you chose in step 3.

### 5. Check Gravity Forms form ID
The plugin defaults to form ID **1**. If your imported form was assigned a different ID:
- Go to **Forms** in the WP admin — the ID is shown in the URL when editing a form
- Either: update the ID on the Book Now page via the filter hook:
  ```php
  add_filter( 'wpl_gravity_form_id', function() { return 2; } ); // change 2 to your form ID
  ```
- Or: add that line to your active theme's `functions.php`

### 6. Verify ACF Options values
Go to **Planner Settings** in the WP admin sidebar (added by this plugin) and confirm your imported ACF values are visible. All pricing, menu names, and copy are pulled from the `options` page.

### 7. Add .visuallyhidden CSS
The booking form template outputs a hidden `<div class="visuallyhidden">` block that the planner JS reads for labels and prices. Add this to your theme's / Elementor's custom CSS:

```css
.visuallyhidden {
    display: none !important;
}
```

---

## Shortcodes

| Shortcode | Description |
|---|---|
| `[wedding_booking_form]` | Renders the Gravity Form + hidden ACF data block + popup menu content |
| `[wedding_estimated_total]` | Renders the full price breakdown (reads `?entry=ID` from the URL) |

---

## Email Estimate Functionality

When a visitor clicks **"Email these estimates to me"** on the estimate page:
1. The JS collects the Gravity Forms entry ID from the hidden `#entry_id` span
2. Prompts the visitor for their email address
3. POSTs to WordPress AJAX (`wpl_email_estimate` action)
4. The plugin sends a `wp_mail()` email with a link to the estimate page

The email uses WordPress's built-in mail function — configure SMTP as normal (e.g. via WP Mail SMTP plugin) if emails are not being delivered.

---

## Bugs Fixed from Original Theme Code

| Location | Original bug | Fix applied |
|---|---|---|
| `page-estimated-total.php` line 89 | `$values[$choice][value]` — bare constant `value` | Changed to `$values[$choice]['value']` |
| `page-estimated-total.php` line 678 | `== Extend` — bare constant | Changed to `== 'Extend'` |
| Multiple `get_field(...)` calls | `'option'` (missing s) | Changed to `'options'` |
| `page-estimated-total.php` lines 77–78 | `$price_row_id` — undefined variable | Removed; index is now just `$i` |
| Various | Missing null checks on repeater array access | Added `isset()` guards throughout |
