Introduction
============

This Facebook SDK aims to replace the official one (http://github.com/facebook/php-sdk).
It only works with PHP 5.3 as it uses namespaces.

Authentication
==============

The authentication process in the SDK use what we name loader and dumper. The authentication process is all about "how to get a valid Access Token".
For this purpose, the SDK use Sessions Loader and Session Dumper.

The loader means "Where and how can i find a valid access token ?"
The dumper means "Where do i store my session ?"

Loaders and dumpers usually works by pair. The dumper dump the session somewhere and the loader retrieve this stored session.
For example, you will dump your session in a cookie, then retrieve it later.


Working with api objects
========================

The Facebook SDK use a fluent interface to work with objects thanks to the magic methods.
Two classes are involved in the objects api processing: Object (Facebook\Object\Object) and Collection (Facebook\Object\Collection)
Object represents any "Facebook Object" having an Facebook ID => User, Album, Photo, etc...
Collection represents a collection of "Facebook Object"

To get a object propery, we'll use getXXX where XXX is the property name (lower case)
ex: $user->getId()

To fetch a connexion, we'll use fetchXXX where XXX is the connexion name
ex: $user->fetchAlbums()    // Return a collection of Facebook Album Object


Installation
============

$configuration = new Configuration(array('appId' => 'myappid', 'appSecret' => 'myappsecret');
$facebook = new Facebook($configuration);
$facebook->addLoader(new Facebook\Loader\SignedRequestLoader($facebook));
$facebook->addLoader(new Facebook\Loader\SessionLoader($facebook));

$facebook->addDumper(new Facebook\Dumper\SessionDumper($facebook));



Examples
========

* Retrieve friends ids
$user->fetchFriends()->getId();


* Retrive user photos ids
$user->fetchAlbums()->fetchPhotos()->getId();

 
