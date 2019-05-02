<?php

    public function prepararFormasRecebimento($movimentacoes, $formasRecebimento=array(), $idPessoa){

        $primeiraDespesa = array_shift($movimentacoes);
        $formaRecebimento = array(
            'ID_FORMARECEBIMENTO_FRP' => $primeiraDespesa['ID_FORMARECEBIMENTO_FRP'],
            'ID_CONTABANCO_CB' => $primeiraDespesa['ID_CONTABANCO_CB'],
            'VL_VALOR_RPE' => $primeiraDespesa['VL_VALOR_MOP'],
        );

        foreach($movimentacoes as $indice => $movimentacao){
            $ehMesmaForma = ($formaRecebimento['ID_FORMARECEBIMENTO_FRP'] == $movimentacao['ID_FORMARECEBIMENTO_FRP']);
            $ehMesmaConta = ($formaRecebimento['ID_CONTABANCO_CB'] == $movimentacao['ID_CONTABANCO_CB']);

            if( $ehMesmaForma && $ehMesmaConta ){
                $formaRecebimento['VL_VALOR_RPE'] += $movimentacao['VL_VALOR_MOP'];
                unset($movimentacoes[$indice]);
            }
        }

        $formasRecebimento = array_merge($formasRecebimento, array($formaRecebimento));

        if(empty($movimentacoes)){
            return self::getDadosComplementaresFormasRecebimento($formasRecebimento, $idPessoa);
        }
        
        return self::prepararFormasRecebimento($movimentacoes, $formasRecebimento, $idPessoa);
    }

    public function getDadosComplementaresFormasRecebimento($formasRecebimento, $idPessoa){

        $helpersProprietario = new Helpers_Proprietarios();
        $dadosFormasRecebimento = $helpersProprietario->getFormasRecebimento($idPessoa, true);

        $dtAtual = new Superlogica_Date();

        $_formasRecebimento = array();
        foreach($formasRecebimento as $indice => $formaRecebimento){
            foreach($dadosFormasRecebimento as $dadosFormaRecebimento){
                if($formaRecebimento['ID_FORMARECEBIMENTO_FRP'] == $dadosFormaRecebimento['ID_FORMARECEBIMENTO_FRP']){
                    $dadosComplementares = array(
                        'DT_VENCIMENTO_RPE' => $dtAtual->toString('m/d/Y'),
                        'ST_FORMARECEBIMENTO_RPE' => $dadosFormaRecebimento['DESCRICAO_FORMA'],
                        'ST_DADOSBANCARIOS_RPE' => $dadosFormaRecebimento['DESCRICAO_BANCARIA'],
                    );
                    $_formasRecebimento[] = array_merge($formasRecebimento[$indice], $dadosComplementares, $dadosFormaRecebimento);
                }
            }
        }

        return $_formasRecebimento;
    }