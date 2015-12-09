<?php
namespace EWW\Dpf\Controller;

class DocumentFormController extends AbstractDocumentFormController {  
  
  
  public function __construct() {
    parent::__construct();
                   
  }
  
  protected function redirectToList($success=FALSE) {
    $this->redirect('list','DocumentForm',NULL,array('success' => $success));    
  }
  
  
  /**
    * action create
    *
    * @param \EWW\Dpf\Domain\Model\DocumentForm $newDocumentForm
    * @return void
    */
    public function createAction(\EWW\Dpf\Domain\Model\DocumentForm $newDocumentForm) {
        
        foreach ( $newDocumentForm->getNewFiles() as $newFile ) {                                      
            if (empty($newFile->getUID())) {
                $newFile->setDownload(TRUE);
            }    
            $files[] = $newFile;
        }
        
        $newDocumentForm->setNewFiles($files);
        
        parent::createAction($newDocumentForm);
    }  
}

?>
