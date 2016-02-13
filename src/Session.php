<?php

namespace JetFire\Http;


class Session extends \Symfony\Component\HttpFoundation\Session\Session{


    /**
     * @param $name
     * @return null
     */
    public function take($name)
    {
        $parsed = explode('.', $name);
        $result = null;
        while ($parsed) {
            $next = array_shift($parsed);
            if (!is_null($this->get($next)))
                $result = $this->get($next);
            else
                return null;
        }
        return $result;
    }


    /**
     * @param $name
     * @param $value
     */
    public function put($name, $value)
    {
        $parsed = explode('.', $name);

        $session =& $_SESSION;

        while (count($parsed) > 1) {
            $next = array_shift($parsed);

            if ( ! isset($session[$next]) || ! is_array($session[$next])) {
                $session[$next] = [];
            }

            $session =& $session[$next];
        }

        $session[array_shift($parsed)] = $value;
    }

    /**
     * @param null $key
     * @return $this
     */
    public function destroy($key = null){
        if(is_null($key)){session_unset();}else{unset($_SESSION[$key]);}
        return $this;
    }

} 