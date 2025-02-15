<?php

namespace App\Utils;

use DateTime;

trait BoletoTrait
{
    public function prepareTicketData($mensalidade, object $conta): array
    {
        if (is_null($mensalidade)) {
            return null;
        }

        $dataVencimento = new DateTime($mensalidade->data_vencimento);
        $dataEmissao = new DateTime($mensalidade->created_at);

        $jurosMora = [
            'tipo' => 1, // 1 = Valor por dia de atraso
            'valor' => 0.33 // Juros de 0.33 por dia
        ];
    
        $multa = [
            'tipo' => 2, // 2 = Percentual da Multa
            'data' => $dataVencimento->modify('+1 day')->format('d.m.Y'), // Um dia após o vencimento
            'porcentagem' => 2.00 // 2% de multa
        ];
        
        $dadosBoleto = [
                "numeroConvenio" => $conta->convenio,
                "numeroCarteira" => 17,
                "numeroVariacaoCarteira" => 19,
                "codigoModalidade" => 1,
                "dataEmissao" => $dataEmissao->format('d.m.Y'),
                "dataVencimento" => $dataVencimento->modify('-1 day')->format('d.m.Y'),
                "valorOriginal" => $mensalidade->valor,
                "valorAbatimento" => 0,
                "quantidadeDiasProtesto" => 5,
                "quantidadeDiasNegativacao" => 90,
                "orgaoNegativador" => 10,
                "indicadorAceiteTituloVencido" => "S",
                "numeroDiasLimiteRecebimento" => 0,
                "codigoAceite" => "A",
                "codigoTipoTitulo" => 99,
                "descricaoTipoTitulo" => "Mensalidade Escolar",
                "indicadorPermissaoRecebimentoParcial" => "N",
                "numeroTituloBeneficiario" => "",
                "campoUtilizacaoBeneficiario" => "",
                "numeroTituloCliente" => '000' . $conta->convenio . str_pad($mensalidade->id , 10 , '0' , STR_PAD_LEFT),
                "mensagemBloquetoOcorrencia" => "",                
                "jurosMora" => $jurosMora,
                "multa" => $multa,
                "pagador" => [
                  "tipoInscricao" => 1,
                  "numeroInscricao" => removeCaracteresEspeciais($mensalidade->responsavel_cpf),
                  "nome" => $mensalidade->responsavel_nome                  
                ],                
                "indicadorPix" => "S"
            ];

        return $dadosBoleto;
    }
}