# Building the Package

## Prerequisites

- Composer installed
- PHP 7.4 or higher
- Git (for versioning)

## Building the Package

### 1. Validate composer.json

```bash
composer validate
```

### 2. Run Tests

```bash
composer install
vendor/bin/phpunit
```

### 3. Create Archive

```bash
composer archive --format=zip --dir=dist
```

This will create a ZIP file in the `dist/` directory.

### 4. Create Tarball (optional)

```bash
composer archive --format=tar --dir=dist
```

## Package Contents

The built package includes:
- `src/` - All source code
- `composer.json` - Package metadata
- `README.md` - Documentation
- `CHANGELOG.md` - Version history
- `LICENSE` - License file (if exists)

Excluded from package (via .gitattributes):
- `tests/` - Test files
- `vendor/` - Dependencies
- `ide/` - IDE files
- `example.php` - Example files
- Development configuration files

## Publishing to Packagist

1. Push your code to a Git repository (GitHub, GitLab, etc.)
2. Create a Git tag for the version:
   ```bash
   git tag -a v1.0.0 -m "Release version 1.0.0"
   git push origin v1.0.0
   ```
3. Submit your package to [Packagist](https://packagist.org/packages/submit)
4. Enable auto-update hook in Packagist (optional)

## Local Installation

To install the package locally from the archive:

```bash
composer require mapquery/poi-searcher:dev-master --prefer-source
```

Or from a local path:

```bash
composer require mapquery/poi-searcher:@dev --prefer-source
```

## Version Management

The package uses semantic versioning (MAJOR.MINOR.PATCH).

To release a new version:
1. Update version in `composer.json`
2. Update `CHANGELOG.md`
3. Create Git tag
4. Build archive
5. Publish to Packagist

