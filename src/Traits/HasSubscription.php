<?php

namespace Atin\LaravelSubscription\Traits;


trait HasSubscription
{
    public function getSubscribedPlan(): int|null
    {
        return 0;
    }
}