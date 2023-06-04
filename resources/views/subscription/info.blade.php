@if (! Auth::user()->subscribed() && Auth::user()->onTrial())
    <div class="py-3 px-5 bg-indigo-100 text-indigo-700 text-sm border-b border-indigo-200 text-center">
        {!!
            trans_choice(
                'laravel-subscription::subscription.trial_until',
                 Auth::user()->trial_ends_at->diff(now())->days,
                 ['days' => Auth::user()->trial_ends_at->diff(now())->days],
            )
        !!}
    </div>
@elseif (! Auth::user()->subscribed())
    <div class="py-3 px-5 bg-red-100 text-red-700 text-sm border-b border-red-200 text-center">
        {!! __('laravel-subscription::subscription.trial_period_is_over') !!}
    </div>
@endif
