<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Leo Unglaub 2011
 * @author     Leo Unglaub <leo@leo-unglaub.net>
 * @package    ac_core
 * @license    LGPL
 * @filesource
 */

/**
 * Class AcCoreAjax
 * 
 * Contains methods to catch ajax calls for the auto completer and 
 * pass them to the Hook "getAutoCompleterChoices"
 * 
 * @copyright  Leo Unglaub 2011
 * @author     Leo Unglaub <leo@leo-unglaub.net>
 * @package    ac_core
 */
class AcCoreAjax extends Controller
{
	/**
	 * Manage all ajax calls for the auto completer
	 * @return mixed
	 */
	public function manageAjaxCals()
	{
		// only work on "ac" ajax calls
		if ($this->Input->get('mode') == 'ac')
		{
			// stop if there is no acid
			if ($this->Input->get('acid') == '')
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

				return json_encode($arrKeywords);
			}
		}

		return false;
	}
}

?>
