<?php

namespace App\Providers;

use App\Support\Ai\OpenRouterDriver;
use App\Support\ExchangeRate\CurrencyConverterDriver;
use App\Support\ExchangeRate\CurrencyFreakDriver;
use App\Support\ExchangeRate\CurrencyLayerDriver;
use App\Support\ExchangeRate\OpenExchangeRateDriver;
use Illuminate\Support\ServiceProvider;
use InvoiceShelf\Modules\Registry;

class DriverRegistryProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerExchangeRateDrivers();
        $this->registerAiDrivers();
    }

    protected function registerExchangeRateDrivers(): void
    {
        Registry::registerExchangeRateDriver('currency_converter', [
            'class' => CurrencyConverterDriver::class,
            'label' => 'settings.exchange_rate.currency_converter',
            'website' => 'https://www.currencyconverterapi.com',
            'config_fields' => [
                [
                    'key' => 'type',
                    'type' => 'select',
                    'label' => 'settings.exchange_rate.server',
                    'options' => [
                        ['label' => 'settings.preferences.premium', 'value' => 'PREMIUM'],
                        ['label' => 'settings.preferences.prepaid', 'value' => 'PREPAID'],
                        ['label' => 'settings.preferences.free', 'value' => 'FREE'],
                        ['label' => 'settings.preferences.dedicated', 'value' => 'DEDICATED'],
                    ],
                    'default' => 'FREE',
                ],
                [
                    'key' => 'url',
                    'type' => 'text',
                    'label' => 'settings.exchange_rate.url',
                    'visible_when' => ['type' => 'DEDICATED'],
                ],
            ],
        ]);

        Registry::registerExchangeRateDriver('currency_freak', [
            'class' => CurrencyFreakDriver::class,
            'label' => 'settings.exchange_rate.currency_freak',
            'website' => 'https://currencyfreaks.com',
        ]);

        Registry::registerExchangeRateDriver('currency_layer', [
            'class' => CurrencyLayerDriver::class,
            'label' => 'settings.exchange_rate.currency_layer',
            'website' => 'https://currencylayer.com',
        ]);

        Registry::registerExchangeRateDriver('open_exchange_rate', [
            'class' => OpenExchangeRateDriver::class,
            'label' => 'settings.exchange_rate.open_exchange_rate',
            'website' => 'https://openexchangerates.org',
        ]);
    }

    protected function registerAiDrivers(): void
    {
        Registry::registerAiDriver('openrouter', [
            'class' => OpenRouterDriver::class,
            'label' => 'settings.ai.openrouter',
            'website' => 'https://openrouter.ai',
            'default_base_url' => 'https://openrouter.ai/api/v1',
            'supported_roles' => ['chat', 'text_generation'],
            'suggested_models' => [
                ['value' => 'openai/gpt-4o', 'label' => 'OpenAI GPT-4o'],
                ['value' => 'openai/gpt-4o-mini', 'label' => 'OpenAI GPT-4o mini'],
                ['value' => 'anthropic/claude-3.5-sonnet', 'label' => 'Anthropic Claude 3.5 Sonnet'],
                ['value' => 'anthropic/claude-3.5-haiku', 'label' => 'Anthropic Claude 3.5 Haiku'],
                ['value' => 'google/gemini-pro-1.5', 'label' => 'Google Gemini Pro 1.5'],
                ['value' => 'meta-llama/llama-3.3-70b-instruct', 'label' => 'Meta Llama 3.3 70B'],
            ],
            'config_fields' => [
                [
                    'key' => 'base_url',
                    'type' => 'text',
                    'label' => 'settings.ai.base_url',
                    'default' => 'https://openrouter.ai/api/v1',
                ],
            ],
        ]);
    }
}
