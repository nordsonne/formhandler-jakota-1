<?php
declare(strict_types=1);

namespace Typoheads\Formhandler\Ajax;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use Typoheads\Formhandler\Component\Manager;
use Typoheads\Formhandler\Utility\Globals;
use Typoheads\Formhandler\View\AjaxValidation;
use TYPO3\CMS\Core\Http\Response;


/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
*                                                                        *
* TYPO3 is free software; you can redistribute it and/or modify it under *
* the terms of the GNU General Public License version 2 as published by  *
* the Free Software Foundation.                                          *
*                                                                        *
* This script is distributed in the hope that it will be useful, but     *
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
* TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
* Public License for more details.                                       *
*
*                                                                        */
/**
 * A class validating a field via AJAX.
 */
class Validate
{

    /**
     * @var array
     */
    protected array $templates = [
        'spanSuccess' => '<span class="success">%s</span>',
        'spanError' => '<span class="error">%s</span>',
    ];

    /**
     * @var Manager
     */
    private Manager $componentManager;

    /**
     * @var array
     */
    private array $settings = [];

    /**
     * @var int
     */
    private int $id = 0;

    /**
     * Main method of the class.
     *
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function main(ServerRequestInterface $request): ResponseInterface
    {
      
        $this->init($request);
        $field = htmlspecialchars(GeneralUtility::_GP('field'));
        $response = GeneralUtility::makeInstance(HtmlResponse::class  , '1');
        if ($field) {
            $randomID = htmlspecialchars(GeneralUtility::_GP('randomID'));
            Globals::setCObj($GLOBALS['TSFE']->cObj);
            Globals::setRandomID($randomID);
            if (Globals::getSession() == null) {
                $ts = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_formhandler_pi1.']['settings.'];
                $sessionClass = \Typoheads\Formhandler\Utility\GeneralUtility::getPreparedClassName(isset($ts['session.']) ? $ts['session.'] : null, 'Session\PHP');
                Globals::setSession($this->componentManager->getComponent($sessionClass));
            }
            $this->settings = (array)Globals::getSession()->get('settings');
            Globals::setFormValuesPrefix(\Typoheads\Formhandler\Utility\GeneralUtility::getSingle($this->settings, 'formValuesPrefix'));
            $gp = \Typoheads\Formhandler\Utility\GeneralUtility::getMergedGP();
            $validator = $this->componentManager->getComponent('\Typoheads\Formhandler\Validator\Ajax');
            $errors = [];
            $valid = $validator->validateAjax($field, $gp, $errors);

            if ($valid) {
                $content = \Typoheads\Formhandler\Utility\GeneralUtility::getSingle($this->settings['ajax.']['config.'], 'ok');
                if (strlen($content) === 0) {
                    $content = '<img src="' . PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('formhandler')) . 'Resources/Public/Images/ok.png' . '" />';
                } else {
                    $gp = [
                        $_GET['field'] => $_GET['value'],
                    ];
                    $view = $this->initView($content);
                    $content = $view->render($gp, $errors);
                }
                $content = sprintf($this->templates['spanSuccess'], $content);
            } else {
                $content = \Typoheads\Formhandler\Utility\GeneralUtility::getSingle($this->settings['ajax.']['config.'], 'notOk');
                if (strlen($content) === 0) {
                    $content = '<img src="' . PathUtility::getAbsoluteWebPath(ExtensionManagementUtility::extPath('formhandler')) . 'Resources/Public/Images/notok.png' . '" />';
                } else {
                    $view = $this->initView($content);
                    $gp = [
                        $_GET['field'] => $_GET['value'],
                    ];
                    $content = $view->render($gp, $errors);
                }
                $content = sprintf($this->templates['spanError'], $content);
            }
            
        }

        return $response->getBody()->write($content)
          ->withStatus(200)
        ;
    }

    /**
     * Initialize the class. Read GET parameters
     */
    protected function init(ServerRequestInterface $request): void
    {
        $GLOBALS['TYPO3_REQUEST'] = $request;

        if (isset($_GET['pid'])) {
            $this->id = (int)($_GET['pid']);
        } else {
            $this->id = (int)($_GET['id']);
        }
        $this->componentManager = GeneralUtility::makeInstance(Manager::class);
        Globals::setAjaxMode(true);
        \Typoheads\Formhandler\Utility\GeneralUtility::initializeTSFE($request);
    }

    /**
     * Initialize the AJAX validation view.
     *
     * @param string $content The raw content
     * @return AjaxValidation The view class  
     */
    protected function initView(string $content): AjaxValidation
    {
        $viewClass = '\Typoheads\Formhandler\View\AjaxValidation';
        $view = $this->componentManager->getComponent($viewClass);
        $view->setLangFiles(\Typoheads\Formhandler\Utility\GeneralUtility::readLanguageFiles([], $this->settings));
        $view->setSettings($this->settings);
        $templateName = 'AJAX';
        $template = str_replace('###fieldname###', htmlspecialchars($_GET['field']), $content);
        $template = '###TEMPLATE_' . $templateName . '###' . $template . '###TEMPLATE_' . $templateName . '###';
        $view->setTemplate($template, 'AJAX');
        return $view;
    }
}
