brick/date-time-doctrine
========================

<img src="https://raw.githubusercontent.com/brick/brick/master/logo.png" alt="" align="left" height="64">

Doctrine type mappings for [brick/date-time](https://github.com/brick/date-time).

[![Build Status](https://github.com/brick/date-time-doctrine/workflows/CI/badge.svg)](https://github.com/brick/date-time-doctrine/actions)
[![Coverage Status](https://coveralls.io/repos/github/brick/date-time-doctrine/badge.svg?branch=master)](https://coveralls.io/github/brick/date-time-doctrine?branch=master)
[![Latest Stable Version](https://poser.pugx.org/brick/date-time-doctrine/v/stable)](https://packagist.org/packages/brick/date-time-doctrine)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

Introduction
------------

This library provides type mappings to use `brick/date-time` objects such as `LocalDate` as Doctrine entity properties.

Installation
------------

This library is installable via [Composer](https://getcomposer.org/):

```bash
composer require brick/date-time-doctrine
```

Requirements
------------

This library requires PHP 7.4 or later.

Project status & release process
--------------------------------

The current releases are numbered `0.x.y`. When a non-breaking change is introduced (adding new methods, optimizing existing code, etc.), `y` is incremented.

**When a breaking change is introduced, a new `0.x` version cycle is always started.**

It is therefore safe to lock your project to a given release cycle, such as `0.3.*`.

If you need to upgrade to a newer release cycle, check the [release history](https://github.com/brick/date-time-doctrine/releases) for a list of changes introduced by each further `0.x.0` version.

Package contents
----------------

- [LocalDateType](https://github.com/brick/date-time-doctrine/blob/master/src/Types/LocalDateType.php)
- [LocalTimeType](https://github.com/brick/date-time-doctrine/blob/master/src/Types/LocalTimeType.php)
- [LocalDateTimeType](https://github.com/brick/date-time-doctrine/blob/master/src/Types/LocalDateTimeType.php)
- [InstantType](https://github.com/brick/date-time-doctrine/blob/master/src/Types/InstantType.php)
- [DayOfWeekType](https://github.com/brick/date-time-doctrine/blob/master/src/Types/DayOfWeekType.php)
- [DurationType](https://github.com/brick/date-time-doctrine/blob/master/src/Types/DurationType.php)
- [PeriodType](https://github.com/brick/date-time-doctrine/blob/master/src/Types/PeriodType.php)
