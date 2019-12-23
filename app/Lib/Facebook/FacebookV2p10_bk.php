<?php

/**
 * Class FacebookV2p10
 * wrapper of Facebook class
 */
class FacebookV2p10 extends Facebook {

    /**
     * Maps aliases to Facebook domains.
     */
    public static $DOMAIN_MAP = array(
        'api'         => 'https://api.facebook.com/v2.10/',
        'api_video'   => 'https://api-video.facebook.com/v2.10/',
        'api_read'    => 'https://api-read.facebook.com/v2.10/',
        'graph'       => 'https://graph.facebook.com/v2.10/',
        'graph_video' => 'https://graph-video.facebook.com/',
        'www'         => 'https://www.facebook.com/',
    );

    /**
     * Build the URL for given domain alias, path and parameters.
     *
     * @param $name string The name of the domain
     * @param $path string Optional path (without a leading slash)
     * @param $params array Optional query parameters
     *
     * @return string The URL for the given parameters
     */
    protected function getUrl($name, $path='', $params=array()) {
        $url = self::$DOMAIN_MAP[$name];
        if ($path) {
            if ($path[0] === '/') {
                $path = substr($path, 1);
            }
            $url .= $path;
        }
        if ($params) {
            $url .= '?' . http_build_query($params, null, '&');
        }

        return $url;
    }

    /**
     * Retrieves an access token for the given authorization code
     * (previously generated from www.facebook.com on behalf of
     * a specific user).  The authorization code is sent to graph.facebook.com
     * and a legitimate access token is generated provided the access token
     * and the user for which it was generated all match, and the user is
     * either logged in to Facebook or has granted an offline access permission.
     *
     * @param string $code An authorization code.
     * @return mixed An access token exchanged for the authorization code, or
     *               false if an access token could not be generated.
     */
    protected function getAccessTokenFromCode($code, $redirect_uri = null) {
        if (empty($code)) {
            return false;
        }

        if ($redirect_uri === null) {
            $redirect_uri = $this->getCurrentUrl();
        }

        try {
            // need to circumvent json_decode by calling _oauthRequest
            // directly, since response isn't JSON format.
            $access_token_response =
                $this->_oauthRequest(
                    $this->getUrl('graph', '/oauth/access_token'),
                    $params = array('client_id' => $this->getAppId(),
                        'client_secret' => $this->getAppSecret(),
                        'redirect_uri' => $redirect_uri,
                        'code' => $code));
        } catch (FacebookApiException $e) {
            // most likely that user very recently revoked authorization.
            // In any event, we don't have an access token, so say so.
            return false;
        }

        if (empty($access_token_response)) {
            return false;
        }

        $response_params = json_decode($access_token_response, true);
        if (!isset($response_params['access_token'])) {
            return false;
        }

        return $response_params['access_token'];
    }
}

