<?php

namespace App\Security;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class JwtTokenTools
{
    /**
     * @param UserProviderInterface $provider
     * @param JWTEncoderInterface $encoder
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param int $tokenJwtTtl
     * @param $username
     * @param $password
     * @param LoggerInterface $logger
     * @return string
     * @throws \Exception
     */
    public function encodeToken(
        UserProviderInterface $provider,
        JWTEncoderInterface $encoder,
        UserPasswordEncoderInterface $passwordEncoder,
        int $tokenJwtTtl,
        $username,
        $password,
        LoggerInterface $logger
    ): string {
        try {
            $user = $provider->loadUserByUsername($username);

            $isValid = $passwordEncoder
                ->isPasswordValid($user, $password);

            if (!$isValid) {
                throw new BadCredentialsException();
            }

            $token = $encoder->encode([
                'username' => $username,
                'exp' => time() + $tokenJwtTtl,
            ]);

            return $token;
        } catch (UsernameNotFoundException $e) {
            $msg = sprintf('Exception: UsernameNotFoundException: %s', $e->getMessage());
            $logger->alert($msg);

            throw new NotFoundHttpException($msg);
        } catch (\Exception $e) {
            $logger->alert(sprintf('Exception: \Exception: %s', $e->getMessage()));

            throw $e;
        }
    }
}
