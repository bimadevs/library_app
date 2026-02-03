# Learnings

## Book Label Preview
- The print layout uses a fixed 3-column grid (`repeat(3, 6.4cm)`).
- The screen preview initially used `repeat(auto-fill, ...)` which could lead to inconsistent layout representation on different screen sizes.
- **Decision**: Updated the screen preview CSS to match the print CSS (`repeat(3, 6.4cm)`) to ensure the user sees exactly what will be printed (3 columns) regardless of screen width (using horizontal scroll if necessary).
- **Verification**: Verified the Livewire component logic (`BookLabelGenerator`) correctly handles book selection, barcode generation, and state transition to preview mode.
