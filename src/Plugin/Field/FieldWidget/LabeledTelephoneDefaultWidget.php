<?php

namespace Drupal\labeled_telephone\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\telephone\Plugin\Field\FieldWidget\TelephoneDefaultWidget;

/**
 * Plugin implementation of the 'labeled_telephone' widget.
 *
 * @FieldWidget (
 *   id = "labeled_telephone_default",
 *   label = @Translation("Telephone number (labeled)"),
 *   field_types = {
 *     "labeled_telephone"
 *   }
 * )
 */
class LabeledTelephoneDefaultWidget extends TelephoneDefaultWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['#attached']['library'][] = 'labeled_telephone/data-entry';
    $element['#attributes']['class'][] = 'labeled-telephone-wrapper';
    $element['#attributes']['class'][] = 'container-inline';
    $element['#type'] = 'fieldset';

    // Override Telephone's settings.
    unset($element['value']['#title_display']);
    $element['value']['#title'] = t('Phone');
    $element['value']['#required'] = FALSE;
    $element['value']['#size'] = 15;

    $element['label'] = array(
      '#type' => 'textfield',
      '#title' => t('Label'),
      '#default_value' => isset($items[$delta]->label) ? $items[$delta]->label : NULL,
      '#size' => 15,
      '#maxlength' => 256,
      '#required' => FALSE,
      '#weight' => 2,
    );

    return $element;
  }
}
