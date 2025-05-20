<?php

/**
 * Texy! is human-readable text to HTML converter (http://texy.info)
 *
 * Copyright (c) 2004, 2014 David Grudl (https://davidgrudl.com)
 */


// Check PHP configuration
if (version_compare(PHP_VERSION, '5.2.0') < 0) {
	throw new Exception('Texy requires PHP 5.2.0 or newer.');
} elseif (ini_get('zend.ze1_compatibility_mode') % 256 ||
	preg_match('#on$|true$|yes$#iA', ini_get('zend.ze1_compatibility_mode'))
) {
	throw new Exception('Texy cannot run with zend.ze1_compatibility_mode enabled.');
}


// load libraries
require_once dirname(__FILE__) . '/texy/TexyPatterns.php';
require_once dirname(__FILE__) . '/texy/TexyObject.php';
require_once dirname(__FILE__) . '/texy/TexyHtml.php';
require_once dirname(__FILE__) . '/texy/TexyModifier.php';
require_once dirname(__FILE__) . '/texy/TexyModule.php';
require_once dirname(__FILE__) . '/texy/TexyParser.php';
require_once dirname(__FILE__) . '/texy/TexyBlockParser.php';
require_once dirname(__FILE__) . '/texy/TexyLineParser.php';
require_once dirname(__FILE__) . '/texy/TexyUtf.php';
require_once dirname(__FILE__) . '/texy/TexyConfigurator.php';
require_once dirname(__FILE__) . '/texy/TexyHandlerInvocation.php';
require_once dirname(__FILE__) . '/texy/TexyRegexp.php';
require_once dirname(__FILE__) . '/texy/Texy.php';
require_once dirname(__FILE__) . '/texy/modules/TexyImage.php';
require_once dirname(__FILE__) . '/texy/modules/TexyLink.php';
require_once dirname(__FILE__) . '/texy/modules/TexyTableCellElement.php';
require_once dirname(__FILE__) . '/texy/modules/TexyParagraphModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyBlockModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyHeadingModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyHorizLineModule.php';
// require_once dirname(__FILE__) . '/texy/modules/TexyHtmlModule.php';
// require_once dirname(__FILE__) . '/texy/modules/TexyFigureModule.php';
// require_once dirname(__FILE__) . '/texy/modules/TexyImageModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyLinkModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyListModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyLongWordsModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyPhraseModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyBlockQuoteModule.php';
// require_once dirname(__FILE__) . '/texy/modules/TexyScriptModule.php';
// require_once dirname(__FILE__) . '/texy/modules/TexyEmoticonModule.php';
// require_once dirname(__FILE__) . '/texy/modules/TexyTableModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyTypographyModule.php';
require_once dirname(__FILE__) . '/texy/modules/TexyHtmlOutputModule.php';
require_once dirname(__FILE__) . '/texy/compatibility.php';
