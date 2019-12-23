<?php

/**
 * Class OauthController
 * @property Facebook $Facebook
 */
class OauthController extends AppController
{
    public $uses = array();

    public $libs = [
        'Facebook' => 'Ique.IqueFacebook',
        'Twitter' => 'Ique.IqueTwitter',
    ];

    public function facebookLogin()
    {
        $redirectUri = $this->request->query('redirect_uri');
        if (!$redirectUri) {
            $redirectUri = $this->referer();
        }
        $this->setRedirectUriAfterFacebookLogin($redirectUri);

        $facebookLoginUrl = $this->Facebook->getLoginUrl([
                'redirect_uri' => Router::url(['action' => 'facebookCallback'], true),
                'scope' => Configure::read('fbscope'),
            ]) . '&auth_type=rerequest';
        return $this->redirect($facebookLoginUrl);
    }

    public function facebookCallback()
    {
        // Facebook認証を実行する
        $user = $this->Facebook->getUser();
        if (!$user) {
            throw new BadRequestException('Facebook Authentication Failed');
        }

        // コールバック処理直後のリダイレクトであることを記憶する
        $this->Session->write('Oauth.Facebook.AfterCallback', true);

        // もとのページへリダイレクトする
        $redirectUrl = $this->getRedirectUriAfterFacebookLogin();
        if (!$redirectUrl) {
            throw new BadRequestException();
        }
        return $this->redirect($redirectUrl);
    }

    protected function setRedirectUriAfterFacebookLogin($uri)
    {
        return $this->Session->write('Oauth.Facebook.RedirectUriAfterLogin', $uri);
    }

    protected function getRedirectUriAfterFacebookLogin()
    {
        return $this->Session->read('Oauth.Facebook.RedirectUriAfterLogin');
    }

    public function twitterLogin()
    {
        $redirectUri = $this->request->query('redirect_uri');
        if (!$redirectUri) {
            $redirectUri = $this->referer();
        }
        $this->setRedirectUriAfterTwitterLogin($redirectUri);

        $twitterLoginUrl = $this->Twitter->getLoginUrl(['action' => 'twitterCallback']);
        return $this->redirect($twitterLoginUrl);
    }

    public function twitterCallback()
    {
        // Twitter認証を実行する
        $user = $this->Twitter->getUser();
        if (!$user) {
            throw new BadRequestException('Twitter Authentication Failed');
        }

        // コールバック処理直後のリダイレクトであることを記憶する
        $this->Session->write('Oauth.Twitter.AfterCallback', true);

        // もとのページへリダイレクトする
        $redirectUrl = $this->getRedirectUriAfterTwitterLogin();
        if (!$redirectUrl) {
            throw new BadRequestException();
        }
        return $this->redirect($redirectUrl);
    }

    protected function setRedirectUriAfterTwitterLogin($uri)
    {
        return $this->Session->write('Oauth.Twitter.RedirectUriAfterLogin', $uri);
    }

    protected function getRedirectUriAfterTwitterLogin()
    {
        return $this->Session->read('Oauth.Twitter.RedirectUriAfterLogin');
    }
}
