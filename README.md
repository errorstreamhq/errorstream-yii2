# errorstreamclient

Yii2 integration for [Errorstream](https://www.errorstream.com/)

#Installation Instructions

First, run the following command on the terminal to download and install the package
```bash
composer require errorstream/errorstream-yii2
```

Next, register the client class in your web.php configuration file. You can find your API token and Project token on the project settings page inside of errorstream.com
```php
    'components' => [
        ...
        'errorstream' => [
            'class'             => 'ErrorStream\ErrorStreamClient\ErrorStreamClient',
            'api_token'         => 'YOUR_API_TOKEN', //Put your api token here
            'project_token'     => 'YOUR_PROJECT_TOKEN', //Put your project token here
            'active'            => true, //You might want to only activate this in production mode
        ]
        ...
    ]
```

Next, you want to edit the errorHandler setting in web.php configuration file. You'll want to place the ErrorStreamErrorHandler as a 'class' for your error handler, like this.
```php
'components' => [
    ...
    'errorHandler' => [
        'errorAction' => 'site/error',
        'class' => 'ErrorStream\ErrorStream\ErrorStreamErrorHandler',
    ],
    ...
]
```

Finally, you want to edit the errorHandler setting in web.php configuration file. You'll want to place the ErrorStreamErrorHandler as a 'class' for your error handler, like this.
```php
    'components' => [
        ...
        'log' => [
            ..
            'targets' => [
                ...
                [
                    'class' => 'ErrorStream\ErrorStream\ErrorStreamLogger',
                    'levels' => ['error', 'warning'], //Only send errors and warnings.
                    'logVars' => [], //Necessary so you don't submit every request to our server.
                ],
                ...
            ],
        ]
    ]
```
