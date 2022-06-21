<?php

declare(strict_types=1);

namespace Typoheads\Formhandler\Validator;

/**
 * This script is part of the TYPO3 project - inspiring people to share!
 *
 * TYPO3 is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 2 as published by
 * the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 */
class Ajax extends AbstractValidator {
  /**
   * Array holding the configured validators.
   */
  protected array $validators;

  public function loadConfig(): void {
    $tsConfig = $this->parseConditionsBlock((array) $this->globals->getSession()->get('settings'));
    $this->settings = [];
    $this->validators = $tsConfig['validators.'];
    if ($tsConfig['ajax.']) {
      $this->settings['ajax.'] = $tsConfig['ajax.'];
    }
  }

  public function validate(array &$errors): bool {
    // Nothing to do here
    return true;
  }

  /**
   * Validates the submitted values using given settings.
   *
   * @param array &$errors Reference to the errors array to store the errors occurred
   */
  public function validateAjax(string $field, array $gp, array &$errors): bool {
    $this->loadConfig();
    if ($this->validators) {
      foreach ($this->validators as $idx => $settings) {
        if (is_array($settings['config.'])) {
          $this->settings = $settings['config.'];
        }
      }
    }
    if (is_array($this->settings['fieldConf.'])) {
      $disableErrorCheckFields = $this->getDisableErrorCheckFields();
      $restrictErrorChecks = $this->getRestrictedErrorChecks();

      $fieldSettings = $this->settings['fieldConf.'][$field.'.'];

      // parse error checks
      if (is_array($fieldSettings['errorCheck.'])) {
        $counter = 0;
        $errorChecks = [];

        // set required to first position if set
        foreach ($fieldSettings['errorCheck.'] as $key => $check) {
          if (!strstr(strval($key), '.') && strlen(trim($check)) > 0) {
            if (!strcmp($check, 'required') || !strcmp($check, 'file_required')) {
              $errorChecks[$counter]['check'] = $check;
              unset($fieldSettings['errorCheck.'][$key]);
              ++$counter;
            }
          }
        }

        // set other errorChecks
        foreach ($fieldSettings['errorCheck.'] as $key => $check) {
          if (!strstr(strval($key), '.')) {
            $errorChecks[$counter]['check'] = $check;
            if (is_array($fieldSettings['errorCheck.'][$key.'.'] ?? null)) {
              $errorChecks[$counter]['params'] = $fieldSettings['errorCheck.'][$key.'.'];
            }
            ++$counter;
          }
        }

        // foreach error checks
        foreach ($errorChecks as $idx => $check) {
          // Skip error check if the check is disabled for this field or if all checks are disabled for this field
          if (!empty($disableErrorCheckFields)
                        && in_array('all', array_keys($disableErrorCheckFields))
                        || (
                          in_array($field, array_keys($disableErrorCheckFields))
                            && (
                              in_array($check['check'], $disableErrorCheckFields[$field])
                                || empty($disableErrorCheckFields[$field])
                            )
                        )
                    ) {
            continue;
          }

          $classNameFix = ucfirst($check['check']);
          if (false === strpos($classNameFix, 'Tx_')) {
            $errorCheckObject = $this->componentManager->getComponent('\\Typoheads\\Formhandler\\Validator\\ErrorCheck\\'.$classNameFix);
            $fullClassName = '\\Typoheads\\\Formhandler\\Validator\\ErrorCheck\\'.$classNameFix;
          } else {
            // Look for the whole error check name, maybe it is a custom check like Tx_SomeExt_ErrorCheck_Something
            $errorCheckObject = $this->componentManager->getComponent($check['check']);
            $fullClassName = $check['check'];
          }
          if (!$errorCheckObject) {
            $this->utilityFuncs->debugMessage('check_not_found', [$fullClassName], 2);
          }
          if (empty($restrictErrorChecks) || in_array($check['check'], $restrictErrorChecks)) {
            $errorCheckObject->init($gp, $check);
            $errorCheckObject->setFormFieldName($field);
            if ($errorCheckObject->validateConfig()) {
              $checkFailed = $errorCheckObject->check();
              if (strlen($checkFailed) > 0) {
                if (!is_array($errors[$field] ?? null)) {
                  $errors[$field] = [];
                }
                $errors[$field][] = $checkFailed;
              }
            } else {
              $this->utilityFuncs->throwException('Configuration is not valid for class "'.$fullClassName.'"!');
            }
          }
        }
      }
    }

    return empty($errors);
  }
}
