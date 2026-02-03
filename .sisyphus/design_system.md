# School Library App - Design System (2025)

> **Aesthetic Direction**: "Modern Academic". Clean, trustworthy, and sophisticated.
> Focusing on legibility, subtle depth, and a refined color palette that feels professional yet welcoming.

## 1. Design Tokens

### Color Palette
We utilize a refined slice of the Tailwind CSS color spectrum.

**Primary (Trust & Action)**:
-   **Brand**: `Indigo-600` (#4F46E5) - Main buttons, active states, key links.
-   **Brand Light**: `Indigo-50` (#EEF2FF) - Backgrounds for active items, badges.
-   **Brand Dark**: `Indigo-800` (#3730A3) - Hover states, dark mode elements.

**Secondary (Accent & Freshness)**:
-   **Accent**: `Teal-500` (#14B8A6) - Success states, highlight indicators.
-   **Accent Light**: `Teal-50` (#F0FDFA).

**Neutrals (Structure & Content)**:
-   **Canvas**: `Slate-50` (#F8FAFC) - Main app background. Soft, not stark white.
-   **Surface**: `White` (#FFFFFF) - Cards, sidebar, modal backgrounds.
-   **Line**: `Slate-200` (#E2E8F0) - Subtle borders, dividers.
-   **Text Main**: `Slate-900` (#0F172A) - Headings, primary data.
-   **Text Muted**: `Slate-500` (#64748B) - Body text, secondary labels.

**Status**:
-   **Success**: `Emerald-600` (Text) / `Emerald-50` (Bg)
-   **Warning**: `Amber-600` (Text) / `Amber-50` (Bg)
-   **Error**: `Rose-600` (Text) / `Rose-50` (Bg)

### Typography
**Font Family**: `Figtree` (Geometric Sans-Serif).
-   **Headings**: Bold (700) or Semibold (600). Tight letter-spacing (`-0.025em`) for large text.
-   **Body**: Regular (400) or Medium (500). Relaxed line-height (`leading-relaxed`).

### Depth & Shape
-   **Radius**: `rounded-xl` (12px) for cards, `rounded-lg` (8px) for inputs, `rounded-full` for buttons/badges.
-   **Shadows**:
    -   `shadow-sm`: Default state for cards/inputs.
    -   `shadow-md`: Hover state for cards.
    -   `shadow-lg`: Modals and dropdowns.
-   **Blur**: `backdrop-blur-sm` for overlays and sticky headers.

---

## 2. Configuration Recommendations

### `tailwind.config.js` Updates
Ensure your configuration extends the default theme to support these preferences.

```javascript
import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // If we need custom aliases
                primary: defaultTheme.colors.indigo,
                danger: defaultTheme.colors.rose,
            },
            boxShadow: {
                'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02)',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
```

---

## 3. Component Library (Blade Snippets)

### A. Buttons
**Primary Action**
```blade
<button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-full shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 active:scale-95">
    {{ $slot }}
</button>
```

**Secondary / Ghost**
```blade
<button type="button" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-full shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
    {{ $slot }}
</button>
```

### B. Cards
**Standard Widget**
```blade
<div class="relative overflow-hidden bg-white border border-slate-200/60 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-300">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
        <div class="mt-4">
            {{ $slot }}
        </div>
    </div>
</div>
```

### C. Form Inputs
**Text Input with Icon**
```blade
<div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <!-- Icon Path -->
        </svg>
    </div>
    <input type="text"
           class="block w-full py-2.5 pl-10 pr-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors duration-200"
           placeholder="Search...">
</div>
```

### D. Badges / Status Pills
```blade
@props(['status'])

@php
$classes = match($status) {
    'success' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
    'warning' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
    'error' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
    default => 'bg-slate-50 text-slate-700 ring-slate-600/20',
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $classes }}">
    {{ $slot }}
</span>
```

### E. Navigation (Sidebar Item)
```blade
<a href="#" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->is('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
    <svg class="flex-shrink-0 w-5 h-5 mr-3 {{ request()->is('dashboard') ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }}" ...>
        <!-- Icon -->
    </svg>
    Dashboard
</a>
```

---

## 4. General Styling Guidelines

1.  **Whitespace is King**: Don't crowd elements. Use `gap-6` or `gap-8` for grids. Use `p-6` or `p-8` for card padding.
2.  **Subtle Borders**: Avoid thick black borders. Use `border-slate-200` or `divide-slate-200`.
3.  **Micro-Interactions**: Add `transition-all duration-200` to interactive elements. Scale buttons slightly on click (`active:scale-95`).
4.  **Data Density**: For tables, keep headers uppercase, small, and bold (`text-xs font-bold uppercase tracking-wider text-slate-500`).
5.  **Hierarchy**:
    -   H1: `text-2xl font-bold text-slate-900`
    -   H2: `text-lg font-semibold text-slate-900`
    -   Body: `text-sm text-slate-600 leading-relaxed`

