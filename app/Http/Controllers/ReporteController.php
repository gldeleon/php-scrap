<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResponseController as RC;
use Zttp\Zttp;
use App\Services\Cesvi;

class ReporteController extends RC {

    public function specs($vin) {
        /* primero intentamos traer las versiones en bd */
        $vin = strtoupper($vin);
        $validavin = $this->validVin($vin);
        if ($validavin == true) {
            $cesvi = new Cesvi($vin);
            $responseVersions = $cesvi->getVersions();
            /* guardamos las versiones en la bd */
            return $this->sendResponse($responseVersions, 'ok');
        } else {
            return $this->sendError('error', 'El vin no es valido');
        }
    }

    public function vin($plate) {

    }

    public function plates($plate) {

    }

    public function validVin($vin) {
        $uri = getenv('URL_AUTOAPI_V2') . "/vehiculos/$vin/validacion_vin";
        $headers = ['Authorization' => 'Bearer ' . getenv('TOKEN_AUTOAPI'),
            'User-Agent' => getenv('APP_ENV')];
        $response = Zttp::withHeaders($headers)->get($uri);
        if ($response->isOk()) {
            $vinresponse = $response->json();
            if ($vinresponse['data']['es_valido']) {
                return $vinresponse['data']['es_valido'];
            } else {
                return false;
            }
        }
    }

}
