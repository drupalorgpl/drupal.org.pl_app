<?php

namespace Drupal\Tests\geysir\Kernel;

use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\geysir\Form\GeysirParagraphForm;
use Drupal\KernelTests\KernelTestBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\paragraphs\ParagraphInterface;

class ParagraphFormTest extends KernelTestBase {

  protected static $modules = [
    'user',
    'entity',
    'entity_reference_revisions',
    'paragraphs',
    'geysir',
  ];

  protected EntityTypeManagerInterface $entityTypeManager;

  protected EntityFormBuilderInterface $entityFormBuilder;

  protected ParagraphInterface $paragraph;

  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->entityFormBuilder = $this->container->get('entity.form_builder');

    $this->installEntitySchema('paragraph');

    $this->paragraph = Paragraph::create([
      'type' => 'default',
    ]);
    $this->paragraph->save();
  }

  public function testParagraphsEditForm() {
    $paragraph_entity_type = $this->entityTypeManager->getDefinition('paragraph');

    $this->assertTrue($paragraph_entity_type->hasLinkTemplate('delete-form'));
    $this->assertNotNull($this->paragraph->toUrl('delete-form'));

    $form = $this->entityFormBuilder->getForm($this->paragraph);

    $this->assertNotEmpty($form['actions']['delete']);
  }


}
