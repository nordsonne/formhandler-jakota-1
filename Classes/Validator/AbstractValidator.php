<?php

declare(strict_types=1);

/*
 * This file is part of TYPO3 CMS-based extension "Formhandler" by JAKOTA.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

namespace Typoheads\Formhandler\Validator;

use TYPO3\CMS\Core\SingletonInterface;
use Typoheads\Formhandler\Domain\Model\Config\FormModel;
use Typoheads\Formhandler\Domain\Model\Config\Validator\AbstractValidatorModel;

abstract class AbstractValidator implements SingletonInterface {
  abstract public function process(FormModel &$formConfig, AbstractValidatorModel &$validatorConfig): bool;
}
