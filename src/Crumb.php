<?php

  namespace Jinx;
  
  class Crumb
  {
    
    protected $attr;
    
    public function __construct(array $attr)
    {  
      
      $this->attr = array_replace([
        'title' => null,
        'url' => null,
      ], $attr);
      
    }
    
    public function __get($name)
    {  
      return array_key_exists($name, $this->attr) ? $this->attr[$name] : null;  
    }
    
    public function __isset($name)
    {
      return array_key_exists($name, $this->attr);
    }
    
  }