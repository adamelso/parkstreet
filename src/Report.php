<?php

namespace ParkStreet;

interface Report
{
    public function getTitle(): string;

    public function getTableRows(): array;
}
