# Contributing to Image AI Metadata

Thank you for your interest in contributing! This document provides guidelines for contributing to the Image AI Metadata WordPress plugin.

## 🌟 Ways to Contribute

- Report bugs
- Suggest new features
- Improve documentation
- Submit code fixes
- Translate the plugin
- Share usage examples
- Help other users

## 🐛 Reporting Bugs

Before reporting a bug:

1. **Check existing issues** - Your bug might already be reported
2. **Test with latest version** - Ensure you're using the latest release
3. **Verify WordPress/PHP versions** - Check minimum requirements are met

When reporting a bug, include:

```markdown
### Bug Description
Clear description of the bug

### Steps to Reproduce
1. Go to...
2. Click on...
3. See error...

### Expected Behavior
What should happen

### Actual Behavior
What actually happens

### Environment
- WordPress version: 6.x.x
- PHP version: 8.x.x
- Plugin version: 1.0.0
- Theme: Theme Name
- Other active plugins: List relevant plugins

### Screenshots
If applicable, add screenshots

### Error Logs
Relevant error messages from debug.log
```

## 💡 Suggesting Features

We love new ideas! When suggesting a feature:

1. **Check existing suggestions** - Someone might have already suggested it
2. **Explain the use case** - Why is this feature needed?
3. **Describe the solution** - How should it work?
4. **Consider alternatives** - Are there other ways to solve this?

Example template:

```markdown
### Feature Request

**Problem:**
Describe the problem this feature would solve

**Proposed Solution:**
Describe how it should work

**Alternatives Considered:**
Other solutions you've thought about

**Additional Context:**
Any other relevant information
```

## 🔧 Development Setup

### Prerequisites

- Local WordPress installation (e.g., Local by Flywheel, XAMPP, MAMP)
- PHP 7.0 or higher
- Git
- Code editor (VS Code, PHPStorm, etc.)
- Hugging Face account and API token

### Setting Up Development Environment

1. **Clone the repository**
   ```bash
   git clone https://github.com/fui86/Titoli-foto---Wordpress.git
   cd Titoli-foto---Wordpress
   ```

2. **Set up WordPress**
   ```bash
   # Create a symlink in your WordPress plugins directory
   ln -s $(pwd) /path/to/wordpress/wp-content/plugins/image-ai-metadata
   ```

3. **Enable WordPress debug mode**
   ```php
   // In wp-config.php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   define('SCRIPT_DEBUG', true);
   ```

4. **Activate the plugin**
   - Go to WordPress admin
   - Navigate to Plugins
   - Activate "Image AI Metadata"

## 📝 Code Guidelines

### WordPress Coding Standards

Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/):

- **Indentation**: Use tabs, not spaces
- **Braces**: Opening brace on same line
- **Naming**: Snake_case for functions, variables
- **Documentation**: Use phpDoc comments

### Code Style Example

```php
<?php
/**
 * Brief description of function
 *
 * Longer description if needed
 *
 * @param int    $attachment_id The attachment ID
 * @param string $description   The image description
 * @return bool True on success, false on failure
 */
function update_image_metadata($attachment_id, $description) {
    // Validate input
    if (!is_numeric($attachment_id) || empty($description)) {
        return false;
    }
    
    // Process
    $result = wp_update_post(array(
        'ID' => $attachment_id,
        'post_title' => $description
    ));
    
    return $result !== 0;
}
```

### Security Best Practices

- **Always sanitize input**: Use `sanitize_text_field()`, `absint()`, etc.
- **Always escape output**: Use `esc_html()`, `esc_attr()`, `esc_url()`
- **Verify nonces**: Check nonces for all form submissions
- **Check capabilities**: Verify user permissions before actions
- **Prepare SQL**: Use `$wpdb->prepare()` for custom queries

Example:

```php
// Sanitize input
$api_token = sanitize_text_field($_POST['api_token']);

// Escape output
echo '<p>' . esc_html($description) . '</p>';

// Verify nonce
if (!wp_verify_nonce($_POST['nonce'], 'action_name')) {
    wp_die('Security check failed');
}

// Check capability
if (!current_user_can('manage_options')) {
    wp_die('Permission denied');
}
```

## 🧪 Testing

### Manual Testing

Before submitting a pull request:

1. **Test image upload** - Upload various image types and sizes
2. **Test API integration** - Verify API calls work correctly
3. **Test error handling** - Try invalid tokens, network errors
4. **Test settings page** - Verify all options save correctly
5. **Test manual processing** - Use the meta box to re-process images
6. **Test with different themes** - Ensure compatibility
7. **Test with other plugins** - Check for conflicts

### Test Checklist

- [ ] Plugin activates without errors
- [ ] Settings page loads correctly
- [ ] API token saves properly
- [ ] Images upload and process automatically (if enabled)
- [ ] Manual processing works from media edit page
- [ ] Error messages display correctly
- [ ] Success messages display correctly
- [ ] Metadata fields populate correctly
- [ ] Alt text, title, caption, description all updated
- [ ] Works with different image formats (JPG, PNG, WebP)
- [ ] Works with large images (> 5MB)
- [ ] No JavaScript errors in console
- [ ] No PHP errors in debug.log

### Automated Testing (Future)

We plan to add:
- PHPUnit tests for core functions
- Integration tests for WordPress hooks
- E2E tests with Cypress or similar

## 🌍 Translation

Help translate the plugin into your language:

1. **Generate POT file** (if needed)
   ```bash
   wp i18n make-pot . languages/image-ai-metadata.pot
   ```

2. **Create PO file**
   - Copy `languages/image-ai-metadata.pot` to `languages/image-ai-metadata-{locale}.po`
   - Translate strings using Poedit or similar tool

3. **Generate MO file**
   - Poedit can generate this automatically
   - Or use: `msgfmt image-ai-metadata-{locale}.po -o image-ai-metadata-{locale}.mo`

4. **Submit translation**
   - Create a pull request with your translation files

### Translation Guidelines

- Keep translations concise
- Maintain the same tone as English version
- Test in actual WordPress interface
- Include translator comments if needed

## 🔀 Pull Request Process

### Before Submitting

1. **Create a new branch**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/bug-description
   ```

2. **Make your changes**
   - Follow coding standards
   - Add comments where needed
   - Update documentation if necessary

3. **Test thoroughly**
   - Manual testing
   - Check for errors
   - Test edge cases

4. **Commit your changes**
   ```bash
   git add .
   git commit -m "Description of changes"
   ```

### Commit Message Format

Use clear, descriptive commit messages:

```
Type: Brief description (50 chars or less)

Longer explanation if needed (wrap at 72 chars).
Explain what and why, not how.

- Bullet points for multiple changes
- Reference issues: Fixes #123
```

Types:
- `feat:` New feature
- `fix:` Bug fix
- `docs:` Documentation changes
- `style:` Code style changes (formatting)
- `refactor:` Code refactoring
- `test:` Adding tests
- `chore:` Maintenance tasks

Examples:
```
feat: Add batch processing for existing images

Adds a new admin page that allows users to process
all existing images in the media library at once.

Fixes #45
```

```
fix: Handle API timeout errors gracefully

Previously, API timeouts would cause fatal errors.
Now displays user-friendly error message.

Fixes #67
```

### Submitting Pull Request

1. **Push to GitHub**
   ```bash
   git push origin feature/your-feature-name
   ```

2. **Create Pull Request**
   - Go to GitHub repository
   - Click "New Pull Request"
   - Fill in the template:

```markdown
## Description
Clear description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Documentation update
- [ ] Code refactoring

## Testing
Describe testing performed

## Checklist
- [ ] Code follows WordPress coding standards
- [ ] Self-review completed
- [ ] Comments added for complex code
- [ ] Documentation updated
- [ ] No new warnings or errors
- [ ] Tested on WordPress 5.0+
- [ ] Tested on PHP 7.0+

## Screenshots (if applicable)
Add screenshots for UI changes
```

3. **Wait for review**
   - Maintainers will review your PR
   - Address any feedback
   - Make requested changes

4. **Merge**
   - Once approved, PR will be merged
   - Your contribution will be credited

## 🎨 UI/UX Guidelines

When adding or modifying UI:

- Follow WordPress admin design patterns
- Use WordPress core components when possible
- Ensure accessibility (WCAG 2.1 AA)
- Test with screen readers if possible
- Mobile-responsive design
- Clear, user-friendly error messages
- Helpful tooltips and descriptions

## 📚 Documentation

When updating documentation:

- Keep both Italian and English versions in sync
- Use clear, simple language
- Include code examples where helpful
- Add screenshots for UI changes
- Update README.md if adding features
- Update USAGE.md for usage instructions
- Update EXAMPLES.md for practical examples

## 🏆 Recognition

Contributors will be:
- Listed in CHANGELOG.md
- Credited in release notes
- Added to contributors list (if significant contribution)

## ❓ Questions?

- Open a discussion on GitHub
- Create an issue with "question" label
- Check existing documentation

## 📜 License

By contributing, you agree that your contributions will be licensed under the GPL-2.0+ License.

---

Thank you for contributing! 🙏
