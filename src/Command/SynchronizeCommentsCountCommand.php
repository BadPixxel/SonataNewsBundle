<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerInterface;


final class SynchronizeCommentsCountCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container){
        parent::__construct();
        $this->container = $container;
    }


    /**
     * @return void
     */
    public function configure(): void
    {
        $this->setName('sonata:news:sync-comments-count');
        $this->setDescription('Synchronize comments count');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $commentManager = $this->container->get('sonata.news.manager.comment');

        $commentManager->updateCommentsCount();

        $output->writeln(' done!');

        return Command::SUCCESS;
    }
}
