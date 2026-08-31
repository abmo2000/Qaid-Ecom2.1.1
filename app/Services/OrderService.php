<?php

namespace App\Services;

use Illuminate\Pipeline\Pipeline;
use App\Commands\CreateGuestCommand;
use App\Commands\CreateOrderCommand;


class OrderService{


    protected array $pipes = [
        CreateGuestCommand::class,
        CreateOrderCommand::class,
    ];

    public function __invoke(array $data)
    {
        return app(Pipeline::class)
            ->send($data)
            ->through($this->pipes)
            ->thenReturn();
    }

  


}