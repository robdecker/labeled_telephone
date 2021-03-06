<?php

namespace Drupal\labeled_telephone\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

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
class LabeledTelephoneLinkFormatter extends LabeledTelephoneFormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'title' => '',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title to replace basic numeric telephone number display'),
      '#default_value' => $this->getSetting('title'),
    );

    $elements += parent::settingsForm($form, $form_state);

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $settings = $this->getSettings();

    if (!empty($settings['title'])) {
      $summary[] = t('Link using text: @title', array('@title' => $settings['title']));
    }
    else {
      $summary[] = t('Link using provided telephone number.');
    }

    return array_merge($summary, parent::settingsSummary());
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

}
