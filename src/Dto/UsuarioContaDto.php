<?php

namespace App\Dto;

class UsuarioContaDto{

    private ?string $id= null;
    private ?string $cpf= null;
    private ?string $nome= null;
    private ?string $email= null;
    private ?string $telefone= null;
    private ?string $numeroConta= null;
    private ?string $Saldo= null;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getCpf()
    {
        return $this->cpf;
    }

    public function setCpf($cpf)
    {
        $this-> cpf = $cpf;
        return $this;
    }

    public function getNome()
    {
        return $this->nome;
    }

    public function setNome($nome)
    {
        $this-> nome = $nome;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this-> email = $email;
        return $this;
    }

    public function getTelefone()
    {
        return $this->telefone;
    }

    public function setTelefone($telefone)
    {
        $this-> telefone = $telefone;
        return $this;
    }


    public function getNumeroConta()
    {
        return $this->numeroConta;
    }

    public function setNumeroConta($numeroConta)
    {
        $this->numeroConta = $numeroConta;

        return $this;
    }

        public function getSaldo()
    {
        return $this->Saldo;
    }

    public function setSaldo($saldo)
    {
        $this-> Saldo = $saldo;
        return $this;
    }

}