<?php

namespace MuseWallet\SDK\Tests\Unit;

use MuseWallet\SDK\Enums\ApplyStatus;
use MuseWallet\SDK\Enums\CardLevel;
use MuseWallet\SDK\Enums\CardStatus;
use MuseWallet\SDK\Enums\Currency;
use MuseWallet\SDK\Enums\DocumentType;
use MuseWallet\SDK\Enums\KycStatus;
use MuseWallet\SDK\Tests\TestCase;

class EnumTest extends TestCase
{
    public function test_apply_status_returns_all_statuses()
    {
        $statuses = ApplyStatus::all();

        $this->assertIsArray($statuses);
        $this->assertArrayHasKey(ApplyStatus::CARD_INIT, $statuses);
        $this->assertArrayHasKey(ApplyStatus::APPROVED, $statuses);
        $this->assertArrayHasKey(ApplyStatus::REJECTED, $statuses);
    }

    public function test_apply_status_identifies_successful_status()
    {
        $this->assertTrue(ApplyStatus::isSuccessful(ApplyStatus::APPROVED));
        $this->assertTrue(ApplyStatus::isSuccessful(ApplyStatus::ISSUED));
        $this->assertFalse(ApplyStatus::isSuccessful(ApplyStatus::REJECTED));
        $this->assertFalse(ApplyStatus::isSuccessful(ApplyStatus::CARD_INIT));
    }

    public function test_apply_status_identifies_pending_status()
    {
        $this->assertTrue(ApplyStatus::isPending(ApplyStatus::CARD_INIT));
        $this->assertTrue(ApplyStatus::isPending(ApplyStatus::APPLYING));
        $this->assertFalse(ApplyStatus::isPending(ApplyStatus::APPROVED));
    }

    public function test_apply_status_identifies_rejected_status()
    {
        $this->assertTrue(ApplyStatus::isRejected(ApplyStatus::REJECTED));
        $this->assertTrue(ApplyStatus::isRejected(ApplyStatus::CARD_REJECT));
        $this->assertFalse(ApplyStatus::isRejected(ApplyStatus::APPROVED));
    }

    public function test_currency_returns_all_currencies()
    {
        $currencies = Currency::all();

        $this->assertIsArray($currencies);
        $this->assertArrayHasKey(Currency::USDT_TRC20, $currencies);
        $this->assertArrayHasKey(Currency::USDT_ERC20, $currencies);
        $this->assertArrayHasKey(Currency::ETH, $currencies);
    }

    public function test_currency_checks_support()
    {
        $this->assertTrue(Currency::isSupported(Currency::USDT_TRC20));
        $this->assertTrue(Currency::isSupported(Currency::ETH));
        $this->assertFalse(Currency::isSupported('INVALID_CURRENCY'));
    }

    public function test_currency_returns_network_info()
    {
        $this->assertEquals('Tron', Currency::network(Currency::USDT_TRC20));
        $this->assertEquals('Ethereum', Currency::network(Currency::ETH));
    }

    public function test_card_status_identifies_usable_card()
    {
        $this->assertTrue(CardStatus::isUsable(CardStatus::ACTIVE));
        $this->assertFalse(CardStatus::isUsable(CardStatus::INACTIVE));
        $this->assertFalse(CardStatus::isUsable(CardStatus::LOCKED));
    }

    public function test_card_status_identifies_activatable_card()
    {
        $this->assertTrue(CardStatus::canBeActivated(CardStatus::INACTIVE));
        $this->assertFalse(CardStatus::canBeActivated(CardStatus::ACTIVE));
        $this->assertFalse(CardStatus::canBeActivated(CardStatus::CLOSED));
    }

    public function test_card_level_validates_level()
    {
        $this->assertTrue(CardLevel::isValid(CardLevel::LEVEL_1));
        $this->assertTrue(CardLevel::isValid(CardLevel::LEVEL_5));
        $this->assertFalse(CardLevel::isValid('999'));
    }

    public function test_kyc_status_identifies_approved()
    {
        $this->assertTrue(KycStatus::isApproved(KycStatus::APPROVED));
        $this->assertFalse(KycStatus::isApproved(KycStatus::NOT_SET));
        $this->assertFalse(KycStatus::isApproved(KycStatus::REFUSED));
    }

    public function test_kyc_status_identifies_pending()
    {
        $this->assertTrue(KycStatus::isPending(KycStatus::WAIT_AUDIT));
        $this->assertTrue(KycStatus::isPending(KycStatus::IN_AUDIT));
        $this->assertFalse(KycStatus::isPending(KycStatus::APPROVED));
    }

    public function test_kyc_status_identifies_rejected()
    {
        $this->assertTrue(KycStatus::isRejected(KycStatus::REFUSED));
        $this->assertFalse(KycStatus::isRejected(KycStatus::APPROVED));
    }

    public function test_document_type_validates_type()
    {
        $this->assertTrue(DocumentType::isValid(DocumentType::PASSPORT));
        $this->assertTrue(DocumentType::isValid(DocumentType::ID_CARD));
        $this->assertFalse(DocumentType::isValid('999'));
    }

    public function test_enums_return_labels()
    {
        $this->assertEquals('Initiated', ApplyStatus::label(ApplyStatus::CARD_INIT));
        $this->assertEquals('USDT (TRC20)', Currency::label(Currency::USDT_TRC20));
        $this->assertEquals('Active', CardStatus::label(CardStatus::ACTIVE));
        $this->assertEquals('Level 1', CardLevel::label(CardLevel::LEVEL_1));
        $this->assertEquals('Approved', KycStatus::label(KycStatus::APPROVED));
        $this->assertEquals('Passport', DocumentType::label(DocumentType::PASSPORT));
    }
}

