<?php
/**
 * Description of MockupController
 *
 * @author ramon
 */
class MockupController extends Controller {
    
    public function actionIndex($view, $ui = NULL) {
        
        if (isset($ui))
            $this->layout = "//layouts/$ui";
        
        $viewPath = "{$this->viewPath}/$view.php";
        if (file_exists($viewPath))
            $this->render($view);
        else
            echo "Mockup not available - please review path";
    }
    
}
