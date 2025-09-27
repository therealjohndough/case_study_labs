# Custom Client Theme

A modern, cyberpunk-inspired WordPress theme built specifically for easy content management via Advanced Custom Fields (ACF) and frontend editing capabilities.

## Features

### 🎨 Design System
- **Cyberpunk/Retro Aesthetic**: Dark theme with neon orange/amber accents (#F15B27, #FF8C42)
- **Custom Typography**: Orbitron and Press Start 2P fonts for a futuristic look
- **Visual Effects**: VHS glitch effects, pixel patterns, scanlines, and animations
- **Fully Responsive**: Mobile-first design that works on all devices
- **Performance Optimized**: Fast loading with optimized CSS and JavaScript

### 🔧 Content Management
- **ACF Integration**: Complete integration with Advanced Custom Fields for easy content editing
- **Frontend Editing**: Logged-in users can edit content directly from the frontend
- **Customizable Homepage**: Drag-and-drop sections via ACF repeater fields
- **Site Settings**: Centralized theme options page for logos, contact info, social links
- **User-Friendly**: Non-technical users can easily update content

### 🔌 Plugin Integrations
- **Brevo Email Marketing**: Ready for integration (placeholder included)
- **Agile Store Locator**: Ready for integration (placeholder included)
- **ACF Pro**: Full support for advanced field types
- **Frontend Admin**: Compatible with frontend editing plugins

## Installation

1. Upload the theme files to `/wp-content/themes/custom-client-theme/`
2. Activate the theme in WordPress Admin → Appearance → Themes
3. Ensure ACF Pro is installed and activated
4. Configure theme settings at WordPress Admin → Theme Settings

## Required Plugins

### Essential
- **Advanced Custom Fields Pro** - For content management
- **ACF Extended Pro** - Already installed, provides additional functionality

### Recommended (for full functionality)
- **Brevo** - Email marketing integration
- **Agile Store Locator** - Store locator functionality

## Content Management Guide

### For Clients (Non-Technical Users)

#### Editing Homepage Content
1. Go to your homepage when logged in as an admin
2. Click the "Edit Homepage Content" button
3. Update the following sections:
   - **Hero Section**: Main title, subtitle, background image, call-to-action button
   - **Content Sections**: Add/remove/edit content blocks with images and text
4. Click "Update Homepage" to save changes

#### Managing Site-Wide Settings
1. Go to WordPress Admin → Theme Settings
2. Configure:
   - **Site Logo**: Upload your custom logo
   - **Site Tagline**: Update your site's tagline
   - **Contact Information**: Email and phone number
   - **Social Media Links**: Add your social media profiles

#### Editing Pages and Posts
- Use the standard WordPress editor or click "Edit Page/Post" on the frontend
- For pages with ACF fields, use the frontend editing forms when available

### For Developers

#### File Structure
```
custom-client-theme/
├── assets/
│   └── js/
│       └── main.js          # Theme JavaScript
├── comments.php             # Comments template
├── footer.php              # Footer template
├── functions.php           # Theme functions and ACF setup
├── header.php              # Header template
├── index.php               # Main blog template
├── page-home.php           # Custom homepage template
├── page.php                # Page template
├── single.php              # Single post template
├── style.css               # Main stylesheet with design system
└── README.md               # This file
```

#### ACF Field Groups

**Site Settings** (`group_site_settings`)
- Site Logo (Image)
- Site Tagline (Text)
- Contact Email (Email)
- Contact Phone (Text)
- Social Links (Repeater)

**Homepage Content** (`group_homepage_content`)
- Hero Title (Text)
- Hero Subtitle (Textarea)
- Hero Background Image (Image)
- Hero CTA Button Text & URL
- Content Sections (Repeater with Title, Content, Image)

#### Customization

**Colors**: Modify CSS custom properties in `style.css`
```css
:root {
  --primary: #F15B27;     /* Main accent color */
  --accent: #FF8C42;      /* Secondary accent */
  --background: #0a0a0a;  /* Main background */
  --foreground: #ffffff;  /* Text color */
}
```

**Typography**: Update font selections in `style.css`
```css
body {
  font-family: 'Orbitron', sans-serif;
}
.font-pixel {
  font-family: 'Press Start 2P', monospace;
}
```

## Plugin Integration

### Brevo Email Marketing
When the Brevo plugin is installed:
1. The theme will automatically detect it
2. Integration placeholders in templates will be replaced with actual forms
3. Custom styling will be applied to match the theme

### Agile Store Locator
When the Agile Store Locator plugin is installed:
1. The theme will automatically detect it
2. Store locator will appear in designated areas
3. Custom map styling will be applied to match the cyberpunk theme

## Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Features
- Optimized CSS with minimal framework dependencies
- Compressed and minified assets
- Lazy loading for images
- Efficient JavaScript with debouncing
- Mobile-first responsive design

## Security Features
- Sanitized outputs for all custom fields
- Proper nonce verification for forms
- Capability checks for admin functions
- Clean, validated HTML5 markup

## Support and Updates

### For Content Updates
- Non-technical users can manage all content via the WordPress admin or frontend editing
- No coding knowledge required for routine updates
- All customization options available through ACF interfaces

### For Design/Development Changes
- Contact your developer for theme modifications
- All customizations should be made in the theme files or via child theme
- Custom CSS can be added via WordPress Customizer

## Troubleshooting

### Common Issues

**Frontend editing not working**
- Ensure ACF Pro is installed and activated
- Check that user has 'edit_posts' capability
- Clear any caching plugins

**Design not displaying correctly**
- Check that Google Fonts are loading (internet connection required)
- Clear browser cache and any caching plugins
- Ensure theme files uploaded correctly

**Plugin integrations not working**
- Install and configure required plugins (Brevo, Agile Store Locator)
- Check plugin settings and API keys
- Verify plugins are activated

## Changelog

### Version 1.0.0
- Initial release
- Complete cyberpunk design system
- ACF integration for content management
- Frontend editing capabilities
- Plugin integration placeholders
- Mobile-responsive design
- Performance optimizations

---

**Theme Author**: Custom Development Team  
**Version**: 1.0.0  
**WordPress Compatibility**: 5.0+  
**PHP Compatibility**: 7.4+  
**License**: Custom License for Client Use