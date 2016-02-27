<?php

namespace JetFire\Http;

use \Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse implements ResponseInterface{

    public function answer($content , $status = 200,$type = 'text/html'){
        $this->setContent($content);
        $this->headers->set('Content-type',$type);
        $this->setStatusCode($status);
        $this->send();
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers = [])
    {
        foreach($headers as $key => $content)
            $this->headers->set($key,$content);
    }

} 