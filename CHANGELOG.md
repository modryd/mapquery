# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2024-01-XX

### Added
- Initial release
- `POISearcher` class for searching POIs by coordinates
- `ViewBox` class for representing geographical bounding boxes
- `POI` class for representing Point of Interest results
- `NominatimClient` for communicating with Nominatim API
- `NominatimClientInterface` for dependency injection
- Comprehensive unit tests (33 tests, 101 assertions)
- Full documentation in README.md
- Example usage file

### Features
- Search POIs by ViewBox object
- Search POIs by coordinates directly
- Filter POIs by amenity type (restaurant, cafe, etc.)
- Coordinate validation
- Error handling with custom exceptions
- Customizable API client (base URL, user agent, timeout)

