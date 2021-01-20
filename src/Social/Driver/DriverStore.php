<?php

namespace BristolSU\Auth\Social\Driver;

interface DriverStore
{

    /**
     * Register a driver to allow it to be used
     *
     * @param string $driver The name of the driver
     * @param \Closure $setup A function to set up the driver
     * @param bool $enabled Whether the driver is enabled
     */
    public function register(string $driver, \Closure $setup, bool $enabled): void;

    /**
     * Get the closure to set up the given driver
     *
     * @param string $driver The name of the driver
     * @return \Closure The function to call to load the driver
     * @throws \Exception If the driver is not registered
     */
    public function getSetup(string $driver): \Closure;

    /**
     * Determine if the given driver is able to be loaded
     *
     * @param string $driver
     * @return bool
     * @throws \Exception If the driver is not registered
     */
    public function isEnabled(string $driver);

    /**
     * Get all the driver names
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get the names of all enabled drivers
     *
     * @return array
     */
    public function allEnabled(): array;

    /**
     * Check if the driver exists
     *
     * @param string $driver The name of the driver
     * @return bool Whether the driver exists
     */
    public function hasDriver(string $driver): bool;

}
