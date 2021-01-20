<?php


namespace BristolSU\Auth\Social\Driver;


class DriverLoader
{

    public function __construct(protected DriverStore $driverStore)
    {
    }

    public function load(string $driver): void
    {
        $setup = $this->driverStore->getSetup($driver);
        $setup();
    }

    public function loadAllEnabled(): void
    {
        foreach($this->driverStore->allEnabled() as $driver) {
            $this->load($driver);
        }
    }

}
