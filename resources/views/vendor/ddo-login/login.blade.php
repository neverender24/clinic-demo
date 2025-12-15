@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;

    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    @props([
        'after' => null,
        'heading' => null,
        'subheading' => null,
    ])

    <div class="fi-simple-layout">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_START, scopes: $renderHookScopes) }}

        @if (($hasTopbar ?? true) && filament()->auth()->check())
            <div class="fi-simple-layout-header">
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                    ])
                @endif

                @if (filament()->hasUserMenu())
                    @livewire(Filament\Livewire\SimpleUserMenu::class)
                @endif
            </div>
        @endif

        <div class="fi-simple-main-ctn bg-radial from-[#e8f4fd] from-40% to-[#c3e0fe]">
            <main
                @class([
                    'fi-simple-main',
                    'px-0',
                    'py-0',
                    'm-0',
                    'rounded-none',
                    'border-none',
                    'bg-transparent',
                    'ring-0',
                    'shadow-none',
                    ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                ])
                id="ddo-login"
            >
                <div class="flex gap-x-2 bg-linear-to-t/srgb from-[#0d2137] to-[#1a4568] rounded-2xl shadow-2xl">
                    <div class="basis-full sm:basis-1/2 flex-col sm:flex items-center justify-center gap-y-5">
                      
                       <div 
                            style="background-image: url('../images/e-medical-logo.png');"
                            class="bg-no-repeat bg-contain bg-center w-32 h-32"
                        ></div>


                       <div class="uppercase text-sepia_brown font-bold text-2xl text-white">
                        {{ config('app.name') ?? 'System Name'}}
                       </div>
                    </div>
                    <div class="basis-full sm:basis-1/2 py-10 px-10 bg-[#0d2137] rounded-r-2xl login-form-panel">
                        <div class="text-2xl text-white font-semibold mb-4">Login</div>
                        {{$slot}}
                    </div>
                </div>
            </main>
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>

<style>
    /* Login form panel styling - Dark mode with line inputs */
    .login-form-panel label,
    .login-form-panel .fi-fo-field-wrp-label span {
        color: #ffffff !important;
        font-weight: 500 !important;
    }

    .login-form-panel input,
    .login-form-panel .fi-input,
    .login-form-panel input[type="text"],
    .login-form-panel input[type="email"],
    .login-form-panel input[type="password"] {
        background-color: transparent !important;
        border: none !important;
        border-bottom: 2px solid rgba(255, 255, 255, 0.4) !important;
        border-radius: 0 !important;
        color: #ffffff !important;
        padding: 12px 4px !important;
        font-size: 15px !important;
        transition: all 0.3s ease !important;
        box-shadow: none !important;
    }

    .login-form-panel input:hover,
    .login-form-panel .fi-input:hover {
        border-bottom-color: rgba(255, 255, 255, 0.7) !important;
        box-shadow: none !important;
    }

    .login-form-panel input:focus,
    .login-form-panel .fi-input:focus,
    .login-form-panel input:focus-within {
        border-bottom-color: #ffffff !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .login-form-panel input::placeholder {
        color: rgba(255, 255, 255, 0.5) !important;
        font-weight: 400 !important;
    }

    /* Input wrapper for Filament - remove background */
    .login-form-panel .fi-input-wrp {
        background-color: transparent !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    /* Checkbox styling */
    .login-form-panel input[type="checkbox"] {
        width: 18px !important;
        height: 18px !important;
        border: 2px solid rgba(255, 255, 255, 0.6) !important;
        border-radius: 4px !important;
        padding: 0 !important;
        accent-color: #ffffff !important;
        background-color: transparent !important;
    }

    /* Button styling */
    .login-form-panel .fi-btn-primary,
    .login-form-panel button[type="submit"] {
        background: #ffffff !important;
        color: #0d2137 !important;
        border: none !important;
        border-radius: 8px !important;
        padding: 12px 24px !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        letter-spacing: 0.5px !important;
        transition: all 0.3s ease !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
    }

    .login-form-panel .fi-btn-primary:hover,
    .login-form-panel button[type="submit"]:hover {
        background: rgba(255, 255, 255, 0.9) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3) !important;
    }

    .login-form-panel .fi-btn-primary:active,
    .login-form-panel button[type="submit"]:active {
        transform: translateY(0) !important;
    }

    /* Links */
    .login-form-panel a {
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: 500 !important;
        transition: color 0.2s ease !important;
    }

    .login-form-panel a:hover {
        color: #ffffff !important;
        text-decoration: underline !important;
    }

    /* Remember me text */
    .login-form-panel .fi-fo-checkbox label span,
    .login-form-panel span {
        color: rgba(255, 255, 255, 0.9) !important;
    }
</style>
