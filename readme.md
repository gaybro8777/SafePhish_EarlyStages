#SafePhish
SafePhish is a custom built web interface to allow businesses to test their employees for awareness of phishing scams.

###Development Notes

Utilizes Laravel PHP Framework <br />
Utilizes the centralized controller `PhishingController.php`, to execute routes and processes. <br />

#####Classes
* _DBManager_ - Manages database connection using PDO
* _Email_ - Executes an email process to send out a batch of emails to a database of users
* _EmailConfiguration_ - Configuration object for maintaining all the necessary information to send an email
* _TemplateConfiguration_ - Configuration object for maintaining all the necessary information to read and access a template
* _User_ - User object
* _Project_ - Projects are groupings to organize email sets sent out based on a specific task
* _PDOIterator_ - Iterator class to iterate through a PDOStatement returned by a PDO Object or DBManager
* _User_test_ - A Test User object being worked on to test best implementation of User object

#####Libraries
* _RandomObjectGeneration_ - Basic Library include for common Random Generation methods

#####Exceptions
* _ConfigurationException_ - Custom exception thrown from `EmailConfiguration` and `TemplateConfiguration` if error occurs
* _EmailException_ - Custom exception thrown from `Email` if error occurs
* _QueryException_ - Custom exception thrown from `DBManager` if error occurs after PDO Object instantiated

#####Templating
Utilizes blade templating for all public facing pages. <br />
Templates organized based on:
* _Errors_
* _Forms_
* _Emails_
    * Educational
    * Phishing
    * Errors
* _Masters_
* _DataDisplays_
* _Authentication_


<br /><br /><br /><br /><br /><br />

## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/downloads.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

