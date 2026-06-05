<?php

namespace Modules\HelloWorld\Providers;

use Illuminate\Support\Str;
use InvoiceShelf\Modules\Registry as ModuleRegistry;
use InvoiceShelf\Modules\Support\ModuleServiceProvider;

/**
 * Reference module that exercises every InvoiceShelf module-system surface.
 *
 * This file started life as the output of `php artisan module:make HelloWorld`
 * — which, thanks to the custom stubs shipped from the invoiceshelf/modules
 * package, already includes the Registry::registerMenu/registerSettings
 * skeleton. The schema below has been expanded beyond the stub default to
 * exercise more field types for test coverage.
 */
class HelloWorldServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'HelloWorld';

    protected string $nameLower = 'helloworld';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $slug = Str::kebab($this->name);

        // ----------------------------------------------------------------
        // Module script (Vue page registration)
        // ----------------------------------------------------------------
        // Registers a compiled JS file that the host app injects as a
        // <script type="module"> tag. The script calls
        // window.InvoiceShelf.booting() to add Vue routes before mount.
        ModuleRegistry::registerScript(
            $slug,
            module_path($this->name, 'resources/dist/init.js')
        );

        // ----------------------------------------------------------------
        // Sidebar menu item
        // ----------------------------------------------------------------
        // Adds a link in the company sidebar under the "Modules" section.
        // The `title` uses a Laravel translation key (namespace::file.key)
        // resolved server-side before being sent to the Vue frontend.
        // The `link` points to the module's own Vue page (registered by init.js).
        // The `icon` is any Heroicon name available in BaseIcon.
        ModuleRegistry::registerMenu($slug, [
            'title' => $this->nameLower.'::menu.title',
            'link' => '/admin/modules/'.$slug.'/dashboard',
            'icon' => 'HandRaisedIcon',
        ]);

        // ----------------------------------------------------------------
        // Schema-driven settings
        // ----------------------------------------------------------------
        // Registers a settings form that appears in the company modules
        // page. Each company configures settings independently. Values are
        // persisted as CompanySetting keys: module.{slug}.{field_key}.
        //
        // Supported field types: text, textarea, select, switch, number
        // Labels use Laravel translation keys, resolved server-side.
        ModuleRegistry::registerSettings($slug, [
            'sections' => [
                [
                    'title' => $this->nameLower.'::settings.greeting_section',
                    'fields' => [
                        [
                            'key' => 'greeting',
                            'type' => 'text',
                            'label' => $this->nameLower.'::settings.greeting',
                            'rules' => ['required', 'max:120'],
                            'default' => 'Hello, world!',
                        ],
                        [
                            'key' => 'recipient',
                            'type' => 'text',
                            'label' => $this->nameLower.'::settings.recipient',
                            'rules' => ['max:60'],
                            'default' => 'friend',
                        ],
                        [
                            'key' => 'show_emoji',
                            'type' => 'switch',
                            'label' => $this->nameLower.'::settings.show_emoji',
                            'default' => true,
                        ],
                    ],
                ],
                [
                    'title' => $this->nameLower.'::settings.style_section',
                    'fields' => [
                        [
                            'key' => 'tone',
                            'type' => 'select',
                            'label' => $this->nameLower.'::settings.tone',
                            'rules' => ['required'],
                            'default' => 'friendly',
                            'options' => [
                                'friendly' => 'Friendly',
                                'formal' => 'Formal',
                                'enthusiastic' => 'Enthusiastic',
                            ],
                        ],
                        [
                            'key' => 'note',
                            'type' => 'textarea',
                            'label' => $this->nameLower.'::settings.note',
                            'rules' => ['max:500'],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
