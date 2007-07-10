<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NNumberFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/toolkit/src/lmbMockToolsWrapper.class.php');
lmb_require('limb/i18n/src/toolkit/lmbI18NTools.class.php');

Mock :: generate('lmbI18NTools', 'MockI18NTools');

class lmbI18NNumberFilterTest extends lmbWactTestCase
{
  function testUseDefault()
  {
    $template = '{$"100000"|i18n_number}';

    $this->registerTestingTemplate('/limb/locale_number_filter_default.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_default.html');

    $this->assertEqual($page->capture(), '100,000.00');
  }

  function testUseOtherLocale()
  {
    $locale = $this->toolkit->createLocale('ru');
    $locale->fract_digits = 4;

    $toolkit = new MockI18NTools();
    $toolkit->setReturnReference('createLocale', $locale, array('ru'));

    lmbToolkit :: merge(new lmbMockToolsWrapper($toolkit, array('createLocale')));

    $template = '{$"100000"|i18n_number:"ru"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_russian.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_russian.html');

    $this->assertEqual($page->capture(), '100,000.0000');
  }

  function testUseFractDigits()
  {
    $template = '{$"100000"|i18n_number:"en","3"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_fract_digits.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_fract_digits.html');

    $this->assertEqual($page->capture(), '100,000.000');
  }

  function testUseDecimalSymbol()
  {
    $template = '{$"100000"|i18n_number:"en","",","}';

    $this->registerTestingTemplate('/limb/locale_number_filter_decimal_symbol.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_decimal_symbol.html');

    $this->assertEqual($page->capture(), '100,000,00');
  }

  function testUseThousandSeparator()
  {
    $template = '{$"100000"|i18n_number:"en","",""," "}';

    $this->registerTestingTemplate('/limb/locale_number_filter_thousand_separator.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_thousand_separator.html');

    $this->assertEqual($page->capture(), '100 000.00');
  }

  function testUseRussianAsCurrentLocale()
  {
    $locale = $this->toolkit->createLocale('ru');
    $locale->fract_digits = 4;
    $locale->decimal_symbol = '_';

    $this->toolkit->setLocale($locale);

    $template = '{$"100000"|i18n_number}';

    $this->registerTestingTemplate('/limb/locale_number_filter_russian_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_russian_locale.html');

    $this->assertEqual($page->capture(), '100,000_0000');
  }

  function testDefaultDBE()
  {
    $template = '{$var|i18n_number}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE.html');

    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000.00');
  }

  function testDBEUseOtherLocale()
  {
    $locale = $this->toolkit->createLocale('ru');
    $locale->fract_digits = 4;

    $toolkit = new MockI18NTools();
    $toolkit->setReturnReference('createLocale', $locale, array('ru'));

    lmbToolkit :: merge(new lmbMockToolsWrapper($toolkit, array('createLocale')));

    $template = '{$var|i18n_number:"ru"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_other_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_other_locale.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000.0000');
  }

  function testDBEUseFractDigits()
  {
    $template = '{$var|i18n_number:"en","3"}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_fract_digits.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_fract_digits.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000.000');
  }

  function testDBEUseDecimalSymbol()
  {
    $template = '{$var|i18n_number:"en","",","}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_decimal_symbol.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_decimal_symbol.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000,00');
  }

  function testDBEUseThousandSeparator()
  {
    $template = '{$var|i18n_number:"en","",""," "}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_thousand_separator.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_thousand_separator.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100 000.00');
  }

  function testDBEUseRussianAsCurrentLocale()
  {
    $locale = $this->toolkit->createLocale('ru');
    $locale->fract_digits = 4;
    $locale->decimal_symbol = '_';

    $this->toolkit->setLocale($locale);

    $template = '{$var|i18n_number}';

    $this->registerTestingTemplate('/limb/locale_number_filter_DBE_russian_locale.html', $template);

    $page = $this->initTemplate('/limb/locale_number_filter_DBE_russian_locale.html');
    $page->set('var', '100000');

    $this->assertEqual($page->capture(), '100,000_0000');
  }

}
?>
