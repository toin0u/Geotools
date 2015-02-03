<?php

/*
 * This file is part of the Geotools library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Geotools\Tests\CLI\Command\Point;

use League\Geotools\CLI\Command\Point\FinalBearing;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Antoine Corcy <contact@sbin.dk>
 */
class FinalBearingTest extends \League\Geotools\Tests\TestCase
{
    protected $application;
    protected $command;
    protected $commandTester;

    protected function setUp()
    {
        $this->application = new Application;
        $this->application->add(new FinalBearing);

        $this->command = $this->application->find('point:final-bearing');

        $this->commandTester = new CommandTester($this->command);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments.
     */
    public function testExecuteWithoutArguments()
    {
        $this->commandTester->execute(array(
            'command' => $this->command->getName(),
        ));
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage It should be a valid and acceptable ways to write geographic coordinates !
     */
    public function testExecuteInvalidArguments()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => 'foo, bar',
            'destination' => ' ',
        ));
    }

    public function testExecute()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertRegExp('/118/', $this->commandTester->getDisplay());
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage Please provide an ellipsoid name !
     */
    public function testExecuteWithEmptyEllipsoidOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--ellipsoid' => ' ',
        ));
    }

    /**
     * @expectedException League\Geotools\Exception\InvalidArgumentException
     * @expectedExceptionMessage foo ellipsoid does not exist in selected reference ellipsoids !
     */
    public function testExecuteWithoutAvailableEllipsoidOption()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--ellipsoid' => 'foo',
        ));
    }

    public function testExecuteWithEllipsoid()
    {
        $this->commandTester->execute(array(
            'command'     => $this->command->getName(),
            'origin'      => '40° 26.7717, -79° 56.93172',
            'destination' => '30°16′57″N 029°48′32″W',
            '--ellipsoid' => 'GRS_1980',
        ));

        $this->assertTrue(is_string($this->commandTester->getDisplay()));
        $this->assertSame('<value>118</value>', trim($this->commandTester->getDisplay()));
    }
}
