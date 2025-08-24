# Merchant Search and Filtering System

This document describes the comprehensive search and filtering system implemented for the merchant dashboard at https://dala3chic.com/merchant/dashboard.

## Overview

The merchant search and filtering system provides powerful capabilities for merchants to search across their products and services with real-time autocomplete, comprehensive filtering options, and mobile-responsive design.

## Features

### 🔍 Global Search Functionality
- **Real-time search** across products and services
- **Autocomplete suggestions** with search history
- **Recent searches** for quick access
- **Cross-entity search** (products, services, categories)
- **Keyboard navigation** support
- **Mobile-optimized** interface

### 🎛️ Advanced Filtering
- **Product Filters:**
  - Category, price range, stock status
  - Availability status (active/inactive)
  - Featured status
  - Date range filters (created/updated)
  - SKU-based filtering

- **Service Filters:**
  - Category, price range, duration
  - Availability status and featured status
  - Service type (home service vs in-store)
  - Date range filters

- **Combined Filters:**
  - Multiple simultaneous filters
  - Filter reset and clear options
  - Active filter indicators with result counts
  - URL parameter support for shareable views

### 📱 Mobile Responsiveness
- Touch-friendly interface
- Responsive filter layouts
- Mobile-optimized modals
- iOS zoom prevention (16px+ font sizes)
- Gesture support

### ⚡ Performance Features
- AJAX-based real-time updates
- Debounced search input
- Efficient pagination
- Loading states and error handling
- Memory leak prevention

## Technical Implementation

### Backend API Endpoints

#### Product Search and Filtering
```
GET /merchant/products?search={query}&category_id={id}&status={status}&...
GET /merchant/products/search/suggestions?q={query}
GET /merchant/products/filter/options
```

#### Service Search and Filtering
```
GET /merchant/services?search={query}&category_id={id}&status={status}&...
GET /merchant/services/search/suggestions?q={query}
GET /merchant/services/filter/options
```

#### Global Dashboard Search
```
GET /merchant/dashboard/search?q={query}
GET /merchant/dashboard/search/suggestions?q={query}
POST /merchant/dashboard/search/save
```

### Frontend Components

#### JavaScript Components
- `MerchantSearch` - Main search component with autocomplete
- `MerchantProductFilters` - Product-specific filtering
- `MerchantServiceFilters` - Service-specific filtering

#### CSS Files
- `public/css/merchant-search.css` - Complete styling with mobile responsiveness

#### Blade Templates
- `resources/views/merchant/partials/search-component.blade.php` - Reusable search component
- `resources/views/merchant/products/partials/products-table.blade.php` - AJAX table partial
- `resources/views/merchant/services/partials/services-table.blade.php` - AJAX table partial

### Database Queries

The system uses optimized database queries with proper indexing:

```php
// Example product search query
Product::where('user_id', $user->id)
    ->where(function ($q) use ($searchTerm) {
        $q->where('name', 'like', "%{$searchTerm}%")
          ->orWhere('description', 'like', "%{$searchTerm}%")
          ->orWhere('sku', 'like', "%{$searchTerm}%")
          ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
              $categoryQuery->where('name', 'like', "%{$searchTerm}%");
          });
    })
    ->with('category')
    ->orderBy('created_at', 'desc')
    ->paginate(15);
```

## Usage Examples

### Basic Search
```javascript
// Initialize search component
const merchantSearch = new MerchantSearch({
    searchInputSelector: '.merchant-search-input',
    suggestionsContainerSelector: '.search-suggestions'
});

// Perform search
merchantSearch.performSearch('laptop');
```

### Advanced Filtering
```javascript
// Initialize product filters
const productFilters = new MerchantProductFilters();

// Apply filters
productFilters.applyAdvancedFilters({
    category_id: 1,
    price_min: 50,
    price_max: 500,
    status: 'active',
    featured: true
});
```

### Mobile Usage
```html
<!-- Mobile-optimized search component -->
@include('merchant.partials.search-component', [
    'placeholder' => 'Search products...',
    'showFilters' => true,
    'type' => 'products'
])
```

## Testing

### Automated Testing
The system includes comprehensive Playwright tests covering:

- **Desktop and mobile viewports**
- **Cross-browser compatibility**
- **Performance testing**
- **Accessibility compliance**
- **Error handling**
- **Security validation**

### Running Tests
```bash
# Run all search and filter tests
npm run test:search

# Run with browser UI
npm run test:search:headed

# Run mobile-specific tests
npm run test:search:mobile

# Run desktop-specific tests
npm run test:search:desktop

# Debug mode
npm run test:search:debug
```

### Test Coverage
- ✅ Search functionality across all viewports
- ✅ Filter application and removal
- ✅ URL parameter handling
- ✅ Pagination with filters
- ✅ Mobile responsiveness
- ✅ Keyboard navigation
- ✅ Performance benchmarks
- ✅ Error handling
- ✅ Security validation

## Performance Considerations

### Optimization Strategies
1. **Debounced Search Input** - 300ms delay to prevent excessive API calls
2. **Efficient Pagination** - Server-side pagination with AJAX updates
3. **Caching** - Browser-side caching of filter options
4. **Lazy Loading** - Progressive loading of search results
5. **Memory Management** - Proper cleanup of event listeners

### Performance Metrics
- Search response time: < 3 seconds
- Filter application: < 5 seconds
- Mobile responsiveness: 60fps animations
- Memory usage: No memory leaks during extended sessions

## Security Features

### Input Validation
- SQL injection prevention
- XSS protection
- Input length limits
- Malicious input sanitization

### Authorization
- Merchant-specific data isolation
- Proper authentication checks
- CSRF protection
- Rate limiting

## Browser Support

### Desktop Browsers
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+

### Mobile Browsers
- ✅ iOS Safari 14+
- ✅ Chrome Mobile 90+
- ✅ Samsung Internet 14+

## Accessibility

### WCAG 2.1 Compliance
- ✅ Keyboard navigation
- ✅ Screen reader support
- ✅ High contrast mode
- ✅ Focus management
- ✅ ARIA labels and roles

### Accessibility Features
- Proper semantic HTML
- Keyboard shortcuts
- Focus indicators
- Screen reader announcements
- High contrast support

## Troubleshooting

### Common Issues

#### Search Not Working
1. Check network connectivity
2. Verify CSRF token
3. Check browser console for errors
4. Ensure proper authentication

#### Filters Not Applying
1. Verify filter options are loaded
2. Check URL parameters
3. Ensure AJAX requests are successful
4. Clear browser cache

#### Mobile Issues
1. Check viewport meta tag
2. Verify touch event handlers
3. Test on actual devices
4. Check font sizes (16px+ requirement)

### Debug Mode
Enable debug mode by adding `?debug=1` to the URL or setting:
```javascript
window.merchantSearchDebug = true;
```

## Future Enhancements

### Planned Features
- [ ] Saved search queries
- [ ] Advanced search operators
- [ ] Bulk actions on filtered results
- [ ] Export filtered data
- [ ] Real-time notifications
- [ ] Voice search support

### Performance Improvements
- [ ] Elasticsearch integration
- [ ] Redis caching
- [ ] CDN optimization
- [ ] Progressive Web App features

## Support

For technical support or questions about the search and filtering system:

1. Check this documentation
2. Review the test files for usage examples
3. Check browser console for error messages
4. Contact the development team

## Changelog

### Version 1.0.0 (Current)
- ✅ Initial implementation
- ✅ Basic search functionality
- ✅ Advanced filtering
- ✅ Mobile responsiveness
- ✅ Comprehensive testing
- ✅ Performance optimization
- ✅ Security features
- ✅ Accessibility compliance
