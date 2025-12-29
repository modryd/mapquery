<?php

namespace modryd\MapQuery;

interface POIClientInterface
{
    public function search(ViewBox $viewBox, array $filters = []): array;
}

