<?php
App::uses('ErrorHandler', 'Error');

class SorapsErrorHandler extends ErrorHandler {
  public static function handleException(Exception $exception) {
    switch(get_class($exception)) {
      case 'SorapsException':
        // Controllerのインスタンスを作成するために、Request,Responseが必要
        $CakeRequest = new CakeRequest();
        $CakeResponse = new CakeResponse();
        // リダイレクト処理の中で使うため、リクエストされたURLをパース
        $CakeRequest->params = Router::parse($CakeRequest->url);
        // AppControllerをインスタンス化
        $AppController = new AppController($CakeRequest, $CakeResponse);
        // Componentが読み込まれる
        $AppController->constructClasses();
        // エラーメッセージをセットしてリダイレクト処理
        $AppController->redirectIndex($exception->getMessage());
        break;
      default:
        parent::handleException($exception);
    }
  }
}

