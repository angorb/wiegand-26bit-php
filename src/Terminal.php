<?php

namespace Angorb\Wiegand26Bit;

class Terminal
{

    private \League\CLImate\CLImate $cli;

    private const FLAG_PROXMARK = 0b00001;
    private const FLAG_FACILITY = 0b00010;
    private const FLAG_CARD = 0b00100;
    private const FLAG_HEX = 0b01000;
    private const FLAG_BINARY = 0b10001;

    public function __construct()
    {
        $this->cli = new \League\CLImate\CLImate();

        $this->cli->flank("<bold><yellow>Wiegand 26Bit Calculator</yellow></bold>", "*", 11); //  this is ugly

        $this->cli->arguments->add(
            [
                'proxmark_id' => [
                    'prefix' => 'p',
                    'description' => 'Proxmark-generated ID string',
                    'castTo' => 'string',
                ],
                'facility_code' => [
                    'prefix' => 'f',
                    'description' => 'Facility or site code [0-255] Must be used with -u',
                    'castTo' => 'int',
                ],
                'card_number' => [
                    'prefix' => 'u',
                    'description' => 'Unique or card code [0-65535] Must be used with -f',
                    'castTo' => 'int',
                ],
                'hex' => [
                    'prefix' => 'x',
                    'description' => 'Hex value of the 26bit RFID tag',
                    'castTo' => 'string',
                ],
                'binary' => [
                    'prefix' => 'b',
                    'description' => 'Binary value of the 26bit RFID tag',
                    'castTo' => 'string',
                ],
                'help' => [
                    'prefix' => 'h',
                    'longPrefix'  => 'help',
                    'description' => 'Prints usage information',
                    'noValue'     => \true,
                ],
            ]
        );

        $this->cli->arguments->parse();

        // DETERMINE MODES
        $modes = 0;
        $modes += $this->cli->arguments->defined('proxmark_id') ? self::FLAG_PROXMARK : 0;
        $modes += $this->cli->arguments->defined('facility_code') ? self::FLAG_FACILITY : 0;
        $modes += $this->cli->arguments->defined('card_number') ? self::FLAG_CARD : 0;
        $modes += $this->cli->arguments->defined('hex') ? self::FLAG_HEX : 0;
        $modes += $this->cli->arguments->defined('binary') ? self::FLAG_BINARY : 0;

        $calc = \null;
        try {
            if ($modes === self::FLAG_PROXMARK) {
                $calc = \Angorb\Wiegand26Bit\Calculator::fromProxmark(
                    $this->cli->arguments->get('proxmark_id')
                );
            }

            if ($modes === self::FLAG_BINARY) {
                $calc = \Angorb\Wiegand26Bit\Calculator::fromBinary(
                    $this->cli->arguments->get('binary')
                );
            }

            if ($modes === self::FLAG_HEX) {
                $calc = \Angorb\Wiegand26Bit\Calculator::fromHex(
                    $this->cli->arguments->get('hex')
                );
            }

            if ($modes === (self::FLAG_FACILITY + self::FLAG_CARD)) {
                $calc = \Angorb\Wiegand26Bit\Calculator::fromFacilityCard(
                    (int) $this->cli->arguments->get('facility_code'),
                    (int) $this->cli->arguments->get('card_number'),
                );
            }
        } catch (\InvalidArgumentException $ex) {
            $this->cli->out(
                "<background_red><black><bold>Error:</bold></black></background_red> " . $ex->getMessage()
            );
            exit(1);
        }

        if (($modes === self::FLAG_FACILITY) || ($modes === self::FLAG_CARD)) {
            switch ($modes) {
                case self::FLAG_FACILITY:
                    $error = "Option -f MUST be used with -u!";
                    break;
                case self::FLAG_CARD:
                    $error = "Option -u MUST be used with -f!";
                    break;
            }
            $this->cli->out("<background_red><black><bold>Error:</bold></black></background_red> " . $error);
            exit(1);
        }

        // show help
        if (
            !($calc instanceof \Angorb\Wiegand26Bit\Calculator)
            || empty($modes)
            || $this->cli->arguments->defined('help')
        ) {
            $this->cli->usage();
            exit(0);
        }

        $this->display($calc);
        exit(0);
    }

    private function display(\Angorb\Wiegand26Bit\Calculator $calc): void
    {
        $padding = $this->cli->padding(20);
        $padding->label('Facility Code')->result($calc->getFacilityCode());
        $padding->label('Card Number')->result($calc->getCardNumber());
        $padding->label('Binary')->result($calc->getBinary());
        $padding->label('Hex')->result($calc->getHex());
        $padding->label('Proxmark')->result($calc->getProxmark());
    }
}
