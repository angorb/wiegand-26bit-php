<?php

namespace Angorb\Wiegand26Bit;

class Calculator
{
    private const MAX_BITS = 26;
    private const MAX_FACILITY_BITS = 8;
    private const MAX_CARD_BITS = 16;
    private const PARITY_BITS_TO_TEST = 12;
    private const PROXMARK_FRONT_BITS = 32;

    private int $facility;
    private int $card;
    private string $binary;
    private string $hex;
    private string $proxmark;

    private function __construct(string $binary)
    {
        $this->binary = $binary;

        $this->facility = \bindec(
            \substr($binary, 1, self::MAX_FACILITY_BITS)
        );

        $this->card = \bindec(
            \substr($binary, 9, self::MAX_CARD_BITS)
        );

        $this->hex = \base_convert($binary, 2, 16);

        $proxHex = \base_convert("1" . $binary, 2, 16);

        $this->proxmark = sprintf(
            "%x%08s",
            self::PROXMARK_FRONT_BITS,
            $proxHex
        );
    }

    /**
     * Returns a Calculator object populated with values derived from a combination of
     * a facility code (0-255) and a unique card/user ID number (0-65535).
     *
     * @param int $facility
     * @param int $card
     * @return Calculator
     * @throws OutOfBoundsException
     */
    public static function fromFacilityCard(int $facility, int $card): self
    {
        if ($facility < 0 || $facility > 255) {
            throw new \OutOfBoundsException(
                \sprintf(
                    "Invalid Facility Code '%s' [Range: 0-255]",
                    $facility
                )
            );
        }

        if ($card < 0 || $card > 65535) {
            throw new \OutOfBoundsException(
                \sprintf(
                    "Invalid Card Number '%s' [Range: 0-65535]",
                    $card
                )
            );
        }

        $facilityBin = \str_pad(
            \decbin($facility),
            self::MAX_FACILITY_BITS,
            '0',
            \STR_PAD_LEFT
        );

        $cardBin = \str_pad(
            \decbin($card),
            self::MAX_CARD_BITS,
            '0',
            \STR_PAD_LEFT
        );

        $combinedBin = $facilityBin . $cardBin;

        $firstBit = self::calcParityBit($combinedBin, \true);
        $lastBit = self::calcParityBit($combinedBin, \false);

        return new self($firstBit . $combinedBin . $lastBit);
    }

    /**
     * @param string $proxmarkId
     * @return Calculator
     */
    public static function fromProxmark(string $proxmarkId): self
    {
        $proxmarkDec = \hexdec($proxmarkId);

        $binary = \substr(
            \decbin($proxmarkDec),
            self::MAX_BITS * -1
        );

        return new self($binary);
    }

    /**
     * @param string $hex
     * @return Calculator
     */
    public static function fromHex(string $hex): self
    {
        $binary = \base_convert($hex, 16, 2);
        return new self($binary);
    }

    /**
     * @param string $binary
     * @return Calculator
     */
    public static function fromBinary(string $binary): self
    {
        return new self($binary);
    }

    /** @return int  */
    public function getFacilityCode(): int
    {
        return $this->facility;
    }

    /** @return int  */
    public function getCardNumber(): int
    {
        return $this->card;
    }

    /** @return string  */
    public function getHex(): string
    {
        return $this->hex;
    }

    /** @return string  */
    public function getBinary(): string
    {
        return $this->binary;
    }

    /** @return string  */
    public function getProxmark(): string
    {
        return $this->proxmark;
    }

    /** @return array  */
    public function getValues(): array
    {
        return [
            'facility' => $this->facility,
            'card' => $this->card,
            'binary' => $this->binary,
            'hex' => $this->hex,
            'proxmark' => $this->proxmark,
        ];
    }

    /**
     * @param string $binary
     * @param bool $parityTest
     * @return string
     */
    private static function calcParityBit(string $binary, bool $parityTest): string
    {
        $parityCount = 0;
        $parityTestOffset = 0;
        $parityBit = '0';

        if (!$parityTest) {
            $parityTestOffset = 12;
        }

        for ($i = 0 + $parityTestOffset; $i < self::PARITY_BITS_TO_TEST + $parityTestOffset; $i++) {
            if (\substr($binary, $i, 1) === "1") {
                $parityCount++;
            }
        }

        if (($parityCount % 2 && $parityTest) || ($parityCount % 2 == 0 && !$parityTest)) {
            $parityBit = '1';
        }

        return $parityBit;
    }
}
