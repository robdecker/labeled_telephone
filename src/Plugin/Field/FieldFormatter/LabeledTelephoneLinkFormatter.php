<?php

namespace Drupal\labeled_telephone\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\telephone\Plugin\Field\FieldFormatter\TelephoneLinkFormatter;

/**
 * Plugin implementation of the 'labeled_telephone_link' formatter.
 *
 * @FieldFormatter (
 *   id = "labeled_telephone_link",
 *   label = @Translation("Telephone link (labeled)"),
 *   field_types = {
 *     "labeled_telephone"
 *   }
 * )
 */
class LabeledTelephoneLinkFormatter extends TelephoneLinkFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'separator' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['separator'] = array(
      '#type' => 'textfield',
      '#title' => t('Character(s) to use in between the telephone link and the label'),
      '#default_value' => $this->getSetting('separator'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $settings = $this->getSettings();

    if (!empty($settings['separator'])) {
      $summary[] = t('Separator using character(s): @separator', array('@separator' => $settings['separator']));
    }
    else {
      $summary[] = t('No separator sprecified');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = array();
    $title_setting = $this->getSetting('title');

    foreach ($items as $delta => $item) {
      // Render each element as link.
      $elements[$delta][0] = array(
        '#type' => 'link',
        // Use custom title if available, otherwise use the telephone number
        // itself as title.
        '#title' => $title_setting ?: $item->value,
        // Prepend 'tel:' to the telephone number.
        '#url' => Url::fromUri('tel:' . rawurlencode(preg_replace('/\s+/', '', $item->value))),
        '#options' => array('external' => TRUE),
        '#prefix' => '<span class="labeled-telephone__telephone">',
        '#suffix' => '</span>',
      );

      if (!empty($item->_attributes)) {
        $elements[$delta]['#options'] += array('attributes' => array());
        $elements[$delta]['#options']['attributes'] += $item->_attributes;
        // Unset field item attributes since they have been included in the
        // formatter output and should not be rendered in the field template.
        unset($item->_attributes);
      }

      $elements[$delta][1] = array(
        '#type' => 'markup',
        '#prefix' => '<span class="labeled-telephone__separator">',
        '#suffix' => '</span>',
        '#markup' => $this->getSetting('separator'),
      );

      $elements[$delta][2] = array(
        '#type' => 'markup',
        '#prefix' => '<span class="labeled-telephone__label">',
        '#suffix' => '</span>',
        '#markup' => $item->label,
      );
    }

    return $elements;
  }

}
