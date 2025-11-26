# CnEduc CSS Class Reference Guide

## Button Classes

### Primary Button
```html
<button class="btn">Default Button</button>
<button class="btn btn-primary">Primary Button</button>
<a class="btn btn-primary" href="#">Link Button</a>
```

### Button Variants
- `.btn-primary` - Blue button (primary action)
- `.btn-secondary` - Gray button (secondary action)
- `.btn-success` - Green button (positive/confirm)
- `.btn-danger` - Red button (delete/cancel)
- `.btn-warning` - Orange button (caution)
- `.btn-info` - Bright blue button (information)

### Button Sizes
- `.btn-lg` - Large button (14px padding, 16px font)
- `.btn` - Normal button (12px padding, 15px font)
- `.btn-sm` - Small button (8px padding, 14px font)

### Button Styles
- `.btn-outline-primary` - Outlined primary button
- `.btn-outline-secondary` - Outlined secondary button
- `.btn-outline-danger` - Outlined danger button
- `.btn-block` - Full-width button

## Form Classes

### Form Structure
```html
<div class="form-group required">
  <label for="username">Username</label>
  <input type="text" id="username" name="username" required>
  <small>Enter your unique username</small>
</div>
```

### Form States
- `.form-error` - Red error message text
- `.form-success` - Green success message text
- `.form-group.required` - Marks field with red asterisk
- `input:focus` - Blue border, light blue background, glow shadow

### Form Elements
- `input[type=text]`
- `input[type=email]`
- `input[type=password]`
- `input[type=number]`
- `input[type=date]`
- `textarea`
- `select`
- `input[type=checkbox]`
- `input[type=radio]`

## Card Classes

### Feature Cards
```html
<div class="feature-card">
  <h3>Feature Title</h3>
  <p>Feature description text</p>
</div>
```

### Class/Level Cards
```html
<a class="class-card" href="#">
  <div class="class-card-title">P1</div>
  <div class="class-card-subtitle">Primary 1</div>
</a>
```

### General Card
```html
<div class="card">
  <h3>Card Title</h3>
  <p>Card content</p>
</div>
```

## Layout Classes

### Grids
- `.grid-2` - 2-column responsive grid (320px min)
- `.grid-3` - 3-column responsive grid (280px min)
- `.grid-4` - 4-column responsive grid (220px min)
- `.grid` - Flex row with gap

### Spacing
- `.sidebar` - 320px fixed sidebar
- `.main` - Flexible main content area

## Table Classes

```html
<table>
  <thead>
    <tr>
      <th>Header 1</th>
      <th>Header 2</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Data 1</td>
      <td>Data 2</td>
    </tr>
  </tbody>
</table>
```

Features:
- Blue gradient header background
- White text in header
- Alternating row colors
- Hover highlight effect
- Box shadow and border-radius

## Badge Classes

### Badge Types
```html
<span class="primary-badge">Primary Class</span>
<span class="secondary-badge">Secondary Class</span>
<span class="university-badge">University</span>
<span class="badge-danger">Danger</span>
<span class="badge-warning">Warning</span>
```

Features:
- Gradient backgrounds
- Rounded pill shape
- Color-coded semantic meaning
- 12px font weight 600

## Alert Classes

### Alert Types
```html
<div class="alert alert-success">Success message here</div>
<div class="alert alert-danger">Error message here</div>
<div class="alert alert-warning">Warning message here</div>
<div class="alert alert-info">Info message here</div>
```

### Alert with Close Button
```html
<div class="alert alert-success">
  <span>Success message</span>
  <button class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
</div>
```

Features:
- Gradient backgrounds
- 5px left border accent
- Slide-down animation
- Close button support
- 14px font size

## Hero Section

```html
<section class="hero">
  <h1>Welcome to CnEduc</h1>
  <p>Learn at your own pace with professional curriculum</p>
  <a class="btn btn-primary" href="#">Get Started</a>
</section>
```

Features:
- Gradient background
- 60px vertical padding
- Animated background circles
- Large typography
- Responsive sizing

## Breadcrumb Navigation

```html
<nav class="breadcrumb">
  <a href="/">Home</a>
  <span class="breadcrumb-separator">/</span>
  <a href="/classes">Classes</a>
  <span class="breadcrumb-separator">/</span>
  <span>Primary 1</span>
</nav>
```

Features:
- Left border accent
- Light blue background
- Primary color links
- Hover underline

## Stats Section

```html
<div class="stats-grid">
  <div class="stat-item">
    <div class="stat-number">256</div>
    <div class="stat-label">Active Students</div>
  </div>
</div>
```

Features:
- Auto-fit responsive grid
- Card-like appearance
- Hover elevation effect
- Large number display (28px, weight 800)

## Header Navigation

```html
<header class="site-header">
  <div class="container">
    <h1 class="site-title">CnEduc</h1>
    <nav class="site-nav">
      <a href="/">Home</a>
      <a href="/explore">Explore</a>
      <a href="/about">About</a>
    </nav>
  </div>
</header>
```

Features:
- Gradient background
- Sticky navigation option
- Animated underline on hover
- Responsive flex layout

## Search Form

```html
<form class="search-form">
  <input type="text" placeholder="Search topics or units...">
  <input type="submit" value="Search">
</form>
```

Features:
- Flex layout
- Blue focus state
- Professional styling
- Mobile responsive

## Container & Page Layout

```html
<div class="container">
  <main class="main">
    <!-- Main content -->
  </main>
  <aside class="sidebar">
    <!-- Sidebar content -->
  </aside>
</div>
```

Features:
- Max-width: 1400px
- 32px padding on desktop
- 16px padding on mobile
- Responsive flex layout

## Section Divider

```html
<div class="section-divider">
  <h2>New Section</h2>
</div>
```

Features:
- Top and bottom borders
- Light gradient background
- Centered heading

## Footer

```html
<footer class="footer">
  <p>&copy; 2024 CnEduc. All rights reserved.</p>
  <p><a href="/privacy">Privacy Policy</a> | <a href="/terms">Terms of Service</a></p>
</footer>
```

Features:
- Light gradient background
- 32px vertical padding
- Centered text
- Professional appearance

## CSS Variables Reference

```css
:root {
  --primary: #0066cc;              /* Main brand blue */
  --primary-dark: #0052a3;         /* Darker blue */
  --primary-light: #f0f5ff;        /* Light blue background */
  --shadow-sm: 0 1px 2px ...;      /* Subtle shadow */
  --shadow-md: 0 4px 12px ...;     /* Medium shadow */
  --shadow-lg: 0 10px 30px ...;    /* Large shadow */
  --border-color: #e5e7eb;         /* Default border color */
}
```

## Responsive Breakpoints

### Large Desktop (1200px+)
- Full container width (1400px)
- All columns visible
- Maximum information density

### Desktop (1025px - 1199px)
- Container slightly narrower
- Grid optimization
- All features visible

### Tablet (769px - 1024px)
- Sidebar below content
- 2-column grids collapse
- Adjusted font sizes
- Touch-friendly spacing

### Mobile (601px - 768px)
- Single column layout
- Centered content
- Larger touch targets
- Reduced padding

### Small Mobile (≤ 600px)
- Minimal padding
- Single column grid
- Smaller font sizes
- Full-width buttons

## Tips for Using Classes

1. **Buttons**: Always use `.btn` base class + variant (e.g., `.btn btn-primary`)
2. **Forms**: Wrap inputs in `.form-group` for consistent spacing
3. **Cards**: Use `.card` or `.feature-card` for consistent styling
4. **Grids**: Choose appropriate grid (grid-2, grid-3, grid-4) based on content
5. **Alerts**: Use semantic types (.alert-success, .alert-danger, etc.)
6. **Badges**: Use type classes for color-coding
7. **Responsive**: Built-in media queries handle all screen sizes
8. **Accessibility**: All components have proper focus states and color contrast

## Color Palette Quick Reference

| Class | Color | Hex | Use Case |
|-------|-------|-----|----------|
| Primary | Blue | #0066cc | Main brand, CTA buttons |
| Secondary | Gray | #6b7280 | Secondary actions |
| Success | Green | #10b981 | Confirmations, success messages |
| Danger | Red | #ef4444 | Deletions, errors, danger |
| Warning | Orange | #f59e0b | Cautions, warnings |
| Info | Bright Blue | #3b82f6 | Information, hints |

---

**Document Version**: 1.0
**Last Updated**: Current session
**Compatible With**: style.css v2.0 (Professional System Redesign)
