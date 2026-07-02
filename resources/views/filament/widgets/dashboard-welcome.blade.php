<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-1">
                <h2 class="text-xl font-semibold tracking-tight text-gray-950 dark:text-white">
                    Добро пожаловать, {{ $this->getUserName() }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Управляйте короткими ссылками и отслеживайте переходы в одном месте.
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap gap-3">
                <x-filament::button
                    tag="a"
                    :href="$this->getCreateLinkUrl()"
                    icon="heroicon-o-plus"
                >
                    Создать ссылку
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="$this->getLinksUrl()"
                    color="gray"
                    icon="heroicon-o-link"
                >
                    Мои ссылки
                </x-filament::button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
