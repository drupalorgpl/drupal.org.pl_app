# This is Drupal 8 module for developers

## Installation:

`composer require droptica/d_application_api`

## Services:
### Revert (Import) a feature programmatically - Drupal 8

`\Drupal::service('d_application_api.features')->import($modules);`

#### Example usage:

```php
<?php

/**
 * Update 8002
 */
function mymodule_update_8002() {
  \Drupal::service('d_application_api.features')->import(['feature_module_1', 'feature_module_2']);
}
```
