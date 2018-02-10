<?php
namespace KDuma\SMS;

use Closure;
use Throwable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use KDuma\SMS\Drivers\LogSenderDriver;
use KDuma\SMS\Drivers\SMSSenderDriverInterface;
use KDuma\SMS\Drivers\SMSChecksBalanceDriverInterface;

class SMSManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved channels.
     *
     * @var array
     */
    protected $channels = [];

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * Create a new Log manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }


    /**
     * Get a SMS channel instance.
     *
     * @param  string|null  $channel
     * @return mixed
     */
    public function channel($channel = null)
    {
        return $this->driver($channel);
    }

    /**
     * Get a SMS driver instance.
     *
     * @param  string|null  $driver
     * @return SMSSenderDriverInterface | SMSChecksBalanceDriverInterface
     */
    public function driver($driver = null)
    {
        return $this->get($driver ?? $this->getDefaultDriver());
    }

    /**
     * Attempt to get the log from the local cache.
     *
     * @param  string  $name
     * @return SMSSenderDriverInterface | SMSChecksBalanceDriverInterface
     */
    protected function get($name)
    {
        return $this->channels[$name] ?? with($this->resolve($name), function ($driver) use ($name) {
            return new SMSSender($driver);
        });
    }

    /**
     * Resolve the given log instance by name.
     *
     * @param  string  $name
     * @return SMSSenderDriverInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->configurationFor($name);
        if (is_null($config)) {
            throw new InvalidArgumentException("Channel [{$name}] is not defined.");
        }
        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($config);
        }
        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }
        throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
    }

    /**
     * Call a custom driver creator.
     *
     * @param  array  $config
     * @return mixed
     */
    protected function callCustomCreator(array $config)
    {
        return $this->customCreators[$config['driver']]($this->app, $config);
    }

    /**
     * Create a custom log driver instance.
     *
     * @param  array  $config
     * @return SMSSenderDriverInterface
     */
    protected function createCustomDriver(array $config)
    {
        return $this->app->make($config['via'])->__invoke($config);
    }

    /**
     * Get the SMS connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function configurationFor($name)
    {
        return $this->app['config']["sms.channels.{$name}"];
    }

    /**
     * Get the default SMS driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['sms.default'];
    }

    /**
     * Set the default SMS driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['sms.default'] = $name;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param  string    $driver
     * @param  \Closure  $callback
     * @return $this
     */
    public function extend($driver, Closure $callback)
    {
        $this->customCreators[$driver] = $callback->bindTo($this, $this);
        return $this;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }

    /**
     * Create an instance of the log SMS driver.
     *
     * @param  array  $config
     * @return SMSSenderDriverInterface
     */
    protected function createLogDriver(array $config)
    {
        return new LogSenderDriver($this->app, $config['level'] ?? 'debug');
    }
}