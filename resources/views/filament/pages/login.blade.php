<div class="flex min-h-screen bg-gradient-to-r from-gray-900 to-black">
<div class="relative hidden w-0 flex-1 lg:block">
    <div class="h-full w-full bg-cover bg-center" style="background-image: url('{{ asset('images/Cara-menentukan-harga-dropship-di-Shopee.jpg') }}');">
    </div>
</div>
    <div class="bg-white dark:bg-gray-900 flex flex-1 flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <div>
                <h2 class="mt-6 text-xl font-bold tracking-tight text-center text-gray-900 dark:text-white/90">Login</h2>
            </div>

            <div class="mt-8">
                <div class="mt-6">
                    <x-filament-panels::form wire:submit="authenticate">
                        {{ $this->form }}

                        <x-filament-panels::form.actions
                            :actions="$this->getCachedFormActions()"
                            :alignment="$this->getFormActionsAlignment()"
                            :full-width="$this->hasFullWidthFormActions()"
                            class="bg-blue-500"
                        />
                    </x-filament-panels::form>
                </div>
            </div>
        </div>
    </div>
    <x-filament-actions::modals />
</div>

