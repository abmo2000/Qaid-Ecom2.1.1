<?php

namespace   App\Models\Interfaces;


interface Cartable
{
    public function getCartPrice(): int;
    public function getCartName(): string;
    public function getCartAlbum(): string|array;
    public function getCartDescription(): ?string;

}