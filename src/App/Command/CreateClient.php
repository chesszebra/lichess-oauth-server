<?php

namespace App\Command;

use App\OAuth\ClientCreatorInterface;
use App\OAuth\Entity\Client;
use function Sodium\randombytes_buf;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

final class CreateClient extends Command
{
    /**
     * @var ClientCreatorInterface
     */
    private $clientCreator;

    /**
     * Initializes a new instance of this class.
     *
     * @param ClientCreatorInterface $clientCreator
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(ClientCreatorInterface $clientCreator)
    {
        parent::__construct('create-client');

        $this->clientCreator = $clientCreator;
    }

    protected function configure()
    {
        $this->setDescription('Creates a new client.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $infoCorrect = false;

        while (!$infoCorrect) {
            $clientId = bin2hex(random_bytes(40));
            $clientName = $helper->ask($input, $output, $this->createClientNameQuestion());
            $clientSecret = $helper->ask($input, $output, $this->createClientSecretQuestion());
            $scopes = $helper->ask($input, $output, $this->createScopesQuestion());

            $redirectUris = [];

            do {
                $redirectUri = $helper->ask($input, $output, $this->createRedirectUriQuestion());

                if ($redirectUri) {
                    $redirectUris[] = $redirectUri;
                }
            } while ($redirectUri !== '');

            $output->writeln('');
            $output->writeln('Client id: ' . $clientId);
            $output->writeln('Client secret: ' . $clientSecret);
            $output->writeln('Client name: ' . $clientName);
            $output->writeln('Scopes: ' . $scopes);
            $output->writeln('Redirect URI: ');

            foreach ($redirectUris as $redirectUri) {
                $output->writeln('  - ' . $redirectUri);
            }

            $infoCorrect = $helper->ask($input, $output, $this->createInfoCorrectQuestion());

            if (!$infoCorrect) {
                $output->writeln('');
                continue;
            }

            $client = new Client($clientId, $clientSecret, $clientName, $redirectUris);

            $this->clientCreator->createClient($client);
        }
    }

    private function createClientNameQuestion()
    {
        $question = new Question('Client name: ');
        $question->setNormalizer(function ($answer) {
            return trim($answer);
        });
        $question->setValidator(function ($answer) {
            if ($answer === '') {
                throw new \Exception('The client name cannot be empty.');
            }

            return $answer;
        });

        return $question;
    }

    private function createClientSecretQuestion()
    {
        return new Question('Client secret: ');
    }

    private function createScopesQuestion()
    {
        return new Question('Scopes (space separated): ');
    }

    private function createRedirectUriQuestion()
    {
        $question = new Question('Redirect URI: ');
        $question->setNormalizer(function ($answer) {
            return trim($answer);
        });

        return $question;
    }

    private function createInfoCorrectQuestion()
    {
        return new ConfirmationQuestion('Is the above information correct? (y/n) ', false);
    }
}
