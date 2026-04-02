@php($statusText = ucfirst($status ?? '-'))
<span @class([
    'admin-badge',
    'admin-badge-pending' => in_array($status, ['pending', 'delayed'], true),
    'admin-badge-confirmed' => in_array($status, ['confirmed', 'paid', 'completed', 'scheduled'], true),
    'admin-badge-cancelled' => in_array($status, ['cancelled', 'failed', 'refunded'], true),
    'admin-badge-default' => ! in_array($status, ['pending', 'delayed', 'confirmed', 'paid', 'completed', 'scheduled', 'cancelled', 'failed', 'refunded'], true),
])>
    {{ $statusText }}
</span>
