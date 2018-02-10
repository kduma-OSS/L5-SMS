<?php
namespace KDuma\SMS;

use Closure;
use Throwable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use KDuma\SMS\Drivers\LogDriver;
use KDuma\SMS\Drivers\SMSDriverInterface;

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
     * @return mixed
     */
    public function driver($driver = null)
    {
        return $this->get($driver ?? $this->getDefaultDriver());
    }

    /**
     * Attempt to get the log from the local cache.
     *
     * @param  string  $name
     * @return SMSSender
     */
    protected function get($name)
    {
//        try {
            return $this->channels[$name] ?? with($this->resolve($name), function ($driver) use ($name) {
                    return $this->tap($name, new SMSSender($driver));
                });
//        } catch (Throwable $e) {
//            return tap($this->createEmergencyLogger(), function ($logger) use ($e) {
//                $logger->emergency('Unable to create configured logger. Using emergency logger.', [
//                    'exception' => $e,
//                ]);
//            });
//        }
    }

    /**
     * Apply the configured taps for the driver.
     *
     * @param  string    $name
     * @param  SMSSender $driver
     * @return SMSSender
     */
    protected function tap($name, SMSSender $driver)
    {
        foreach ($this->configurationFor($name)['tap'] ?? [] as $tap) {
            list($class, $arguments) = $this->parseTap($tap);
            $this->app->make($class)->__invoke($driver, ...explode(',', $arguments));
        }
        return $driver;
    }

    /**
     * Parse the given tap class string into a class name and arguments string.
     *
     * @param  string  $tap
     * @return array
     */
    protected function parseTap($tap)
    {
        return Str::contains($tap, ':') ? explode(':', $tap, 2) : [$tap, ''];
    }

    /**
     * Resolve the given log instance by name.
     *
     * @param  string  $name
     * @return SMSDriverInterface
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
     * @return SMSDriverInterface
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
     * @return SMSDriverInterface
     */
    protected function createLogDriver(array $config)
    {
        return new LogDriver($this->app, $config['level'] ?? 'debug');
    }
}