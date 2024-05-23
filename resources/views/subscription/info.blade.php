@php
    $stripeStatus = auth()->user()?->stripeSubscription?->stripe_status;
@endphp

@if(in_array($stripeStatus, ['incomplete', 'pending'], true))
    <div class="py-3 px-5 bg-yellow-100 text-yellow-700 text-sm border-b border-yellow-200 text-center">
        {!! __('laravel-subscription::subscription.incomplete_or_pending') !!}
    </div>
@elseif($stripeStatus === 'past_due')
    <div class="py-3 px-5 bg-red-100 text-red-700 text-sm border-b border-red-200 text-center">
        {!! __('laravel-subscription::subscription.past_due') !!}
    </div>
@elseif(! auth()->user()?->subscribed())
    <div class="py-3 px-5 bg-red-100 text-red-700 text-sm border-b border-red-200 text-center">
        {!! __('laravel-subscription::subscription.trial_period_is_over') !!}
    </div>
@endif
