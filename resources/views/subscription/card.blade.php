<x-action-section>
    <x-slot name="title">
        {{ __('laravel-subscription::subscription.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('laravel-subscription::subscription.description') }}
    </x-slot>

    <x-slot name="content">

        <h3 class="text-lg font-medium text-gray-900">
            @if (Auth()->user()->subscribed())
                {{ __('laravel-subscription::subscription.card-title') }}:

                <span class="text-blue-500 uppercase font-bold">
                    {{ __(config('spark.billables.user.plans.' . Auth()->user()->getSubscribedPlan() . '.name')) }}
                </span>

            @else
                {{ __('laravel-subscription::subscription.card-no-subscription') }}
            @endif
        </h3>

        <div class="mt-3 max-w-xl text-sm text-gray-600">
            <p>
                {{ __('laravel-subscription::subscription.card-description') }}
            </p>
        </div>

        <div class="mt-5">
            <a href="/billing">
                <x-button type="button" href="/billing">
                    @if (Auth()->user()->subscribed())
                        {{ __('laravel-subscription::subscription.card-action-1') }}
                    @else
                        {{ __('laravel-subscription::subscription.card-action-2') }}
                    @endif
                </x-button>
            </a>
        </div>
    </x-slot>
</x-action-section>
