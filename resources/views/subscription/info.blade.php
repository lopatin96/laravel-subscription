@if (in_array(auth()->user()?->stripeSubscription?->stripe_status, ['incomplete', 'pending'], true))
    <div class="py-3 px-5 bg-yellow-100 text-yellow-700 text-sm border-b border-yellow-200 text-center">
        {!! __('laravel-subscription::subscription.incomplete_or_pending') !!}
    </div>
@elseif (auth()->user()?->stripeSubscription?->stripe_status === 'past_due')
    <div class="py-3 px-5 bg-red-100 text-red-700 text-sm border-b border-red-200 text-center">
        {!! __('laravel-subscription::subscription.past_due') !!}
    </div>
@elseif (! auth()->user()?->subscribed() && auth()->user()?->onTrial())
    <div class="py-3 px-5 bg-indigo-100 text-indigo-700 text-sm border-b border-indigo-200 text-center">
        {!!
            trans_choice(
                'laravel-subscription::subscription.trial_until',
                 auth()->user()?->trial_ends_at->diff(now())->days,
                 ['days' => auth()->user()?->trial_ends_at->diff(now())->days],
            )
        !!}
    </div>
@elseif (! auth()->user()?->subscribed())
    <div class="py-3 px-5 bg-red-100 text-red-700 text-sm border-b border-red-200 text-center">
        {!! __('laravel-subscription::subscription.trial_period_is_over') !!}
    </div>
@endif
