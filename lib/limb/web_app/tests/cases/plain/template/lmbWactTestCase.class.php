<?php
require_once('limb/web_app/src/template/lmbWactTemplateConfig.class.php');
require_once('limb/wact/tests/cases/WactTestTemplateLocator.class.php');
require_once('limb/datasource/src/lmbArrayDataset.class.php');
require_once('limb/wact/src/WactTemplate.class.php');
require_once('limb/wact/src/compiler/WactDictionaryHolder.class.php');
require_once('limb/validation/src/lmbErrorList.class.php');

class lmbWactTestCase extends UnitTestCase
{
  protected $toolkit;
  protected $locator;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->config = new lmbWactTemplateConfig();
    $this->locator = new WactTestTemplateLocator($this->config);

    $this->initWactDictionaries();
  }

  function initTemplate($template_file)
  {
    return new WactTemplate($template_file, $this->config, $this->locator);
  }

  function tearDown()
  {
    $this->locator->clearTestingTemplates();

    lmbToolkit :: restore();
  }

  function registerTestingTemplate($file, $template)
  {
    $this->locator->registerTestingTemplate($file, $template);
  }

  function initWactDictionaries()
  {
    WactDictionaryHolder :: initialize(new lmbWactTemplateConfig());
  }
}

?>