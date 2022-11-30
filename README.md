# codeception-wiremock-extension
This Codeception Extension allows developers and testers to use WireMock to mock external services when running codeception tests.

codeception-wiremock-extension connects to an already running instance of WireMock or can also run automatically a local standalone one. And, it is able to download the version of wiremock you preffer and run it too.
After the tests are finished it will close the connection and turn wiremock service off (when it started it).

## See also

* [WireMock PHP library](https://github.com/rowanhill/wiremock-php)
* [Stubbing using WireMock](http://wiremock.org/stubbing.html)
* [Verifying using WireMock](http://wiremock.org/verifying.html)

## Note

If you need an application with a functionality that is similar to the one offered by WireMock and is 100% PHP, please give Phiremock a try: [Phiremock](https://github.com/mcustiel/phiremock), it also has a nice [codeception extension](https://github.com/mcustiel/phiremock-codeception-extension). 

## Installation

### Composer:

This project is published in packagist, so you just need to add it as a dependency in your composer.json:

```bash
$ composer require lamoda/codeception-wiremock-extension
```
## Configuration Examples

### Module
The module allow you to connect to a WireMock instance, it can be the one ran by the extension or an already running one.

```yaml
# acceptance.suite.yml
modules:
    enabled:
        - WireMock:
            host: my.wiremock.host # defaults to 127.0.0.1
            port: 80 # defaults to 8080
```

## How to use

### Prepare your application

First of all, configure your application so when it is being tested it will replace its external services with WireMock.
For instance, if you make some requests to a REST service located under http://your.rest.interface, replace that url in configuration with the url where WireMock runs, for instance: http://localhost:8080/rest_interface.

### Write your tests

```php
// YourCest.php
class YourCest extends \Codeception\TestCase\Test
{
    public function _after(\AcceptanceTester $I)
    {
        $I->resetMappingsAndRequestJournalInWireMock();
    }

    // tests
    public function tryToTest(\AcceptanceTester $I)
    {
        $I->expectRequestToWireMock(
            WireMock::get(WireMock::urlEqualTo('/some/url'))
                ->willReturn(WireMock::aResponse()
                ->withHeader('Content-Type', 'text/plain')
                ->withBody('Hello world!'))
        );
        // Here you should execute your application in a way it requests wiremock. I do this directly to show it. 
        $response = file_get_contents('http://localhost:18080/some/url');
        
        $I->assertEquals('Hello world!', $response);
        $I->receivedRequestInWireMock(
            WireMock::getRequestedFor(WireMock::urlEqualTo('/some/url'))
        );
    }
    
    // Also, you can access wiremock-php library directly
    public function moreComplexTest()
    {
        $wiremockPhp = Codeception\Extension\WiremockConnection::get();
        // Now you can use wiremock-php library
    }
}
```
