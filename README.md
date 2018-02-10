# An SMS sender for Laravel 5 with Notification Channel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

An SMS sender for Laravel 5 with Notification Channel

## Install

Via Composer

```bash
$ composer require kduma/sms
```

In Laravel 5.6, service provider is automatically discovered. If you don't use package discovery, 
add the Service Provider to the providers array in `config/app.php`:

    KDuma\SMS\SMSServiceProvider::class,
    
And following facade to facades array:

    'SMS' => KDuma\SMS\Facades\SMS::class,
    
## Usage
``` php
SMS::send('phone number', 'Message.');
SMS::balance();
SMS::driver('serwersms')->send('phone number', 'Message.');
```

## Laravel 5.3 Notifications Channel Usage

Follow Laravel's documentation to add the channel your Notification class, for example:

```php
use Illuminate\Notifications\Notification;
use KDuma\SMS\NotificationChannel\SMSChannel;
use KDuma\SMS\NotificationChannel\SMSMessage;

class NotificationSMSChannelTestNotification extends Notification
{
    public function via($notifiable)
    {
        return [SMSChannel::class];
    }
    
    public function toSMS($notifiable)
    {
        return SMSMessage::create('This is a test SMS sent via Simple SMS using Laravel Notifications!');
    }
}
```  

Also you need to add a `routeNotificationForSMS` method to your Notifiable model to return the phone number, for example:  

```php
public function routeNotificationForSMS()
{
    return $this->phone_number;
}
```    

### `SMSMessage` Available methods

* `content()` - SMS content
* `channel()` - Set the configured SMS channel

## Credits

- [Krystian Duma][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kduma/sms.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kduma/sms.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/kduma/sms
[link-downloads]: https://packagist.org/packages/kduma/sms
[link-author]: https://github.com/kduma
[link-contributors]: ../../contributors
