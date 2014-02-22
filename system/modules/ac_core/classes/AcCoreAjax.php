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
 * Class AcCoreAjax
 * Contains methods to catch ajax calls for the auto completer and
 * pass them to the Hook "getAutoCompleterChoices"
 */
class AcCoreAjax extends Controller
{
	/**
	 * Manage all ajax calls for the auto completer
	 *
	 * @param void
	 * @return string|bool
	 */
	public function manageAjaxCalls()
	{
		// only work on "ac" ajax calls
		if (Input::get('mode') == 'ac')
		{
			// stop if there is no acid
			if (Input::get('acid') == '')
			{
				header('HTTP/1.1 412 Precondition Failed');
				die('Invalid AC call, missing the get parameter "acid"');
			}

			// run the hook
			if (is_array($GLOBALS['TL_HOOKS']['getAutoCompleterChoices']))
			{
				$arrKeywords = array();

				foreach ($GLOBALS['TL_HOOKS']['getAutoCompleterChoices'] as $callback)
				{
					$this->import($callback[0]);
					$arrReturn = $this->$callback[0]->$callback[1]();

					// only if we have an array we add the result to the ajax response
					if (is_array($arrReturn))
					{
						$arrKeywords = array_merge($arrKeywords, $arrReturn);
					}
				}

				header('Content-Type: application/json');
				echo json_encode($arrKeywords);
				exit;
			}
		}
	}
}
