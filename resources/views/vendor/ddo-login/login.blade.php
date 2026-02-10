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

        {{-- Login Background Container --}}
        <div class="login-background min-h-dvh flex items-center justify-center
                    w-full max-md:items-start
                    p-0 md:!p-0 lg:!p-8">
            <main
                @class([
                    'fi-simple-main',
                    '!max-w-full !p-0 !m-0 !rounded-none !border-none !bg-transparent !ring-0 !shadow-none',
                    'max-lg:!w-full',
                    'lg:flex lg:justify-center',
                ])
                id="ddo-login"
            >
                {{-- Login Card --}}
                {{--
                    Tailwind Breakpoints:
                    - Default (< 640px): Mobile phones - stacked layout
                    - sm (640px+): Small tablets - stacked layout
                    - md (768px+): iPad portrait - side by side
                    - lg (1024px+): iPad landscape / Desktop
                    - xl (1280px+): Large desktop
                --}}
                <div class="login-card
                            flex flex-col w-full min-h-dvh
                            max-md:!shadow-none max-md:!rounded-none
                            md:min-h-0 md:max-w-3xl md:mx-auto md:rounded-2xl md:flex-row
                            lg:max-w-4xl lg:rounded-3xl
                            overflow-hidden">

                    {{-- Mobile Brand Header (visible only on mobile < md) --}}
                    <div class="login-brand-mobile
                                flex flex-col items-center justify-center
                                py-8 px-4
                                md:hidden">
                        <div
                            style="background-image: url('../images/e-medical-logo.png');"
                            class="login-logo-mobile
                                   w-16 h-16 sm:w-20 sm:h-20
                                   bg-no-repeat bg-contain bg-center"
                        ></div>
                        <div class="login-app-name-mobile
                                    text-white font-bold uppercase tracking-wider text-center
                                    text-base sm:text-lg
                                    mt-2 sm:mt-3">
                            {{ config('app.name') ?? 'System Name'}}
                        </div>
                    </div>

                    {{-- Desktop/Tablet Brand Panel (hidden on mobile, visible md+) --}}
                    <div class="login-brand-panel
                                hidden
                                md:flex md:basis-2/5
                                lg:basis-1/2
                                relative overflow-hidden
                                p-6 lg:p-8 xl:p-12">
                        <div class="login-brand-content
                                    flex flex-col items-center justify-center
                                    gap-4 lg:gap-6
                                    relative z-10 w-full">
                            <div class="login-logo-glow absolute"></div>
                            <div
                                style="background-image: url('../images/e-medical-logo.png');"
                                class="login-logo
                                       w-20 h-20 lg:w-28 lg:h-28
                                       bg-no-repeat bg-contain bg-center relative z-10"
                            ></div>
                            <div class="login-app-name
                                        text-white font-bold uppercase tracking-wider text-center
                                        text-lg lg:text-2xl">
                                {{ config('app.name') ?? 'System Name'}}
                            </div>
                            <div class="login-tagline
                                        text-white/80 tracking-wide text-center
                                        text-xs lg:text-sm">
                                Smart, Simple, and Customizable for Every Clinic
                            </div>
                        </div>
                        <div class="login-brand-decoration absolute inset-0 pointer-events-none"></div>
                    </div>

                    {{-- Form Panel --}}
                    <div class="login-form-panel
                                flex-1
                                p-6
                                md:basis-3/5 md:p-8
                                lg:basis-1/2 lg:p-10
                                xl:p-12">
                        <div class="login-form-header
                                    mb-6 lg:mb-8
                                    text-center md:text-left">
                            <div class="login-welcome
                                        font-bold mb-2
                                        text-2xl sm:text-2xl lg:text-3xl">
                                Welcome back
                            </div>
                            <div class="login-subtitle
                                        text-gray-500
                                        text-xs sm:text-sm">
                                Sign in to continue to your account
                            </div>
                        </div>
                        {{$slot}}
                    </div>
                </div>
            </main>
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIMPLE_LAYOUT_END, scopes: $renderHookScopes) }}
    </div>
</x-filament-panels::layout.base>
