<?php

namespace App\Service;

class MessageResponse
{
    private $msg;

    /**
     * Get the value of message
     */ 
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */ 
    public function setMsg($msg)
    {
        $this->msg[] = $msg;
        return $this;
    }
}