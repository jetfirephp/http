<?php

namespace JetFire\Http;

use Symfony\Component\HttpFoundation\RedirectResponse;

class Redirect extends RedirectResponse{

    protected $session;
    protected $request;

    public function setSession(Session $session){
        $this->session = $session;
    }

    public function setRequest(Request $request){
        $this->request = $request;
    }

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

    public function with($key, $value = null)
    {
        $key = is_array($key) ? $key : [$key => $value];
        foreach ($key as $k => $v)
            $this->session->flash($k, $v);
        return $this;
    }

    public function withCookies(array $cookies)
    {
        foreach ($cookies as $cookie)
            $this->headers->setCookie($cookie);
        return $this;
    }


} 