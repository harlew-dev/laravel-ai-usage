<thead {{ $attributes->merge(['class' => 'sticky top-0 z-10']) }}>
    <tr class="bg-background-secondary">
        {{ $slot }}
    </tr>
</thead>
