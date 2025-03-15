# PHP Property Transform

[![Scrutinizer Quality Score](https://img.shields.io/scrutinizer/g/mackrais-organization/property-transform.svg?style=flat-square)](https://scrutinizer-ci.com/g/mackrais-organization/property-transform/)
[![Build Status](https://img.shields.io/github/actions/workflow/status/mackrais-organization/property-transform/ci.yaml?branch=master&style=flat-square)](https://github.com/mackrais-organization/property-transform/actions?query=workflow%3ACI+branch%3Amaster+)
[![License](http://poser.pugx.org/mackrais-organization/property-transform/license)](https://packagist.org/packages/mackrais-organization/property-transform)
[![Latest Stable Version](https://img.shields.io/packagist/v/mackrais-organization/property-transform.svg?style=flat-square)](https://packagist.org/packages/mackrais-organization/property-transform)
[![Latest Unstable Version](https://poser.pugx.org/mackrais-organization/property-transform/v/unstable)](https://packagist.org/packages/mackrais-organization/property-transform)
[![CodeCov](https://img.shields.io/codecov/c/github/mackrais-organization/property-transform.svg?style=flat-square)](https://codecov.io/github/mackrais-organization/property-transform)
[![StyleCI](https://styleci.io/repos/139721416/shield?style=flat-square)](https://styleci.io/repos/139721416)
[![Gitter](https://img.shields.io/badge/gitter-join%20chat-brightgreen.svg?style=flat-square)](https://gitter.im/mackrais-organization/property-transform)
[![Total Downloads](https://poser.pugx.org/mackrais-organization/property-transform/downloads)](https://packagist.org/packages/mackrais-organization/property-transform)
[![Monthly Downloads](https://poser.pugx.org/mackrais-organization/property-transform/d/monthly)](https://packagist.org/packages/mackrais-organization/property-transform)
[![Daily Downloads](https://poser.pugx.org/mackrais-organization/property-transform/d/daily)](https://packagist.org/packages/mackrais-organization/property-transform)
[![PHP Version Require](http://poser.pugx.org/mackrais-organization/property-transform/require/php)](https://packagist.org/packages/mackrais-organization/property-transform)
---

## Overview

The **Property Transform** library provides a powerful way to automatically transform DTO properties using **PHP attributes**.  
This allows you to apply transformations such as trimming, formatting, and sanitization **directly on properties**, while also supporting **nested object transformation** and **dependency injection**.

### 🔥 Features

- ✅ **Transform DTO properties using PHP attributes**
- ✅ **Supports multiple transformations per property** (e.g., trim + lowercase)
- ✅ **Applies transformations to nested objects automatically**
- ✅ **Supports callable PHP functions (`trim`, `strtolower`, etc.)**
- ✅ **Works with Dependency Injection (DI) for custom transformations**
- ✅ **Supports class methods as transformers (`[ClassName::class, 'method']`)**
- ✅ **Lightweight & efficient – no runtime overhead**

---

## 📌 Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Examples](#examples)
- [Dependency Injection Support](#dependency-injection-support)
- [License](#license)

---

## 🛠 Installation

Install via **Composer**:

```sh
composer require mackrais-organization/property-transform
```

---

## 🚀 Usage

### 1️⃣ **Basic DTO Transformation**
You can use built-in PHP functions as transformers by adding attributes:

```php
use MackRais\PropertyTransform\Transform;

class UserDto
{
    #[Transform('trim')]
    #[Transform('strtolower')]
    public string $name;

    #[Transform('intval')]
    public string $age;
}
```

Then, apply transformations:

```php
$dto = new UserDto();
$dto->name = '  John Doe ';
$dto->age = '25';

$dataTransformer = new DataTransformer(new TransformerFactory($container));
$dataTransformer->transform($dto);

echo $dto->name; // Output: "john doe"
echo $dto->age; // Output: 25 (converted to integer)
```

---

## 🎯 Examples

### 2️⃣ **Nested DTO Transformation**
If a DTO contains another DTO, transformations will apply recursively:

```php
class AddressDto
{
    #[Transform('trim')]
    #[Transform('strtolower')]
    public string $city;
}

class UserDto
{
    #[Transform('trim')]
    #[Transform('strtolower')]
    public string $name;

    #[Transform] // Required for nested transformation
    public AddressDto $address;
}
```

Usage:

```php
$address = new AddressDto();
$address->city = '  New York ';

$dto = new UserDto();
$dto->name = '  Jane Doe ';
$dto->address = $address;

$dataTransformer->transform($dto);

echo $dto->name; // Output: "jane doe"
echo $dto->address->city; // Output: "new york"
```

---

### 3️⃣ **Using Class Methods as Transformers**
You can also define a custom transformer method inside a class:

```php
class SecuritySanitizer
{
    public function sanitize(?string $input): string
    {
        return strip_tags((string) $input);
    }
}
```

And use it as a transformer:

```php
class UserDto
{
    #[Transform([SecuritySanitizer::class, 'sanitize'])]
    public string $bio;
}
```

---

## 🏗 Dependency Injection Support

Transformers can be registered as **public services** in Symfony or any DI container.

Example using **PSR-11 Container**:

```php
use Psr\Container\ContainerInterface;

$container = new SomePsr11Container();
$factory = new TransformerFactory($container);
$dataTransformer = new DataTransformer($factory);
```

Now, any **service-based transformer** (like `SecuritySanitizer`) will be **automatically resolved**.

---

## 📜 License

**Property Transform** is released under the **MIT License**. See the [`LICENSE.md`](LICENSE.md) file for details.
