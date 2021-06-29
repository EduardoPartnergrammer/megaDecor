<?php
namespace App\Http\Middleware;
use Closure;
class Cors
{
  public function handle($request, Closure $next)
  {
    return $next($request)
       //Url a la que se le dará acceso en las peticiones
      ->header("Access-Control-Allow-Origin", '*')
      ->header("Access-Control-Allow-Credentials", "false")
      //Métodos que a los que se da acceso
      ->header("Access-Control-Allow-Methods", "*")
      //Headers de la petición
      ->header("Access-Control-Allow-Headers", "Origin, Content-Type, X-Auth-Token , Authorization"); 
  }
}