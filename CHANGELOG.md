# Changelog

## [1.0.1] - 2025-12-29
- modryd namespace
- Overpass API


## [1.0.0] - 2024-01-XX

### Added
- Initial release
- `POISearcher` class for searching POIs by coordinates
- `ViewBox` class for representing geographical bounding boxes
- `POI` class for representing Point of Interest results
- `NominatimClient` for communicating with Nominatim API
- `POIClientInterface` for dependency injection (unified interface for both Nominatim and Overpass clients)
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


