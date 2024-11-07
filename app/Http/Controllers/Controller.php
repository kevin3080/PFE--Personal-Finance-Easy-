<?php

namespace App\Http\Controllers;

/**
* @OA\Info(title="Personal Finance API", version="1.0")
*
* @OA\Server(url="http://localhost:8000")
*
* @OA\SecurityScheme(
*     securityScheme="bearerAuth",
*     type="http",
*     scheme="bearer",
*     bearerFormat="JWT",
*     description="Se requiere un token de acceso Bearer JWT"
* )
*/
abstract class Controller
{
    //
}
