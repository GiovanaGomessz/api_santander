<?php

namespace App\Dto;

class TransacoesDto{
    private ?string $id_conta_origem= null;
    private ?string $id_conta_destino= null;
    private ?string $valor= null;


    public function getId_conta_origem()
    {
        return $this->id_conta_origem;
    }

    public function setId_conta_origem($id_conta_origem)
    {
        $this->id_conta_origem = $id_conta_origem;

        return $this;
    }


    public function getId_conta_destino()
    {
        return $this->id_conta_destino;
    }

    public function setId_conta_destino($id_conta_destino)
    {
        $this->id_conta_destino = $id_conta_destino;

        return $this;
    }



    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }
}