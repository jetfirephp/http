<?php

namespace JetFire\Http;

use RuntimeException;
use SplFileInfo;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Request extends SymfonyRequest{

    public function __construct(){
        parent::__construct(
            $_GET,
            $_POST,
            array(),
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

    /**
     * Return the Request instance.
     *
     * @return $this
     */
    public function instance()
    {
        return $this;
    }

    /**
     * Get the session associated with the request.
     *
     * @throws \RuntimeException
     */
    public function session()
    {
        if (! $this->hasSession()) {
            throw new RuntimeException('Session store not set on request.');
        }
        return $this->getSession();
    }
    /**
     * Get the request method.
     *
     * @return string
     */
    public function method()
    {
        return $this->getMethod();
    }
    /**
     * Get the root URL for the application.
     *
     * @return string
     */
    public function root()
    {
        return rtrim($this->getSchemeAndHttpHost().$this->getBaseUrl(), '/');
    }
    /**
     * Get the URL (no query string) for the request.
     *
     * @return string
     */
    public function url()
    {
        return rtrim(preg_replace('/\?.*/', '', $this->getUri()), '/');
    }
    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl()
    {
        $query = $this->getQueryString();
        return $query ? $this->url().'?'.$query : $this->url();
    }
    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        $pattern = trim($this->getPathInfo(), '/');
        return $pattern == '' ? '/' : $pattern;
    }
    /**
     * Get the current encoded path info for the request.
     *
     * @return string
     */
    public function decodedPath()
    {
        return rawurldecode($this->path());
    }
    /**
     * Get all of the segments for the request path.
     *
     * @return array
     */
    public function segments()
    {
        $segments = explode('/', $this->path());
        return array_values(array_filter($segments, function ($v) {
            return $v != '';
        }));
    }
    /**
     * Determine if the request is the result of an AJAX call.
     *
     * @return bool
     */
    public function ajax()
    {
        return $this->isXmlHttpRequest();
    }
    /**
     * Determine if the request is the result of an PJAX call.
     *
     * @return bool
     */
    public function pjax()
    {
        return $this->headers->get('X-PJAX') == true;
    }
    /**
     * Determine if the request is over HTTPS.
     *
     * @return bool
     */
    public function secure()
    {
        return $this->isSecure();
    }
    /**
     * Returns the client IP address.
     *
     * @return string
     */
    public function ip()
    {
        return $this->getClientIp();
    }
    /**
     * Returns the client IP addresses.
     *
     * @return array
     */
    public function ips()
    {
        return $this->getClientIps();
    }
    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
        $input = $this->all();
        foreach ($keys as $value) {
            if (! array_key_exists($value, $input)) {
                return false;
            }
        }
        return true;
    }
    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : func_get_args();
        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
                return false;
            }
        }
        return true;
    }
    /**
     * Determine if the given input key is an empty string for "has".
     *
     * @param  string  $key
     * @return bool
     */
    protected function isEmptyString($key)
    {
        $value = $this->input($key);
        $boolOrArray = is_bool($value) || is_array($value);
        return ! $boolOrArray && trim((string) $value) === '';
    }
    /**
     * Retrieve a query string item from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function query($key = null, $default = null)
    {
        return $this->retrieveItem('query', $key, $default);
    }
    /**
     * Determine if a cookie is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasCookie($key)
    {
        return ! is_null($this->cookie($key));
    }
    /**
     * Retrieve a cookie from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function cookie($key = null, $default = null)
    {
        return $this->retrieveItem('cookies', $key, $default);
    }
    /**
     * Retrieve a file from the request.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|array|null
     */
    public function file($key = null, $default = null)
    {
        return $this->files->get($key,$default);
    }
    /**
     * Determine if the uploaded data contains a file.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasFile($key)
    {
        if (! is_array($files = $this->file($key))) {
            $files = [$files];
        }
        foreach ($files as $file) {
            if ($this->isValidFile($file)) {
                return true;
            }
        }
        return false;
    }
    /**
     * Check that the given file is a valid file instance.
     *
     * @param  mixed  $file
     * @return bool
     */
    protected function isValidFile($file)
    {
        return $file instanceof SplFileInfo && $file->getPath() != '';
    }
    /**
     * Retrieve a header from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function header($key = null, $default = null)
    {
        return $this->retrieveItem('headers', $key, $default);
    }
    /**
     * Retrieve a server variable from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function server($key = null, $default = null)
    {
        return $this->retrieveItem('server', $key, $default);
    }
    /**
     * Retrieve an old input item.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function old($key = null, $default = null)
    {
        return $this->session()->getOldInput($key, $default);
    }
    /**
     * Flash the input for the current request to the session.
     *
     * @param  string  $filter
     * @param  array   $keys
     * @return void
     */
    public function flash($filter = null, $keys = [])
    {
        $flash = (! is_null($filter)) ? $this->$filter($keys) : $this->all();
        $this->session()->getFlashBag()->set($flash);
    }
    /**
     * Flash only some of the input to the session.
     *
     * @param  array|mixed  $keys
     * @return void
     */
    public function flashOnly($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return $this->flash('only', $keys);
    }
    /**
     * Flash only some of the input to the session.
     *
     * @param  array|mixed  $keys
     * @return void
     */
    public function flashExcept($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        return $this->flash('except', $keys);
    }
    /**
     * Flush all of the old input from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->session()->getFlashBag()->clear();
    }

    /**
     * Retrieve a parameter item from a given source.
     *
     * @param  string  $source
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    protected function retrieveItem($source, $key, $default)
    {
        if (is_null($key)) {
            return $this->$source->all();
        }
        return $this->$source->get($key, $default);
    }
    /**
     * Merge new input into the current request's input array.
     *
     * @param  array  $input
     * @return void
     */
    public function merge(array $input)
    {
        $this->getInputSource()->add($input);
    }
    /**
     * Replace the input for the current request.
     *
     * @param  array  $input
     * @return void
     */
    public function replace(array $input)
    {
        $this->getInputSource()->replace($input);
    }
    /**
     * Get the JSON payload for the request.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if (! isset($this->json)) {
            $this->json = new ParameterBag((array) json_decode($this->getContent(), true));
        }
        if (is_null($key)) {
            return $this->json;
        }
        return $this->json->get($key,$default);
    }
    /**
     * Get the input source for the request.
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->json();
        }
        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }
    /**
     * Determine if the given content types match.
     *
     * @param  string  $actual
     * @param  string  $type
     * @return bool
     */
    public static function matchesType($actual, $type)
    {
        if ($actual === $type) {
            return true;
        }
        $split = explode('/', $actual);
        if (isset($split[1]) && preg_match('/'.$split[0].'\/.+\+'.$split[1].'/', $type)) {
            return true;
        }
        return false;
    }
    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return (strpos($this->header('CONTENT_TYPE')[0], '/json') !== false || strpos($this->header('CONTENT_TYPE')[0], '+json') !== false );
    }
    /**
     * Determine if the current request is asking for JSON in return.
     *
     * @return bool
     */
    public function wantsJson()
    {
        $acceptable = $this->getAcceptableContentTypes();
        return (isset($acceptable[0]) && (strpos($acceptable[0], '/json') !== false || strpos($acceptable[0], '+json') !== false));
    }
    /**
     * Determines whether the current requests accepts a given content type.
     *
     * @param  string|array  $contentTypes
     * @return bool
     */
    public function accepts($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();
        if (count($accepts) === 0) {
            return true;
        }
        $types = (array) $contentTypes;
        foreach ($accepts as $accept) {
            if ($accept === '*/*' || $accept === '*') {
                return true;
            }
            foreach ($types as $type) {
                if ($this->matchesType($accept, $type) || $accept === strtok($type, '/').'/*') {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * Return the most suitable content type from the given array based on content negotiation.
     *
     * @param  string|array  $contentTypes
     * @return string|null
     */
    public function prefers($contentTypes)
    {
        $accepts = $this->getAcceptableContentTypes();
        $contentTypes = (array) $contentTypes;
        foreach ($accepts as $accept) {
            if (in_array($accept, ['*/*', '*'])) {
                return $contentTypes[0];
            }
            foreach ($contentTypes as $contentType) {
                $type = $contentType;
                if (! is_null($mimeType = $this->getMimeType($contentType))) {
                    $type = $mimeType;
                }
                if ($this->matchesType($type, $accept) || $accept === strtok($type, '/').'/*') {
                    return $contentType;
                }
            }
        }
    }

    /**
     * Get all of the input and files for the request.
     *
     * @return array
     */
    public function all(){
        return array_merge($this->request->all(),$this->query->all());
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function input($key = null, $default = null)
    {
        $input = array_merge($this->getInputSource()->all(),$this->query->all());
        return isset($input[$key])?$input[$key]:$default;
    }
    /**
     * Get a subset of the items from the input data.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];
        $input = $this->all();
        foreach ($keys as $key) {
            $results[$key] = $input[$key];
        }
        return $results;
    }
    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];
        $inputs = $this->all();
        foreach ($inputs as $key => $input) {
            if(!in_array($key,$keys))
                $results[$key] = $input[$key];
        }
        return $results;
    }

    public function submit($value = null,$token = []){
        if(is_array($value))$token = $value;
        if(!isset($token['token'])) {
            if (!isset($token['time'])) $token['time'] = 600;
            if (!isset($token['name'])) $token['name'] = '';
            if (!isset($token['referer'])) $token['referer'] = null;
            if (!$this->isToken($token['time'], $token['name'], $token['referer'])) return false;
        }
        if(!is_array($value) && !is_null($value))
            return (isset($_POST[$value]))?true:false;
        return (isset($_POST['submit']))?true:false;
    }

    private function isToken($time, $name = '', $referer = null)
    {
        if (!is_null($this->session()->get($name . '_token')) && !is_null($this->session()->get($name . '_token_time')) && $this->request->get($name . '_token') != '') {
            if ($this->session()->get($name . '_token') == $this->request->get($name . '_token')) {
                if ($this->session()->get($name . '_token_time') >= (time() - $time)) {
                    if (is_null($referer)) return true;
                    else if (!is_null($referer) && $this->server->get('HTTP_REFERER') == $referer) return true;
                    $this->session()->remove($name . '_token');
                    $this->session()->remove($name . '_token_time');
                }
            }
        }
        return false;
    }
} 