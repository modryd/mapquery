<?php

namespace MapQuery\POI;

interface NominatimClientInterface
{
    public function search(ViewBox $viewBox, array $filters = []): array;
}

