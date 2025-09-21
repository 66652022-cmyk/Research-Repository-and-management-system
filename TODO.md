# TODO - Convert Landing Page and Components from Tailwind CSS to Vanilla CSS

## Overview
Convert the landing page (index.php) and its components (navbar.php, footer.php) from Tailwind CSS utility classes to vanilla CSS. Organize CSS files in an OOP-like folder structure under css/index, css/components/navbar, and css/components/footer.

## Steps

### 1. Landing Page (index.php)
- Create css/index/style.css
- Extract Tailwind utility classes used in index.php and convert to vanilla CSS classes in style.css
- Move inline styles from index.php <style> tag to style.css
- Replace Tailwind classes in index.php with new vanilla CSS classes
- Update index.php to link css/index/style.css instead of src/output.css

### 2. Navbar Component (components/navbar.php)
- Create css/components/navbar/style.css
- Extract Tailwind utility classes used in navbar.php and convert to vanilla CSS classes in style.css
- Replace Tailwind classes in navbar.php with new vanilla CSS classes
- Update navbar.php to link css/components/navbar/style.css

### 3. Footer Component (components/footer.php)
- Create css/components/footer/style.css
- Extract Tailwind utility classes used in footer.php and convert to vanilla CSS classes in style.css
- Replace Tailwind classes in footer.php with new vanilla CSS classes
- Update footer.php to link css/components/footer/style.css

### 4. Testing
- Test landing page rendering and functionality after conversion
- Verify navbar and footer display correctly with new styles

## Notes
- Do not modify any backend PHP logic or functionality
- Only update frontend markup classes and CSS files
- Follow OOP-like folder structure for CSS organization
