<?php

namespace App\Controller;

use App\Dto\ContaDto;
use App\Dto\TransacoesExtratoDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\TransacoesRealizarDto;
use App\Repository\ContaRepository;
use App\Entity\Transacao;
use App\Repository\TransacaoRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/api')]
final class TransacoesController extends AbstractController
{
    #[Route('/transacoes', name: 'transacoes_realizar', methods: ['POST'])]
    public function realizar(
        #[MapRequestPayload(acceptFormat: 'json')]
        TransacoesRealizarDto $entrada,

        ContaRepository $contaRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        // 1. validar o DTO de entrada
        $erros = [];
        if(!$entrada->getIdContaOrigem()){
            array_push($erros, [
                'message' => 'Insira uma conta de origem válida!'
            ]);
        }

        if(!$entrada->getIdContaDestino()){
            array_push($erros, [
                'message' => 'Insira uma conta de destino válida!'
            ]);
        }

        if(!$entrada->getValor()){
            array_push($erros, [
                'message' => 'Valor é obrigatório!'
            ]);
        }
        
        if(!$entrada->getValor() || $entrada->getValor() <= 0){
            array_push($erros, [
                'message' => 'Valor tem que ser maior que zero!'
            ]);
        }

        // 2. validar se as contas são iguais 
        if(!$entrada->getIdContaOrigem() === $entrada->getIdContaDestino()){
            array_push($erros, [
                'message' => 'As contas devem ser distintas!'
            ]);
        }

        // 3. validar se as contas existem
        //dd($entrada);
        $contaOrigem= $contaRepository->findByUsuarioId($entrada->getIdContaOrigem());
        if(!$contaOrigem){
            return $this->json([
                'message' => 'Conta de origem não encontrada!'
            ], 404);
        }

        $contaDestino = $contaRepository->findByUsuarioId($entrada->getIdContaDestino());
        if(!$contaDestino){
            return $this->json([
                'message' => 'Conta de destino não encontrada!'
            ], 404);
        }

        
        //4.validar se a origem tem saldo suficiente
        if ((float) $contaOrigem->getSaldo() < (float) $entrada->getValor()){
            return $this->json([
                'menssage' => 'Saldo insuficiente!'
            ]);
        }

        //5.realizar a transação e salvar no banco 
        $saldo = (int) $contaOrigem->getSaldo();
        $valor = (float) $entrada->getValor();
        $saldoDestino = (float) $contaDestino->getSaldo();

        $contaOrigem->setSaldo( $saldo - $valor);
        $entityManager->persist(($contaOrigem));

        $contaDestino->setSaldo($valor + $saldoDestino);
        $entityManager->persist(($contaDestino));

        $transacao = new Transacao();
        $transacao->setDataHora(new DateTime());
        $transacao->setValor($entrada->getValor());
        $transacao->setContaOrigem($contaOrigem);
        $transacao->setContaDestino($contaDestino);
        $entityManager->persist($transacao);

        $entityManager->flush();
         

        return $this->json([
            'message' => 'Transação realizada com sucesso!',
        ], 204);

        //DICA: caso precise retornar vazio use     ------>      /*return new Response(status: 204);*/


        if(count($erros)> 0){
            return $this->json($erros, 422);
        }


        dd((float) $entrada->getValor());

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TransacoesController.php',
        ]);
    }

    /*
    Entrada:
        idUsuario = quem deseja ver o seu extrato

    Processamento:
        validar se o usuário existe

        buscar as transações

        montar a saída

        --extrato conforme a saida--

    Saída:
    [
        { "id": 1,
            "valor": "199,90",
            "dataHora": "2025-07-04 00:11:33",
            "tipo": "", //RECEBEU / ENVIOU
            "origem": {
                "id": "",
                "cpf": "",
                "nome": "",
                "numeroConta": "",
            },
            "destino": {
                "id": "",
                "cpf": "",
                "nome": "",
                "numeroConta": "",
            },
        }
    ]
    */

    #[Route('/transacoes/{idUsuario}/extrato', name: 'tranasacoes_extrato', methods:['GET'])]
    public function gerarExtrato(
        int $idUsuario,
        ContaRepository $contaRepository,
        TransacaoRepository $transacaoRepository
    ): JsonResponse {
        $conta = $contaRepository->findByUsuarioId($idUsuario);
        if (!$conta){
            return$this->json([
                'menssage' => 'Usuário não encontrado!'
            ], 404);
        }

        $transacoes =  $transacaoRepository->findByContaOrigemAndContaDestino($conta->getId());

        $saida = [];
        foreach ($transacoes as $transacao){
            //getter e setter

            $transacaoDto = new TransacoesExtratoDto();
            $transacaoDto->setId($transacao->getId());
            $transacaoDto->setValor($transacao->getValor());            
            $transacaoDto->setDataHora($transacao->getDataHora());       
            if ($conta->getUsuario()->getId() === $transacao->getContaOrigem()->getId()){
                $transacaoDto->setTipo('ENVIOU');
            } else if ($conta->getId() === $transacao->getContaDestino()->getId()) {
                $transacaoDto->setTipo('RECEBEU');
            };
            
            //origem
            $origem = $transacao->getContaOrigem();
            $contaOrigemDto = new ContaDto();  

            $contaOrigemDto->setId($origem->getUsuario()->getId());
            $contaOrigemDto->setNome($origem->getUsuario()->getNome());
            $contaOrigemDto->setCpf($origem->getUsuario()->getCpf());
            $contaOrigemDto->setNomeConta($origem->getUsuario()->getNome());

            $transacaoDto->setOrigem(($contaOrigemDto));

            //destino
            $destino= $transacao->getContaDestino();
            $contaDestinoDto = new ContaDto();  

            $contaDestinoDto->setId($destino->getUsuario()->getId());
            $contaDestinoDto->setNome($destino->getUsuario()->getNome());
            $contaDestinoDto->setCpf($destino->getUsuario()->getCpf());
            $contaDestinoDto->setNomeConta($destino->getUsuario()->getNome());

            $transacaoDto->setDestino(($contaDestinoDto));

            array_push($saida, $transacaoDto);
        }

        return $this->json($saida);
    }
}