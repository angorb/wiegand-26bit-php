# wiegand-26bit-php
>The communications protocol used on a Wiegand interface is known as the Wiegand protocol. The original Wiegand format had one parity bit, 8 bits of facility code, 16 bits of ID code, and a trailing parity bit for a total of 26 bits. The first parity bit is calculated from the first 12 bits of the code and the trailing parity bit from the last 12 bits. However, many inconsistent implementations and extensions to the basic format exist. - [Wiegand Interface on Wikipedia](https://en.wikipedia.org/wiki/Wiegand_interface#Protocol)

## Why?

While doing some research for a RFID project, I started playing around with Wiegand calculators online. I couldn't find any examples in PHP but I found [this one](https://github.com/jonathansm/wiegand-26bit-calculator) in C and decided to reimplement it in a friendlier way for my own web projects.

## See Also
- HID Global - [Understanding Card Data Formats](https://www.hidglobal.com/sites/default/files/hid-understanding_card_data_formats-wp-en.pdf)

## Usage
```php
<?php

// create a calculator from a facility code / card number pair...
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromFacilityCode(99, 999);

// ... or any other static method
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromHex("0C607CF");
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromProxmark("2004c607cf");
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromBinary("00110001100000011111001111");

// get converted values
echo $wiegand->getHex();            // prints "0C607CF"
echo $wiegand->getProxmark();       // prints "2004c607cf"
echo $wiegand->getBinary();         // prints "00110001100000011111001111"
echo $wiegand->getFacilityCode();   // prints 99
echo $wiegand->getCardNumber();     // prints 999

```
## Command Line Utility

### Requirements
- PHP >7.3
- ext-mbstring or symfony/polyfill-mbstring
- ext-zlib (or disable compression in box.json before building)


### Building
Install box-project/box (not included in composer.json, [install your preferred way](https://github.com/box-project/box/blob/master/doc/installation.md))
```
❯ composer install
❯ box compile
```
**Note:** Building and executing the resulting PHAR should work fine on *nix, MacOS and Windows, but the setup process for running PHARs without specifying the path to the PHP executable on Windows is super annoying.
### Usage
```shell
❯ bin/wiegand-26bit-php
*********** Wiegand 26Bit Calculator ***********
Usage: bin/wiegand-26bit-php [-b binary] [-f facility_code] [-h, --help] [-p proxmark_id] [-u card_number] [-x hex]

Optional Arguments:
        -p proxmark_id
                Proxmark-generated ID string
        -f facility_code
                Facility or site code [0-255] Must be used with -u
        -u card_number
                Unique or card code [0-65535] Must be used with -f
        -x hex
                Hex value of the 26bit RFID tag
        -b binary
                Binary value of the 26bit RFID tag
        -h, --help
                Prints usage information


❯ bin/wiegand-26bit-php -f 255 -u 999
*********** Wiegand 26Bit Calculator ***********
Facility Code....... 255
Card Number......... 999
Binary.............. 01111111100000011111001111
Hex................. 1fe07cf
Proxmark............ 2005fe07cf
```
:robot: :heart: :robot: