<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

class Handler extends ExceptionHandler
{
  // Lista de exceções que não devem ser reportadas
  protected $dontReport = [
    //
  ];

  // Lista de entradas que não devem ser expostas em validação
  protected $dontFlash = [
    'current_password',
    'password',
    'password_confirmation',
  ];

  // Reporta a exceção
  public function report(Throwable $exception)
  {
    parent::report($exception);
  }

  // Renderiza a exceção para a resposta HTTP
  public function render($request, Throwable $exception)
  {
    return parent::render($request, $exception);
  }

  // Aqui que ajustamos a resposta para autenticação falha
  protected function unauthenticated($request, AuthenticationException $exception)
  {
    if ($request->expectsJson()) {
      return response()->json(['error' => 'Não autenticado.'], 401);
    }

    return redirect()->guest(route('login'));
  }
}
