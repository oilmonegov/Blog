# UI Design Guide

This document outlines the design system, color palette, and UI components used in the application. The design focuses on a **bold, colorful, and modern** aesthetic with full dark mode support.

## 1. Design Philosophy

- **Vibrant & Energetic**: Uses strong gradients and bold colors to create visual interest.
- **Clean & Readable**: Maintains high contrast and generous spacing for content readability.
- **Interactive**: Extensive use of hover effects, transitions, and subtle animations.
- **Adaptive**: Fully responsive layouts and a manual dark mode toggle.

## 2. Color Palette

The design relies heavily on a gradient-based color system using Tailwind CSS colors.

### Primary Gradients
Used for buttons, headers, and accents.

- **Main Gradient**: `from-purple-600` → `to-pink-500`
  - *Light Mode*: `#9333ea` → `#ec4899`
  - *Hover*: `#7e22ce` → `#db2777`
- **Hero Gradient**: `from-purple-600` → `via-pink-500` → `to-purple-600`
- **Secondary Gradient (Blue/Indigo)**: `from-indigo-500` → `to-purple-500`
- **Accent Gradient (Pink/Rose)**: `from-pink-500` → `to-rose-500`

### Backgrounds
- **Light Mode**: 
  - Page: `bg-gray-50`
  - Cards/Surfaces: `bg-white`
- **Dark Mode**:
  - Page: `bg-gray-900`
  - Cards/Surfaces: `bg-gray-800`

### Text Colors
- **Headings**: `text-gray-900` (Light) / `text-gray-100` (Dark)
- **Body**: `text-gray-700` (Light) / `text-gray-300` (Dark)
- **Muted**: `text-gray-500` (Light/Dark)
- **Gradient Text**: Applied to headings for emphasis using `bg-clip-text`.

## 3. Typography

- **Font Family**: `Figtree` (Sans-serif)
- **Weights**: 
  - Regular (400): Body text
  - Medium (500): Buttons, Navigation
  - Bold (700): Headings
- **Prose**: Enhanced using `@tailwindcss/typography` with custom gradient headings.

## 4. UI Components

Custom utility classes are defined in `resources/css/app.css`.

### Buttons (`.btn-gradient`)
A prominent call-to-action button style.
- **Normal**: Gradient background (Purple → Pink), White text.
- **Hover**: Slightly darker gradient, Shadow (`shadow-lg`), Scale up (`scale-105`).
- **Transition**: Smooth `0.3s` transition.

### Cards (`.card-gradient`)
Interactive cards used for post listings and comments.
- **Normal**: White/Gray-800 background, subtle border.
- **Hover**: 
  - Border becomes a gradient (Purple → Pink) using `background-origin: border-box`.
  - Elevation increases (`shadow-xl`).
  - Content remains readable.

### Badges (`.badge-gradient`)
Used for categories and tags.
- **Style**: Pill-shaped, small text, gradient background.
- **Hover**: Scale up (`scale-110`).

### Avatars (`.avatar-gradient`)
User profile pictures or initials.
- **Style**: Circular with a gradient border ring.
- **Implementation**: A gradient container holding a slightly smaller solid circle.

### Prose (`.prose-enhanced`)
Optimized content display for blog posts.
- **Features**: 
  - Max-width removed (`max-w-none`).
  - Headings (h1-h3) use gradient text clipping.
  - Dark mode support (`dark:prose-invert`).

## 5. Animations

Custom animations configured in `tailwind.config.js`.

- **Fade In** (`animate-fade-in`):
  - Duration: 0.5s
  - Effect: Opacity 0 → 1
- **Slide Up** (`animate-slide-up`):
  - Duration: 0.5s
  - Effect: Translate Y 20px → 0, Opacity 0 → 1

## 6. Dark Mode System

- **Strategy**: Class-based (`darkMode: 'class'`).
- **Toggle**: 
  - JavaScript helper in `resources/js/app.js`.
  - Persists preference in `localStorage`.
  - Checks system preference (`prefers-color-scheme`) on first load.
- **Implementation**: 
  - Parent `html` tag receives `.dark` class.
  - Components use `dark:` variant modifier (e.g., `dark:bg-gray-800`).

## 7. Usage Examples

### Creating a Gradient Button
```html
<button class="btn-gradient">
    Click Me
</button>
```

### Creating a Card
```html
<article class="card-gradient p-6">
    <h2>Card Title</h2>
    <p>Card content...</p>
</article>
```

### Gradient Text Header
```html
<h1 class="bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent font-bold text-4xl">
    My Title
</h1>
```

