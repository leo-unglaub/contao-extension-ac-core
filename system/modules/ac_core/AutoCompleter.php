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
 * @copyright  Leo Unglaub 2012
 * @author     Leo Unglaub <leo@leo-unglaub.net>
 * @package    ac_core
 * @license    LGPL
 * @filesource
 */


/**
 * Class AutoCompleter
 * Provide mathods for the auto completer
 * 
 * @copyright  Leo Unglaub 2012
 * @author     Leo Unglaub <leo@leo-unglaub.net>
 * @package    ac_core
 */
class AutoCompleter extends Controller
{
	/**
	 * Formular id
	 * @var sting
	 */
	protected $strFormId;

	/**
	 * Config array
	 * @var array
	 */
	protected $arrConfig = array();

	/**
	 * additional config string
	 * @var string
	 */
	protected $strConfigAdditional;

	/**
	 * additional url parameter
	 * @var string
	 */
	protected $strUrlAdditional;

	/**
	 * Contain all valid and supported config options
	 * @var array
	 */
	protected $arrConfigOptions = array
	(
		'minLength', 'markQuery', 'width', 'maxChoices', 'visibleChoices', 'className', 'zIndex', 'delay', 'autoSubmit', 'overflow', 
		'overflowMargin', 'selectFirst', 'forceSelect', 'selectMode', 'multiple', 'separator', 'autoTrim', 'allowDupes', 'cache',
		'relative', 'indicatorClass'
	);


	/**
	 * Set some default parameters and call the
	 * parent cunstructor
	 */
	public function __construct()
	{
		$this->indicatorClass = 'autocompleter-loading';
		parent::__construct();
	}


	/**
	 * set a object property or a config option
	 * 
	 * @param string $strKey
	 * @param mixed $varValue
	 * @return void
	 */
	public function __set($strKey, $varValue)
	{
		switch ($strKey)
		{
			case 'formId':
				$this->strFormId = $varValue;
				break;

			case 'configAdditional':
				$this->strConfigAdditional = $varValue;
				break;

			case 'urlAdditional':
				$this->strUrlAdditional = $varValue;
				break;

			default:
				// if $strKey is a valid config option, set them
				if (in_array($strKey, $this->arrConfigOptions))
				{
					$this->arrConfig[$strKey] = $varValue;
					break;
				}

				throw new Exception(sprintf('Invalid argument "%s"', $strKey));
				break;
		}
	}

	/**
	 * return a object property or a config option
	 * 
	 * @param string $strKey
	 * @return mixed
	 */
	public function __get($strKey)
	{
		switch ($strKey)
		{
			case 'formId':
				return $this->strFormId;
				break;

			case 'configAdditional':
				return $this->strConfigAdditional;
				break;

			case 'urlAdditional':
				return $this->strUrlAdditional;
				break;

			default:
				if (in_array($strKey, $this->arrConfig))
				{
					return $this->arrConfig[$strKey];
					break;
				}

				return null;
				break;
		}
	}

	public function generate()
	{
		// check if the formularfield id is set
		if ($this->strFormId == '')
		{
			throw new Exception('Missing the form field id. Please set the form field id like $objAc->formId = "foo";');
			exit;
		}

		// add the auto completer core to the site header
		if ($GLOBALS['TL_CONFIG']['debugMode'])
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/ac_core/html/ac_core.src.js';
		}
		else
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/ac_core/html/ac_core.js';
		}

		// prepare the config
		$strConfig = '';

		foreach ($this->arrConfig as $k=>$v)
		{
			if ($v === true)
			{
				$strConfig .= "'$k': " . 'true,';
				continue;
			}

			if ($v === false)
			{
				$strConfig .= "'$k': " . 'false,';
				continue;
			}

			if ($v === null)
			{
				$strConfig .= "'$k': " . 'null,';
				continue;
			}

			if (is_int($v))
			{
				$strConfig .= "'$k': " . $v . ',';
				continue;
			}

			$strConfig .= "'$k': '" . $v . "',";
		}

		// add the additional options
		$strConfig .= $this->strConfigAdditional;

		// IE Fix: remove the last , to prevent an js error
		if (substr($strConfig, -1) == ',')
		{
			$strConfig = substr($strConfig, 0, -1);
		}


		$strBuild = 'document.addEvent(\'domready\',function(){new Autocompleter.Request.JSON(\'' . $this->strFormId . '\',\'ajax.php?mode=ac&acid=' . $this->strFormId . $this->strUrlAdditional . '\',{' . $strConfig . '});});';
		global $objPage;

		// add an old xhtml version if we are in the frontend and the outputFormat is not HTML5
		if (TL_MODE == 'FE' && $objPage->outputFormat != 'html5')
		{
			// add the new auto completer js instance to the site header
			$GLOBALS['TL_HEAD'][] = '<script type="text/javascript">/* <![CDATA[ */ ' . $strBuild . ' /* ]]> */</script>';

			return;
		}
		
		// add the HTML5 version
		$GLOBALS['TL_HEAD'][] = '<script>' . $strBuild . '</script>';
	}
}

?>
