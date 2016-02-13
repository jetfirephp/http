<?php

namespace JetFire\Http;

use \Symfony\Component\HttpFoundation\RedirectResponse as SymfonyResponse;

class Response extends SymfonyResponse{

    public function answer($content , $status = 200,$type = 'text/html'){
        $this->setContent($content);
        $this->headers->set('Content-type',$type);
        $this->setStatusCode($status);
        $this->send();
    }

    public function redirect($path,$status = null,$type = 'text/html'){
        if(!is_null($status))$this->setStatusCode($status);
        $this->headers->set('Content-type',$type);
        return $this->setTargetUrl($path);
    }

} 