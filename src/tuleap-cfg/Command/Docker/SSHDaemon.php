<?php
/**
 * Copyright (c) Enalean, 2020-Present. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);

namespace TuleapCfg\Command\Docker;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use TuleapCfg\Command\ProcessFactory;

final class SSHDaemon
{
    private ?Process $process;

    public function __construct(private readonly ProcessFactory $process_factory)
    {
    }

    public function startDaemon(OutputInterface $output): void
    {
        $output->writeln("Start SSH Daemon");
        $this->generateSSHServerKeys();
        $this->process = $this->process_factory->getProcess(['/usr/sbin/sshd', '-E', '/dev/stderr', '-D']);
        $this->process->start();
    }

    public function shutdownDaemon(OutputInterface $output): void
    {
        if ($this->process) {
            $output->writeln("Shutdown SSH Daemon");
            $this->process->stop(0, SIGTERM);
        }
    }

    private function generateSSHServerKeys(): void
    {
        if (! is_file('/etc/ssh/ssh_host_ecdsa_key')) {
            if (is_executable('/usr/sbin/sshd-keygen')) {
                $this->process_factory->getProcess(['/usr/sbin/sshd-keygen'])->mustRun();
            } elseif (is_executable('/usr/libexec/openssh/sshd-keygen')) {
                $this->process_factory->getProcess(['/usr/libexec/openssh/sshd-keygen', 'rsa'])->mustRun();
                $this->process_factory->getProcess(['/usr/libexec/openssh/sshd-keygen', 'ecdsa'])->mustRun();
                $this->process_factory->getProcess(['/usr/libexec/openssh/sshd-keygen', 'ed25519'])->mustRun();
            } else {
                throw new \RuntimeException('No valid sshd-keygen executable found');
            }
        }
    }
}
