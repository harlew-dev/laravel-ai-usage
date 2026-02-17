@props([
    'types',
    'providers',
])

<div>
    <x-ai-usage::sidebar.desktop :types="$types" :providers="$providers" />
    <x-ai-usage::sidebar.mobile :types="$types" :providers="$providers" />
</div>
