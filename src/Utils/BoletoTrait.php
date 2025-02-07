<?php

namespace App\Utils;

trait BoletoTrait
{
    public function prepareTicketData(object $mensalidades, object $conta): array
    {
        $mensalidade = getJsonToObject($mensalidades->mensalidades) ?? null;

        $estudante = getJsonToObject($mensalidades->estudante_mensalidade) ?? null;

        if (is_null($mensalidade)) {
            return null;
        }
        
        $dadosBoleto = [
            'numeroConvenio' => $conta->convenio,
            'numeroCarteira' => 17,
            'numeroVariacaoCarteira' => 35,
            'codigoModalidade' => 1,
            'dataEmissao' => brDate(date('Y-m-d')),
            'dataVencimento' => brDate($mensalidade[0]->data_vencimento),
            'valorOriginal' => $mensalidade[0]->valor,
            'valorAbatimento' => 0.00, 
            'valorDesconto' => 0.00, 
            'valorMulta' => 0.00, 
            'valorJurosMora' => 0.00, 
            'numeroTituloCliente' => '000' . $conta->convenio . str_pad($mensalidade[0]->id , 10 , '7' , STR_PAD_LEFT), 
            'pagador' => [
                'tipoInscricao' => 1, // 1 para CPF, 2 para CNPJ
                'numeroInscricao' => removeCaracteresEspeciais($estudante->responsavel->cpf), // CPF ou CNPJ
                'nome' => $estudante->responsavel->nome
            ]
        ];

        return $dadosBoleto;
    }
}