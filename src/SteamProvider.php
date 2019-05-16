<?php

namespace RodriigoGS\Socialite\Steam;

use Illuminate\Support\Arr;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use LightOpenID;

class SteamProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Endpoints Steam
     */
    const OPENID_URL = 'https://steamcommunity.com/openid';
    const API_URL = 'https://api.steampowered.com';

    /**
     * Returns the Open ID object.
     *
     * @return \LightOpenID
     */
    protected function getOpenID()
    {
        return tap(new LightOpenID($this->redirectUrl), function ($openID) {
            $openID->returnUrl = $this->redirectUrl;
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        $openID = $this->getOpenID();

        $openID->identity = self::OPENID_URL;

        return $openID->authUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function user()
    {
        $openID = $this->getOpenID();

        if (!$openID->validate()) {
            throw new OpenIDValidationException();
        }

        $user = $this->mapUserToObject($this->getUserByToken(
            $this->parseAccessToken($openID->identity)
        ));

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['steamid'],
            'token'    => $user['steamid'],
            'nickname' => Arr::get($user, 'personaname'),
            'name'     => Arr::get($user, 'realname'),
            'email'    => null,
            'avatar'   => Arr::get($user, 'avatarmedium'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            self::API_URL . sprintf('/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s', $this->clientSecret, $token)
        );

        $contents = json_decode($response->getBody()->getContents(), true);

        return Arr::get($contents, 'response.players.0');
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body)
    {
        preg_match('/\/id\/(\d+)$/i', $body, $matches);

        return $matches[1];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
    }
}
