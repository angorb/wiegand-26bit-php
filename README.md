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

:robot: :heart: :robot: