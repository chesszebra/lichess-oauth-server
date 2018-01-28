<?php

namespace App\Command;

use App\OAuth\ExpirableTokensInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ClearTokens extends Command
{
    /**
     * @var ExpirableTokensInterface
     */
    private $accessTokenRepository;

    /**
     * @var ExpirableTokensInterface
     */
    private $refreshTokenRepository;

    /**
     * @var ExpirableTokensInterface
     */
    private $authCodeRepository;

    /**
     * Initializes a new instance of this class.
     *
     * @param ExpirableTokensInterface $accessTokenRepository
     * @param ExpirableTokensInterface $refreshTokenRepository
     * @param ExpirableTokensInterface $authCodeRepository
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(
        ExpirableTokensInterface $accessTokenRepository,
        ExpirableTokensInterface $refreshTokenRepository,
        ExpirableTokensInterface $authCodeRepository
    ) {
        parent::__construct('clear-tokens');

        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->authCodeRepository = $authCodeRepository;
    }

    protected function configure()
    {
        $this->setDescription('Clears all tokens that are expired');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->accessTokenRepository->clearExpiredTokens();
        $this->refreshTokenRepository->clearExpiredTokens();
        $this->authCodeRepository->clearExpiredTokens();
    }
}
