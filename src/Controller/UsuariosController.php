<?php

namespace App\Controller;

use App\Dto\UsuarioContaDto;
use App\Dto\UsuarioDto;
use App\Entity\Conta;
use App\Entity\Usuario;
use App\Repository\ContaRepository;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UsuariosController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_criar', methods:['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat: 'json')]
        UsuarioDto $usuarioDto,

        EntityManagerInterface $entityManager,
        UsuarioRepository $usuarioRepository
    ): JsonResponse
    {
        $erros = [];
        if(!$usuarioDto->getCpf()){
            array_push($erros, [
                'message' => 'CPF é obrigatório'
            ]);
        }

        if(!$usuarioDto->getNome()){
            array_push($erros, [
                'message' => 'nome é obrigatório'
            ]);
        }

        if(!$usuarioDto->getEmail()){
            array_push($erros, [
                'message' => 'Email é obrigatório'
            ]);
        }

        if(!$usuarioDto->getTelefone()){
            array_push($erros, [
                'message' => 'Telefone é obrigatório'
            ]);
        }

        if(!$usuarioDto->getSenha()){
            array_push($erros, [
                'message' => 'Senha é obrigatório'
            ]);
        }

        if(count($erros)> 0){
            return $this->json($erros, 422);
        }

        //valida se o cpf ja esta cadastrado
        $usuarioExistente = $usuarioRepository->findByCpf($usuarioDto->getCpf());
        if ($usuarioExistente){
            return $this->json([
                'message' => 'O CPF informado já está cadastrado'
            ], 409);
        }

        //criar um objeto da entidade usuário
        $usuario = new  Usuario();
        $usuario->setCpf($usuarioDto->getCpf());
        $usuario->setNome($usuarioDto->getNome());
        $usuario->setEmail($usuarioDto->getEmail());
        $usuario->setTelefone($usuarioDto->getTelefone());
        $usuario->setSenha($usuarioDto->getSenha());

        //criar registro na tb usuário
        $entityManager->persist($usuario);
        $entityManager->flush();

        //instanciar o objeto na conta
        $conta = new Conta();
        $numeroConta = preg_replace('/\D/', '', uniqid());
        $conta->setNumero($numeroConta);
        $conta->setsaldo('0');
        $conta->setUsuario($usuario);

        //criar registro na tb conta

        $entityManager->persist($conta);
        $entityManager->flush();

        //retornaar os dados de usuário e conta
        $usuarioContaDto = new UsuarioContaDto();
        $usuarioContaDto->setId($usuario->getId());
        $usuarioContaDto->setNome($usuario->getNome());
        $usuarioContaDto->setCpf($usuario->getCpf());
        $usuarioContaDto->setEmail($usuario->getEmail());
        $usuarioContaDto->setTelefone($usuario->getTelefone());
        $usuarioContaDto->setNumeroConta($conta->getNumero());
        $usuarioContaDto->setSaldo($conta->getSaldo());

        return $this->json($usuarioContaDto, status: 201);
    }

    #[Route('/usuarios/{id}', name: 'usuarios_buscar', methods: ['GET'])]
    public function buscarPorId(
        int $id,
        ContaRepository $contaRepository

    ){
        $conta = $contaRepository->findByUsuarioId($id);

        if (!$conta){
            return $this->json([
                'message' => 'Usuário não encontrado!'
            ], status: 404);
        }

        $usuarioContaDto = new UsuarioContaDto();
        $usuarioContaDto->setId($conta->getUsuario()->getId());
        $usuarioContaDto->setNome($conta->getUsuario()->getNome());
        $usuarioContaDto->setCpf($conta->getUsuario()->getCpf());
        $usuarioContaDto->setEmail($conta->getUsuario()->getEmail());
        $usuarioContaDto->setTelefone($conta->getUsuario()->getTelefone());
        $usuarioContaDto->setNumeroConta($conta->getNumero());
        $usuarioContaDto->setSaldo($conta->getSaldo());

        return $this->json($usuarioContaDto);


    }
}
