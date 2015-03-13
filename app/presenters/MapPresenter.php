<?php

namespace App\Presenters;

use Nette\Utils\Validators,
    Nette\Application\Responses\JsonResponse;

/**
 * Map presenter = everything in this something.
 */
class MapPresenter extends BasePresenter {
    
    private $allowWithoutAjax = false;

    /** @inject @var \App\Model\Wall */
    public $wall;
    
    protected function beforeRender() {
        parent::beforeRender();
        $this->allowWithoutAjax = $this->context->parameters['debugMode'] ? true : false;
    }
    
    public function renderMap($x1, $y1, $x2, $y2, $lastUpdate = null) {

        if (Validators::isNumericInt($x1) && Validators::isNumericInt($y1) && Validators::isNumericInt($x2) && Validators::isNumericInt($y2)) {
            if ($this->isAjax() || $this->allowWithoutAjax) {
                
                $map = $this->wall->loadMap($x1, $y1, $x2, $y2, $lastUpdate);

                $this->sendResponse($map);
            } else {
                throw new \Nette\Application\ForbiddenRequestException("Only avaleible trought ajax.");
            }
        } else {
            throw new \Nette\InvalidArgumentException("Argumets are missing");
        }
    }
    
    public function renderSearch($search) {

        if ($this->isAjax() || $this->allowWithoutAjax) {
            
            $searchResult = $this->wall->search($search);

            if ($searchResult instanceof \App\Model\WallError) {
                $this->simpleResponse($searchResult);
            } else {
                $this->sendResponse($searchResult);
            }
        } else {
            throw new \Nette\Application\ForbiddenRequestException("Only avaleible trought ajax.");
        }
    }

    private function simpleResponse($response) {
        if ($response instanceof \App\Model\WallError) {
            $this->sendResponse(new JsonResponse(array('response' => $response->errorType, 'message' => $response->errorMessage)));
        } else {
            $this->sendResponse(new JsonResponse(array('response' => 'success')));
        }
    }

}