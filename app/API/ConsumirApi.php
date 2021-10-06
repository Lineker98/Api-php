<?php

namespace App\API;

use Illuminate\Support\Facades\Http;

class ConsumirApi{
    
    public static function getData($moeda, $dataCotacao){

        $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/CotacaoMoedaDia(moeda=@moeda,dataCotacao=@dataCotacao)?@moeda='$moeda'&@dataCotacao='$dataCotacao'&\$top=1&\$skip=4&\$format=json";
        $respose = Http::get($url);

        return $respose->body();

    }
}