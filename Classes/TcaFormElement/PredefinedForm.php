<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "Formhandler" by JAKOTA.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Typoheads\Formhandler\TcaFormElement;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Typoheads\Formhandler\Definitions\FormhandlerExtensionConfig;

class PredefinedForm implements SingletonInterface {
  public function __construct(
    private readonly BackendConfigurationManager $backendConfigurationManager
  ) {
  }

  /**
   * Add predefined forms item list.
   *
   * @param array<string, mixed> &$params
   */
  public function addItems(array &$params): void {
    $ts = $this->backendConfigurationManager->getTypoScriptSetup();
    $params['items'] = (array) ($params['items'] ?? []);

    // Check if forms are available
    if (
      !isset($ts['plugin.']) || !is_array($ts['plugin.'])
      || !isset($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.']) || !is_array($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.'])
      || !isset($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.']['settings.']) || !is_array($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.']['settings.'])
      || !isset($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.']['settings.']['predefinedForms.']) || !is_array($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.']['settings.']['predefinedForms.'])
      || 0 === count($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.']['settings.']['predefinedForms.'])
    ) {
      $params['items'][] = [
        0 => LocalizationUtility::translate('LLL:EXT:'.FormhandlerExtensionConfig::EXTENSION_KEY.'/Resources/Private/Language/locallang_flexform.xlf:template_predefined_missing_config'),
        1 => '',
      ];

      return;
    }

    $predef = [];

    // Parse all forms
    foreach ($ts['plugin.'][FormhandlerExtensionConfig::EXTENSION_PLUGIN_SIGNATURE.'.']['settings.']['predefinedForms.'] as $key => $form) {
      // Check if form has a name
      if (!is_array($form) || !isset($form['formName']) || !is_string($form['formName'])) {
        continue;
      }

      $key = rtrim($key, '.');
      $formName = $form['formName'];

      // Check if form name can be translated
      $data = explode(':', $formName);
      if ('lll' === strtolower($data[0])) {
        array_shift($data);
        $langFileAndKey = implode(':', $data);
        $formName = LocalizationUtility::translate('LLL:'.$langFileAndKey) ?? $formName;
      }

      $predef[] = [$formName, $key];
    }

    if (0 == count($predef)) {
      $params['items'][] = [
        0 => LocalizationUtility::translate('LLL:EXT:'.FormhandlerExtensionConfig::EXTENSION_KEY.'/Resources/Private/Language/locallang_flexform.xlf:template_predefined_missing_config'),
        1 => '',
      ];

      return;
    }

    // Add label
    $params['items'][] = [
      0 => LocalizationUtility::translate('LLL:EXT:'.FormhandlerExtensionConfig::EXTENSION_KEY.'/Resources/Private/Language/locallang_flexform.xlf:template_predefined_please_select'),
      1 => '',
    ];

    // add to list
    $params['items'] = array_merge($params['items'], $predef);
  }
}
