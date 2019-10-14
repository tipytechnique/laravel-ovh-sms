# Changelog

All notable changes to `laravel-ovh-sms` will be documented in this file

## 0.1.2 - 2019-10-14

- Get blacklisted numbers : add a method to get all blacklisted numbers (contacts that respond "STOP")
- Delete blacklisted numbers : add a method to delete a given number from the blacklist

## 0.1.1 - 2019-07-15

- Get messages : when using getMessages method, "dateStart" and "dateEnd" filters can be strings values or `DateTime` objects
- Fix missing documentation about publishing `laravel-ovh-sms.php` config file (optionnal)

## 0.1.0 - 2019-07-12

- initial release
