<?php


namespace BristolSU\Auth\Social\Driver;


class DriverStoreSingleton implements DriverStore
{

    /**
     * A list of drivers
     * @var array
     */
    protected array $drivers = [];

    /**
     * The setup callbacks
     *
     * @var array|\Closure[]
     */
    protected array $setup = [];

    /**
     * The enabled drivers
     *
     * @var array|bool[]
     */
    protected array $enabled = [];

    public function register(string $driver, \Closure $setup, bool $enabled): void
    {
        if(!$this->hasDriver($driver)) {
            $this->drivers[] = $driver;
            $this->setup[$driver] = $setup;
            $this->enabled[$driver] = $enabled;
        }
    }

    public function getSetup(string $driver): \Closure
    {
        if($this->hasDriver($driver)) {
            return $this->setup[$driver];
        }
        throw new \Exception(sprintf('The %s social driver has not been registered.', $driver));
    }

    public function isEnabled(string $driver)
    {
        if($this->hasDriver($driver)) {
            return $this->enabled[$driver];
        }
        throw new \Exception(sprintf('The %s social driver has not been registered.', $driver));    }

    public function all(): array
    {
        return $this->drivers;
    }

    public function allEnabled(): array
    {
        return array_filter($this->drivers, fn($driver): bool => $this->enabled[$driver]);
    }

    public function hasDriver(string $driver): bool
    {
        return in_array($driver, $this->drivers);
    }
}
