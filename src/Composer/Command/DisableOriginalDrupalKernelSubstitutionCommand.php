<?php

declare(strict_types=1);

/*
 * This file is part of the ekino Drupal Debug project.
 *
 * (c) ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\Drupal\Debug\Composer\Command;

use Composer\Command\BaseCommand;
use Ekino\Drupal\Debug\Composer\Helper\ManageConfigurationHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DisableOriginalDrupalKernelSubstitutionCommand extends BaseCommand
{
    /**
     * @var string
     */
    const NAME = 'drupal-debug:disable-original-drupal-kernel-substitution';

    /**
     * @var string
     */
    const ALIASES = array('drupal-debug:disable');

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName(self::NAME)
            ->setAliases(self::ALIASES);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        return (new ManageConfigurationHelper($this->getComposer(), $this->getIO()))->toggleOriginalDrupalKernelSubstitution(false) ? 0 : 1;
    }
}
