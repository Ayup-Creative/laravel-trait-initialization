# Laravel Trait Initialisation

## Overview

Laravel Trait Initialisation is a powerful package that simplifies trait management in Laravel applications by providing
a clean and intuitive way to initialise traits dynamically.

## Key Features

- **Dynamic Trait Initialisation**: Easily initialise traits with a simple method naming convention
- **Dependency Injection Support**: Automatically resolve and inject dependencies in trait initialisation methods
- **Flexible Configuration**: Works seamlessly with Laravel's model and class system

## Installation

Install the package via Composer:

```bash
composer require ayup-creative/laravel-trait-initialization
```

## Usage

### Basic Trait Initialisation

```php
trait MyCustomTrait 
{
    public function initialiseMyCustomTrait()
    {
        // This method will be automatically called when the trait is used
        // Perform initialisation logic here
    }
}

class MyModel extends Model
{
    use MyCustomTrait;
}
```

### Dependency Injection

```php
trait ServiceTrait 
{
    public function initialiseServiceTrait(SomeService $service)
    {
        // Laravel will automatically inject the SomeService dependency
        $this->service = $service;
    }
}
```

## How It Works

- When a class uses a trait, Laravel automatically detects and calls the `initialize<TraitName>` method
- The method is called during class instantiation
- Supports full dependency injection for more complex initialisation scenarios

## Best Practices

- Use the `initialise<TraitName>` method naming convention
- Keep initialisation logic lightweight and focused
- Leverage dependency injection for complex setup requirements

## Contributing

Contributions are welcome! Please submit pull requests or open issues on the GitHub repository.

## License

This package is open-sourced software licensed under the MIT license.

## Contact

Developed by Ayup Creative

- GitHub: @Ayup-Creative
