<?php
class SorapsException extends HttpException {
  public function __construct($type = null, $_message = null) {
    switch($type) {
      case 'BadRequestMethod':
        $message = 'リクエストメソッドが不正です';
        $code    = 400;
        break;
      case 'BadLoginRole':
        $message = false;
        $code    = 403;
        break;
      case 'RequireAccessibleChildAccount':
        $message = '子アカウントにアクセスできません';
        $code    = 403;
        break;
      case 'RequireClientAccount':
        $message = 'クライアントアカウント以下のアクセスではありません';
        $code    = 403;
        break;
      case 'BadCampaignType':
        $message = 'キャンペーンの種類が不正です';
        $code    = 403;
        break;
      case 'BadEditType':
        $message = '編集の種類が不正です';
        $code    = 403;
        break;
      case 'BadEntryType':
        $message = '応募方法が不正です';
        $code    = 403;
        break;
      case 'Entried':
        $message = '応募済みです';
        $code    = 403;
        break;
      case 'RequireSocialLogin':
        $message = 'アプリ認証が必要です';
        $code    = 403;
        break;
      case 'NotFound':
        $message = ($_message ? $_message : 'データ') . 'が見つかりません';
        $code    = 404;
        break;
      default:
        $message = 'エラーが発生しました';
        $code    = 400;
    }
    parent::__construct($message, $code);
  }
}
