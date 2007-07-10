<?php
lmb_require('src/model/Category.class.php');
lmb_require('limb/web_app/src/controller/lmbController.class.php');

class ProjectsController extends lmbController
{
  function doDisplay()
  {
    $this->view->findChild('categories')->registerDataset(Category :: findAllCategories());
  }
}

?>
