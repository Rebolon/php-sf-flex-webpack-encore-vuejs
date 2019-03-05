<?php
namespace App\Command;

use App\Security\JwtTokenTools;
use Http\Client\HttpAsyncClient;
use Http\Message\MessageFactory;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Yaml\Yaml;

class GetJwtToken extends Command
{
    /**
     * @var JWTEncoderInterface
     */
    protected $encoder;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $passwordEncoder;

    /**
     * @var InMemoryUserProvider
     */
    protected $provider;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var JwtTokenTools
     */
    protected $tokenTools;

    /**
     * @var int
     */
	protected $tokenJwtTtl;

	/**
	 * @var string
	 */
	protected $tokenJwtBearer;
	/**
	 * @var string
	 */
	protected $rootDir;

    /**
     * GetJwtToken constructor.
     * @param LoggerInterface $logger
     * @param InMemoryUserProvider $provider
     * @param JWTEncoderInterface $encoder
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param JwtTokenTools $tokenTools
     * @param int $tokenJwtTtl
     * @param string $tokenJwtBearer
     * @param string $kernelProjectDir
     */
    public function __construct(
        LoggerInterface $logger,
        InMemoryUserProvider $provider,
        JWTEncoderInterface $encoder,
        UserPasswordEncoderInterface $passwordEncoder,
        JwtTokenTools $tokenTools,
        int $tokenJwtTtl,
        string $tokenJwtBearer,
        string $kernelProjectDir
    ) {
        $this->logger = $logger;
        $this->provider = $provider;
        $this->encoder = $encoder;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenTools = $tokenTools;
        $this->tokenJwtTtl = (int) $tokenJwtTtl;
        $this->tokenJwtBearer = $tokenJwtBearer;
        $this->rootDir = $kernelProjectDir;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:new-jwt-token')
            ->setDescription('Generate a new valid JWT token')
            ->setHelp('Generate a new valid JWT token, to use with API for example.')
            ->addArgument('username', InputArgument::OPTIONAL, 'the username (default from in_memory provider will be used if exists).')
            ->addArgument('password', InputArgument::OPTIONAL, 'the password (default from in_memory provider will be used if exists).');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $defaultLogin = '';
        $defaultPass = '';

        try {
            $user = $this->loadSecurityConfig();
            $defaultLogin = $user['username'];
            $defaultPass = $user['password'];
        } catch (\Exception $e) {
            $this->logger->warning('No default values from security.yaml file (works only with in_memory provider)');
        }

        $username = $input->getArgument('username') ?: $defaultLogin;
        $password = $input->getArgument('password') ?: $defaultPass;

        $token = $this->tokenTools->encodeToken(
            $this->provider,
            $this->encoder,
            $this->passwordEncoder,
            $this->tokenJwtTtl,
            $username,
            $password,
            $this->logger
        );

        $output->writeln([
            'Here is your valid token',
            '======================================================================================',
            $token,
            '',
            'Copy paste this into the HTTP Authorization header of your HTTP Request',
            '=======================================================================',
            $this->tokenJwtBearer . ' ' . $token,
            '=======================================================================',
        ]);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function loadSecurityConfig(): array
    {
        $configDir = $this->rootDir . '/config/';
        $values = Yaml::parseFile($configDir . 'packages/security.yaml');
        $config = $values['security'];

        if ($config['providers']
            && $config['providers']['in_memory']
            && $config['providers']['in_memory']['memory']
            && $config['providers']['in_memory']['memory']['users']) {
            $user = next($config['providers']['in_memory']['memory']['users']);

            $user['username'] = key($config['providers']['in_memory']['memory']['users']);

            return $user;
        }

        throw new \Exception('Missing providers in memory section in api_platform.yaml');
    }
}
