<?php

declare(strict_types=1);

namespace Typoheads\Formhandler\Domain\Model\Config\Logger;

use Typoheads\Formhandler\Logger\DatabaseLogger;

/** Documentation:Start:Loggers/DatabaseLogger.rst.
 *
 *.. _databaselogger:
 *
 *==============
 *DatabaseLogger
 *==============
 *
 *Will log into tx_formhandler_log. The logs can be accessed via the Formhandler backend module.
 *
 *.. list-table::
 *   :align: left
 *   :width: 100%
 *   :widths: 20 80
 *   :header-rows: 0
 *   :stub-columns: 0
 *
 *   * - **TypoScript Path**
 *     - plugin.tx_formhandler_form.settings.predefinedForms.[x].loggers
 *
 *.. toctree::
 *   :maxdepth: 2
 *   :hidden:
 *
 *Documentation:End
 */
class DatabaseLoggerModel extends AbstractLoggerModel {
  /**
   * @param array<string, mixed> $settings
   */
  public function __construct(array $settings) {
    if (isset($settings['FIXME'])) {
    }
  }

  public function class(): string {
    return DatabaseLogger::class;
  }
}
