What is "WebUIMonitor"?
=======================

WebUIMonitor is the user interface that shows errors in your system in real time and plays a sound if there is a critical error.

This component has two parts. One is a PHP web socket server that must be run on a server listening to web socket clients and RabbitMQ request. The other part is a client developed in Javascript that runs on the browser and connects to the server displaying messages.


Requirements
============

- PHP 5.3.2 and up.
- RabbitMQ or ZMQ.


Libraries and services used
===========================

- PHP
	- Silex
	- Symfony Components:
		- YAML
	- php-websocket
	- PhpAmqpLib
	- Monolog
- ExtJS 4
- WebSockets
- RabbitMQ/ZMQ+OpenPGM


Installation
============

The best way to install is to clone the repository and then configure as you need. See "Configuration" section.

After cloning you must update vendors:

	./update_vendors.sh


Usage
=====

Start websocket server:

	php app/websocket_server.php

In your browser write the url where project is found, example:

	http://localhost/WebUIMonitor/web/index.php


Configuration
=============

All configuration is done using a YAML file.

Config file has 3 sections:

- class:
	- class name that subscribe to the channel to get messages.

- subscriber:
	- channel where subscribe.

- websocket:
	- host and port where websocket server will run.


See config file for more details.


Known issues
============

RabbitMQ library use a blocking connections so new websocket connections from the browser can not be established until a new message is published.

A solution could be create a proxy process that handles the RabbitMQ subscription and sends new messages to the main process using a pipe with a non-blocking connection.


Extra notes
===========

Use of ZMQ is discontinued because a memory leak using ZMQ with OpenPGM PUB/SUB.
