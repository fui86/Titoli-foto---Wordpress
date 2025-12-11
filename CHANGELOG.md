# Changelog

All notable changes to the Image AI Metadata plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-12-11

### Added
- Initial release of Image AI Metadata plugin
- Automatic image recognition using Hugging Face AI models
- Integration with BLIP Image Captioning model (default)
- Automatic population of image metadata:
  - Alternative text (alt text)
  - Title
  - Caption
  - Description
- Settings page for API configuration
- Support for custom API endpoints
- Automatic processing on image upload (optional)
- Manual processing via meta box on media edit page
- Error handling and user-friendly error messages
- Italian language support (i18n ready)
- Translation template (POT file)
- Comprehensive documentation:
  - README.md (English and Italian)
  - USAGE.md (detailed usage guide)
  - EXAMPLES.md (practical examples)
  - INSTALLATION.md (installation guide)
  - CHANGELOG.md (this file)
- Test script for API verification (test-api.php)
- GPL-2.0+ license
- .gitignore for WordPress projects

### Security
- Nonce verification for all form submissions
- Capability checks for admin actions
- Input sanitization and validation
- Output escaping for all displayed data
- Secure API token storage

### Features
- Hugging Face API integration
- Support for multiple AI models
- Configurable automatic/manual processing
- Last processing timestamp tracking
- Success/error notifications
- Compatible with WordPress 5.0+
- PHP 7.0+ support
- WordPress best practices implementation

### Documentation
- Multilingual documentation (Italian and English)
- Step-by-step installation guide
- Configuration instructions
- Troubleshooting section
- Usage examples
- API integration examples
- Best practices guide

## [Unreleased]

### Planned Features
- Batch processing interface for existing images
- Support for additional AI models
- Multilingual description generation
- Custom field mapping
- Integration with popular SEO plugins
- Scheduled processing for large libraries
- Advanced analytics and reporting
- Custom AI model training support
- Integration with image optimization plugins

### Under Consideration
- Automatic translation of descriptions
- Custom prompts for AI models
- Support for video thumbnails
- Integration with cloud storage services
- WordPress CLI support
- REST API endpoints
- Export/import settings

---

## Version History

### Version Numbering

We use [Semantic Versioning](https://semver.org/):
- MAJOR version: Incompatible API changes
- MINOR version: New features (backward compatible)
- PATCH version: Bug fixes (backward compatible)

### Support Policy

- Latest major version: Full support
- Previous major version: Security updates only
- Older versions: No support (please upgrade)

### Upgrade Notes

#### From Nothing to 1.0.0
- First installation
- No upgrade required

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on:
- Reporting bugs
- Suggesting features
- Submitting pull requests
- Code style guidelines

---

## Links

- [GitHub Repository](https://github.com/fui86/Titoli-foto---Wordpress)
- [Issue Tracker](https://github.com/fui86/Titoli-foto---Wordpress/issues)
- [Documentation](README.md)
- [Hugging Face](https://huggingface.co/)

---

## Credits

### Maintainers
- fui86 - Original author and maintainer

### Contributors
- Thanks to all contributors who help improve this plugin!

### Third-Party Services
- [Hugging Face](https://huggingface.co/) - AI model hosting and inference API
- [Salesforce BLIP](https://huggingface.co/Salesforce/blip-image-captioning-large) - Default image captioning model

### Special Thanks
- WordPress community
- Hugging Face team
- All users and testers

---

## License

This project is licensed under the GPL-2.0+ License - see the [LICENSE](LICENSE) file for details.
