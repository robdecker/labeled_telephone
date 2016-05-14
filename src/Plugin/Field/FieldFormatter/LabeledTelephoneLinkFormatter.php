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
      '#title' => t('Character(s) to use in between the telephone link and the label'),
      '#default_value' => $this->getSetting('separator'),
    );

    $elements['order'] = array(
      '#type' => 'select',
      '#title' => t('The rendering order of the telephone link and the label'),
      '#options' => $this->getOrderOptions(),
      '#default_value' => $this->getSetting('order'),
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
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
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode = NULL) {
    $elements = array();
    $title_setting = $this->getSetting('title');

    // Render each element as link.
    $telephone = array(
      '#type' => 'link',
      '#title' => '',
      '#url' => '',
      '#options' => array('external' => TRUE),
      '#prefix' => '<span class="labeled-telephone__telephone">',
      '#suffix' => '</span>',
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
      // Use custom title if available, otherwise use the telephone number
      // itself as title.
      $telephone['#title'] = $title_setting ?: $item->value;
      // Prepend 'tel:' to the telephone number.
      $telephone['#url'] = Url::fromUri('tel:' . rawurlencode(preg_replace('/\s+/', '', $item->value)));

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
