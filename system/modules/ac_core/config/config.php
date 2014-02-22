<?php

/**
 * Contao Open Source CMS
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @copyright	Leo Unglaub 2014
 * @author		Leo Unglaub <leo@leo-unglaub.net>
 * @package		ac_core
 * @license		GPL
 */


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['simpleAjax'][] = array('AcCoreAjax', 'manageAjaxCalls');
