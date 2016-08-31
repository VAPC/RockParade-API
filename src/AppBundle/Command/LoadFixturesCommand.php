<?php

namespace AppBundle\Command;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/** {@inheritDoc} */
class LoadFixturesCommand extends ContainerAwareCommand
{

    /** {@inheritDoc} */
    protected function configure()
    {
        $this->setName('rock:fixture:load');
        $this->setDescription('Load fixtures to database');
    }

    /** {@inheritDoc} */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrineService = $this->getContainer()->get('doctrine');
        $entityManager = $doctrineService->getManager();

        $fixtures = [
            // add fixtures if needed
        ];

        foreach ($fixtures as $fixture) {
            if ($fixture instanceof FixtureInterface) {
                $fixture->load($entityManager);
            }
        }

        $output->writeln('Fixtures loaded to database.');
    }
}
