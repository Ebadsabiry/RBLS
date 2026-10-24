<?php

return [
    // Local servers archive inquiries; Vercel requires successful mail delivery.
    'delivery' => env('INQUIRY_DELIVERY', 'storage'),
    'recipient' => env('INQUIRY_RECIPIENT', 'info@romanbahram.com'),
];
