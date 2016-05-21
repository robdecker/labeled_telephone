<?php

namespace Drupal\labeled_telephone\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'labeled_telephone' formatter.
 *
 * @FieldFormatter (
 *   id = "labeled_telephone_default",
 *   label = @Translation("Telephone (labeled)"),
 *   field_types = {
 *     "labeled_telephone"
 *   }
 * )
 */
class LabeledTelephoneDefaultFormatter extends LabeledTelephoneFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = array();

    // Render each element as link.
    $telephone = array(
      '#type' => 'markup',
      '#prefix' => '<span class="labeled-telephone__telephone">',
      '#suffix' => '</span>',
      '#markup' => '',
    );

    $label = array(
      '#type' => 'markup',
      '#prefix' => '<span class="labeled-telephone__label">',
      '#suffix' => '</span>',
      '#markup' => '',
    );

    $separator = array(
      '#type' => 'markup',
      '#prefix' => '<span class="labeled-telephone__separator">',
      '#suffix' => '</span>',
      '#markup' => $this->getSetting('separator'),
    );

    foreach ($items as $delta => $item) {
      $telephone['#markup'] = $item->value;

      $label['#markup'] = $item->label;

      if (!empty($item->_attributes)) {
        $elements[$delta]['#options'] += array('attributes' => array());
        $elements[$delta]['#options']['attributes'] += $item->_attributes;
        // Unset field item attributes since they have been included in the
        // formatter output and should not be rendered in the field template.
        unset($item->_attributes);
      }

      switch ($this->getSetting('order')) {
        case 'label_first':
          $elements[$delta][0] = $label;
          $elements[$delta][1] = $separator;
          $elements[$delta][2] = $telephone;
          break;

        case 'telephone_first':
        default:
          $elements[$delta][0] = $telephone;
          $elements[$delta][1] = $separator;
          $elements[$delta][2] = $label;
          break;
      }
    }

    return $elements;
  }

}
