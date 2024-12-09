<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace cachestore_mbsredis;

use core_cache\definition;

/**
 * TTL support test for Redis cache.
 *
 * If you wish to use these unit tests all you need to do is add the following definition to
 * your config.php file.
 *
 * define('TEST_CACHESTORE_MBSREDIS_TESTSERVERS', '127.0.0.1');
 *
 * @package cachestore_mbsredis
 * @copyright   2024 ISB Bayern
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \cachestore_mbsredis
 */
final class ttl_test extends \advanced_testcase {
    /** @var \cachestore_mbsredis|null Cache store  */
    protected $store = null;

    public function setUp(): void {
        // Make sure cachestore_mbsredis is available.
        require_once(__DIR__ . '/../lib.php');
        if (!\cachestore_mbsredis::are_requirements_met() || !defined('TEST_CACHESTORE_MBSREDIS_TESTSERVERS')) {
            $this->markTestSkipped('Could not test cachestore_mbsredis. Requirements are not met.');
        }

        // Set up a Redis store with a fake definition that has TTL set to 1 second.
        $definition = definition::load('core/wibble', [
                'mode' => 1,
                'simplekeys' => true,
                'simpledata' => true,
                'ttl' => 1,
                'component' => 'core',
                'area' => 'wibble',
                'selectedsharingoption' => 2,
                'userinputsharingkey' => '',
                'sharingoptions' => 15,
        ]);
        $this->store = new \cachestore_mbsredis('Test', \cachestore_mbsredis::unit_test_configuration());
        $this->store->initialise($definition);

        parent::setUp();
    }

    protected function tearDown(): void {
        parent::tearDown();

        if ($this->store instanceof \cachestore_mbsredis) {
            $this->store->purge();
        }
    }

    /**
     * Test calling set_many with an empty array
     *
     * Trivial test to ensure we don't trigger an ArgumentCountError when calling zAdd with invalid parameters
     */
    public function test_set_many_empty(): void {
        $this->assertEquals(0, $this->store->set_many([]));
    }

    /**
     * Tests expiring data.
     */
    public function test_expire_ttl(): void {
        $this->resetAfterTest();

        $this->store->set('a', 1);
        $this->store->set('b', 2);

        $this->store->set_many([['key' => 'c', 'value' => 3], ['key' => 'd', 'value' => 4]]);
        $this->assertTrue($this->store->has('a'));
        $this->assertTrue($this->store->has('b'));
        $this->assertTrue($this->store->has('c'));
        $this->assertTrue($this->store->has('d'));
        // We set the TTL to 1 second. So if we wait 1 second here, all the keys should have expired.
        sleep(2);
        $this->assertFalse($this->store->has('a'));
        $this->assertFalse($this->store->has('b'));
        $this->assertFalse($this->store->has('c'));
        $this->assertFalse($this->store->has('d'));

        // Set up a Redis store with a fake definition that has TTL set to 10 seconds.
        $definition = definition::load('core/wibble', [
                'mode' => 1,
                'simplekeys' => true,
                'simpledata' => true,
                'component' => 'core',
                'area' => 'wibble',
                'selectedsharingoption' => 2,
                'userinputsharingkey' => '',
                'sharingoptions' => 15,
        ]);
        $this->store = new \cachestore_mbsredis('Test', \cachestore_mbsredis::unit_test_configuration());
        $this->store->initialise($definition);

        $this->store->set('a', 1);
        $this->store->set('b', 2);

        $this->store->set_many([['key' => 'c', 'value' => 3], ['key' => 'd', 'value' => 4]]);
        $this->assertTrue($this->store->has('a'));
        $this->assertTrue($this->store->has('b'));
        $this->assertTrue($this->store->has('c'));
        $this->assertTrue($this->store->has('d'));
        // We did not set any TTL, so let's wait a bit to be sure, but then all the keys should still be there.
        sleep(2);
        $this->assertTrue($this->store->has('a'));
        $this->assertTrue($this->store->has('b'));
        $this->assertTrue($this->store->has('c'));
        $this->assertTrue($this->store->has('d'));
    }
}
