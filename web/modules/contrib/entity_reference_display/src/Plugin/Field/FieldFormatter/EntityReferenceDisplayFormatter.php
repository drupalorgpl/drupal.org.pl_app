<?php

namespace Drupal\entity_reference_display\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Attribute\FieldFormatter;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceEntityFormatter;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Plugin implementation of the 'entity_reference_display_default' formatter.
 *
 * @FieldFormatter(
 *   id = "entity_reference_display_default",
 *   label = @Translation("Selected display mode"),
 *   description = @Translation("This formatter allows you to render referenced entities by selected display mode."),
 *   field_types = {
 *     "entity_reference",
 *     "entity_reference_revisions"
 *   }
 * )
 */
#[FieldFormatter(
  id: 'entity_reference_display_default',
  label: new TranslatableMarkup('Selected display mode'),
  description: new TranslatableMarkup('This formatter allows you to render referenced entities by selected display mode.'),
  field_types: ['entity_reference', 'entity_reference_revisions'],
)]
class EntityReferenceDisplayFormatter extends EntityReferenceEntityFormatter {

  use EntityReferenceDisplayFormatterTrait;

}
