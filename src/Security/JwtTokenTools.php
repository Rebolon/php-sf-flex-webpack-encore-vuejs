<?php

namespace App\Security;

use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class JwtTokenTools
 *
 * It was used by the command GetJwtToken that has been deprecated coz LexiKBundle already has a command to generate token for a user.
 * So this class may not be useful anymore, but for the sample of generating a token programmatically.
 *
 * @package App\Security
 */
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
     * @throws Exception
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

            return $encoder->encode([
                'username' => $username,
                'exp' => time() + $tokenJwtTtl,
            ]);
        } catch (UsernameNotFoundException $e) {
            $msg = sprintf('Exception: UsernameNotFoundException: %s', $e->getMessage());
            $logger->alert($msg);

            throw new NotFoundHttpException($msg);
        } catch (Exception $e) {
            $logger->alert(sprintf('Exception: \Exception: %s', $e->getMessage()));

            throw $e;
        }
    }
}
