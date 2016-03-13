<?php

namespace JetFire\Http;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirect extends RedirectResponse{

    public function to($url,$status = 302,$type = 'text/html'){
        if(!is_null($status))$this->setStatusCode($status);
        $this->headers->set('Content-type',$type);
        $this->setTargetUrl($url);
        return $this;
    }

    public function back(){
        $this->setTargetUrl($this->request->server['HTTP_REFERER']);
        return $this;
    }

    public function withCookies(array $cookies)
    {
        foreach ($cookies as $cookie)
            $this->headers->setCookie($cookie);
        return $this;
    }


} 