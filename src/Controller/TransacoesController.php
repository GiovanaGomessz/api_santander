<?php

namespace App\Controller;

use App\Dto\UsuarioContaDto;
use App\Dto\UsuarioDto;
use App\Dto\TransacoesDto;
use App\Entity\Conta;
use App\Entity\Usuario;
use App\Repository\ContaRepository;
use App\Repository\UsuarioRepository;
use App\Repository\TransacaoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class TransacoesController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_transacoes', methods:['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacoesDto $transacoesDto,

        EntityManagerInterface $entityManager,
        TransacaoRepository $transacaoRepository
    ): JsonResponse
    {
        $erros = [];
        if(!$transacoesDto->getId_conta_origem()){
            array_push($erros, [
                'message' => 'Conta não encontrada'
            ]);
        }

        if(!$transacoesDto->getId_conta_destino()){
            array_push($erros, [
                'message' => 'Conta não encontrada'
            ]);
        }

        if(!$transacoesDto->getValor()< 1){
            array_push($erros, [
                'message' => 'Digite um valor válido!'
            ]);
        }

        if(count($erros)> 0){
            return$this->json($erros, 422);
        }

        return null
    }
}
