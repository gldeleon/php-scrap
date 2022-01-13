<?php

namespace App\traits;

use Illuminate\Support\Facades\Log;

trait handlerExceptions
{
    public function getErrorMessage($e)
    {
        $response = [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ];

        return response(['error' => $response], 500);
    }

    public function logErrorMessage($e, $title = null)
    {
        Log::error('################# An exception has ocurred #################');
        if($title !== null){
            Log::error($title);
        }
        Log::error('message: ' . $e->getMessage());
        Log::error('file: ' . $e->getFile());
        Log::error('line: ' . $e->getLine());
        Log::error('################# END | An exception has ocurred #################');
    }
}
