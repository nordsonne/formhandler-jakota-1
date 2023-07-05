<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "Formhandler" by JAKOTA.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Typoheads\Formhandler\Domain\Model\Config\PreProcessor;

use Typoheads\Formhandler\PreProcessor\AbstractPreProcessor;

/** Documentation:Start:TocTree:PreProcessors/Index.rst.
 *
 *.. _preprocessors:
 *
 *==============
 *Pre Processors
 *==============
 *
 *You can enter as many :ref:`PreProcessors` as you like. Each entry requires a model name of the :ref:`PreProcessor <PreProcessors>`. Optionally you can enter specific configuration for the :ref:`PreProcessor <PreProcessors>` in the config section. The :ref:`PreProcessors` are only called the first time the form is shown.
 *
 *.. list-table::
 *   :align: left
 *   :width: 100%
 *   :widths: 20 80
 *   :header-rows: 0
 *   :stub-columns: 0
 *
 *   * - **TypoScript Path**
 *     - plugin.tx_formhandler_form.settings.predefinedForms.[x].preProcessors
 *
 *:ref:`SetSelectOptionsPreProcessor`
 *  Sets a named Options array for select boxes.
 *
 *.. toctree::
 *   :maxdepth: 2
 *   :hidden:
 *
 *   SetSelectOptionsPreProcessor
 *
 *Documentation:End
 */
abstract class AbstractPreProcessorModel {
  /**
   * @param array<string, mixed> $settings
   */
  abstract public function __construct(array $settings);

  /**
   * @return class-string<AbstractPreProcessor>
   */
  abstract public function class(): string;
}
