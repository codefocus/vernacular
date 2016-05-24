#Vernacular

A natural language processing package for PHP / Laravel 5.

Vernacular uses a simple Bayesian model to gain contextual information from text, based on prior information.

##Notice

This library is in development. __Breaking changes *will* happen__.

Do not use in production.

##Usage

```php
class YourModel extends Model
{
    use Codefocus\Vernacular\Traits\Learnable;
    
    protected $vernacularAttributes = ['description'];
    protected $vernacularTags = [];
    
…
```

##Code style

`php-cs-fixer fix ./ --level=psr2 --fixers=-psr0`

##Contribute
