# ComputableFacts Behat Contexts

Different contexts ready to use with Behat for testing our projects.

This Behat context is inspire by [Imbo Behat API Extension](https://github.com/imbo/behat-api-extension) that is great to 
test REST API with Behat.

When using Laravel to develop a REST API, we want to be able to use Laravel testing framework (like Facade) but 
Imbo Behat API Extension use the Guzzle HTTP Client. It's why we create this Behat context that have the same Behat steps
than Imbo Behat API Extension but use the Laracast Behat Laravel Extension to have access to the Laravel Framework.  


## Installation

The easiest way to install is by using [Composer](https://getcomposer.org):

`$> composer require --dev computablefacts/behat-contexts`

## Usage

### ApiContext

It's the main class. You can extend this class for your Behat context:

```php
<?php

use ComputableFacts\Behat\Context\Laravel\ApiContext as CfApiContext;

class ApiContext extends CfApiContext
{   
    // ...
}
```



TODO




