<?php

namespace Drupal\geysir\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\TranslatableRevisionableInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides the geysir forms entity revisions support.
 *
 * @package Drupal\geysir\Form
 */
trait GeysirFormEntityRevisionTrait {

  /**
   * Saves the revision on the parent entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The parent entity.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The system time.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   *
   * @return int
   *   Save status.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function saveParentEntityRevision(EntityInterface $entity, TimeInterface $time, AccountInterface $current_user): int {
    if ($entity instanceof RevisionableInterface) {
      $entity->setNewRevision();

      if ($entity instanceof TranslatableRevisionableInterface) {
        $entity->setRevisionTranslationAffected(TRUE);
      }
      if ($entity instanceof RevisionLogInterface) {
        $entity->setRevisionCreationTime($time->getRequestTime());
        $entity->setRevisionUserId($current_user->id());
      }
    }

    return $entity->save();
  }

}
