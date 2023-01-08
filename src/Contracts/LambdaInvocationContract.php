<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface LambdaInvocationContract
{
    public function getContext(): LambdaInvocationContextContract;

    public function getEvent(): InvocationHttpEventContract|InvocationEventContract;
}
