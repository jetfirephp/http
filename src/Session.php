<?php

namespace JetFire\Http;

use \Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * Class Session
 * @package JetFire\Http
 */
class Session extends SymfonySession{


    /**
     * @param $key
     * @return null
     */
    public function take($key)
    {
        $key = str_replace('.','/',$key);
        return $this->get($key);
    }


    /**
     * @param $key
     * @param $value
     */
    public function put($key, $value)
    {
        $key = str_replace('.','/',$key);
        $this->set($key,$value);
    }

    /**
     * @param null $key
     * @return $this
     */
    public function destroy($key = null){
        if(is_null($key)){$this->clear();}else{
            $key = str_replace('.','/',$key);
            $this->remove($key);
        }
        return $this;
    }

    /**
     * @param $key
     * @param $value
     */
    public function flash($key,$value){
        $this->getFlashBag()->add($key,$value);
    }

    /**
     * @param $key
     * @param array $default
     * @return array
     */
    public function getFlash($key,$default = []){
        return $this->getFlashBag()->get($key,$default);
    }

    /**
     * @return array
     */
    public function allFlash(){
        return $this->getFlashBag()->all();
    }
} 