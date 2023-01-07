<?php

declare(strict_types=1);

namespace WPFortress\Runtime\Contracts;

interface InvocationContract
{
    public function getContext(): InvocationContextContract;

    public function getEvent(): InvocationHttpEventContract|InvocationEventContract;
}
