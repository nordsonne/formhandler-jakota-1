<?php

declare(strict_types=1);

namespace Typoheads\Formhandler\Debugger;

use TYPO3\CMS\Core\Utility\DebugUtility;
use Typoheads\Formhandler\Domain\Model\Config\Debugger\PrintToScreenDebuggerModel;

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

/**
 * A simple debugger printing the messages on the screen.
 */
class PrintToScreenDebugger extends AbstractDebugger {
  public function processDebugLog(
    array $debugLog,
  ): ?string {
    if (!$this->debuggerConfig instanceof PrintToScreenDebuggerModel) {
      return null;
    }
    if (!$this->debuggerConfig->active || 'html' != $this->formConfig->responseType) {
      return null;
    }

    $debugOutput = '';

    foreach ($debugLog as $section => $logData) {
      $debugOutput .= str_replace('|', $section, $this->debuggerConfig->sectionHeaderWrap);
      $sectionContent = '';
      foreach ($logData as $messageData) {
        $message = str_replace("\n", '<br />', $messageData['message']);
        $message = str_replace('|', $message, $this->debuggerConfig->severityWrap[$messageData['severity']]);

        $sectionContent .= str_replace('|', $message, $this->debuggerConfig->messageWrap);

        if ($messageData['data']) {
          $sectionContent .= DebugUtility::viewArray($messageData['data']);
          $sectionContent .= '<br />';
        }
      }
      $debugOutput .= str_replace('|', $sectionContent, $this->debuggerConfig->sectionWrap);
    }

    return $debugOutput;
  }
}
