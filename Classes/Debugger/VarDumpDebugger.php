<?php

declare(strict_types=1);

namespace Typoheads\Formhandler\Debugger;

use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use Typoheads\Formhandler\Domain\Model\Config\Debugger\VarDumpDebuggerModel;

class VarDumpDebugger extends AbstractDebugger {
  public function processDebugLog(
    array $debugLog,
  ): ?string {
    if (!$this->debuggerConfig instanceof VarDumpDebuggerModel) {
      return null;
    }
    if (!$this->debuggerConfig->active || 'html' != $this->formConfig->responseType) {
      return null;
    }

    DebuggerUtility::var_dump(
      $debugLog,
      $this->debuggerConfig->title,
      $this->debuggerConfig->maxDepth,
      $this->debuggerConfig->plainText,
      $this->debuggerConfig->ansiColors,
      $this->debuggerConfig->return,
      $this->debuggerConfig->blacklistedClassNames,
      $this->debuggerConfig->blacklistedPropertyNames,
    );

    return null;
  }
}
