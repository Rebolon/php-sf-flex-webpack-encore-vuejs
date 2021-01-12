<?php
namespace App\Security;

use InvalidArgumentException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class CsrfToken
{
    /**
     * @var CsrfTokenManagerInterface
     */
    protected $tokenManager;

    public function __construct(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param $tokenId
     * @return \Symfony\Component\Security\Csrf\CsrfToken
     */
    public function getToken($tokenId)
    {
        return $this->tokenManager->getToken($tokenId);
    }

    /**
     * @param $tokenId
     * @param $tokenValue
     * @return bool
     */
    public function tokenCheck($tokenId, $tokenValue)
    {
        $token = new \Symfony\Component\Security\Csrf\CsrfToken($tokenId, $tokenValue);

        if (!$tokenValue || !$this->tokenManager->isTokenValid($token)) {
            throw new InvalidArgumentException('Invalid token');
        }

        return true;
    }
}
