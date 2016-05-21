<?php

namespace Drupal\labeled_telephone\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for 'LabeledTelephone Field formatter' plugin implementations.
 */
abstract class LabeledTelephoneFormatterBase extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'separator' => ': ',
      'order' => 'telephone_first',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['separator'] = array(
      '#type' => 'textfield',
      '#title' => t('Character(s) to use in between the telephone and the label'),
      '#default_value' => $this->getSetting('separator'),
    );

    $elements['order'] = array(
      '#type' => 'select',
      '#title' => t('The rendering order of the telephone and the label'),
      '#options' => $this->getOrderOptions(),
      '#default_value' => $this->getSetting('order'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $settings = $this->getSettings();
    $options = $this->getOrderOptions();

    if (!empty($settings['separator'])) {
      $summary[] = t('Separator using character(s): @separator', array('@separator' => $settings['separator']));
    }
    else {
      $summary[] = t('No separator specified');
    }

    $summary[] = t('Order: @order', array('@order' => $options[$this->getSetting('order')]));

    return $summary;
  }

  /**
   * Gets all possible sub-field ordering options.
   *
   * @return array
   *   The array of options.
   */
  protected function getOrderOptions() {
    $options = array(
      'telephone_first' => 'Telephone first, label second',
      'label_first' => 'Label first, telephone second',
    );

    return $options;
  }

}
