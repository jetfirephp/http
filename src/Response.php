<?php

namespace JetFire\Http;

use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse{

    public function answer($content , $status = 200,$type = 'text/html'){
        $this->setContent($content);
        $this->headers->set('Content-type',$type);
        $this->setStatusCode($status);
        $this->send();
    }

} 