# wiegand-26bit-calculator-php
A Wiegand 26 Bit calculator for encoding/decoding 26-bit binary card data commonly used in RFID access control systems.

## Usage
```php
<?php

// create a calculator from a facility code / card number pair...
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromFacilityCode(99, 999);

// get converted values
echo $wiegand->getHex();            // prints "0C607CF"
echo $wiegand->getProxmark();       // prints "2004c607cf"
echo $wiegand->getBinary();         // prints "00110001100000011111001111"
echo $wiegand->getFacilityCode();   // prints 99
echo $wiegand->getCardNumber();     // prints 999

// ... creating a calculator using any method populates all values
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromHex("0C607CF");
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromProxmark("2004c607cf");
$wiegand = \Angorb\Wiegand26Bit\Calculator::fromBinary("00110001100000011111001111");
```

(I :heart: :robot:s.)