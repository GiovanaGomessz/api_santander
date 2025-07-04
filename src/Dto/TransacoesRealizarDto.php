<?php

namespace App\Dto;

class TransacoesRealizarDto{
    private ?string $idContaOrigem= null;
    private ?string $idContaDestino= null;
    private ?string $valor= null;


    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }


    /**
     * Get the value of idContaDestino
     */ 
    public function getIdContaDestino()
    {
        return $this->idContaDestino;
    }

    /**
     * Set the value of idContaDestino
     *
     * @return  self
     */ 
    public function setIdContaDestino($idContaDestino)
    {
        $this->idContaDestino = $idContaDestino;

        return $this;
    }

    /**
     * Get the value of idContaOrigem
     */ 
    public function getIdContaOrigem()
    {
        return $this->idContaOrigem;
    }

    /**
     * Set the value of idContaOrigem
     *
     * @return  self
     */ 
    public function setIdContaOrigem($idContaOrigem)
    {
        $this->idContaOrigem = $idContaOrigem;

        return $this;
    }
}