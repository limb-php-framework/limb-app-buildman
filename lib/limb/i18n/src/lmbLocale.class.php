<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLocale.class.php 4998 2007-02-08 15:36:32Z pachanga $
 * @package    i18n
 */
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require('limb/i18n/src/lmbLocaleSpec.class.php');

/*
Handles locale information and can format time, date, numbers and currency
for correct display for a given locale. The locale conversion uses plain numerical values for
dates, times, numbers and currency, if you want more elaborate conversions consider using the
date, time, date_time and currency classes.

Countries are specified by the ISO 3166 country Code
http://www.iso.ch/iso/en/prods-services/iso3166ma/index.html
User-assigned code elements
http://www.iso.ch/iso/en/prods-services/iso3166ma/04background-on-iso-3166/reserved-and-user-assigned-codes.html#userassigned

language is specified by the ISO 639 language Code
http://www.w3.org/WAI/ER/IG/ert/iso639.htm

currency/funds are specified by the ISO 4217
http://www.bsi-global.com/Technical+Information/Publications/_Publications/tig90.xalter
*/

@define('LIMB_LOCALE_DIR', lmb_resolve_include_path('limb/i18n/i18n/locale/'));

class lmbLocale
{
  var $is_valid = false;

  var $date_format = ''; // format of dates
  var $short_date_format = ''; // format of short dates
  var $time_format = ''; // format of times
  var $date_time_format = '';
  var $short_date_time_format = '';
  var $short_date_short_time_format = '';
  var $short_time_format = ''; // format of short times
  var $is_monday_first = false; // true if monday is the first day of the week
  var $am_name = 'am';
  var $pm_name = 'pm';
  var $charset = '';
  var $LC_ALL = '';
  // numbers
  var $decimal_symbol = '';
  var $thousand_separator = '';
  var $fract_digits = '';
  var $negative_symbol = '';
  var $positive_symbol = '';
  // currency
  var $currency_name = '';
  var $currency_short_name = '';
  var $currency_decimal_symbol = '';
  var $currency_thousand_separator = '';
  var $currency_fract_digits = '';
  var $currency_negative_symbol = '';
  var $currency_positive_symbol = '';
  var $currency_symbol = '';
  var $currency_positive_format = '';
  var $currency_negative_format = '';
  // help arrays
  var $short_month_names = array();
  var $long_month_names = array();
  var $short_day_names = array();
  var $long_day_names = array();
  var $week_days = array(0, 1, 2, 3, 4, 5, 6);
  var $months = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11);

  var $country = '';
  var $country_comment = '';
  var $language_comment = '';

  var $language_name = ''; // name of the language
  var $intl_language_name = ''; // internationalized name of the language
  var $language_direction = 'ltr';

  var $locale_spec;

  function lmbLocale($ini, $locale_spec)
  {
    $this->locale_spec = $locale_spec;

    $this->initLocaleSettings($ini);
  }

  function initLocaleSettings($locale_ini)
  {
    if(!is_a($locale_ini, 'lmbIni'))
      throw new lmbInvalidArgumentException('argument must be an lmbIni instance');

    $this->_initCountrySettings($locale_ini);
    $this->_initLanguageSettings($locale_ini);
  }

  function isValid()
  {
    return $this->is_valid;
  }

  protected function _initCountrySettings($country_ini)
  {
    $country_ini->assignOption($this->time_format, 'time_format', 'date_time');
    $country_ini->assignOption($this->short_time_format, 'short_time_format', 'date_time');
    $country_ini->assignOption($this->date_format, 'date_format', 'date_time');
    $country_ini->assignOption($this->short_date_format, 'short_date_format', 'date_time');
    $country_ini->assignOption($this->date_time_format, 'date_time_format', 'date_time');
    $country_ini->assignOption($this->short_date_time_format, 'short_date_time_format', 'date_time');
    $country_ini->assignOption($this->short_date_short_time_format, 'short_date_short_time_format', 'date_time');

    if($country_ini->hasOption('is_monday_first', 'date_time'))
      $this->is_monday_first = strtolower($country_ini->getOption('is_monday_first', 'date_time')) == 'yes';

    if($this->is_monday_first)
      $this->week_days = array(1, 2, 3, 4, 5, 6, 0);
    else
      $this->week_days = array(0, 1, 2, 3, 4, 5, 6);

    $country_ini->assignOption($this->country, 'country', 'regional_settings');
    $country_ini->assignOption($this->country_comment, 'country_comment', 'regional_settings');

    $country_ini->assignOption($this->decimal_symbol, 'decimal_symbol', 'numbers');
    $country_ini->assignOption($this->thousand_separator, 'thousands_separator', 'numbers');
    $country_ini->assignOption($this->fract_digits, 'fract_digits', 'numbers');
    $country_ini->assignOption($this->negative_symbol, 'negative_symbol', 'numbers');
    $country_ini->assignOption($this->positive_symbol, 'positive_symbol', 'numbers');

    $country_ini->assignOption($this->currency_decimal_symbol, 'decimal_symbol', 'currency');
    $country_ini->assignOption($this->currency_name, 'name', 'currency');
    $country_ini->assignOption($this->currency_short_name, 'short_name', 'currency');
    $country_ini->assignOption($this->currency_thousand_separator, 'thousands_separator', 'currency');
    $country_ini->assignOption($this->currency_fract_digits, 'fract_digits', 'currency');
    $country_ini->assignOption($this->currency_negative_symbol, 'negative_symbol', 'currency');
    $country_ini->assignOption($this->currency_positive_symbol, 'positive_symbol', 'currency');
    $country_ini->assignOption($this->currency_symbol, 'symbol', 'currency');
    $country_ini->assignOption($this->currency_positive_format, 'positive_format', 'currency');
    $country_ini->assignOption($this->currency_negative_format, 'negative_format', 'currency');
  }

  protected function _initLanguageSettings($language_ini)
  {
    $language_ini->assignOption($this->language_name, 'language_name', 'regional_settings');
    $language_ini->assignOption($this->intl_language_name, 'international_language_name', 'regional_settings');
    $language_ini->assignOption($this->language_comment, 'language_comment', 'regional_settings');
    $language_ini->assignOption($this->language_direction, 'language_direction', 'regional_settings');
    $language_ini->assignOption($this->LC_ALL, 'LC_ALL', 'regional_settings');

    $charset = false;
    if($language_ini->hasOption('preferred', 'charset'))
    {
      $charset = $language_ini->getOption('preferred', 'charset');
      if($charset != '')
        $this->charset = $charset;
    }

    if(!is_array($this->short_day_names))
      $this->short_day_names = array();
    if(!is_array($this->long_day_names))
      $this->long_day_names = array();

    foreach ($this->week_days as $day)
    {
      if($language_ini->hasOption($day, 'short_day_names'))
        $this->short_day_names[$day] = $language_ini->getOption($day, 'short_day_names');
      if($language_ini->hasOption($day, 'long_day_names'))
        $this->long_day_names[$day] = $language_ini->getOption($day, 'long_day_names');
    }

    if(!is_array($this->short_month_names))
      $this->short_month_names = array();
    if(!is_array($this->long_month_names))
      $this->long_month_names = array();

    foreach ($this->months as $month)
    {
      if($language_ini->hasOption($month, 'short_month_names'))
        $this->short_month_names[$month] = $language_ini->getOption($month, 'short_month_names');
      if($language_ini->hasOption($month, 'long_month_names'))
        $this->long_month_names[$month] = $language_ini->getOption($month, 'long_month_names');
    }

    if(!is_array($this->short_day_names))
      $this->short_day_names = array();
    if(!is_array($this->long_day_names))
      $this->long_day_names = array();

    foreach($this->week_days as $wday)
    {
      if($language_ini->hasOption($wday, 'short_day_names'))
        $this->short_day_names[$wday] = $language_ini->getOption($wday, 'short_day_names');
      if($language_ini->hasOption($wday, 'long_day_names'))
        $this->long_day_names[$wday] = $language_ini->getOption($wday, 'long_day_names');
    }
  }

  function getLocaleSpec()
  {
    return $this->locale_spec;
  }

  function getLocaleString()
  {
    return $this->locale_spec->getLocaleString();
  }

  function getLanguage()
  {
    return $this->locale_spec->getLanguage();
  }

  function setPHPLocale()
  {
    setlocale(LC_ALL, $this->LC_ALL);
  }

  function getCharset()
  {
    return $this->charset;
  }

  function getLanguageDirection()
  {
    return $this->language_direction;
  }

  function getCountryName()
  {
    return $this->country;
  }

  function getCountryComment()
  {
    return $this->country_comment;
  }

  function getLanguageComment()
  {
    return $this->language_comment;
  }

  function getLanguageName()
  {
    return $this->language_name;
  }

  function getIntlLanguageName()
  {
    return $this->intl_language_name;
  }

  function getCurrencySymbol()
  {
    return $this->currency_symbol;
  }

  function getCurrencyName()
  {
    return $this->currency_name;
  }

  function getCurrencyShortName()
  {
    return $this->currency_short_name;
  }

  function getTimeFormat()
  {
    return $this->time_format;
  }

  function getShortTimeFormat()
  {
    return $this->short_time_format;
  }

  function getDateFormat()
  {
    return $this->date_format;
  }

  function getShortDateFormat()
  {
    return $this->short_date_format;
  }

  function getShortDateTimeFormat()
  {
    return $this->short_date_time_format;
  }

  function getShortDateShortTimeFormat()
  {
    return $this->short_date_short_time_format;
  }

  function getDateTimeFormat()
  {
    return $this->date_time_format;
  }

  function isMondayFirst()
  {
    return $this->is_monday_first;
  }

  function getWeekDays()
  {
    return $this->week_days;
  }

  function getMonths()
  {
    return $this->months;
  }

  function getWeekDayNames($short = false)
  {
    if($short)
      return $this->short_day_names;
    else
      return $this->long_day_names;
  }

  function getMonthNames($short = false)
  {
    if($short)
      return $this->short_month_names;
    else
      return $this->long_month_names;
  }

  function getMeridiemName($hour)
  {
    return ($hour < 12) ? $this->am_name : $this->pm_name;
  }

  function getPmName()
  {
    return $this->pm_name;
  }

  function getAmName()
  {
    return $this->am_name;
  }

  function getDayName($num, $short = false)
  {
    if($num < 0 || $num > 6)
      return null;

    if($short)
      return $this->short_day_names[$num];
    else
      return $this->long_day_names[$num];
  }

  function getMonthName($num, $short = false)
  {
    if($num < 0 || $num > 11)
      return null;

    if($short)
      return $this->short_month_names[$num];
    else
      return $this->long_month_names[$num];
  }

  static function create($locale_id, $dir = LIMB_LOCALE_DIR)
  {
    $spec = new lmbLocaleSpec($locale_id);
    $file = $spec->getLocaleString();
    $locale_ini = new lmbCachedIni($dir . '/' . $file . '.ini');

    if(!$variation = $spec->getCountryVariation())
      return new lmbLocale($locale_ini, $spec);

    list($base_file,) = explode('@', $file);

    $base_ini = new lmbCachedIni($dir . '/' . $base_file . '.ini');
    return new lmbLocale($base_ini->mergeWith($locale_ini), $spec);
  }
}
?>